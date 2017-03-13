## Changelog
### 2016-08-10
* 更新body拼接


## 使用方法

* 设置 APP KEY `$client->setAppKey("23423044")`
* 设置 APP SECRET `$client->setAppSecret("135e18e3b99daab454457fe4a3761e54")` 
* 设置 HOST`$client->setHost("http://e1a73a396ea945b5beea12b895652fc7-cn-beijing.alicloudapi.com")` 
* 设置 PATH`$client->setPath("/login")` 
* 设置 CONTENT-TYPE`$client->setContentType(CONTENT_TYPE_FORM)` 
* 设置 METHOD`$client->setMethod("POST")` 
* 设置 BODY`$client->setBody()` 
* 设置 HEADER`$client->setHeaders([ 'V-App-Client-Information' => "plat:android|ver:1.0.1|device:xiaomi_4|os:android5.0|channel_name:渠道|app_name:hxwx|udid:设备编号|ip:192.168.0.1"])` 
* 发送请求`$client->executeCurl()` 
* 获取返回信息`$client->getRespose()->getBody()` 
