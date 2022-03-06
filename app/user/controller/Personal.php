<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\user\controller;

use app\base\BaseController;
use app\user\model\DepartmentChange as DepartmentChange;
use app\user\model\PersonalQuit as PersonalQuit;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Personal extends BaseController
{
    //调部门列表
    public function change()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['keywords'])) {
                $where['u.name|p.remark|a.title|b.title'] = ['like', '%' . $param['keywords'] . '%'];
            }
            $where['p.status'] = array('eq', 1);
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $list = DepartmentChange::where($where)
                ->field('p.*,u.name as name,ad.name as admin,a.title as adepartment,b.title as bdepartment')
                ->alias('p')
                ->join('admin u', 'p.uid = u.id', 'LEFT')
                ->join('admin ad', 'p.admin_id = ad.id', 'LEFT')
                ->join('department a', 'p.from_did = a.id', 'LEFT')
                ->join('department b', 'p.to_did = b.id', 'LEFT')                
                ->order('p.id desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->move_time = date('Y-m-d', $item->move_time);
                });
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //新增&编辑调部门
    public function change_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['move_time'] = isset($param['move_time']) ? strtotime($param['move_time']) : 0;
            $count = Db::name('Department')->where(['leader_id' => $param['uid']])->count();
            if($count>0){
                return to_assign(1,'请先撤销该员工的部门负责人头衔再调部门');
            }
            if ($param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('DepartmentChange')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $res = Db::name('DepartmentChange')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $res, $param);
            }
            if ($res!==false) {
                Db::name('Admin')->where('id', $param['uid'])->update(['did' => $param['to_did']]);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $department = set_recursion(get_department());
            if ($id > 0) {
                $detail = Db::name('DepartmentChange')->where(['id' => $id])->find();
                $detail['name'] = Db::name('Admin')->where(['id' => $detail['uid']])->value('name');
                $detail['from_department'] = Db::name('Department')->where(['id' => $detail['from_did']])->value('title');
                $detail['move_time'] = date('Y-m-d', $detail['move_time']);
                View::assign('detail', $detail);
            }
            View::assign('department', $department);
            View::assign('id', $id);
            return view();
        }
    }

    //离职
    public function leave()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where['u.name|p.remark'] = ['like', '%' . $param['keywords'] . '%'];
            }
            $where['p.status'] = array('eq', 1);
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $list = PersonalQuit::where($where)
                ->field('p.*,u.name as name,d.title as department,ps.title as position')
                ->alias('p')
                ->join('admin u', 'p.uid = u.id', 'LEFT')
                ->join('department d', 'u.did = d.id', 'LEFT')
                ->join('position ps', 'u.position_id = ps.id', 'LEFT')
                ->order('p.id desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->quit_time = date('Y-m-d', $item->quit_time);
                    $item->lead_admin = Db::name('admin')->where(['id' => $item->lead_admin_id])->value('name');
                    $this_uids_name = Db::name('admin')->where([['id','in', $item->connect_uids]])->column('name');
                    $item->connect_names = implode(',', $this_uids_name);
                });
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加离职档案
    public function leave_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $count = Db::name('Department')->where(['leader_id' => $param['uid']])->count();
            if($count>0){
                return to_assign(1,'请先撤销该员工的部门负责人头衔再添加离职档案');
            }
            $param['quit_time'] = isset($param['quit_time']) ? strtotime($param['quit_time']) : 0;
            if ($param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('PersonalQuit')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $res = Db::name('PersonalQuit')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $res, $param);
            }
            if ($res!==false) {
                Db::name('Admin')->where('id', $param['uid'])->update(['status' => 2]);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $where = array();
            if ($id>0) {
                $where['p.id'] = array('eq', $id);
                $detail = Db::name('PersonalQuit')
                    ->field('p.*,u.name as name,l.name as lead_admin_name,d.title as department')
                    ->alias('p')
                    ->join('admin u', 'p.uid = u.id', 'LEFT')
                    ->join('admin l', 'p.lead_admin_id = l.id', 'LEFT')
                    ->join('department d', 'u.did = d.id', 'LEFT')
                    ->where($where)
                    ->find();
                $this_uids_name = Db::name('Admin')->where([['id','in', $detail['connect_uids']]])->column('name');
                $detail['connect_names'] = implode(',', $this_uids_name);
                $detail['quit_time'] = date('Y-m-d', $detail['quit_time']);
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //删除离职档案
    public function leave_delete()
    {
        $id = get_params("id");
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('PersonalQuit')->update($data) !== false) {
            $uid = Db::name('PersonalQuit')->where('id', $id)->value('uid');
            Db::name('Admin')->where('id', $uid)->update(['status' => 1]);
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
