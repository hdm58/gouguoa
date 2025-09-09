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
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$uid = $this->uid;
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if($tab == 0){
				//全部
				$auth = isAuth($uid,'office_admin','conf_1');
				if($auth == 0){
					$whereOr[] = ['admin_id', '=', $uid];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
					$dids_a = get_leader_departments($uid);	
					$dids_b = get_role_departments($uid);
					$dids = array_merge($dids_a, $dids_b);
					if(!empty($dids)){
						$whereOr[] = ['did','in',$dids];
					}
				}
			}
			if($tab == 1){
				//我创建的
				$where[] = ['admin_id', '=', $this->uid];
			}
			if($tab == 2){
				//待我审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			}
			if($tab == 3){
				//我已审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			}
			if($tab == 4){
				//抄送给我的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
			}
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			if (!empty($param['did'])) {
				$where[] = ['did', '=',$param['did']];
            }
			//按时间检索
			if (!empty($param['move_time'])) {
				$move_time =explode('~', $param['move_time']);
				$where[] = ['move_time', 'between', [strtotime(urldecode($move_time[0])),strtotime(urldecode($move_time[1]))]];
			}
			$model = new DepartmentChange();
            $list = $model->datalist($param,$where,$whereOr);
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
			$model = new DepartmentChange();
            if ($param['id'] > 0) {
                $model->edit($param);
            } else {
				$uid = $param['uid'];
				$map = [];
				$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',leader_ids)")];
				$count = Db::name('Department')->where($map)->count();
				if($count>0){
					return to_assign(1,'请先撤销该员工的部门负责人头衔再申请');
				}
				$has = Db::name('DepartmentChange')->where(['uid'=>$param['uid'],'status'=>1,'delete_time'=>0])->count();
				if($has>0){
					return to_assign(1, "该员工有调动记录未完成，不能重复申请");
				}
                $param['admin_id'] = $this->uid;
                $model->add($param);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $department = set_recursion(get_department());
            if ($id > 0) {
                $model = new DepartmentChange();
				$detail = $model->getById($id);
                View::assign('detail', $detail);
            }
            View::assign('department', $department);
            View::assign('id', $id);
			if(is_mobile()){
				return view('qiye@/approve/add_change');
			}
            return view();
        }
    }
	
    //查看调部门申请
    public function change_view()
    {
        $param = get_params();
        $model = new DepartmentChange();
		$detail = $model->getById($param['id']);
		View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/approve/view_change');
		}
       return view();
    }
	
    //删除调部门申请
    public function change_delete()
    {
		$param = get_params();
		$id = $param['id'];
		if (request()->isDelete()) {
			$model = new DepartmentChange();
			$model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }
	

    //离职
    public function leave()
    {
 		$param = get_params();
        if (request()->isAjax()) {
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$uid = $this->uid;
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if($tab == 0){
				//全部
				$auth = isAuth($uid,'office_admin','conf_1');
				if($auth == 0){
					$whereOr[] = ['admin_id', '=', $this->uid];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
					$dids_a = get_leader_departments($uid);	
					$dids_b = get_role_departments($uid);
					$dids = array_merge($dids_a, $dids_b);
					if(!empty($dids)){
						$whereOr[] = ['did','in',$dids];
					}
				}
			}
			if($tab == 1){
				//我创建的
				$where[] = ['admin_id', '=', $uid];
			}
			if($tab == 2){
				//待我审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			}
			if($tab == 3){
				//我已审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			}
			if($tab == 4){
				//抄送给我的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
			}
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			if (!empty($param['did'])) {
				$where[] = ['did', '=',$param['did']];
            }
			//按时间检索
			if (!empty($param['quit_time'])) {
				$quit_time =explode('~', $param['quit_time']);
				$where[] = ['quit_time', 'between', [strtotime(urldecode($quit_time[0])),strtotime(urldecode($quit_time[1]))]];
			}
			$model = new PersonalQuit();
            $list = $model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }

    //添加离职申请
    public function leave_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['quit_time'] = isset($param['quit_time']) ? strtotime($param['quit_time']) : 0;
            $model = new PersonalQuit();
            if ($param['id'] > 0) {
				$has = Db::name('PersonalQuit')->where([['uid','=',$param['uid']],['delete_time','=',0],['id','<>',$param['id']]])->count();
				if($has>0){
					return to_assign(1, "该员工已申请有离职记录，不能重复申请");
				}
				$detail = $model->edit($param);
            } else {
				$has = Db::name('PersonalQuit')->where(['uid'=>$param['uid'],'delete_time'=>0])->count();
				if($has>0){
					return to_assign(1, "该员工已申请有离职记录，不能重复申请");
				}
                $param['admin_id'] = $this->uid;
                $detail = $model->add($param);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
			$uid = isset($param['uid']) ? $param['uid'] : 0;
            $detail=[];
			if($uid>0){
				$admin = get_admin($uid);
				$detail['name'] = $admin['name'];
				$detail['did'] = $admin['did'];
				$detail['department'] = $admin['department'];
			}
            if ($id>0) {
                $model = new PersonalQuit();
				$detail = $model->getById($id);
            }
            View::assign('id', $id);
            View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/approve/add_leave');
			}
            return view();
        }
    }
	
    //查看离职申请
    public function leave_view()
    {
        $param = get_params();
        $model = new PersonalQuit();
		$detail = $model->getById($param['id']);
		View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/approve/view_leave');
		}
        return view();
    }

    //删除离职申请
    public function leave_delete()
    {
		$param = get_params();
		$id = $param['id'];
		if (request()->isDelete()) {
			$model = new PersonalQuit();
			$model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }
}
