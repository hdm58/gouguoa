<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\home\validate;

use think\Validate;

class FlowTypeCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:flow_type',
        'name' => 'require|lower|min:2|unique:flow_type',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '名称不能为空',
        'title.unique' => '同样的名称已经存在',
        'name.require' => '标识不能为空',
        'name.lower' => '标识只能是小写字符',
        'name.min' => '标识至少需要2个小写字符',
        'name.unique' => '同样的标识已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title','name'],
        'edit' => ['id','title','name'],
    ];
}
