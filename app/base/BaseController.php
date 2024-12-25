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

namespace app\base;

use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use systematic\Systematic;

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
     * 分页数量
     * @var string
     */
    protected $pageSize = 20;
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];
    protected $module;
    protected $controller;
    protected $action;
    protected $uid;
    protected $did;
    protected $pid;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
	protected $model;
    public function __construct()
    {
        $this->module = strtolower(app('http')->getName());
        $this->controller = strtolower(Request::controller());
        $this->action = strtolower(Request::action());
        $this->uid = 0;
        $this->did = 0;
        $this->pid = 0;
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
        if ($this->controller !== 'login' && $this->controller !== 'captcha') {
            $session_admin = get_config('app.session_admin');
            if (!Session::has($session_admin)) {
                if (request()->isAjax()) {
                    return to_assign(404, '请先登录');
                } else {
                    redirect('/home/login/index.html')->send();
                    exit;
                }
            } else {
                $this->uid = Session::get($session_admin);
				$login_admin = get_admin($this->uid);
				$this->did = $login_admin['did'];
				$this->pid = $login_admin['pid'];			
				$is_lock = $login_admin['is_lock'];
				$last_login_time = Db::name('Admin')->where(['id' => $this->uid])->value('last_login_time');
				$timeDiff = time() - $last_login_time;
				// 如果超过2小时（7200秒），则用户需要重新登录
				if ($timeDiff > 7200) {
					Session::delete($session_admin);
					redirect('/home/login/index.html')->send();
                    exit;
				}
				Db::name('Admin')->where(['id' => $this->uid])->update(['last_login_time' => time()]);
				if($is_lock==1){
					redirect('/home/login/lock.html')->send();
					exit;
				}
	            View::assign('login_admin', $login_admin);	
                // 验证用户访问权限
                if ($this->module == 'home' && $this->controller == 'index') {
					return true;
				}
				else{
					$regPwd = $login_admin['reg_pwd'];
					if($regPwd!==''){
						redirect('/home/index/edit_password.html')->send();
						exit;
					}
                    if (!$this->checkAuth()) {
                        if (request()->isAjax()) {
                            return to_assign(405, '你没有权限,请联系管理员或者人事部');
                        } else {
							redirect('/home/index/role')->send();
							exit;
                        }
                    }
                }
            }
        }
    }

    /**
     * 验证用户访问权限
     * @DateTime 2020-12-21
     * @param    string $controller 当前访问控制器
     * @param    string $action 当前访问方法
     * @return   [type]
     */
    protected function checkAuth()
    {
        //Cache::delete('RulesSrc' . $uid);
		$uid = $this->uid;
		$GOUGU = new Systematic();
        $GOUGU->auth($uid);
		$auth_list_all = Cache::get('RulesSrc0');
        $auth_list = Cache::get('RulesSrc' . $uid);
        $pathUrl = $this->module . '/' . $this->controller . '/' . $this->action;
        if (!in_array($pathUrl, $auth_list)) {
            return false;
        } else {
            return true;
        }
    }
}
