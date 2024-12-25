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

use app\api\BaseController;
use think\facade\Db;
use think\facade\View;

class Approve extends BaseController
{
	public function index()
    {
		$department = $this->did;
		$module = Db::name('FlowModule')->where(['status'=>1])->select()->toArray();
		$whereOr = [];
		if($this->uid>1){				
			$map1=[
				['department_ids', '=', '']
			];
			$map2=[
				['', 'exp', Db::raw("FIND_IN_SET('{$department}',department_ids)")]
			];
			$whereOr =[$map1,$map2];
		}
		
		foreach ($module as &$row) {
			// 处理每一行数据
			$row['list'] = Db::name('FlowCate')
				->where([['module_id','=',$row['id']],['status','=',1],['is_list','=',1]])
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->select()->toArray();
		}
		View::assign('module', $module);
		return view();
	}
	
	public function get_list($where,$param)
    {
			$tables = Db::name('FlowCate')->field('name,check_table')->where('status',1)->select()->toArray();
			$prefix = get_config('database.connections.mysql.prefix');
			$sqlParts = [];
			$sqlCounts = [];
			$fortable =[];
			foreach ($tables as $table) {
				$dbname = $table['check_table'];
				if(in_array($dbname,$fortable)){
					continue;
				}
				$check_name = $table['name'];
				$tableName = $prefix.$dbname;
				$sqlPart = "SELECT id,admin_id,did,create_time,check_status,check_flow_id,check_step_sort,check_uids,check_last_uid,check_history_uids,check_copy_uids,check_time,'{$dbname}' as table_name,'{$check_name}' as check_name,'{$check_name}' as invoice_type,'{$check_name}' as types FROM {$tableName} WHERE {$where}";
				if($dbname=='invoice' || $dbname=='ticket'){
					$sqlPart = "SELECT id,admin_id,did,create_time,check_status,check_flow_id,check_step_sort,check_uids,check_last_uid,check_history_uids,check_copy_uids,check_time,'{$dbname}' as table_name,'{$check_name}' as check_name,invoice_type,'{$check_name}' as types FROM {$tableName} WHERE {$where}";
				}
				if($dbname=='approve'){
					$sqlPart = "SELECT id,admin_id,did,create_time,check_status,check_flow_id,check_step_sort,check_uids,check_last_uid,check_history_uids,check_copy_uids,check_time,'{$dbname}' as table_name,'{$check_name}' as check_name,'{$check_name}' as invoice_type,types FROM {$tableName} WHERE {$where}";
				}
				$sqlCount = "SELECT COUNT(*) AS count FROM {$tableName} WHERE {$where}";
				// 查询数据库中是否存在该数据表
				$is_table = Db::query("SHOW TABLES LIKE '{$tableName}'");
				// 判断查询结果
				if (!empty($is_table)) {
					$sqlParts[] = $sqlPart;
					$sqlCounts[] = $sqlCount;
					$fortable[] = $table['check_table'];
				}
			}

			// 使用implode将各个部分用UNION ALL连接起来
			$unionSql = implode(" UNION ALL ", $sqlParts);
			
			$totalCount = 0;
			foreach ($sqlCounts as $sql) {
				$count = Db::query($sql)[0]['count']; // 假设每个查询都返回了一个包含'count'键的数组
				$totalCount += $count;
			}
			// 添加排序和分页逻辑
			$page = isset($param['page']) ? $param['page'] : 1;
			$pageSize = $param['limit'];
			$offset = ($page - 1) * $pageSize;

			// 注意：不同的数据库分页语法可能有所不同，这里以MySQL为例
			$finalSql = $unionSql . " ORDER BY create_time DESC LIMIT {$offset}, {$pageSize}";

			// 执行查询
			$result = Db::query($finalSql);
			// 处理结果
			foreach ($result as &$row) {
				// 处理每一行数据
				$row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);
				$row['admin_name'] = Db::name('Admin')->where('id',$row['admin_id'])->value('name');
				$row['department'] = Db::name('Department')->where('id',$row['did'])->value('title');
				$row['check_status_str'] = check_status_name($row['check_status']);
				if($row['check_status']==1 && !empty($row['check_uids'])){
					$check_users = Db::name('Admin')->where('id','in',$row['check_uids'])->column('name');
					$row['check_users'] = implode(',',$check_users);
				}
				else{
					$row['check_users']='-';
				}
				if(!empty($row['check_copy_uids'])){
					$check_copy_users = Db::name('Admin')->where('id','in',$row['check_copy_uids'])->column('name');
					$row['check_copy_users'] = implode(',',$check_copy_users);
				}
				else{
					$row['check_copy_users']='-';
				}
				
				$check_name=$row['check_name'];
				if($row['table_name'] == 'invoice' || $row['table_name']=='ticket'){
					if($row['invoice_type']==0){
						$check_name=$row['table_name'].'a';
					}
					else{
						$check_name=$row['table_name'];
					}
				}
				if($row['table_name'] == 'approve'){
					$check_name='approve_'.$row['types'];
				}
				$flow_cate = Db::name('FlowCate')->where('name',$check_name)->find();
				$row['types_name'] = $flow_cate['title'];
				$row['view_url'] = $flow_cate['view_url'];
				$row['add_url'] = $flow_cate['add_url'];
			}
			$list=array(
				'data'=>$result,
				'total'=>$totalCount
			);
			return $list;
	}
	
	
	//我申请的
    public function mylist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status']) ? $param['status'] : 0;
			$uid = $this->uid;
			$where = "delete_time = 0";
			if($status == 1){
				$where.= " AND check_status < 2";
			}
			if($status == 2){
				$where.= " AND check_status = 2";
			}
			if($status == 3){
				$where.= " AND check_status > 2";
			}
			$where.= ' AND admin_id = '.$uid;
			//关联抄送人
			//$where.= " AND FIND_IN_SET('{$uid}',check_copy_uids)";
			//关联审核人
			//$where.= " AND (FIND_IN_SET('{$uid}',check_uids) or FIND_IN_SET('{$uid}',check_history_uids))";			
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
    public function checklist()
    {
         if (request()->isAjax()) {
			$param = get_params();
			$uid = $this->uid;
			$status = isset($param['status']) ? $param['status'] : 0;
			$where = "delete_time = 0";
			if($status == 0){
				$where.= " AND (FIND_IN_SET('{$uid}',check_uids) or FIND_IN_SET('{$uid}',check_history_uids))";	
			}
			if($status == 1){
				$where.= " AND FIND_IN_SET('{$uid}',check_uids)";	
			}
			if($status == 2){
				$where.= " AND FIND_IN_SET('{$uid}',check_history_uids)";	
			}		
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

	public function copylist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status']) ? $param['status'] : 0;
			$uid = $this->uid;
			$where = "delete_time = 0";
			if($status == 1){
				$where.= " AND check_status < 2";
			}
			if($status == 2){
				$where.= " AND check_status = 2";
			}
			if($status == 3){
				$where.= " AND check_status > 2";
			}
			//关联抄送人
			$where.= " AND FIND_IN_SET('{$uid}', check_copy_uids) > 0";
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//全部审批
    public function all()
    {
		$auth_approve = isAuth($this->uid,'office_admin','conf_1');
        if (request()->isAjax()) {
			if($auth_approve==0){
				return to_assign(1, '无权限访问');
			}
			$param = get_params();
			$status = isset($param['status']) ? $param['status'] : 0;
			$uid = $this->uid;
			$where = "delete_time = 0";
			if($status == 1){
				$where.= " AND check_status < 2";
			}
			if($status == 2){
				$where.= " AND check_status = 2";
			}
			if($status == 3){
				$where.= " AND check_status > 2";
			}
			if(!empty($param['uid'])){
				$where.= ' AND admin_id = '.$param['uid'];
			}
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
			if($auth_approve==0){
				throw new \think\exception\HttpException(405, '无权限访问');
			}
            return view();
        }
    }
}
