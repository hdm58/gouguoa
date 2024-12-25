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

namespace app\finance\validate;
use think\Validate;
use think\facade\Db;

class ExpenseValidate extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$count = Db::name('Expense')->where([['code','=',$data['code']],['id','<>',$id],['delete_time','=',0]])->count();
		return $count == 0 ? true : false;
	}
	
    protected $rule = [
		'code' => 'require|checkOne',
		'id' => 'require',
	];

    protected $message = [
		'code.require' => '报销凭证编号不能为空',
		'code.checkOne' => '同样的报销凭证编号已经存在',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['code'],
        'edit' => ['code', 'id'],
    ];
}