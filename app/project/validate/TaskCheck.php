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

namespace app\project\validate;

use think\Validate;

class TaskCheck extends Validate
{
    protected $rule = [
        'title' => 'require',
        'plan_hours' => 'require|regex:/^[0-9]+(.[0-9]{1,1})?$/',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '任务主题不能为空',
        'plan_hours.require' => '预估工时不能为空',
        'plan_hours.regex' => '预估工时只能是整数或者1位小数的数字',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title'],
        'hours' => ['plan_hours'],
        'edit' => ['id'],
    ];
}
