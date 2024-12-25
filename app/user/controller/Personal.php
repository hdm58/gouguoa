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
use app\user\model\DepartmentChange as DepartmentChange;
use app\user\model\PersonalQuit as PersonalQuit;
use app\user\model\Department as DepartmentModel;
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
			$where = [];
            if (!empty($param['keywords'])) {
                $where[] = ['u.name|p.remark|a.title|b.title','like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['p.status','=', 1];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = DepartmentChange::where($where)
                ->field('p.*,u.name as name,ad.name as admin')
                ->alias('p')
                ->join('admin u', 'p.uid = u.id', 'LEFT')
                ->join('admin ad', 'p.admin_id = ad.id', 'LEFT')              
                ->order('p.id desc')
				->paginate(['list_rows'=> $rows])
                ->each(function ($item, $key) {
                    $item->move_time = date('Y-m-d', $item->move_time);
					$adepartment = Db::name('Department')->whereIn('id',$item->from_did)->column('title');
					$item->adepartment = implode(',', $adepartment);
					$bdepartment = Db::name('Department')->whereIn('id',$item->to_did)->column('title');
					$item->bdepartment = implode(',', $bdepartment);
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
            if ($param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('DepartmentChange')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
				$uid = $param['uid'];
				$map = [];
				$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',leader_ids)")];
				$count = Db::name('Department')->where($map)->count();
				if($count>0){
					return to_assign(1,'请先撤销该员工的部门负责人头衔再调部门');
				}
				$param['move_time'] = isset($param['move_time']) ? strtotime($param['move_time']) : 0;
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $res = Db::name('DepartmentChange')->strict(false)->field(true)->insertGetId($param);
				if ($res!==false) {
					add_log('add', $res, $param);
					Db::name('Admin')->where('id', $param['uid'])->update(['did' => $param['to_did']]);
					Db::name('DepartmentAdmin')->where(['admin_id'=>$param['uid'],'department_id'=>$param['to_did']])->delete();
					
					$info = Db::name('Admin')->where('id', $param['uid'])->find();
					$model = new DepartmentModel();
					$auth_dids = $model->get_auth_departments($info);
					$son_dids = $model->get_son_departments($info);
					Db::name('Admin')->where('id',$param['uid'])->update(['auth_dids'=>$auth_dids,'son_dids'=>$son_dids]);
				}
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
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = PersonalQuit::where($where)
                ->field('p.*,u.name as name,ps.title as position')
                ->alias('p')
                ->join('admin u', 'p.uid = u.id', 'LEFT')
                ->join('position ps', 'u.position_id = ps.id', 'LEFT')
                ->order('p.id desc')
                ->paginate(['list_rows'=> $rows])
                ->each(function ($item, $key) {
                    $item->quit_time = date('Y-m-d', $item->quit_time);
					$item->connect_time_str='-';
					if($item->connect_time>0){
						$item->connect_time_str = date('Y-m-d', $item->connect_time);
					}
                    $item->lead_admin = Db::name('admin')->where(['id' => $item->lead_admin_id])->value('name');
                    $item->connect_name = Db::name('admin')->where(['id' => $item->connect_id])->value('name');
                    $this_uids_name = Db::name('admin')->where([['id','in', $item->connect_uids]])->column('name');
                    $item->connect_names = implode(',', $this_uids_name);
					
					$dids =  Db::name('DepartmentAdmin')->where('admin_id',$item->uid)->column('department_id');
					$department = Db::name('Department')->whereIn('id',$dids)->column('title');
					$item->department = implode(',', $department);
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
                    ->field('p.*,u.name as name,l.name as lead_admin_name')
                    ->alias('p')
                    ->join('admin u', 'p.uid = u.id', 'LEFT')
                    ->join('admin l', 'p.lead_admin_id = l.id', 'LEFT')
                    ->where($where)
                    ->find();
                $this_uids_name = Db::name('Admin')->where([['id','in', $detail['connect_uids']]])->column('name');
                $detail['connect_names'] = implode(',', $this_uids_name);
                $detail['quit_time'] = date('Y-m-d', $detail['quit_time']);
				$detail['connect_name'] = Db::name('admin')->where(['id' => $detail['connect_id']])->value('name');
				
				$dids =  Db::name('DepartmentAdmin')->where('admin_id',$detail['uid'])->column('department_id');
				$department = Db::name('Department')->whereIn('id',$dids)->column('title');
				$detail['department'] = implode(',', $department);
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
	
	//一键交接资料
    public function leave_check()
    {
        $id = get_params("id");
        $data['id'] = $id;
        $data['connect_time'] = time();
		$detail = Db::name('PersonalQuit')->where('id', $id)->find();
        $uid =  $detail['uid'];
        $connect_uid = $detail['connect_id'];
        if (Db::name('PersonalQuit')->update($data) !== false) {
			//项目负责人
            Db::name('Project')->where([['director_uid','=',$uid],['status','<',3]])->update(['director_uid' => $connect_uid]);
			//任务负责人
            Db::name('ProjectTask')->where([['director_uid','=',$uid],['status','<',3]])->update(['director_uid' => $connect_uid]);			
			//客户所属人
			$did = Db::name('Admin')->where('id', $connect_uid)->value('did');
            Db::name('Customer')->where([['belong_uid','=',$uid]])->update(['belong_uid' => $connect_uid,'belong_did'=>$did]);
			//合同
            Db::name('Contract')->where([['admin_id','=',$uid],['check_status','<',3]])->update(['admin_id' => $connect_uid]);
            add_log('hand', $id);
            return to_assign(0, "交接成功");
        } else {
            return to_assign(1, "交接失败");
        }
    }
}
