<?php

require_once __DIR__ . '/../autoload.php';
use AliyunExt\Test\BaseClass;

class TestClass extends  BaseClass
{
    //Array Params
    public  $data = [
        'source'    => 'test',
        'host'      => 'http://api.public.hxsd.tv',
        'path'      => '/v3/login',
        'method'    => 'post',
        'params'    =>  [
        'username' => '131619029690',
        'password' => 'testpwd',
        //'sign' => $this->orderSign()
        ],
    ];

    public function __construct()
    {
        parent::__construct($this->data);
    }

    //执行结果
    public function index()
    {
        $this->executeApi();

    }
}

$test = new TestClass();
$test->index();