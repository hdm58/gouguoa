<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\oa\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Approve extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (isset($param['status'])) {
				if($param['status'] == 1){
					$where[] = ['check_status','<',2];
				}
				if($param['status'] == 2){
					$where[] = ['check_status','=',2];
				}
                if($param['status'] == 3){
					$where[] = ['check_status','>',2];
				}
            }
			$where[] = ['create_admin_id','=',$this->uid];
			$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $list = Db::name('Approve')
				->field('f.*,a.name,d.title as department_name,t.title as flow_type')
				->alias('f')
                ->join('Admin a', 'a.id = f.create_admin_id', 'left')
                ->join('Department d', 'd.id = f.department_id', 'left')
                ->join('FlowType t', 't.id = f.type', 'left')
				->where($where)
				->order('f.id desc')
                ->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function($item, $key){
					$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
					$item['check_user'] = '-';
					if($item['check_status']<2 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
					return $item;
				});
            return table_assign(0, '', $list);
        } else {
			$list = Db::name('FlowType')->where(['status'=>1])->select()->toArray();			
			View::assign('list', $list);
            return view();
        }
    }
	
    public function list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status'])?$param['status']:0;
			$user_id = $this->uid;
			//查询条件
			$map1 = [];
			$map2 = [];
			$map1[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',check_admin_ids)")];
			$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',flow_admin_ids)")];
			
			$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
			
			if($status == 0){
				$list = Db::name('Approve')
					->field('f.*,a.name,d.title as department_name,t.title as flow_type')
					->alias('f')
					->join('Admin a', 'a.id = f.create_admin_id', 'left')
					->join('Department d', 'd.id = f.department_id', 'left')
					->join('FlowType t', 't.id = f.type', 'left')
					->whereOr([$map1,$map2])
					->order('f.id desc')
					->group('f.id')
					->paginate(['list_rows' => $rows, 'query' => $param])
					->each(function($item, $key){
						$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
						$item['check_user'] = '-';
						if($item['check_status']<2 && !empty($item['check_admin_ids'])){
							$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
							$item['check_user'] = implode(',',$check_user);
						}
						return $item;
					});
			}	
			
			if($status == 1){
				$list = Db::name('Approve')
					->field('f.*,a.name,d.title as department_name,t.title as flow_type')
					->alias('f')
					->join('Admin a', 'a.id = f.create_admin_id', 'left')
					->join('Department d', 'd.id = f.department_id', 'left')
					->join('FlowType t', 't.id = f.type', 'left')
					->whereOr($map1)
					->order('f.id desc')
					->group('f.id')
					->paginate(['list_rows' => $rows, 'query' => $param])
					->each(function($item, $key){
						$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
						$item['check_user'] = '-';
						if($item['check_status']<2 && !empty($item['check_admin_ids'])){
							$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
							$item['check_user'] = implode(',',$check_user);
						}
						return $item;
					});
			}
			if($status == 2){
				$list = Db::name('Approve')
					->field('f.*,a.name,d.title as department_name,t.title as flow_type')
					->alias('f')
					->join('Admin a', 'a.id = f.create_admin_id', 'left')
					->join('Department d', 'd.id = f.department_id', 'left')
					->join('FlowType t', 't.id = f.type', 'left')
					->where($map2)
					->order('f.id desc')
					->group('f.id')
					->paginate(['list_rows' => $rows, 'query' => $param])
					->each(function($item, $key){
						$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
						$item['check_user'] = '-';
						if($item['check_status']<2 && !empty($item['check_admin_ids'])){
							$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
							$item['check_user'] = implode(',',$check_user);
						}
						return $item;
					});
            }	
            return table_assign(0, '', $list);
        } else {
			$list = Db::name('FlowType')->where(['status'=>1])->select()->toArray();			
			View::assign('list', $list);
            return view();
        }
    }

    //添加新增/编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['detail_time'])) {
                $param['detail_time'] = strtotime($param['detail_time']);
            }
			if (isset($param['start_time'])) {
                $param['start_time'] = strtotime($param['start_time']);
            }
			if (isset($param['end_time'])) {
                $param['end_time'] = strtotime($param['end_time']);
				if ($param['end_time'] <= $param['start_time']) {
					return to_assign(1, "时间选择有误");
				}
            }
			if (isset($param['start_time_a'])) {
                $param['start_time'] = strtotime($param['start_time_a'] . '' . $param['start_time_b']);
            }
            if (isset($param['end_time_a'])) {
                $param['end_time'] = strtotime($param['end_time_a'] . '' . $param['end_time_b']);
				if ($param['end_time'] <= $param['start_time']) {
					return to_assign(1, "结束时间需要大于开始时间");
				}
            }
			$flow_list = Db::name('Flow')->where('id',$param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);
            if ($param['id'] > 0) {
                $param['update_time'] = time();
                $param['check_status'] = 0;
				$param['check_step_sort'] = 0;
				Db::name('FlowStep')->where(['action_id'=>$param['id'],'type'=>1,'delete_time'=>0])->update(['delete_time'=>time()]);
				Db::name('FlowRecord')->where(['action_id'=>$param['id'],'type'=>1,'delete_time'=>0])->update(['delete_time'=>time()]);				
				
				if (!isset($param['check_admin_ids'])) {
					if($flow[0]['flow_type'] == 1){
						//部门负责人
						$leader = get_department_leader($this->uid);
						if($leader == 0){
							return to_assign(1,'审批流程设置有问题：当前部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}						
					}
					else if($flow[0]['flow_type'] == 2){
						//上级部门负责人
						$leader = get_department_leader($this->uid,1);
						if($leader == 0){
							return to_assign(1,'审批流程设置有问题：上级部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}
					}
					else{
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}					
					Db::name('Approve')->strict(false)->field(true)->update($param);
					foreach ($flow as $key => &$value){
						$value['action_id'] = $param['id'];
						$value['sort'] = $key;
						$value['create_time'] = time();
					}
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				else{
					Db::name('Approve')->strict(false)->field(true)->update($param);
					$flow_step = array(
						'action_id' => $param['id'],
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
                add_log('edit', $param['id'], $param);
            } else {
                $param['create_admin_id'] = $this->uid;
                $param['department_id'] = get_login_admin('did');
				$param['create_time'] = time();
				
				if (!isset($param['check_admin_ids'])) {
					if($flow[0]['flow_type'] == 1){
						//部门负责人
						$leader = get_department_leader($this->uid);
						if($leader == 0){
							return to_assign(1,'当前部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}						
					}
					else if($flow[0]['flow_type'] == 2){
						//上级部门负责人
						$leader = get_department_leader($this->uid,1);
						if($leader == 0){
							return to_assign(1,'上级部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}
					}
					else{
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}
					$aid = Db::name('Approve')->strict(false)->field(true)->insertGetId($param);
					foreach ($flow as $key => &$value){
						$value['action_id'] = $aid;
						$value['sort'] = $key;
						$value['create_time'] = time();
					}
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				else{
					$aid = Db::name('Approve')->strict(false)->field(true)->insertGetId($param);
					$flow_step = array(
						'action_id' => $aid,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
                add_log('add', $aid, $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $type = isset($param['type']) ? $param['type'] : 0;
            if($id>0){
                $detail = Db::name('Approve')->where('id',$id)->find();
				$detail['start_time_a'] = date('Y-m-d',$detail['start_time']);
				$detail['start_time_b'] = date('H:i',$detail['start_time']);
				$detail['end_time_a'] = date('Y-m-d',$detail['end_time']);
				$detail['end_time_b'] = date('H:i',$detail['end_time']);
				$detail['detail_time'] = date('Y-m-d',$detail['detail_time']);
				
				$detail['days'] = floor($detail['duration']*10/75);
				$detail['hours'] = (($detail['duration']*10)%75)/10;
				$type = $detail['type'];
                View::assign('detail', $detail);
            }
			$department = get_login_admin('did');
			//获取审批流程
			$flows = get_flows($type,$department);
			$moban=Db::name('FlowType')->where('id',$type)->value('name');
			View::assign([
				'flows' => $flows,
				'id' => $id,
				'type' => $type,
			]);
            return view('add_'.$moban);
        }
    }

    //查看
    public function view()
    {
		$param = get_params();
		$detail = Db::name('Approve')->where('id',$param['id'])->find();
		if($detail['start_time']>0){
			$detail['start_time'] = date('Y-m-d H:i',$detail['start_time']);
		}
		if($detail['end_time']>0){
			$detail['end_time'] = date('Y-m-d H:i',$detail['end_time']);
		}
		if($detail['detail_time']>0){
			$detail['detail_time'] = date('Y-m-d',$detail['detail_time']);
		}
		
		$detail['days'] = floor($detail['duration']*10/75);
		$detail['hours'] = (($detail['duration']*10)%75)/10;
		
		$detail['create_user'] = Db::name('Admin')->where('id',$detail['create_admin_id'])->value('name');
		$flows = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		$detail['check_user'] = '-';
		$check_user_ids = [];
		if($detail['check_status']<2){
			if($flows['flow_type']==1){
				$detail['check_user'] = '部门负责人';				
				$check_user_ids[]=get_department_leader($detail['create_admin_id']);
			}
			else if($flows['flow_type']==2){
				$detail['check_user'] = '上级部门负责人';
				$check_user_ids[]=get_department_leader($detail['create_admin_id'],1);
			}
			else{
				$check_user_ids = explode(',',$flows['flow_uids']);
				$check_user = Db::name('Admin')->where('id','in',$flows['flow_uids'])->column('name');
				$detail['check_user'] = implode(',',$check_user);			
			}
		}
		
		$is_check_admin = 0;
		$is_create_admin = 0;
		if($detail['create_admin_id'] == $this->uid){
			$is_create_admin = 1;
		}
		if(in_array($this->uid,$check_user_ids)){
			$is_check_admin = 1;
			//当前审核节点详情
			$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
			if($step['flow_type'] == 4){
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>1,'step_id'=>$step['id'],'check_user_id'=>$this->uid])->count();
				if($check_count>0){
					$is_check_admin = 0;
				}
			}
		}
		$moban=Db::name('FlowType')->where('id',$detail['type'])->value('name');
		View::assign('is_create_admin', $is_create_admin);
		View::assign('is_check_admin', $is_check_admin);
		View::assign('detail', $detail);
		View::assign('flows', $flows);
        return view('view_'.$moban);
    }

    //审核
    public function check()
    {
        $param = get_params();
		$detail = Db::name('Approve')->where('id',$param['id'])->find();
		//当前审核节点详情
		$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		//审核通过
		if($param['status'] == 1){
			//多人会签审批
			if($step['flow_type'] == 4){
				//查询当前会签记录数
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>1,'step_id'=>$step['id']])->count();
				//当前会签记应有记录数
				$flow_count = explode(',', $step['flow_uids']);
				if(($check_count+1) >=count($flow_count)){
					$next_step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
					if($next_step){
						//存在下一步审核
						$param['check_step_sort'] = $detail['check_step_sort']+1;
						$param['check_status'] = 1;
					}
					else{
						//不存在下一步审核，审核结束
						$param['check_status'] = 2;
					}
				}
			}
			else if($step['flow_type'] == 0){
				//自由人审批
				if($param['check_node'] == 2){
					$next_step = $detail['check_step_sort']+1;
					$flow_step = array(
						'action_id' => $detail['id'],
						'sort' => $next_step,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					$fid = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					//下一步审核步骤
					$param['check_admin_ids'] = $param['check_admin_ids'];
					$param['check_step_sort'] = $next_step;
					$param['check_status'] = 1;
				}
				else{
					//不存在下一步审核，审核结束
					$param['check_status'] = 2;
					$param['check_admin_ids'] ='';
				}
			}
			else{
				$next_step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
				if($next_step){
					//存在下一步审核
					if($next_step['flow_type'] == 1){
						$param['check_admin_ids'] = get_department_leader($this->uid);
					}
					else if($next_step['flow_type'] == 2){
						$param['check_admin_ids'] = get_department_leader($this->uid,1);
					}
					else{
						$param['check_admin_ids'] = $next_step['flow_uids'];
					}
					$param['check_step_sort'] = $detail['check_step_sort']+1;
					$param['check_status'] = 1;
				}
				else{
					//不存在下一步审核，审核结束
					$param['check_status'] = 2;
					$param['check_admin_ids'] ='';
				}
			}			
			//审核通过数据操作
			$param['last_admin_id'] = $this->uid;
			$param['flow_admin_ids'] = $detail['flow_admin_ids'].$this->uid.',';
			$res = Db::name('Approve')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'check_time' => time(),
					'status' => $param['status'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('check', $param['id'], $param);
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}
		}
		else if($param['status'] == 2){
			//拒绝审核，数据操作
			$param['check_status'] = 3;
			$param['last_admin_id'] = $this->uid;
			$param['flow_admin_ids'] = $detail['flow_admin_ids'].$this->uid.',';
			$param['check_admin_ids'] ='';
			$res = Db::name('Approve')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'check_time' => time(),
					'status' => $param['status'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('refue', $param['id'], $param);
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}			
		}		
		else if($param['status'] == 3){
			if($detail['create_admin_id'] != $this->uid){
				return to_assign(1,'你没权限操作');
			}
			//撤销审核，数据操作
			$param['check_status'] = 4;
			$param['check_admin_ids'] ='';
			$res = Db::name('Approve')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => 0,
					'check_user_id' => $this->uid,
					'check_time' => time(),
					'status' => $param['status'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('back', $param['id'], $param);
				return to_assign();
			}else{
				return to_assign(1,'操作失败');
			}
		}		
    }
}
