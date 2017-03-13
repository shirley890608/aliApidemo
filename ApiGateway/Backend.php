<?php
namespace AliyunExt\ApiGateway;

require_once __DIR__.'/../Constant.php';

use AliyunExt\Core\Auth\ShaHmac256Signer;
use AliyunExt\Core\Sign\SignatureComposer;
use Exception;

class Backend
{
    protected $appKey;
    protected $appSecret;
    protected $signtureComposer;
    protected $shahmac256;
    protected $secretList = null;

    public function __construct($secretList = [])
    {
        $this->signtureComposer = new SignatureComposer('backend');
        $this->shahmac256 = new ShaHmac256Signer();
        $this->secretList = $secretList;
    }

    /**
     * @param $path   string  访问路径 /path/api/test
     * @param $httpMethod string POST GET
     * @param $headers array 请求头部 $_SERVER中的HTTP_*
     * @param $body array  from表单数据
     * @param null $inputStreamBytes 二进制流数据  get_file_contents("php://input()")
     * @return bool
     * @throws Exception
     */
    public function vertifySign($path, $httpMethod, $headers, $body = [], $inputStreamBytes = null)
    {
        $path       = urldecode($path);
        $httpMethod = strtoupper($httpMethod);
        $this->arrayKeyToLower($headers);

        if ( ! $headers)
            throw new Exception("Headers 不能为空");
        if ( ! isset($headers[strtolower(X_CA_PROXY_SIGNATURE)]))
            throw new Exception("签名不能为空");
        if ( ! isset($headers[strtolower(HTTP_HEADER_CONTENT_TYPE)]))
            throw new Exception("请设置请求类型Content-Type");
        if ( ! isset($headers[strtolower('V-App-Client-Information')]))
            throw new Exception("请设置客户端信息.V-App-Client-Information");

        $contentMd5 = null;
        if (in_array($httpMethod, [HTTP_METHOD_POST, HTTP_METHOD_PUT])
            && stripos($headers[strtolower(HTTP_HEADER_CONTENT_TYPE)], CONTENT_TYPE_STREAM) !== false
            && $inputStreamBytes) {
            $contentMd5 = $this->signtureComposer->buildContentMd5($inputStreamBytes);
            if (isset($headers[strtolower(HTTP_HEADER_CONTENT_MD5)]) && $contentMd5 != $headers[strtolower(HTTP_HEADER_CONTENT_MD5)]){
                throw new Exception("Content-Md5 error!");
            }
        }

        $serviceSign = $this->serviceSign($path, $httpMethod, $headers, $body, $contentMd5);

        if ($headers[strtolower(X_CA_PROXY_SIGNATURE)] != $serviceSign)
            throw new Exception("签名验证失败");

        return true;
    }

    /**
     * @param $path
     * @param $httpMethod
     * @param $headers
     * @param array $body
     * @param null $contentMd5
     * @return string
     * @throws Exception
     */
    public function serviceSign($path, $httpMethod, $headers, $body = [], $contentMd5 = null)
    {
        $this->arrayKeyToLower($headers);

        if ( ! $this->secretList)
            throw new Exception("无效SecretList");

        if ( ! isset($headers[strtolower(X_CA_PROXY_SIGNATURE_SECRET_KEY)]))
            throw new Exception("1无效SecretKey");

        if ( ! isset($this->secretList[$headers[strtolower(X_CA_PROXY_SIGNATURE_SECRET_KEY)]]))
            throw new Exception("2无效SecretKey");

        $secret = $this->secretList[$headers[strtolower(X_CA_PROXY_SIGNATURE_SECRET_KEY)]];

        $signString = $this->signtureComposer->buildSignStr($path, $httpMethod, $headers, $body, $contentMd5);
        $sign = $this->shahmac256->signString($signString, $secret);

        return $sign;
    }

    /**
     * 设置 appKey
     * @param $data
     */
    public function setSecretList($data)
    {
        $this->secretList = $data;
    }

    /**
     * 获取 appSecret
     * @return mixed
     */
    public function getSecretList()
    {
        return $this->secretList;
    }

    public function arrayKeyToLower(&$arr)
    {
        if ( ! $arr)
            return false;

        $newArr = [];
        foreach($arr as $key=>$value) {
            $newArr[strtolower($key)] = $value;
            unset($arr[$key]);
        }

        $arr = $newArr;

        return true;
    }
}