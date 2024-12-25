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

namespace app\api\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Request;
use think\Response;

class Auth
{
    public function handle($request, \Closure $next)
    {
        $token = Request::header('Token');
        if ($token) {
            if (count(explode('.', $token)) != 3) {
                return json(['code'=>404,'msg'=>'非法请求']);
            }
			$config = get_system_config('token');
			//var_dump($config);exit;
            try {
				JWT::$leeway = 60;//当前时间减去60，把时间留点余地
					$decoded = JWT::decode($token, new Key($config['secrect'], 'HS256')); //HS256方式，这里要和签发的时候对应
					//return (array)$decoded;
					$decoded_array = json_decode(json_encode($decoded),TRUE);
					$jwt_data = $decoded_array['data'];
					//$request->uid = $jwt_data['userid'];
					define('JWT_UID', $jwt_data['userid']);
					$response = $next($request);
					return $response;
					//return $next($request);
				} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
					return json(['code'=>403,'msg'=>'签名错误']);
				}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
					return json(['code'=>401,'msg'=>'token失效']);
				}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
					return json(['code'=>401,'msg'=>'token已过期']);
				}catch(Exception $e) {  //其他错误
					return json(['code'=>404,'msg'=>'非法请求']);
				}catch(\UnexpectedValueException $e) {  //其他错误
					return json(['code'=>404,'msg'=>'非法请求']);
				} catch(\DomainException $e) {  //其他错误
					return json(['code'=>404,'msg'=>'非法请求']);
				}
        } else {
            return json(['code'=>404,'msg'=>'token不能为空']);
        }
        return $next($request);
    }
}
