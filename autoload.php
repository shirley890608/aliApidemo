<?php
/**
 * Created by PhpStorm.
 * User: wusongjian
 * Date: 16/7/4
 * Time: 下午4:37
 */
$mapping = array(
    'AliyunExt\Core\Auth\SignerInterface' => __DIR__ . '/Core/Auth/SignerInterface.php',
    'AliyunExt\Core\Auth\ShaHmac256Signer' => __DIR__ . '/Core/Auth/ShaHmac256Signer.php',
    'AliyunExt\Core\Http\HttpHelper' => __DIR__ . '/Core/Http/HttpHelper.php',
    'AliyunExt\Core\Http\HttpResponse' => __DIR__ . '/Core/Http/HttpResponse.php',
    'AliyunExt\Core\Sign\SignatureComposer' => __DIR__ . '/Core/Sign/SignatureComposer.php',
    'AliyunExt\ApiGateway\Client' => __DIR__ . '/ApiGateway/Client.php',
    'AliyunExt\ApiGateway\Backend' => __DIR__ . '/ApiGateway/Backend.php',
    'AliyunExt\Test\BaseClass' => __DIR__ . '/Demo/BaseClass.php',
);


spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require $mapping[$class];
    }
}, true);
