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

class CareValidate extends Validate
{	
    protected $rule = [
		'uid' => 'require',
		'id' => 'require',
	];

    protected $message = [
		'title.require' => '员工不能为空',
		'id.require' => '缺少更新条件',
	];
	
    protected $scene = [
        'add' => ['uid'],
        'edit' => ['uid', 'id'],
    ];
}