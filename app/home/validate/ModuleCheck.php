<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\home\validate;

use think\Validate;

class ModuleCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:admin_module',
        'name' => 'require|upper|min:2|unique:admin_module',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '模块名称不能为空',
        'title.unique' => '同样的模块名称已经存在',
        'name.require' => '模块标识不能为空',
        'name.upper' => '模块标识只能是大写字符',
        'name.min' => '模块标识至少需要2个大写字符',
        'name.unique' => '同样的模块标识已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title','name'],
        'edit' => ['id','title','name'],
    ];
}
