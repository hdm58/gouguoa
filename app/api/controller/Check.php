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
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Check extends BaseController
{	
	//获取审核流程
    public function get_flows($check_name='')
    {
		$cate_id = Db::name('FlowCate')->where(['name' => $check_name,'status'=>1])->value('id');
        $flow = Db::name('Flow')->where(['cate_id' => $cate_id,'status'=>1,'delete_time'=>0])->select()->toArray();
        return to_assign(0, '', $flow);
    }
	//获取审核步骤人员
    public function get_flow_users($id=0)
    {
        $flow = Db::name('Flow')->where(['id' => $id])->find();
        $flow_data = unserialize($flow['flow_list']);
		if(!empty($flow_data)){
			foreach ($flow_data as $key => &$val) {
				$val['check_position'] = '';
				if($val['check_role'] == 1){
					$val['check_uids'] = get_department_leader($this->uid);
				}
				if($val['check_role'] == 2){
					$val['check_uids'] = get_department_leader($this->uid,1);
				}
				if($val['check_role'] == 3){
					$val['check_position'] = Db::name('Position')->where('id',$val['check_position_id'])->value('title');
					$check_uids = Db::name('Admin')->where(['position_id'=>$val['check_position_id'],'status'=>1])->column('id');
					$val['check_uids'] = implode(',',$check_uids);
				}
				$val['check_uids_info'] = Db::name('Admin')->field('id,name,thumb')->where('id','in',$val['check_uids'])->select()->toArray();
			}
		}
		else{
			$flow_data = [];
		}
		
		$data['copy_uids'] = $flow['copy_uids'];
		$data['copy_unames'] ='';
		if(!empty($flow['copy_uids'])){
			$copy_unames = Db::name('Admin')->where('id', 'in', $flow['copy_uids'])->column('name');
			$data['copy_unames'] = implode(',', $copy_unames);
		}
		$data['flow_data'] = $flow_data;
        return to_assign(0, '', $data);
    }
	
	//提交审批申请
    public function submit_check()
    {
		$param = get_params();
		$flow_cate = Db::name('FlowCate')->where(['name' => $param['check_name']])->find();
		$flow_list = Db::name('Flow')->where('id',$param['flow_id'])->value('flow_list');
		$flow = unserialize($flow_list);
		$subject = $flow_cate['title'];
		$check_table = $flow_cate['check_table'];
		//var_dump($flow);exit;
		//删除原来的审核流程和审核记录
		Db::name('FlowStep')->where(['action_id'=>$param['action_id'],'flow_id'=>$param['flow_id'],'delete_time'=>0])->update(['delete_time'=>time()]);
		Db::name('FlowRecord')->where(['action_id'=>$param['action_id'],'check_table'=>$check_table,'delete_time'=>0])->update(['delete_time'=>time()]);		
		$recordData=array(
			'action_id' => $param['action_id'],
			'check_table' => $check_table,
			'step_id' => 0,
			'check_uid' => $this->uid,
			'flow_id' => $param['flow_id'],
			'check_time' => time(),
			'check_status' => 0,
			'content' => '提交申请',
			'create_time' => time()
		);	
		if (!isset($param['check_uids'])) {
			//非自由审批模式
			$step=[];
			$sort=0;
			foreach ($flow as $key => &$value){
				if($value['check_role'] == 1){
					$value['check_uids'] = get_department_leader($this->uid);
					$value['flow_name'] = '当前部门负责人';
					$value['check_position_id']=0;
				}
				if($value['check_role'] == 2){
					$value['check_uids'] = get_department_leader($this->uid,1);
					$value['flow_name'] = '上级部门负责人';
					$value['check_position_id']=0;
				}
				if($value['check_role'] == 3){
					$check_position = Db::name('Position')->where('id',$value['check_position_id'])->value('title');
					$check_uids = Db::name('Admin')->where(['position_id'=>$value['check_position_id'],'status'=>1])->column('id');
					$value['check_uids'] = implode(',',$check_uids);
					$value['flow_name'] = $check_position;
				}
				if($value['check_role'] == 4){
					$value['flow_name'] = '指定人员';
					$value['check_position_id']=0;
				}
				if($value['check_role'] == 5){
					$value['flow_name'] = '指定人员';
					$value['check_position_id']=0;
					$value['check_types']=1;
				}
				if(!empty($value['check_uids'])){
					$step[]=[
						'action_id' => $param['action_id'],
						'flow_id' => $param['flow_id'],
						'flow_name' => $value['flow_name'],
						'check_position_id' => $value['check_position_id'],
						'check_role' => $value['check_role'],
						'check_types' => $value['check_types'],
						'check_uids' => $value['check_uids'],
						'create_time' => time(),
						'sort'=>$sort					
					];
					$sort++;
				}
			}
			if(empty($step)){
				return to_assign(1,'审批流程设置有问题，无法提交审批申请，请联系HR或者管理员重新设置审批流程');
			}
			$res = Db::name('FlowStep')->strict(false)->field(true)->insertAll($step);
			if($res!=false){
				Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($recordData);
				Db::name($check_table)->strict(false)->field(true)->update([
					'id'=>$param['action_id'],
					'check_flow_id'=>$param['flow_id'],
					'check_status'=>1,
					'check_step_sort'=>0,
					'check_uids'=>$step[0]['check_uids'],
					'check_copy_uids'=>isset($param['check_copy_uids'])?$param['check_copy_uids']:''
				]);
				//发送消息通知
				if($flow_cate['template_id']>0){
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$step[0]['check_uids'],//接收人
						'template_id'=>$flow_cate['template_id'],//消息模板ID
						'template_field'=>'0',//消息模板字段
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$param['action_id'],
							'title' => $subject
						]
					];
					event('SendMessage',$msg);
				}
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}
		}
		else{
			//自由审批模式
			$flow_step = array(
				'action_id' => $param['action_id'],
				'flow_id' => $param['flow_id'],
				'flow_name' => '自由审批',
				'check_uids' => $param['check_uids'],
				'create_time' => time()
			);
			$res = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
			if($res!=false){
				Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($recordData);
				Db::name($check_table)->strict(false)->field(true)->update([
					  'id'=>$param['action_id'],
					  'check_flow_id'=>$param['flow_id'],
					  'check_status'=>1,
					  'check_step_sort'=>0,
					  'check_uids'=>$param['check_uids'],
					  'check_copy_uids'=>isset($param['check_copy_uids'])?$param['check_copy_uids']:''
				]);
				//发送消息通知
				if($flow_cate['template_id']>0){
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$param['check_uids'],//接收人
						'template_id'=>$flow_cate['template_id'],//消息模板ID
						'template_field'=>'0',//消息模板字段
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$param['action_id'],
							'title' => $subject
						]
					];
					event('SendMessage',$msg);
				}
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}
		}
	}
	
	//获取审核流程节点
    public function get_flow_nodes($check_name='',$action_id=0,$flow_id=0)
    {		
		$flow_cate = Db::name('FlowCate')->where(['name' => $check_name])->find();
		if($action_id==0){
			$did = $this->did;
			$map = [];
			$map[] = ['cate_id','=',$flow_cate['id']];
			$map[] = ['status','=',1];
			$map[] = ['delete_time','=',0];		
			$map1=[
				['department_ids','=','']
			];
			$map2=[
				['', 'exp', Db::raw("FIND_IN_SET('{$did}',department_ids)")]
			];
			$whereOr =[$map1,$map2];
			$flow = Db::name('Flow')
				->where($map)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->select()->toArray();
			return to_assign(0, '', $flow);
		}
		$check_table = $flow_cate['check_table'];
		$detail = Db::name($check_table)->where('id',$action_id)->field('id,admin_id,check_status,check_flow_id,check_step_sort,check_uids,check_copy_uids')->find();
		//创建人
		$is_creater=0;
		if($detail['admin_id'] == $this->uid){
			$is_creater=1;
		}
		$detail['is_creater'] = $is_creater;
		$detail['admin_name'] = Db::name('Admin')->where('id',$detail['admin_id'])->value('name');
		//当前审批人
		$is_checker=0;
		if(in_array($this->uid,explode(',',$detail['check_uids']))){
			$is_checker=1;
		}
		$detail['is_checker'] = $is_checker;
		//审批记录
		$check_record = Db::name('FlowRecord')
						->field('f.*,a.name')
						->alias('f')
						->join('Admin a', 'a.id = f.check_uid', 'left')
						->where(['f.action_id' => $action_id,'f.check_table'=>$check_table])->select()->toArray();				
		foreach ($check_record as $kk => &$vv) {		
			$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
			$vv['status_str'] = '提交';
			if($vv['check_status'] == 1){
				$vv['status_str'] = '审核通过';
			}
			else if($vv['check_status'] == 2){
				$vv['status_str'] = '审核拒绝';
			}
			if($vv['check_status'] == 3){
				$vv['status_str'] = '撤销';
			}
			if($vv['check_status'] == 4){
				$vv['status_str'] = '反确认';
			}
		}
		$detail['check_record'] = $check_record;
		if($detail['check_status']==0 || $detail['check_status']==4){
			$flow = Db::name('Flow')->where(['cate_id' => $flow_cate['id'],'status'=>1,'delete_time'=>0])->select()->toArray();
			$detail['flow'] = $flow;
		}
		else{				
			//当前审批人
			$detail['check_unames']='-';
			if(!empty($detail['check_uids'])){
				$check_unames = Db::name('Admin')->where('id','in',$detail['check_uids'])->column('name');
				$detail['check_unames'] = implode(',',$check_unames);
			}
			//抄送人
			$detail['copy_unames']='-';
			if(!empty($detail['check_copy_uids'])){
				$copy_uids = Db::name('Admin')->where('id','in',$detail['check_copy_uids'])->column('name');
				$detail['copy_unames'] = implode(',',$copy_uids);
			}
			
			//审批节点步骤
			$nodes = Db::name('FlowStep')->where(['action_id'=>$action_id,'flow_id'=>$flow_id,'delete_time'=>0])->order('sort asc')->select()->toArray();
			foreach ($nodes as $key => &$val) {
				$check_uids_info = Db::name('Admin')->field('id,name,thumb')->where('id','in',$val['check_uids'])->select()->toArray();						
				foreach ($check_uids_info as $k => &$v) {
					$v['check_time'] = 0;
					$v['content'] = '';
					$v['check_status'] = 0;			
					$check_array = Db::name('FlowRecord')->where(['check_uid' => $v['id'],'step_id' => $val['id']])->order('check_time desc')->select()->toArray();
					if(!empty($check_array)){
						$checked = $check_array[0];
						$v['check_time'] = date('Y-m-d H:i', $checked['check_time']);
						$v['content'] = $checked['content'];
						$v['check_status'] = $checked['check_status'];	
					}
				}
				$val['check_uids_info'] = $check_uids_info;
				
				if(!empty($val['check_position_id'])){
					$val['check_position'] = Db::name('Position')->where('id',$val['check_position_id'])->value('title');
				}
				else{
					$val['check_position'] = '';
				}

				$check_list = [];
				foreach ($check_record as $kkk => $vvv) {
					if($vvv['step_id'] == $val['id'])
					$check_list[] = $vvv;
				}
				$val['check_list'] = $check_list;
			}
			$detail['nodes'] = $nodes;
			
			//当前审核节点
			$step = Db::name('FlowStep')->where(['action_id'=>$action_id,'flow_id'=>$flow_id,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
			$detail['step'] = $step;
		}
        return to_assign(0, '', $detail);
    }
	
    //流程审核
    public function flow_check()
    {
        $param = get_params();
		$flow_cate = Db::name('FlowCate')->where(['name' => $param['check_name']])->find();
		$subject = $flow_cate['title'];
		$action_id = $param['action_id'];
		$check_table = $flow_cate['check_table'];
		//审核内容详情
		$detail = Db::name($check_table)->where(['id' => $action_id])->find();
		if (empty($detail)){		
			return to_assign(1,'审批数据错误');
		}
		//当前审核节点详情
		$step = Db::name('FlowStep')->where(['action_id'=>$action_id,'flow_id'=>$detail['check_flow_id'],'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		//审核通过时
		if($param['check'] == 1){
			$check_uids = explode(",",strval($detail['check_uids']));
			if (!in_array($this->uid, $check_uids)){		
				return to_assign(1,'您没权限审核该审批');
			}
			//审批通过
			if($step['check_role'] == 0){
				//自由人审批
				if($param['check_node'] == 2){
					$next_step = $detail['check_step_sort']+1;
					$flow_step = array(
						'action_id' => $action_id,
						'sort' => $next_step,
						'flow_id' => $detail['check_flow_id'],
						'check_uids' => $param['check_uids'],
						'create_time' => time()
					);
					$fid = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					//下一步审核步骤
					$param['check_step_sort'] = $next_step;
					$param['check_status'] = 1;
				}
				else{
					//不存在下一步审核，审核结束
					$param['check_status'] = 2;
					$param['check_uids'] ='';
				}
			}
			else{
				//查询当前步骤审批记录数
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$action_id,'flow_id'=>$detail['check_flow_id'],'step_id'=>$step['id']])->count();
				//当前当前步骤审批应有记录数
				$flow_count = explode(',', $step['check_uids']);
				$param['check_status'] = 1;
				$uids_array = explode(',',$detail['check_uids']);
				$new_uids= array_diff($uids_array, [$this->uid]);
				$param['check_uids'] = implode(',',$new_uids);
				if((($check_count+1) >= count($flow_count) && $step['check_types']==1) || $step['check_types']==2){
					//会签
					$next_step = Db::name('FlowStep')->where(['action_id'=>$action_id,'flow_id'=>$detail['check_flow_id'],'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
					if($next_step){
						//存在下一步审核
						if($next_step['check_role'] == 1){
							$param['check_uids'] = get_department_leader($detail['admin_id']);
						}
						else if($next_step['check_role'] == 2){
							$param['check_uids'] = get_department_leader($detail['admin_id'],1);
						}
						else if($next_step['check_role'] == 3){						
							$uids = Db::name('Admin')->where(['position_id'=>$next_step['check_position_id'],'status'=>1])->column('id');
							$param['check_uids'] = implode(',' ,$uids);
						}
						else{
							$param['check_uids'] = $next_step['check_uids'];
						}
						$param['check_step_sort'] = $detail['check_step_sort']+1;
						$param['check_status'] = 1;
					}
					else{
						//不存在下一步审核，审核结束
						$param['check_status'] = 2;
						$param['check_uids'] ='';
					}
				}
			}
			if($param['check_status'] == 1 && empty($param['check_uids'])){
				return to_assign(1,'找不到下一步的审批人，该审批流程设置有问题，请联系HR或者管理员');
			}			
			//添加历史审核人
			if(empty($detail['check_history_uids'])){
				$param['check_history_uids'] = $this->uid;
			}
			else{
				$param['check_history_uids'] = $detail['check_history_uids'].','.$this->uid;
			}			
			$res = Db::name($check_table)->strict(false)->field('check_step_sort,check_status,check_history_uids,check_uids')->where(['id' => $action_id])->update($param);
			
			if($res!==false){
				$checkData=array(
					'action_id' => $action_id,
					'check_table' => $check_table,
					'step_id' => $step['id'],
					'check_uid' => $this->uid,
					'flow_id' => $detail['check_flow_id'],
					'check_time' => time(),
					'check_status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('check', $action_id, $param,$subject);
				//发送消息通知
				if($param['check_status'] == 1){
					if($flow_cate['template_id']>0){
						$msg=[
							'from_uid'=>$detail['admin_id'],//发送人
							'to_uids'=>$param['check_uids'],//接收人
							'template_id'=>$flow_cate['template_id'],//消息模板ID
							'template_field'=>'0',//消息模板字段
							'content'=>[ //消息内容
								'create_time'=>date('Y-m-d H:i:s',$detail['create_time']),
								'action_id'=>$action_id,
								'title' => $subject
							]
						];
						event('SendMessage',$msg);
					}
				}
				if($param['check_status'] == 2){
					if($flow_cate['template_id']>0){
						$msg=[
							'from_uid'=>$this->uid,//发送人
							'to_uids'=>$detail['admin_id'],//接收人
							'template_id'=>$flow_cate['template_id'],//消息模板ID
							'template_field'=>'1',//消息模板字段
							'content'=>[ //消息内容
								'create_time'=>date('Y-m-d H:i:s',$detail['create_time']),
								'action_id'=>$action_id,
								'title' => $subject
							]
						];
						event('SendMessage',$msg);
					}
				}
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}
		}
		else if($param['check'] == 2){
			$check_uids = explode(",",strval($detail['check_uids']));
			if (!in_array($this->uid, $check_uids)){		
				return to_assign(1,'您没权限审核该审批');
			}
			//拒绝审核，数据操作
			$param['check_status'] = 3;
			//添加历史审核人
			if(empty($detail['check_history_uids'])){
				$param['check_history_uids'] = $this->uid;
			}
			else{
				$param['check_history_uids'] = $detail['check_history_uids'].','.$this->uid;
			}
			$param['check_uids'] ='';
			if($step['check_role'] == 5){
				//获取上一步的审核信息
				$prev_step = Db::name('FlowStep')->where(['action_id'=>$action_id,'flow_id'=>$detail['check_flow_id'],'sort'=>($detail['check_step_sort']-1),'delete_time'=>0])->find();
				if($prev_step){
					//存在上一步审核
					$param['check_step_sort'] = $prev_step['sort'];
					$param['check_uids'] = $prev_step['check_uids'];
					$param['check_status'] = 1;
				}
				else{
					//不存在上一步审核，审核初始化步骤
					$param['check_step_sort'] = 0;
					$param['check_uids'] = '';					
					$param['check_status'] = 0;
				}
			}
			
			$res = Db::name($check_table)->strict(false)->field('check_step_sort,check_status,check_history_uids,check_uids')->where(['id' => $action_id])->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $action_id,
					'check_table' => $check_table,
					'step_id' => $step['id'],
					'check_uid' => $this->uid,
					'flow_id' => $detail['check_flow_id'],
					'check_time' => time(),
					'check_status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('refue', $action_id, $param,$subject);
				//发送消息通知
				if($flow_cate['template_id']>0){
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>$flow_cate['template_id'],//消息模板ID
						'template_field'=>'2',//消息模板字段
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s',$detail['create_time']),
							'action_id'=>$detail['id'],
							'title' => $subject
						]
					];
					event('SendMessage',$msg);
				}
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}			
		}		
		else if($param['check'] == 3){
			//审批撤回
			if($detail['admin_id'] != $this->uid){
				return to_assign(1,'你没权限操作');
			}
			//撤销审核，数据操作
			$param['check_status'] = 4;
			$param['check_uids'] ='';
			$param['check_step_sort'] =0;
			
			$res = Db::name($check_table)->strict(false)->field('check_step_sort,check_status,check_uids')->where(['id' => $action_id])->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $action_id,
					'check_table' => $check_table,
					'step_id' => $step['id'],
					'check_uid' => $this->uid,
					'flow_id' => $detail['check_flow_id'],
					'check_time' => time(),
					'check_status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('back', $action_id, $param,$subject);
				return to_assign();
			}else{
				return to_assign(1,'操作失败');
			}
		}
		else if($param['check'] == 4){
			//审批反确认
			//反确认审核，数据回到待提交审批
			$param['check_status'] = 0;
			$param['check_uids'] ='';
			$param['check_step_sort'] =0;
			
			$res = Db::name($check_table)->strict(false)->field('check_step_sort,check_status,check_uids')->where(['id' => $action_id])->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $action_id,
					'check_table' => $check_table,
					'step_id' => $step['id'],
					'check_uid' => $this->uid,
					'flow_id' => $detail['check_flow_id'],
					'check_time' => time(),
					'check_status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('back', $action_id, $param,$subject);
				return to_assign();
			}else{
				return to_assign(1,'操作失败');
			}
		}
    }
}
