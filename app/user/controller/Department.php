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
use app\user\model\Department as DepartmentModel;
use app\user\validate\DepartmentCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Department extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $list = Db::name('Department')->order('sort desc,id asc')->select()->toArray();
			foreach ($list as $key => &$v) {
				$admin_array = Db::name('Admin')->where([['id','in',$v['leader_ids']]])->column('name');
				$v['leader'] = split_array_field($admin_array);
			}
			$res = generateTree($list);
            return to_assign(0, '', $res);
        } else {
            return view();
        }
    }

    //添加部门
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if ($param['id'] > 0) {
                try {
                    validate(DepartmentCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $department_array = get_department_son($param['id']);
                if (in_array($param['pid'], $department_array)) {
                    return to_assign(1, '上级部门不能是该部门本身或其下属部门');
                } else {
                    Db::name('Department')->strict(false)->field(true)->update($param);
					$model = new DepartmentModel();
					$model->update_auth_dids_son_dids();
                    add_log('edit', $param['id'], $param);
                    return to_assign();
                }
            } else {
                try {
                    validate(DepartmentCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $did = Db::name('Department')->strict(false)->field(true)->insertGetId($param);
				$model = new DepartmentModel();
				$model->update_auth_dids_son_dids();
                add_log('add', $did, $param);
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
            $department = set_recursion(get_department());
            if ($id > 0) {
                $detail = Db::name('Department')->where(['id' => $id])->find();
				//获取子部门
				$department_array = get_department_son($id);
                $users = get_department_employee($id);
                View::assign('users', $users);
                View::assign('detail', $detail);
            }
            View::assign('department', $department);
            View::assign('pid', $pid);
            View::assign('id', $id);
            return view();
        }
    }

    //删除
    public function delete()
    {
        $id = get_params("id");
        $count = Db::name('Department')->where([['pid', '=', $id], ['status', '>=', 0]])->count();
        if ($count > 0) {
            return to_assign(1, "该部门下还有子部门，无法删除");
        }
        $users = Db::name('Admin')->where([['did', '=', $id], ['status', '>=', 0]])->count();
        if ($users > 0) {
            return to_assign(1, "该部门下还有员工，无法删除");
        }
        if (Db::name('Department')->delete($id) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除部门成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
