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

namespace app\user\model;
use think\Model;
use think\facade\Db;
class Department extends Model
{
	// 定义与员工的多对多关联关系
    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'department_admin');
    }
	
	public function update_auth_dids_son_dids(){
		$admin = Db::name('Admin')->field('id,did,auth_did')->select()->toArray();
		foreach ($admin as $key => $val) {
			$auth_dids = $this->get_auth_departments($val);
			$son_dids = $this->get_son_departments($val);
			Db::name('Admin')->where('id',$val['id'])->update(['auth_dids'=>$auth_dids,'son_dids'=>$son_dids]);
		}
	}
	
	/*获取某员工所能看的部门数据(dids)
	*传入某员工uid，输出部门数组，如:'1,2,3'。
	*超级管理员默认返回全部部门数据
	*/
	public function get_auth_departments($admin)
	{
		$str='';
		$uid = $admin['id'];
		$did = $admin['did'];
		$auth_did = $admin['auth_did'];
		$dids = Db::name('Department')->where('status',1)->column('id');
		$departments = Db::name('Department')->where('status',1)->select()->toArray();
		//次要部门did
		$secondary_dids = Db::name('DepartmentAdmin')->where('admin_id',$uid)->column('department_id');
		//全部可见部门
		$all_dids = array_merge($secondary_dids,[$did]);
		
		//超级管理员||所有部门数据权限
		if($uid==1 || $auth_did==10){
			$str = implode(',',$dids);
		}
		//仅自己关联的数据
		if($auth_did==0){
			$str='';
		}

		//所属主部门的数据
		if($auth_did==1){
			$str=$did;
		}
		
		//所属次部门的数据
		if($auth_did==2){
			$str = implode(',',$secondary_dids);
		}
		
		//仅所属主次部门数据
		if($auth_did==3){
			$str = implode(',',$all_dids);
		}
		
		//所属主部门及其子部门数据
		if($auth_did==4){
			//获取子部门
			$department_list = get_data_node($departments, $did);
			$department_array = array_column($department_list, 'id');
			//包括自己部门在内
			$department_array[] = $did;
			$str = implode(',',$department_array);
		}
		
		//所属次部门及其子部门数据
		if($auth_did==5){
			//获取子部门
			$list_array = [];
			foreach ($secondary_dids as $key => $value) {
				$department_list = get_data_node($departments, $value);
				$department_array = array_column($department_list, 'id');
				//包括自己部门在内
				$department_array[] = $value;
				$list_array = array_merge($list_array,$department_array);
			}
			$new_array = array_unique($list_array);
			$str = implode(',',$new_array);
		}
		
		//所属主次部门及其子部门数据
		if($auth_did==6){
			//获取子部门
			$list_array = [];
			foreach ($all_dids as $key => $value) {
				$department_list = get_data_node($departments, $value);
				$department_array = array_column($department_list, 'id');
				//包括自己部门在内
				$department_array[] = $value;
				$list_array = array_merge($list_array,$department_array);
			}
			$new_array = array_unique($list_array);
			$str = implode(',',$new_array);
		}
		
		//所属主部门所在顶级部门及其子部门数据
		if($auth_did==7){
			//获取顶级部门
			$top_did = get_department_top($did);
			$new_array = get_department_son($top_did,1);
			$str = implode(',',$new_array);
		}
		
		//所属次部门所在顶级部门及其子部门数据
		if($auth_did==8){
			//获取顶级部门
			$top_dids =[];
			foreach ($secondary_dids as $key => $value) {
				array_push($top_dids, get_department_top($value));
			}
			
			$list_array = [];
			foreach ($top_dids as $key => $value) {
				$list_array = array_merge($list_array,get_department_son($value,1));
			}
			$new_array = array_unique($list_array);
			$str = implode(',',$new_array);
		}
		
		//所属主次部门所在顶级部门及其子部门数据
		if($auth_did==9){
			//获取顶级部门
			$top_dids =[];
			foreach ($all_dids as $key => $value) {
				array_push($top_dids, get_department_top($value));
			}
			
			$list_array = [];
			foreach ($top_dids as $key => $value) {
				$list_array = array_merge($list_array,get_department_son($value,1));
			}
			$new_array = array_unique($list_array);
			$str = implode(',',$new_array);
		}
		return $str;
	}
	
	/*获取某负责人所负责的部门的数据集(ids),传入某员工uid，输出部门字符串，如:1,2,3,
	*逻辑：先判断传入的uid是否是负责人，如果是负责人再读取对应的部门数据。
	*超级管理员默认返回全部部门数据
	*/
	public function get_son_departments($admin)
	{
		$str='';
		$uid = $admin['id'];
		$dids = Db::name('Department')->where('status',1)->column('id');
		$departments = Db::name('Department')->where('status',1)->select()->toArray();
		if($uid==1){
			$str = implode(',',$dids);
		}
		else{
			$map = [];
			$map[] = ['status','=',1];
			$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',leader_ids)")];
			$leader_dids = Db::name('Department')->where($map)->column('id');
			//判断是否是部门负责人
			if(!empty($leader_dids)){
				//获取子部门
				$list_array = [];
				foreach ($leader_dids as $key => $value) {
					$department_list = get_data_node($departments, $value);
					$department_array = array_column($department_list, 'id');
					//包括自己部门在内
					$department_array[] = $value;
					$list_array = array_merge($list_array,$department_array);
				}
				$str = implode(',',$list_array);
			}
		}
		return $str;
	}
}
