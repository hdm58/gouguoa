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

namespace app\user\controller;

use app\base\BaseController;
use app\user\validate\PositionCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Position extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $list = Db::name('Position')->where('status', '>=', 0)->order('create_time asc')->select()->toArray();
            foreach ($list as &$val) {
                $groupId = Db::name('PositionGroup')->where(['pid' => $val['id']])->column('group_id');
                $groupName = Db::name('AdminGroup')->where('id', 'in', $groupId)->column('title');
                $val['groupName'] = implode(',', $groupName);
            }
            $res['data'] = $list;
            return table_assign(0, '', $res);
        } else {
            return view();
        }
    }

    //添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                if($param['id']==1){
                    return to_assign(1, '超级管理员不能编辑');
                }
                try {
                    validate(PositionCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                // 启动事务
                Db::startTrans();
                try {
                    Db::name('Position')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                    Db::name('PositionGroup')->where(['pid' => $param['id']])->delete();
                    foreach ($param['group_id'] as $k => $v) {
                        $data[$k] = [
                            'pid' => $param['id'],
                            'group_id' => $v,
                            'create_time' => time(),
                        ];
                    }
                    Db::name('PositionGroup')->strict(false)->field(true)->insertAll($data);
                    add_log('edit', $param['id'], $param);
                    //清除菜单\权限缓存
                    clear_cache('adminMenu');
                    clear_cache('adminRules');
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return to_assign(1, '提交失败:' . $e->getMessage());
                }
            } else {
                try {
                    validate(PositionCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                // 启动事务
                Db::startTrans();
                try {
                    $uid = Db::name('Position')->strict(false)->field(true)->insertGetId($param);
                    foreach ($param['group_id'] as $k => $v) {
                        $data[$k] = [
                            'pid' => $uid,
                            'group_id' => $v,
                            'create_time' => time(),
                        ];
                    }
                    Db::name('PositionGroup')->strict(false)->field(true)->insertAll($data);
                    add_log('add', $uid, $param);
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return to_assign(1, '提交失败:' . $e->getMessage());
                }
            }
            return to_assign();
        }
        else{
            $id = isset($param['id']) ? $param['id'] : 0;
            $group = Db::name('AdminGroup')->order('create_time asc')->select()->toArray();
            if ($id > 0) {
                $detail = Db::name('Position')->where(['id' => $id])->find();
                $detail['group_id'] = Db::name('PositionGroup')->where(['pid' => $id])->column('group_id');
                foreach ($group as &$val) {
                    if (in_array($val['id'], $detail['group_id'])) {
                        $val['checked'] = 1;
                    } else {
                        $val['checked'] = 0;
                    }
                }
                View::assign('detail', $detail);
            }
            View::assign('group', $group);
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = get_params('id');
        $group = Db::name('AdminGroup')->order('create_time asc')->select()->toArray();
        $detail = Db::name('Position')->where(['id' => $id])->find();
        $detail['group_id'] = Db::name('PositionGroup')->where(['pid' => $id])->column('group_id');
        foreach ($group as &$val) {
            if (in_array($val['id'], $detail['group_id'])) {
                $val['checked'] = 1;
            } else {
                $val['checked'] = 0;
            }
        }

        $rule = Db::name('AdminRule')->order('sort asc,id asc')->select()->toArray();
        $user_groups = Db::name('PositionGroup')
            ->alias('a')
            ->join("AdminGroup g", "a.group_id=g.id", 'LEFT')
            ->where("a.pid='{$id}' and g.status='1'")
            ->select()
            ->toArray();
        $groups = $user_groups ?: [];

        $rules = [];
        foreach ($groups as $g) {
            $rules = array_merge($rules, explode(',', trim($g['rules'], ',')));
        }
        $rules = array_unique($rules);

        $role_rule = create_tree_list(0, $rule, $rules);
        View::assign('role_rule', $role_rule);
        View::assign('detail', $detail);
        View::assign('group', $group);
        add_log('view', $id);
        return view();
    }
    //删除
    public function delete()
    {
        $id = get_params("id");
        if ($id == 1) {
            return to_assign(0, "超级岗位，不能删除");
        }
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Position')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除岗位成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
