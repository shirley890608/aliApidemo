<?php

namespace AliyunExt\Core\Auth;

class ShaHmac1Signer implements SignerInterface
{
    public function signString($source, $accessSecret)
    {
        return	base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
    }

    public function  getSignatureMethod() {
        return "HMAC-SHA1";
    }

    public function getSignatureVersion() {
        return "1.0";
    }

}
