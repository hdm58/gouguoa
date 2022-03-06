<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
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
