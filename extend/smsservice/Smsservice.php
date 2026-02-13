<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 2021~2024 http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/

declare (strict_types = 1);
namespace smsservice;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use AlibabaCloud\Tea\Exception\TeaError;
use think\facade\Config;
use think\facade\Log;

class Smsservice
{
    protected $client;

    public function __construct()
    {
        $config = config('aliyun');

        $configData = new \Darabonba\OpenApi\Models\Config([
            'accessKeyId' => $config['access_key_id'],
            'accessKeySecret' => $config['access_key_secret'],
            'endpoint' => 'dysmsapi.aliyuncs.com',
            'protocol' => 'HTTP',
            'regionId' => $config['region_id']
        ]);

        $this->client = new Dysmsapi($configData);
    }

    /**
     * 发送短信
     *
     * @param string $phone 手机号
     * @param string $templateCode 模板CODE（如：SMS_123456789）
     * @param array $params 模板变量，如 ['code' => '1234']
     * @return array
     */
    public function sendSms(string $phone, string $templateCode, array $params = [])
    {		
		$request = new SendSmsRequest([
            "signName" => config('aliyun.sign_name'),
            "templateCode" => $templateCode,
            "phoneNumbers" => $phone,
            "templateParam" => json_encode($params, JSON_UNESCAPED_UNICODE)
        ]);

        try {
            $response = $this->client->sendSms($request);
            $body = $response->body;
			return json_decode(json_encode($body),true);
        } catch (TeaUnableRetryError $e) {
            return ['code' => 1, 'msg' => '网络错误或请求失败', 'error' => $e->getMessage()];
        }
    }
}
