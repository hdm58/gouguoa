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

namespace app\disk\validate;
use think\Validate;
use think\facade\Db;

class DiskValidate extends Validate
{	
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$count = Db::name('Disk')->where([['name','=',$data['name']],['group_id','=',$data['group_id']],['id','<>',$data['id']]])->count();
		return $count == 0 ? true : false;
	}
    protected $rule = [
		'name' => 'require|checkOne',
		'id' => 'require',
	];

    protected $message = [
		'name.require' => '名称不能为空',
		'name.checkOne' => '同一目录下不能有相同名称的文件',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['name'],
        'edit' => ['name', 'id'],
    ];
}