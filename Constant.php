<?php

// 代理
define('ENABLE_HTTP_PROXY', FALSE);
define('HTTP_PROXY_IP', '127.0.0.1');
define('HTTP_PROXY_PORT', '8888');

// 签名
define('SIGNATURE_PREFIX', 'X-Ca-');
define('X_CA_SIGNATURE', 'X-Ca-Signature');
define('X_CA_SIGNATURE_HEADERS', 'X-Ca-Signature-Headers');
define('X_CA_TIMESTAMP', 'X-Ca-Timestamp');
define('X_CA_NONCE', 'X-Ca-Nonce');
define('X_CA_KEY', 'X-Ca-Key');
define('X_CA_STAGE', 'X-Ca-Stage');

// 后台签名
define('X_CA_PROXY_SIGNATURE', 'x-ca-proxy-signature');
define('X_CA_PROXY_SIGNATURE_SECRET_KEY', 'x-ca-proxy-signature-secret-key');
define('X_CA_PROXY_SIGNATURE_HEADERS', 'x-ca-proxy-signature-headers');

// header
define('HTTP_HEADER_ACCEPT', 'Accept');
define('HTTP_HEADER_CONTENT_MD5', 'Content-MD5');
define('HTTP_HEADER_CONTENT_TYPE', 'Content-Type');
define('HTTP_HEADER_USER_AGENT', 'User-Agent');
define('HTTP_HEADER_DATE', 'Date');

// protocol
define('HTTP_PROTOCOL_HTTP', 'http');
define('HTTP_PROTOCOL_HTTPS', 'https');

// http method
define('HTTP_METHOD_GET', 'GET');
define('HTTP_METHOD_POST', 'POST');
define('HTTP_METHOD_PUT', 'PUT');
define('HTTP_METHOD_DELETE', 'DELETE');
define('HTTP_METHOD_HEADER', 'HEADER');

// content-type
define('CONTENT_TYPE_FORM', 'application/x-www-form-urlencoded');
define('CONTENT_TYPE_STREAM', 'application/octet-stream');
define('CONTENT_TYPE_JSON', 'application/json');
define('CONTENT_TYPE_XML', 'application/xml');
define('CONTENT_TYPE_TEXT', 'application/text');

// body type
define('BODY_TYPE_FORM', 'FORM');
define('BODY_TYPE_STREAM', 'STREAM');
