<?php

namespace AliyunExt\ApiGateway;

use Exception;

class VerifyClientInfo
{
    protected $clientInfo = null;

    /**
     * 获取客户端信息
     * @param $clientHeader
     * @return array
     */
    public function getClientInfo($clientHeader)
    {
        if ( ! $clientHeader) return [];

        $clientHeader = explode('|', $clientHeader);
        if ( ! $clientHeader) return [];
        $clientArr  = [];
        foreach ($clientHeader as $clientInfo) {
            $client = explode(':', $clientInfo);
            if ( ! (isset($client[0]) && $client[0] && isset($client[1])))
                continue;

            $clientArr[$client[0]] = $client[1];
        }

        return $clientArr;
    }

    /**
     * 验证信息
     * @param $clientInfo
     * @return mixed
     * @throws Exception
     */
    public function verifyClientInfo($clientInfo)
    {
        if ( ! $clientInfo)
            throw new Exception("请设置客户端信息");

        if (isset($clientInfo['app_name'])){
            $clientInfo['client'] = 'app';
            if ( ! (isset($clientInfo['app_name']) && $clientInfo['app_name']))
                throw new Exception("请设置APP名称");
            if ( ! (isset($clientInfo['plat']) && $clientInfo['plat']))
                throw new Exception("请设置APP平台");
            if ( ! (isset($clientInfo['ver']) && $clientInfo['ver']))
                throw new Exception("请设置APP版本");
            if ( ! (isset($clientInfo['device']) && $clientInfo['device']))
                throw new Exception("请设置APP使用设备");
            if ( ! (isset($clientInfo['os']) && $clientInfo['os']))
                throw new Exception("请设置APP操作系统");
            if ( ! (isset($clientInfo['channel_name']) && $clientInfo['channel_name']))
                throw new Exception("请设置APP下载渠道");
            if ( ! (isset($clientInfo['udid']) && $clientInfo['udid']))
                throw new Exception("请设置APP的唯一编码");
            if ( ! (isset($clientInfo['ip']) && $clientInfo['ip']))
                throw new Exception("请设置APP的IP地址");
        } else if (isset($clientInfo['name'])) {
            $clientInfo['client'] = 'service';
            if ( ! (isset($clientInfo['name']) && $clientInfo['name']))
                throw new Exception("请设置服务端项目名称");
            if ( ! (isset($clientInfo['user-agent']) && $clientInfo['user-agent']))
                throw new Exception("请设置浏览器信息");
            if ( ! (isset($clientInfo['client_ip']) && $clientInfo['client_ip']))
                throw new Exception("请设置浏览器IP地址");
            if ( ! (isset($clientInfo['service_ip']) && $clientInfo['service_ip']))
                $clientInfo['service_ip'] = '';
        } else {
            throw new Exception("请设置客户端名称");
        }

        return $clientInfo;
    }
}
