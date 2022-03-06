<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Invoice as InvoiceList;
use app\finance\validate\InvoiceCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Invoice extends BaseController
{
	//发票列表检索
	public function get_list($where = [],$param=[], $type='and')
    {
		$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
		if($type == 'or'){
			$list = Db::name('Invoice')
				->field('i.*,a.name,d.title as department_name')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'left')
				->join('Department d', 'd.id = i.did', 'left')
				->whereOr($where)
				->order('i.id desc')
				->group('i.id')
				->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function($item, $key){
					$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
					if ($item['open_time'] > 0) {
						$item['open_time'] = empty($item['open_time']) ? '0' : date('Y-m-d', $item['open_time']);
						$item['open_name'] = Db::name('Admin')->where('id',$item['open_admin_id'])->value('name');
					}
					else{
						$item['open_time'] = '';
						$item['open_name'] = '-';
					}
					$item['check_user'] = '-';
					if($item['check_status']<2 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
					return $item;
				});
		}
		else{
			$list = Db::name('Invoice')
				->field('i.*,a.name,d.title as department_name')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'left')
				->join('Department d', 'd.id = i.did', 'left')
				->where($where)
				->order('i.id desc')
				->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function($item, $key){
					$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
					$item['check_user'] = '-';
					if ($item['open_time'] > 0) {
						$item['open_time'] = empty($item['open_time']) ? '0' : date('Y-m-d', $item['open_time']);
						$item['open_name'] = Db::name('Admin')->where('id',$item['open_admin_id'])->value('name');
					}
					else{
						$item['open_time'] = '';
						$item['open_name'] = '-';
					}
					if($item['check_status']<2 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
					return $item;
				});
		}
        return $list;
    }
	
	//发票详情
    public function detail($id = 0)
    {
        $invoice = Db::name('Invoice')->where(['id' => $id])->find();
        if ($invoice) {
            $invoice['create_user'] = Db::name('Admin')->where(['id' => $invoice['admin_id']])->value('name');
            $invoice['department'] = Db::name('Department')->where(['id' => $invoice['did']])->value('title');
            $invoice['check_admin'] = Db::name('Admin')->where(['id' => $invoice['check_admin_id']])->value('name');
            $invoice['open_admin'] = Db::name('Admin')->where(['id' => $invoice['open_admin_id']])->value('name');
            if ($invoice['check_time'] > 0) {
                $invoice['check_time'] = empty($invoice['check_time']) ? '0' : date('Y-m-d H:i', $invoice['check_time']);
            }
            if ($invoice['open_time'] > 0) {
                $invoice['open_time'] = empty($invoice['open_time']) ? '0' : date('Y-m-d', $invoice['open_time']);
            }
            else{
                $invoice['open_time'] = '-';
            }
        }
        return $invoice;
    }

	//我申请的发票
    public function index()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['check_status'])) {
				$where[] = ['i.check_status','=',$param['check_status']];
            }
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $where[] = ['i.create_time', 'between', [$start_time, $end_time]];
            }			
			$where[] = ['i.admin_id','=',$this->uid];
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//待审批的发票
    public function list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status'])?$param['status']:0;
			$user_id = $this->uid;
			//查询条件
			$map1 = [];
			$map2 = [];
			$map1[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',i.check_admin_ids)")];
			$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',i.flow_admin_ids)")];
			
			if($status == 0){
				$list = $this->get_list([$map1,$map2],$param,'or');
			}			
			if($status == 1){
				$list = $this->get_list($map1,$param);
			}
			if($status == 2){
				$list = $this->get_list($map2,$param);
            }	
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//发票开具
	public function checkedlist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['check_status'])) {
				$where[] = ['i.check_status','=',$param['check_status']];
            }
			else{
				$where[] = ['i.check_status','in',[2,5,10]];
			}
            //按时间检索
            $start_time = !empty($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = !empty($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $where[] = ['i.create_time', 'between', [$start_time, $end_time]];
            }			
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['check_status'] = 1;
			$param['check_step_sort'] = 0;
			$flow_list = Db::name('Flow')->where('id',$param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);
            if ($param['type'] == 1) {
                if (!$param['invoice_tax']) {
                    return to_assign(1, '纳税人识别号不能为空');
                }
                if (!$param['invoice_bank']) {
                    return to_assign(1, '开户银行不能为空');
                }
                if (!$param['invoice_account']) {
                    return to_assign(1, '银行账号不能为空');
                }
                if (!$param['invoice_banking']) {
                    return to_assign(1, '银行营业网点不能为空');
                }
                if (!$param['invoice_address']) {
                    return to_assign(1, '银地址不能为空');
                }
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(InvoiceCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
				
				//删除原来的审核流程和审核记录
				Db::name('FlowStep')->where(['action_id'=>$param['id'],'type'=>3,'delete_time'=>0])->update(['delete_time'=>time()]);
				Db::name('FlowRecord')->where(['action_id'=>$param['id'],'type'=>3,'delete_time'=>0])->update(['delete_time'=>time()]);		
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
					foreach ($flow as $key => &$value){
						$value['action_id'] = $param['id'];
						$value['sort'] = $key;
						$value['type'] = 3;
						$value['create_time'] = time();
					}
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				else{
					$flow_step = array(
						'action_id' => $param['id'],
						'type' => 3,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
				
                $res = InvoiceList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res !== false) {
                    add_log('edit', $param['id'], $param);
                    return to_assign();   
                } else {
                    return to_assign(1, '操作失败');
                }
            } else {
                try {
                    validate(InvoiceCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $admin_id = $this->uid;
                $param['admin_id'] = $admin_id;
                $param['did'] = get_login_admin('did');
                $param['create_time'] = time();
				
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
					$exid = InvoiceList::strict(false)->field(true)->insertGetId($param);
					foreach ($flow as $key => &$value){
						$value['action_id'] = $exid;
						$value['sort'] = $key;
						$value['type'] = 3;
						$value['create_time'] = time();
					}
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				else{
					$exid = InvoiceList::strict(false)->field(true)->insertGetId($param);
					$flow_step = array(
						'action_id' => $exid,
						'type' => 3,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
				
                
                if ($exid) {
                    add_log('apply', $exid, $param);
                    return to_assign();   
                } else {
                     return to_assign(1, '操作失败');
                }
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = $this->detail($id);
                View::assign('detail', $detail);
            }
			$department = get_login_admin('did');
			//获取发票审批流程
			$flows = get_type_flows(7,$department);
            View::assign('user', get_admin($this->uid));
            View::assign('id', $id);
			View::assign('flows', $flows);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = $this->detail($id);
		$flows = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>3,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		$detail['check_user'] = '-';
		$check_user_ids = [];
		if($detail['check_status']<2){
			if($flows['flow_type']==1){
				$detail['check_user'] = '部门负责人';				
				$check_user_ids[]=get_department_leader($detail['admin_id']);
			}
			else if($flows['flow_type']==2){
				$detail['check_user'] = '上级部门负责人';
				$check_user_ids[]=get_department_leader($detail['admin_id'],1);
			}
			else{
				$check_user_ids = explode(',',$flows['flow_uids']);
				$check_user = Db::name('Admin')->where('id','in',$flows['flow_uids'])->column('name');
				$detail['check_user'] = implode(',',$check_user);			
			}
		}
		
		$is_check_admin = 0;
		$is_create_admin = 0;
		if($detail['admin_id'] == $this->uid){
			$is_create_admin = 1;
		}
		if(in_array($this->uid,$check_user_ids)){
			$is_check_admin = 1;
			//当前审核节点详情
			$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>3,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
			if($step['flow_type'] == 4){
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>3,'step_id'=>$step['id'],'check_user_id'=>$this->uid])->count();
				if($check_count>0){
					$is_check_admin = 0;
				}
			}
		}
		View::assign('is_create_admin', $is_create_admin);
		View::assign('is_check_admin', $is_check_admin);
		View::assign('detail', $detail);
		View::assign('flows', $flows);
        View::assign('uid', $this->uid);
        return view();
    }

    //删除
    public function delete()
    {
        $id = get_params("id");
        $detail = $this->detail($id);
        if ($detail['invoice_status'] == 2) {
            return to_assign(1, "已审核的发票不能删除");
        }
        if ($detail['invoice_status'] == 3) {
            return to_assign(1, "已开具的发票不能删除");
        }
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Invoice')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
	
    //审核
    public function check()
    {
        $param = get_params();
		$detail = Db::name('Invoice')->where(['id' => $param['id']])->find();
		//当前审核节点详情
		$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>3,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		//审核通过
		if($param['status'] == 1){
			//多人会签审批
			if($step['flow_type'] == 4){
				//查询当前会签记录数
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>3,'step_id'=>$step['id']])->count();
				//当前会签记应有记录数
				$flow_count = explode(',', $step['flow_uids']);
				if(($check_count+1) >=count($flow_count)){
					$next_step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>3,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
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
						'type' => 3,
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
				$next_step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>3,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
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
			$res = Db::name('Invoice')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'type' => 3,
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
			$res = Db::name('Invoice')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'type' => 3,
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
			if($detail['admin_id'] != $this->uid){
				return to_assign(1,'你没权限操作');
			}
			//撤销审核，数据操作
			$param['check_status'] = 4;
			$param['check_admin_ids'] ='';
			$param['check_step_sort'] =0;
			$res = Db::name('Invoice')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => 0,
					'check_user_id' => $this->uid,
					'type' => 3,
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
	
	
    //作废
    public function tovoid()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if ($param['check_status'] == 10) {
                $count = Db::name('InvoiceIncome')->where(['inid'=>$param['id'],'status'=>1])->count();
                if($count>0){
                    return to_assign(1, "发票已经新增有到账记录，请先反到账后再作废发票");
                }
                else{
                    $param['update_time'] = time();
                    add_log('tovoid', $param['id'],$param);
                }
            }
            $res = InvoiceList::where('id', $param['id'])->strict(false)->field('check_status')->update($param);
            if ($res !== false) {
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //开具发票
    public function open()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$detail = Db::name('Invoice')->where(['id' => $param['id']])->find();
            if ($detail['check_status'] == 2) {
                $param['check_status'] = 5;
                $param['open_admin_id'] = $this->uid;
            }
			$param['open_time'] = isset($param['open_time']) ? strtotime(urldecode($param['open_time'])) : 0;
            $res = InvoiceList::where('id', $param['id'])->strict(false)->field('code,check_status,open_time,open_admin_id,delivery')->update($param);
            if ($res !== false) {
				add_log('open', $param['id'],$param);
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
}
