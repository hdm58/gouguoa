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
use think\Validate;
use think\facade\Db;

class Talent extends Validate
{	
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
        $map[] = ['status', '=', 1];
        return !Db::name($table)->where($map)->count();
    }

    protected $rule = [
		'name' => 'require',
        'mobile' => 'require|number|length:11|checkUnique:Admin,mobile,id',
		'id' => 'require',
	];

    protected $message = [
		'name.require' => '员工姓名不能为空',
        'name.chs' => '员工姓名只能是汉字',
		'mobile.require' => '手机不能为空',
		'mobile.number' => '手机号码只能填写数字',
        'mobile.length' => '手机号码只能填写11位数字',
		'mobile.checkUnique' => '同样的手机号码已经存在入职员工内，请与人事核对后再申请',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['name', 'mobile'],
        'edit' => ['name', 'mobile','id'],
    ];
}