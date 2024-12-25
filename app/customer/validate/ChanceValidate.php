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

namespace app\customer\validate;
use think\Validate;
use think\facade\Db;

class ChanceValidate extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$count = Db::name('CustomerChance')->where([['title','=',$data['title']],['id','<>',$id],['delete_time','=',0]])->count();
		return $count == 0 ? true : false;
	}
	
    protected $rule = [
		'title' => 'require|checkOne',
		'id' => 'require',
	];

    protected $message = [
		'title.require' => '名称不能为空',
		'title.checkOne' => '同样的名称已经存在',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['title'],
        'edit' => ['title', 'id'],
    ];
}