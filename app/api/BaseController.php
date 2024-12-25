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

namespace app\api;

use think\exception\HttpResponseException;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use think\facade\Db;
use think\Response;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 分页数量
     * @var string
     */
    protected $pageSize = 20;

    /**
     * jwt配置
     * @var string
     */
    protected $jwt_conf = [
        'secrect' => 'gouguoa',
        'iss' => 'www.gougucms.com', //签发者 可选
        'aud' => 'gouguoa', //接收该JWT的一方，可选
        'exptime' => 7200, //过期时间,这里设置2个小时
    ];
    protected $module;
    protected $controller;
    protected $action;
    protected $uid;
    protected $did;
    protected $pid;
    /**
     * 构造方法
     * @access public
     * @param  App $app 应用对象
     */
    public function __construct()
    {
        $this->module = strtolower(app('http')->getName());
        $this->controller = strtolower(Request::controller());
        $this->action = strtolower(Request::action());
        $this->uid = 0;
        $this->did = 0;
		$this->pid = 0;
        $this->jwt_conf = get_system_config('token');
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        // 检测权限
        $this->checkLogin();
        //每页显示数据量
        $this->pageSize = Request::param('limit', \think\facade\Config::get('app.page_size'));
    }

    /**
     *验证用户登录
     */
    protected function checkLogin()
    {
        $session_admin = get_config('app.session_admin');
        if (!Session::has($session_admin)) {
            $this->apiError('请先登录');
        }
		else{
            $this->uid = Session::get($session_admin);
			$login_admin = get_admin($this->uid);
			$this->did = $login_admin['did'];
			$this->pid = $login_admin['pid'];
			View::assign('login_admin', $login_admin);
		}
    }
    /**
     * Api处理成功结果返回方法
     * @param      $message
     * @param null $redirect
     * @param null $extra
     * @return mixed
     * @throws ReturnException
     */
    protected function apiSuccess($msg = 'success', $data = [])
    {
        return $this->apiReturn($data, 0, $msg);
    }

    /**
     * Api处理结果失败返回方法
     * @param      $error_code
     * @param      $message
     * @param null $redirect
     * @param null $extra
     * @return mixed
     * @throws ReturnException
     */
    protected function apiError($msg = 'fail', $data = [], $code = 1)
    {
        return $this->apiReturn($data, $code, $msg);
    }

    /**
     * 返回封装后的API数据到客户端
     * @param  mixed   $data 要返回的数据
     * @param  integer $code 返回的code
     * @param  mixed   $msg 提示信息
     * @param  string  $type 返回数据格式
     * @param  array   $header 发送的Header信息
     * @return Response
     */
    protected function apiReturn($data, int $code = 0, $msg = '', string $type = '', array $header = []): Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type = $type ?: 'json';
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

}
