<?php

namespace AliyunExt\Core\Auth;

interface SignerInterface
{
    public function  getSignatureMethod();

    public function  getSignatureVersion();

    public function signString($source, $accessSecret);
}
