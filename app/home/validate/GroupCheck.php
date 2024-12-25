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

class GroupCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:admin_group',
        'id' => 'require',
        'status' => 'require|checkStatus:-1,1',
    ];

    protected $message = [
        'title.require' => '名称不能为空',
        'title.unique' => '同样的记录已经存在',
        'id.require' => '缺少更新条件',
        'status.require' => '状态为必选',
        'status.checkStatus' => '系统所有者组不能被禁用',
    ];

    protected $scene = [
        'add' => ['title', 'status'],
        'edit' => ['id', 'title', 'status'],
    ];

    // 自定义验证规则
    protected function checkStatus($value, $rule, $data)
    {
        if ($value == -1 and $data['id'] == 1) {
            return $rule == false;
        }
        return $rule == true;
    }
}
