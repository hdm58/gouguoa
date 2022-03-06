<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\api;

use think\App;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\facade\Session;
use think\Response;
use think\facade\View;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

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
    protected $pageSize = '';

    /**
     * 构造方法
     * @access public
     * @param  App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->module = strtolower(app('http')->getName());
        $this->controller = strtolower($this->request->controller());
        $this->action = strtolower($this->request->action());
        $this->uid = 0;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        // 检测权限
        $this->checkLogin();
        //每页显示数据量
        $this->pageSize = Request::param('page_size', \think\facade\Config::get('app.page_size'));
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
			$this->uid = Session::get($session_admin)['id'];
            View::assign('login_user', $this->uid);
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
    protected function apiSuccess($msg = 'success',$data=[])
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
    protected function apiError($msg = 'fail',$data=[], $code = 1)
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
