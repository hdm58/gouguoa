<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\user\validate;

use think\Validate;

class AdminCheck extends Validate
{
	protected $regex = [ 'checkUser' => '/^[A-Za-z]{1}[A-Za-z0-9_-]{4,19}$/'];
	
    protected $rule = [
        'name' => 'require|chs',
        'username' => 'require|regex:checkUser|unique:admin',
        'mobile' => 'require|mobile|unique:admin',
        'reg_pwd' => 'require|min:6',
        'did' => 'require',
        'position_id' => 'require',
        'type' => 'require',
        'entry_time' => 'require',
        'id' => 'require',
        'pwd' => 'require|min:6|confirm',
        'status' => 'require|checkStatus:-1,1',
        'old_pwd' => 'require|different:pwd',
    ];

    protected $message = [
	    'name.require' => '员工姓名不能为空',
        'name.chs' => '员工姓名只能是汉字',
        'username.require' => '登录账号不能为空',
        'username.regex' => '登录账号必须是以字母开头，只能包含字母数字下划线和减号，5到20位',
        'username.unique' => '同样的登录账号已经存在，建议增加数字，如：xxx123',
		'mobile.require' => '手机不能为空',
        'mobile.mobile' => '手机格式错误',
		'mobile.unique' => '同样的手机号码已经存在，请检查一下是否被离职或者禁用员工占用',
        'reg_pwd.require' => '密码不能为空',
        'reg_pwd.min' => '密码至少要6个字符',
		'did.require' => '请选择所在部门',
        'position_id.require' => '请选择职位',
		'type.require' => '请选择员工类型',
		'entry_time.require' => '请选择入职时间',
        'id.require' => '缺少更新条件',
        'pwd.require' => '密码不能为空',
		'pwd.min' => '密码至少要6个字符',
        'pwd.confirm' => '两次密码不一致',
        'old_pwd.require' => '请提供旧密码',
        'old_pwd.different' => '新密码不能和旧密码一样',
    ];

    protected $scene = [
        'add' => ['name', 'username', 'mobile','reg_pwd', 'did', 'position_id', 'type', 'entry_time'],
        'edit' => ['name', 'username', 'mobile', 'did', 'position_id', 'entry_time', 'id'],
        'editPwd' => ['old_pwd', 'pwd'],
    ];

}
