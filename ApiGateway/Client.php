<?php
namespace AliyunExt\ApiGateway;

/**
 * Created by PhpStorm.
 * User: wusongjian
 * Date: 16/7/4
 * Time: 下午3:09
 */
require_once __DIR__.'/../Constant.php';

use AliyunExt\Core\Auth\ShaHmac256Signer;
use AliyunExt\Core\Http\HttpHelper;
use AliyunExt\Core\Sign\SignatureComposer;
use Exception;

class Client
{
    protected $body;
    protected $path;
    protected $url;
    protected $host;
    protected $headers = [];
    protected $method;
    protected $contentType;
    protected $appKey;
    protected $appSecret;
    protected $signatureHeaders = '';
    protected $accept;
    protected $response = null;

    /**
     * 发送信息
     * @return mixed
     * @throws Exception
     */
    public function executeCurl()
    {
        if ( ! $this->host)
            throw new Exception("Host Not required");

        if ( ! $this->path)
            throw new Exception("Path Not required");

        if ( ! $this->method)
            throw new Exception("Method Not required");

        $headers = $this->buildHeaders();
        $http = new HttpHelper();
        $this->response = $http->curl($this->url, $this->method, $this->body, $headers);

        return [
            'status' => $this->response->getStatus(),
            'header' => $this->response->getHeader(),
            'body' => $this->response->getBody(),
        ];
    }

    /**
     * 设置header
     * @return array
     */
    public function buildHeaders()
    {
        $headers = $this->headers;          //header头参数
        $headers[X_CA_KEY] = $this->appKey; //appkey
        $headers[X_CA_TIMESTAMP] = $this->getMillisecond(); //微秒时间戳

        if ($this->contentType){
            $headers[HTTP_HEADER_CONTENT_TYPE] = $this->contentType;
        } else {
            $headers[HTTP_HEADER_CONTENT_TYPE] = CONTENT_TYPE_JSON;     //请求内容参数类型
        }

        if ($this->accept) {
            $headers[HTTP_HEADER_ACCEPT] = $this->accept;
        } else {
            $headers[HTTP_HEADER_ACCEPT] = CONTENT_TYPE_JSON;           //返回内容类型
        }

        $signatureComposer = new SignatureComposer();
        if (in_array($this->method, [HTTP_METHOD_POST, HTTP_METHOD_PUT])
            && $headers[HTTP_HEADER_CONTENT_TYPE] != CONTENT_TYPE_FORM) {
            $headers[HTTP_HEADER_CONTENT_MD5] = base64_encode(md5($this->body, true));
            $strTosign = $signatureComposer->buildSignStr($this->path, $this->method, $headers);                    //非正常请求
        } else {
            $strTosign = $signatureComposer->buildSignStr($this->path, $this->method, $headers, $this->body);       //get 、post请求
        }

        $shahmac256 = new ShaHmac256Signer();
        $headers[X_CA_SIGNATURE] = $shahmac256->signString($strTosign, $this->appSecret);
        $this->headers = $headers;


        return $this->headers;
    }

    /**
     * 获取返回信息
     * @return null
     */
    public function getRespose()
    {
        return $this->response;
    }

    /**
     * 获取Body
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * 设置body
     * @param $data
     */
    public function setBody($data)
    {
        $this->body = $data;
    }

    /**
     * 获取accept
     * @return mixed
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * 设置accept
     * @param $data
     */
    public function setAccept($data)
    {
        $this->accept = $data;
    }

    /**
     * 获取url
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 获取url
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * 设置url
     * @param $data
     */
    public function setHost($data)
    {
        $this->host = $data;
        if ($this->path)
            $this->url = $this->host.$this->path;
    }

    /**
     * 获取url
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * 设置url
     * @param $data
     */
    public function setPath($data)
    {
        $this->path = $data;
        if ($this->host)
            $this->url = $this->host.$this->path;
    }

    /**
     * 获取 headers
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 设置 headers
     * @param $data
     */
    public function setHeaders($data)
    {
        $this->headers = $data;
    }

    /**
     * 获取 method
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * 设置 method
     * @param $data
     */
    public function setMethod($data)
    {
        $this->method = strtoupper($data);
    }

    /**
     * 获取 contentType
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 设置 contentType
     * @param $data
     */
    public function setContentType($data)
    {
        $this->contentType = $data;
    }

    /**
     * 获取 appKey
     * @return mixed
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * 设置 appKey
     * @param $data
     */
    public function setAppKey($data)
    {
        $this->appKey = $data;
    }

    /**
     * 获取 appSecret
     * @return mixed
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * 设置 appSecret
     * @param $data
     */
    public function setAppSecret($data)
    {
        $this->appSecret = $data;
    }

    /**
     * 获取毫秒
     * @return int
     */
    protected function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());

        return (int)((floatval($t1)+floatval($t2))*1000);
    }
}