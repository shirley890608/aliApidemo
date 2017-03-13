<?php
namespace AliyunExt\Core\Http;

/**
 * Created by PhpStorm.
 * User: wusongjian
 * Date: 16/7/4
 * Time: 下午3:03
 */
class HttpResponse
{
    private $body;
    private $status;
    private $headers;

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setHeader($headers)
    {
        $this->headers = $headers;
    }

    public function getHeader()
    {
        return $this->headers;
    }

    public function setStatus($status)
    {
        $this->status  = $status;
    }

    public function isSuccess()
    {
        if(200 <= $this->status && 300 > $this->status)
        {
            return true;
        }
        return false;
    }
}