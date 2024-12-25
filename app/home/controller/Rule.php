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

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\validate\RuleCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Rule extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $rule = Db::name('adminRule')
                ->field('a.*,m.title as module_title')
                ->alias('a')
                ->leftJoin('adminModule m', 'a.module = m.name')
                ->order('a.sort asc,a.id asc')
                ->select();
			$list = generateTree($rule);
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['src'] = preg_replace('# #', '', $param['src']);
            if ($param['id'] > 0) {
                try {
                    validate(RuleCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                Db::name('AdminRule')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(RuleCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $rid = Db::name('AdminRule')->strict(false)->field(true)->insertGetId($param);
                //自动为系统超级管理员角色组分配新增的节点
                $group = Db::name('AdminGroup')->find(1);
                if (!empty($group)) {
                    $newGroup['id'] = 1;
                    $newGroup['rules'] = $group['rules'] . ',' . $rid;
                    Db::name('AdminGroup')->strict(false)->field(true)->update($newGroup);
                    add_log('add', $rid, $param);
                }
            }
            // 删除后台节点缓存
            clear_cache('adminRules');
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
            if ($id > 0) {
                $detail = Db::name('AdminRule')->where('id', $id)->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('pid', $pid);
            return view();
        }
    }
    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $count = Db::name('AdminRule')->where(["pid" => $id])->count();
            if ($count > 0) {
                return to_assign(1, "该节点下还有子节点，无法删除");
            }
            if (Db::name('AdminRule')->delete($id) !== false) {
                clear_cache('adminRules');
                add_log('delete', $id, []);
                return to_assign(0, "删除节点成功");
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
}
