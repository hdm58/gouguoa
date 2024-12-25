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

namespace app\home\controller;

use app\home\validate\UserCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Session;

class Login
{
    //登录
    public function index()
    {
		$wxwork = is_wxwork();
		$mobile = is_mobile();
		if($wxwork){
			return redirect('/qiye/login/login');
		}
		if($mobile){
			return redirect('/qiye/login/index');
		}
        return View();
    }
    //提交登录
    public function login_submit()
    {
        $param = get_params();
        try {
            validate(UserCheck::class)->check($param);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return to_assign(1, $e->getError());
        }

        $admin = Db::name('Admin')->where(['username' => $param['username']])->find();
        if (empty($admin)) {
            $admin = Db::name('Admin')->where(['mobile' => $param['username']])->find();
            if (empty($admin)) {
                return to_assign(1, '用户名或手机号码错误');
            }
        }
        $param['pwd'] = set_password($param['password'], $admin['salt']);
        if ($admin['pwd'] !== $param['pwd']) {
            return to_assign(1, '用户或密码错误');
        }
        if ($admin['status'] != 1) {
            return to_assign(1, '该用户禁止登录,请与管理者联系');
        }
        $data = [
			'is_lock' => 0,
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'login_num' => $admin['login_num'] + 1,
        ];
        Db::name('admin')->where(['id' => $admin['id']])->update($data);
        $session_admin = get_config('app.session_admin');
        Session::set($session_admin, $admin['id']);
        $token = make_token();
        set_cache($token, $admin, 7200);
        $admin['token'] = $token;
		$logdata = [
			'uid' => $admin['id'],
            'type' => 'login',
            'action' => '登录',
            'subject' => '系统',
			'param_id'=>$admin['id'],
			'param'=>'[]',
            'ip' => request()->ip(),
			'create_time' => time()
        ];
		Db::name('AdminLog')->strict(false)->field(true)->insert($logdata);
        return to_assign(0, '登录成功', ['uid' => $admin['id']]);
    }

    //退出登录
    public function login_out()
    {
        $session_admin = get_config('app.session_admin');
        Session::delete($session_admin);
        return to_assign(0, "退出成功");
    }

	//锁屏
    public function lock()
    {
		$session_admin = get_config('app.session_admin');
		$admin_id= Session::get($session_admin);
		$admin = Db::name('admin')->where(['id' => $admin_id])->find();
		if (request()->isAjax()) {
			$param = get_params();
			if($param['lock_password'] == ''){
				return to_assign(1, '请输入登录密码解锁');
			}			
			if(empty($admin)){
				return to_assign(2, '登录超时，请重新登录');
			}
			$pwd = set_password($param['lock_password'], $admin['salt']);
			if ($admin['pwd'] !== $pwd) {
				return to_assign(1, '密码错误');
			}
			else{
				Db::name('admin')->where('id',$admin['id'])->update(['is_lock'=>0]);
				return to_assign(0, '解锁成功', ['uid' => $admin['id']]);
			}
        }
		Db::name('admin')->where('id',$admin['id'])->update(['is_lock'=>1]);
        return View();
    }
}
