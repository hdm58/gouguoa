<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
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
