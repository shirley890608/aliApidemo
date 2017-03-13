<?php
namespace AliyunExt\Test;
use AliyunExt\ApiGateway\Client;

class BaseClass
{
    protected $appKey = '';
    protected $appSercet = '';
    protected $headers=[];

    protected $onlineAppKey = '2347602211';
    protected $testAppKey = '2342304422';
    protected $onlineAppSercet = '4e02e14f7895ccd3f72f6ecac7580df1';
    protected $testAppSercet = '135e18e3b99daab454457fe4a3761e54';

    public function __construct($data) {
        $this->client = new Client();
        $this->setData($data);
        $this->envChoose();
    }

    /**
     * SET received Array Params
     * @param $data
     */
    protected function setData($data) {
        foreach($data as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * check the received params
     * @throws \Exception
     */
    public function checkParams() {
        if(!(isset($this->path) && $this->path))
            throw new \Exception("Params miss host");

        if(!(isset($this->host) && $this->host))
            throw new \Exception("Params miss host");

        if(!(isset($this->method) && $this->method))
            throw new \Exception("Params miss request method");
    }

    /**
     * environ
     */
    public function envChoose()
    {
        if('test' == strtolower($this->source)) {
            $this->appKey = $this->testAppKey;
            $this->appSercet = $this->testAppSercet;
            $this->headers = [
                X_CA_STAGE => 'test',
                'V-App-Client-Information' => "name:me|client_ip:192.168.0.1|user-agent:test"
            ];
        } else if('online' == strtolower($this->source)) {
            $this->appKey = $this->onlineAppKey;
            $this->appSercet = $this->onlineAppSercet;
            $this->headers = [
                'V-App-Client-Information' => "plat:ios|ver:2.5.5|device:iphone|os:ios|channel_name:ios|app_name:hxwx|udid:D3DE706E-E9BD-40B4-B11B-299DE35E009C|ip:169.254.179.248"
            ];
        }
    }

    /**
     * get params
     * @author lulijuan
     * @return string
     */
    public function getParams()
    {
        if(strtolower($this->method) == 'post')
        {
            return $this->params;
        } else if(strtolower($this->method) == 'get'){
            return '?'.http_build_query($this->params);
        }
    }


    /**
     * 设置clien类的参数
     */
    public function setClient()
    {
        $this->client->setAppKey($this->appKey);
        $this->client->setAppSecret($this->appSercet);
        $this->client->setHost($this->host);
        $this->client->setContentType(CONTENT_TYPE_FORM);
        $this->client->setMethod($this->method);
        if(strtolower($this->method) == 'get') {
            $this->path = $this->path.$this->getParams();
        } else if(strtolower($this->method) == 'post') {
            $this->client->setBody($this->getParams());
        }
        $this->client->setPath($this->path);
        $this->client->setHeaders($this->headers);

    }

    /**
     * 获取订单签名
     * @param $data
     * @return string
     */
    private function orderSign($data)
    {
        if(!$data)
            return '';
        foreach ($data as $k => $v) {
            if($k == 'sign' || $k == 'time_stamp' || $k == 'request_client_info' || !$v)
                continue;

            $keys[] = $k;
        }

        rsort($keys);

        $str = '';
        foreach ($keys as $key => $value) {
            $str = $str. $keys[$key] . '=' .$data[$value];
        }

        $sign = md5(strtolower($str));

        return $sign;
    }

    /**
     * execute aliyun api request
     * @throws \Exception
     */
    public function executeApi() {
        $this->checkParams();
        $this->setClient();
        $result = $this->client->executeCurl();

        var_dump($result);

    }

}

