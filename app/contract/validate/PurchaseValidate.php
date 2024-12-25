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

namespace app\contract\validate;
use think\Validate;
use think\facade\Db;

class PurchaseValidate extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$count = Db::name('Purchase')->where([['code','=',$data['code']],['id','<>',$id],['delete_time','=',0]])->count();
		return $count == 0 ? true : false;
	}
	
	protected function checkNumer($value,$rule,$data=[])
	{
		return is_numeric($value);
	}
	
    protected $rule = [
		'code' => 'require|checkOne',
		'name' => 'require',
        'cost'   => 'require|checkNumer',
		'id' => 'require',
	];

    protected $message = [
		'name.require' => '合同名称不能为空',
		'code.require' => '合同编码不能为空',
		'code.checkOne' => '同样的合同编码已经存在',
        'cost.require' => '合同金额不能为空',
        'cost.checkNumer' => '合同金额只能是数字',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['name','code','cost'],
        'edit' => ['name','code','cost','id'],
		'change' => ['id'],
    ];
}