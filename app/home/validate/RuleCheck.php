<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\home\validate;

use think\Validate;

class RuleCheck extends Validate
{
    protected $rule = [
        'title' => 'require',
        'name' => 'require',
        'menu' => 'require',
        'src' => 'unique:admin_rule',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '菜单节点名称不能为空',
        'src.unique' => '同样的菜单节点URL已经存在',
        'menu.require' => '是否是左侧菜单需要选择',
        'name.require' => '节点日志操作名称不能为空',
        'id.require' => '缺少更新条件'
    ];

    protected $scene = [
        'add' => ['title','src','name','menu'],
        'edit' => ['id', 'title','src','name','menu']
    ];
}
