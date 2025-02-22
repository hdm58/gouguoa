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

namespace app\home\validate;

use think\Validate;

class ConfCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:config',
        'name' => 'require|unique:config',
    ];

    protected $message = [
        'title.require' => '配置名称不能为空',
        'title.unique' => '同样的配置名称已经存在',
        'name.require' => '配置标识不能为空',
        'name.unique' => '同样的配置标识已经存在',
    ];
}
