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

namespace app\user\validate;
use think\facade\Db;
use think\Validate;

class AdminCheck extends Validate
{
	protected $regex = [ 'checkUser' => '/^[A-Za-z]{1}[A-Za-z0-9_-]{3,19}$/'];
	// 自定义验证规则
    protected function checkUnique($value, $rule, $data)
    {
        [$table, $field, $id] = explode(',', $rule);
        $idField = $id ?: 'id';
        $idValue = $data[$idField] ?? null;
        $map = [
            [$field, '=', $value],
        ];
        if (!is_null($idValue)) {
            $map[] = [$idField, '<>', $idValue];
        }
        $map[] = ['status', '>=', 0];
        return !Db::name($table)->where($map)->count();
    }
	
    protected $rule = [
        'name' => 'require|chs',
        'username' => 'require|regex:checkUser',
        'mobile' => 'require|mobile|checkUnique:Admin,mobile,id',
        'email' => 'require|email|checkUnique:Admin,email,id',
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
        'username.regex' => '登录账号必须是以字母开头，只能包含字母数字下划线和减号，4到20位',
		'mobile.require' => '手机不能为空',
        'mobile.mobile' => '手机格式错误',
		'mobile.checkUnique' => '同样的手机号码已经存在，请检查一下是否被离职或者禁用员工占用',
		'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式错误',
		'email.checkUnique' => '同样的邮箱已经存在，请检查一下是否被离职或者禁用员工占用',
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
        'add' => ['name', 'username', 'mobile','email','reg_pwd', 'did', 'position_id', 'type', 'entry_time'],
        'edit' => ['name', 'mobile','email', 'did', 'position_id', 'entry_time', 'id'],
        'editPwd' => ['old_pwd', 'pwd'],
    ];

}
