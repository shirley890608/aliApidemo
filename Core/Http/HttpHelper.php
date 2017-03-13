<?php
namespace AliyunExt\Core\Http;

/**
 * Created by PhpStorm.
 * User: wusongjian
 * Date: 16/7/4
 * Time: 下午3:02
 */

use Exception;

class HttpHelper
{
    public static $connectTimeout = 30000;//30 second
    public static $readTimeout = 80000;//80 second

    public static function curl($url, $httpMethod = "GET", $postFields = null,$headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        if(ENABLE_HTTP_PROXY) {
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY_IP);
            curl_setopt($ch, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? http_build_query($postFields) : $postFields);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if (self::$readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
        }
        if (self::$connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
        }
        //https request
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_array($headers) && 0 < count($headers))
        {
            $httpHeaders =self::getHttpHearders($headers);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$httpHeaders);
        }
        $cContent = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $httpResponse = new HttpResponse();
        $httpResponse->setBody(substr($cContent, $headerSize));
        $httpResponse->setStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $httpResponse->setHeader(substr($cContent, 0, $headerSize));
        if (curl_errno($ch))
        {
            throw new Exception("Speicified endpoint or uri is not valid.", "SDK.ServerUnreachable");
        }
        curl_close($ch);
        return $httpResponse;
    }

    static function getHttpHearders($headers)
    {
        $httpHeader = array();
        foreach ($headers as $key => $value)
        {
            array_push($httpHeader, $key.":".$value);
        }
        return $httpHeader;
    }
}