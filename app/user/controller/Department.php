<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\user\controller;

use app\base\BaseController;
use app\user\validate\DepartmentCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Department extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $list = Db::name('Department')
                ->field('d.*,a.name as leader')
                ->alias('d')
                ->join('Admin a', 'a.id = d.leader_id', 'LEFT')
                ->order('d.id asc')
                ->select();
            return to_assign(0, '', $list);
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
                add_log('add', $did, $param);
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
            $department = set_recursion(get_department());
            if ($id > 0) {
                $detail = Db::name('Department')->where(['id' => $id])->find();
                $users = Db::name('Admin')->where(['did' => $id, 'status' => 1])->select();
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
