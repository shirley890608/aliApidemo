<?php

require_once __DIR__.'/../autoload.php';

use AliyunExt\ApiGateway\Backend;

class BackendDemo
{
    protected $backendSign;
    public function __construct()
    {
        $this->backendSign = new Backend();
        $this->backendSign->setSecretList(["DemoKey1"=>"DemoSecret1"]);
    }

    public function sign()
    {
        $headers = [
            'X-Ca-Proxy-Signature' => "vYpUZCP7O+xF0Ynumi8+O8GV3JveCA32nEXLucpf+QQ=",
            'X-Ca-Proxy-Signature-Headers' => "HeaderKey1,HeaderKey2",
            'X-Ca-Proxy-Signature-Secret-Key' => "DemoKey1",
            'HeaderKey1' => "HeaderValue1",
            'HeaderKey2' => "HeaderValue2",
            'Content-Type' => "application/x-www-form-urlencoded",
        ];
        $body = [
            'QueryKey1' => 'QueryValue1',
            'QueryKey2' => 'QueryValue2',
            'FormKey1' => 'FormValue1',
            'FormKey2' => 'FormValue2',
        ];
        $path = "/demo/uri";
        $method = "POST";

        print "headers-sign: ".$headers['X-Ca-Proxy-Signature'];
        print "\n";
        print "service-sign: ".$this->backendSign->serviceSign($path, $method, $headers, $body)."\n";
    }
}

$test = new BackendDemo();
$test->sign();