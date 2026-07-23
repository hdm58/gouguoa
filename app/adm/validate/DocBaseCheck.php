<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/

namespace app\adm\validate;

use think\Validate;

class DocBaseCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:doc_base',
        'prefix' => 'require|unique:doc_base',
        'code' => 'require|unique:doc_base',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '文号规则名称不能为空',
        'prefix.require' => '文号规则前缀不能为空',
        'code.require' => '文号规则编码不能为空',
        'title.unique' => '同样的文号规则名称已经存在',
        'prefix.unique' => '同样的文号规则前缀已经存在',
        'code.unique' => '同样的文号规则编码已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title','prefix','code'],
        'edit' => ['id','title','prefix','code'],
    ];
}
