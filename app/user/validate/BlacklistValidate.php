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

class BlacklistValidate extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$count_a = Db::name('Blacklist')->where([['idcard','=',$data['idcard']],['id','<>',$id],['delete_time','=',0]])->count();
		$count_b = Db::name('Blacklist')->where([['mobile','=',$data['mobile']],['id','<>',$id],['delete_time','=',0]])->count();
		if($count_a>0 || $count_b>0){
			return false;
		}
		else{
			return true;
		}
	}
	
    protected $rule = [
		'name' => 'require',
		'mobile' => 'require|checkOne',
		'idcard' => 'checkOne',
		'id' => 'require',
	];

    protected $message = [
		'name.require' => '姓名不能为空',
		'mobile.require' => '手机号码不能为空',
		'mobile.checkOne' => '同样的手机号码已经存在',
		'idcard.checkOne' => '同样的身份证号码已经存在',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['name','idcard'],
        'edit' => ['name','idcard', 'id'],
    ];
}