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
namespace app\api\controller;

use think\facade\Db;
use think\facade\View;


class Map
{	
	/**
     * 构造函数
     */
	protected $key = '5DFBZ-BUJCI-Y5DGO-UK6P6-QACGK-3RBV2';
	//标注坐标
    public function to_marker()
    {
		$param = get_params();
		$address = isset($param['address'])?$param['address']:'北京市天安门广场';
		$address = preg_replace('/\s+/', '',$address);
        $key = $this->key;
		$location = ['lat'=>39.90374,'lng'=>116.397827];
		try {
            $url = "https://apis.map.qq.com/ws/geocoder/v1/?address={$address}&key={$key}";            
            $result = file_get_contents($url);
            $data = json_decode($result, true);            
            if ($data['status'] == 0 && isset($data['result'])) {
				$location = $data['result']['location'];
            }
        } catch (\Exception $e) {
            
        }
		View::assign('address', $address);
		View::assign('location', $location);
		View::assign('key', $key);
		return View();
    }
	
	//坐标
    public function marker()
    {
		$param = get_params();
		$address = isset($param['address'])?$param['address']:'北京市天安门广场';
		$address = preg_replace('/\s+/', '',$address);
		$latlng = isset($param['latlng'])?$param['latlng']:'39.90374,116.397827';
        $key = $this->key;
		View::assign('address', $address);
		View::assign('latlng', $latlng);
		View::assign('key', $key);
		return View();
    }
	
	//H5的iframe定位打卡
    public function geolocation_clock()
    {
		$param = get_params();
        $key = $this->key;
		View::assign('key', $key);
		return View();
    }

	//H5的iframe定位
    public function geolocation()
    {
		$param = get_params();
        $key = $this->key;
        $key = '5DFBZ-BUJCI-Y5DGO-UK6P6-QACGK-3RBV2';
		View::assign('key', $key);
		return View();
    }
	
	//H5的js定位
    public function geolocation_js()
    {
		$param = get_params();
        $key = $this->key;
		View::assign('key', $key);
		return View();
    }
	
	//php定位打卡
    public function geolocation_php()
    {
		$param = get_params();
        $key = $this->key;
		View::assign('key', $key);
		return View();
    }
	
	
	/**
     * 获取位置信息（腾讯地图获取经纬坐标）
     */
    public function getLocation()
    {
		$param = get_params();
        $address = $param['address'];   
        if (empty($address)) {
			return to_assign(1, '地址参数错误');
        }
		$address = preg_replace('/\s+/', '',$address);		
        try {
            $key = $this->key;
            $url = "https://apis.map.qq.com/ws/geocoder/v1/?address={$address}&key={$key}";
            
            $result = file_get_contents($url);
            $data = json_decode($result, true);            
            if ($data['status'] == 0 && isset($data['result'])) {
				dd($data['result']);exit;
                $address = $data['result']['title'] ?? '位置信息';
                $address_components = $data['result']['address_components'];
				return json([
                    'code' => 0, 
                    'msg' => '获取成功',
                    'data' => [
                        'location' => $data['result']['location'],
						'province' => $address_components['province'],
						'city' => $address_components['city'],
						'district' => $address_components['district'],
						'street' => $address_components['street'],
						'street_number' => $address_components['street_number'],
						'address' => $address,
                    ]
                ]);
            } else {
                return json(['code' => 1, 'msg' => '地址解析失败']);
            }
        } catch (\Exception $e) {
            // API调用失败时返回基本信息
			return to_assign(1,'地址解析失败，返回基本信息',[
				'address' => $address
			]);
        }
    }
	
    /**
     * 获取位置信息（腾讯地图逆地理编码）
     */
    public function getLocationInfo()
    {
		$param = get_params();
        $latitude = $param['latitude'];
        $longitude = $param['longitude'];
        
        if ($latitude <= 0 || $longitude <= 0) {
			return to_assign(1, '经纬度参数错误');
        }
        $key = $this->key;
        try {
            // 腾讯地图逆地理编码API
            $key = get_system_config('other','tencentmapkey');
			if(empty($key)){
				return to_assign(1, '腾讯地图API密钥未填写');
			}
            $url = "https://apis.map.qq.com/ws/geocoder/v1/?location={$latitude},{$longitude}&key={$key}&get_poi=1";
            
            $result = file_get_contents($url);
            $data = json_decode($result, true);
            
            if ($data['status'] == 0 && isset($data['result'])) {
				//dd($data['result']);exit;
                $address = $data['result']['address'] ?? '位置信息';
                $address_component = $data['result']['address_component'];
                $formatted_addresses = $data['result']['formatted_addresses'] ?? [];
                $recommend = $formatted_addresses['recommend'] ?? '';
                $rough = $formatted_addresses['rough'] ?? '';
                
                $displayAddress = $recommend ?: $rough ?: $address;

				return json([
                    'code' => 0, 
                    'msg' => '获取成功',
                    'data' => [
                        'location' => $data['result']['location'],
						'province' => $address_component['province'],
						'city' => $address_component['city'],
						'district' => $address_component['district'],
						'address' => $displayAddress,
						'formatted_address' => $displayAddress
                    ]
                ]);
            } else {
                return json(['code' => 1, 'msg' => '地址解析失败']);
            }
        } catch (\Exception $e) {
            // API调用失败时返回基本信息
			return to_assign(1,'地址解析失败，返回基本信息',[
				'address' => "纬度: {$latitude}, 经度: {$longitude}",
				'location' => ['lat' => $latitude, 'lng' => $longitude]
			]);
        }
    }
}
