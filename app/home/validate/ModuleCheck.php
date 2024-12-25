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

class ModuleCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:admin_module',
        'name' => 'require|lower|min:2|unique:admin_module',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '模块名称不能为空',
        'title.unique' => '同样的模块名称已经存在',
        'name.require' => '模块标识不能为空',
        'name.lower' => '模块目录只能是小写字母',
        'name.min' => '模块目录至少需要2个小写字母',
        'name.unique' => '同样的模块目录已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title','name'],
        'edit' => ['id','title','name'],
    ];
}
