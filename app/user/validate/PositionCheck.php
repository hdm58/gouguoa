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

namespace app\user\validate;
use think\facade\Db;
use think\Validate;

class PositionCheck extends Validate
{	
	// 自定义验证规则
    protected function checkUnique($value, $rule, $data)
    {
        [$table, $field, $id] = explode(',', $rule);
        $idField = $id ?: 'id';
        $idValue = $data[$idField] ?? null;
        $map = [
            [$field, '=', $value],
        ];
        if (!is_null($idValue)) {
            $map[] = [$idField, '<>', $idValue];
        }
        $map[] = ['status', '=', 1];
        return !Db::name($table)->where($map)->count();
    }
    protected $rule = [
        'title' => 'require|checkUnique:Position,title,id',
        'work_price' => 'require|number',
        'group_id' => 'require',
        'id' => 'require'
    ];

    protected $message = [
        'title.require' => '岗位名称不能为空',
        'title.checkUnique' => '同样的岗位名称已经存在',
        'work_price.require' => '岗位工时单价不能为空',
        'work_price.number' => '岗位工时单价只能是整数',
        'group_id.require' => '至少要选择一个角色权限',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title', 'work_price', 'group_id'],
        'edit' => ['title', 'work_price', 'group_id', 'id'],
    ];
}
