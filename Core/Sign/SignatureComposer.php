<?php
namespace AliyunExt\Core\Sign;

/**
 * Created by PhpStorm.
 * User: wusongjian
 * Date: 16/7/4
 * Time: 下午3:12
 */
require_once __DIR__.'/../../Constant.php';

class SignatureComposer
{
    protected $signType = 'client';

    public function __construct($type = 'client')
    {
        $this->signType = $type;
    }

    /**
     * 设置签名类型
     * @param $type
     */
    public function setSignType($type)
    {
        $this->signType = $type;
    }

    /**
     * 组装签名字符串
     * @param $path
     * @param $method
     * @param $headers
     * @param array $body
     * @return string
     */
    public function buildSignStr($path, $method, &$headers, $body = [], $contentMd5 = null)
    {
        if ($this->signType == 'client')
            return $this->buildSignStrByClient($path, $method, $headers, $body);

        if ($this->signType == 'backend')
            return $this->buildSignStrByBackend($path, $method, $headers, $body, $contentMd5);

        return '';
    }

    /**
     * 组装签名字符串
     * @param $path
     * @param $method
     * @param $headers
     * @param array $body
     * @return string
     */
    protected function buildSignStrByBackend($path, $method, &$headers, $body = [], $contentMd5 = null)
    {
        $lf      = "\n";
        // method
        $signStr = $method;
        $signStr .= $lf;

        // content-md5
        if ($contentMd5) {
            $signStr .= trim($contentMd5)."\r\n";
        }
        $signStr .= $lf;

        // headers
        $signStr .= $this->formatHeaderByBackend($headers);

        // body
        $signStr .= $this->buildResource($path, $contentMd5 ? [] : $body);

        return $signStr;
    }

    /**
     * 后台contentMd5
     * @param $inputStreamBytes
     * @return string
     */
    public function buildContentMd5($inputStreamBytes)
    {
        if ( ! $inputStreamBytes)
            return '';

        return base64_encode(md5($inputStreamBytes, true));
    }

    /**
     * 后台header验证
     * @param $headers
     * @return string
     */
    protected function formatHeaderByBackend($headers)
    {
        if ( ! isset($headers[strtolower(X_CA_PROXY_SIGNATURE_HEADERS)]))
            return '';

        $proxySignHeaders =  strtolower($headers[strtolower(X_CA_PROXY_SIGNATURE_HEADERS)]);
        if ( ! $proxySignHeaders)
            return '';

        $proxySignHeaders = explode(",", $proxySignHeaders);
        $signHeaders = "";
        foreach($proxySignHeaders as $signHeaderKey) {
            if ( ! isset($headers[$signHeaderKey]))
                return "";

            $signHeaders .= $signHeaderKey.':'.$headers[$signHeaderKey]."\n";
        }

        return $signHeaders;
    }

    /**
     * 组装签名字符串
     * @param $path
     * @param $method
     * @param $headers
     * @param array $body
     * @return string
     */
    protected function buildSignStrByClient($path, $method, &$headers, $body = [])
    {
        $lf      = "\n";
        // method
        $signStr = $method;
        $signStr .= $lf;

        // accept
        if (isset($headers[HTTP_HEADER_ACCEPT]) && $headers[HTTP_HEADER_ACCEPT]) {
            $signStr .= $headers[HTTP_HEADER_ACCEPT];
        }
        $signStr .= $lf;

        // content-md5
        if (isset($headers[HTTP_HEADER_CONTENT_MD5]) && $headers[HTTP_HEADER_CONTENT_MD5]) {
            $signStr .= $headers[HTTP_HEADER_CONTENT_MD5];
        }
        $signStr .= $lf;

        // content-type
        if (isset($headers[HTTP_HEADER_CONTENT_TYPE]) && $headers[HTTP_HEADER_CONTENT_TYPE]) {
            $signStr .= $headers[HTTP_HEADER_CONTENT_TYPE];
        }
        $signStr .= $lf;

        // date
        if (isset($headers[HTTP_HEADER_DATE]) && $headers[HTTP_HEADER_DATE]) {
            $signStr .= $headers[HTTP_HEADER_DATE];
        }
        $signStr .= $lf;

        // headers
        $signStr .= $this->formatHeader($headers);

        // body
        $signStr .= $this->buildResource($path, $body);

        return $signStr;
    }

    /**
     * 组装body
     * @param $path
     * @param array $body
     * @return string
     */
    protected function buildResource($path, $body = [])
    {
        $uriArr = explode('?', $path);
        $query = isset($uriArr[1]) ? $uriArr[1] : false;
        $signStr = $uriArr[0];

        $queryArr = [];
        if ($query) {
            $query = explode('&', $query);
            foreach ($query as $paramStr) {
                $paramArr = explode('=', $paramStr);
                $key = $paramArr[0];
                $value = isset($paramArr[1]) ? $paramArr[1] : '';
                $queryArr[$key] = $value;
            }
            ksort($queryArr);
        }

        if ($body) {
            ksort($body);
            foreach ($body as $key => $value) {
                if ( ! isset($queryArr[$key]))
                    $queryArr[$key] = $value;
            }
        }
        $body = $queryArr;
        if ( ! $body)
            return $signStr;

        if ($body) {
            foreach ($body as $key => $value) {
                if (is_array($value)) {
                    ksort($value);
                    $body[$key] = $value;
                }
            }
        }

        $body = http_build_query($body);
        if ( ! $body)
            return $signStr;

        $bodyArr = explode('&', $body);
        if ( ! $bodyArr)
            return $signStr;

        $body = [];
        foreach ($bodyArr as $key => $value) {
            $query = explode('=', $value);
            if ($query[1] === '') {
                $body[] = $query[0];
            } else {
                $body[] = $query[0].'='.urldecode($query[1]);
            }
        }

        return $signStr.'?'.join('&', $body);
    }

    /**
     * 组装header
     * @param $headers
     * @return string
     */
    protected function formatHeader(&$headers)
    {
        $lf = "\n";
        $tempHeaders = '';
        if ($headers) {
            ksort($headers);
            $signHeaders = [];
            foreach ($headers as $key => $value) {
                if ($key == X_CA_SIGNATURE || $key == X_CA_SIGNATURE_HEADERS)
                    continue;

                if (stripos($key, SIGNATURE_PREFIX) !== false){
                    $tempHeaders .= $key.':'.$value.$lf;
                    $signHeaders[] = $key;
                }
            }
            $headers[X_CA_SIGNATURE_HEADERS] = join(',',$signHeaders);
        }

        return $tempHeaders;
    }
}
