<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Expense as ExpenseList;
use app\finance\validate\ExpenseCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Expense extends BaseController
{
    public function get_list($where = [], $param = [], $type='and')
    {
        $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
		if($type == 'or'){
			$expense = ExpenseList::whereOr($where)
				->order('id desc')
				->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
					$item->income_month = empty($item->income_month) ? '-' : date('Y-m', $item->income_month);
					$item->expense_time = empty($item->expense_time) ? '-' : date('Y-m-d', $item->expense_time);
					$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
					$item->department = Db::name('Department')->where(['id' => $item->admin_id])->value('title');
					$item->pay_name = Db::name('Admin')->where(['id' => $item->pay_admin_id])->value('name');
					$item->pay_time = empty($item->pay_time) ? '-' : date('Y-m-d H:i', $item->pay_time);
					$item->amount = Db::name('ExpenseInterfix')->where(['exid' => $item->id])->sum('amount');
					$item['check_user'] = '-';
					if($item['check_status']<2 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
				});
		}
		else{
        $expense = ExpenseList::where($where)
            ->order('id desc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
                $item->income_month = empty($item->income_month) ? '-' : date('Y-m', $item->income_month);
                $item->expense_time = empty($item->expense_time) ? '-' : date('Y-m-d', $item->expense_time);
                $item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
                $item->department = Db::name('Department')->where(['id' => $item->admin_id])->value('title');
                $item->pay_name = Db::name('Admin')->where(['id' => $item->pay_admin_id])->value('name');
                $item->pay_time = empty($item->pay_time) ? '-' : date('Y-m-d H:i', $item->pay_time);
                $item->amount = Db::name('ExpenseInterfix')->where(['exid' => $item->id])->sum('amount');
				$item['check_user'] = '-';
				if($item['check_status']<2 && !empty($item['check_admin_ids'])){
					$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
					$item['check_user'] = implode(',',$check_user);
				}
            });
		}
        return $expense;
    }

    public function detail($id = 0)
    {
        $expense = Db::name('Expense')->where(['id' => $id])->find();
        if ($expense) {
            $expense['income_month'] = empty($expense['income_month']) ? '-' : date('Y-m', $expense['income_month']);
            $expense['expense_time'] = empty($expense['expense_time']) ? '-' : date('Y-m-d', $expense['expense_time']);
            $expense['create_user'] = Db::name('Admin')->where(['id' => $expense['admin_id']])->value('name');
            $expense['department'] = Db::name('Department')->where(['id' => $expense['did']])->value('title');
            $expense['amount'] = Db::name('ExpenseInterfix')->where(['exid' => $expense['id']])->sum('amount');
            if ($expense['pay_time'] > 0) {
                $expense['pay_time'] = date('Y-m-d H:i:s', $expense['pay_time']);
				$expense['pay_admin'] = Db::name('Admin')->where(['id' => $expense['pay_admin_id']])->value('name');
            }
            else{
                $expense['pay_time'] = '-';
            }
            $expense['list'] = Db::name('ExpenseInterfix')
                ->field('a.*,c.title as cate_title')
                ->alias('a')
                ->join('ExpenseCate c', 'a.cate_id = c.id','LEFT')
                ->where(['a.exid' => $expense['id']])
                ->select();
        }
        return $expense;
    }

    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
            //按时间检索
            $start_time = !empty($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = !empty($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $where[] = ['expense_time', 'between', [$start_time, $end_time]];
            }

			$where[] = ['admin_id','=',$this->uid];
            if (!empty($param['check_status']) && $param['check_status']!='') {
                $where[] = ['check_status', '=', $param['check_status']];
            }            
            $expense = $this->get_list($where, $param);
            return table_assign(0, '', $expense);
        } else {
            return view();
        }
    }
	
	//待审批的报销
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
	
	//报销打款
	public function checkedlist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['check_status'])) {
				$where[] = ['check_status','=',$param['check_status']];
            }
			else{
				$where[] = ['check_status','in',[2,5]];
			}
            //按时间检索
            $start_time = !empty($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = !empty($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $where[] = ['expense_time', 'between', [$start_time, $end_time]];
            }			
			$list = $this->get_list($where,$param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $dbRes = false; 
            $admin_id = $this->uid;       
            $param['income_month'] = isset($param['income_month']) ? strtotime(urldecode($param['income_month'])) : 0;
            $param['expense_time'] = isset($param['expense_time']) ? strtotime(urldecode($param['expense_time'])) : 0;
            $param['check_status'] = 1;
			$param['check_step_sort'] = 0;
			$flow_list = Db::name('Flow')->where('id',$param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ExpenseCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::startTrans();
                try {
					//删除原来的审核流程和审核记录
					Db::name('FlowStep')->where(['action_id'=>$param['id'],'type'=>2,'delete_time'=>0])->update(['delete_time'=>time()]);
					Db::name('FlowRecord')->where(['action_id'=>$param['id'],'type'=>2,'delete_time'=>0])->update(['delete_time'=>time()]);		
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
							$value['type'] = 2;
							$value['create_time'] = time();
						}
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
					}
					else{
						$flow_step = array(
							'action_id' => $param['id'],
							'type' => 2,
							'flow_uids' => $param['check_admin_ids'],
							'create_time' => time()
						);
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					}
					
                    $res = ExpenseList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                    if ($res !== false) {
                        $exid = $param['id'];
                        //相关内容多个数组;
                        $amountData = isset($param['amount']) ? $param['amount'] : '';
                        $remarksData = isset($param['remarks']) ? $param['remarks'] : '';
                        $cateData = isset($param['cate_id']) ? $param['cate_id'] : '';
                        $idData = isset($param['expense_id']) ? $param['expense_id'] : 0;
                        if ($amountData) {
                            foreach ($amountData as $key => $value) {
                                if (!$value) {
                                    continue;
                                }    
                                $data = [];
                                $data['id'] = $idData[$key];
                                $data['exid'] = $exid;
                                $data['admin_id'] = $admin_id;
                                $data['amount'] = $amountData[$key];
                                $data['cate_id'] = $cateData[$key];
                                $data['remarks'] = $remarksData[$key];
                                if ($data['amount'] == 0) {
                                    Db::rollback();
                                    return to_assign(1, '第' . ($key + 1) . '条报销金额不能为零');
                                }
                                if ($data['id'] > 0) {
                                    $data['update_time'] = time();
                                    $resa = Db::name('ExpenseInterfix')->strict(false)->field(true)->update($data);
                                } else {
                                    $data['create_time'] = time();
                                    $eid = Db::name('ExpenseInterfix')->strict(false)->field(true)->insertGetId($data);
                                }
                            }
                        }		
						
                        add_log('edit', $exid, $param);
                        Db::commit();
                        $dbRes = true;
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
                Db::rollback();
                    return to_assign(1, $e->getMessage());
                }
            } else {
                try {
                    validate(ExpenseCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $admin_id;
                $param['did'] = get_login_admin('did');
                Db::startTrans();
                try {
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
						$exid = ExpenseList::strict(false)->field(true)->insertGetId($param);
						foreach ($flow as $key => &$value){
							$value['action_id'] = $exid;
							$value['sort'] = $key;
							$value['type'] = 2;
							$value['create_time'] = time();
						}
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
					}
					else{
						$exid = ExpenseList::strict(false)->field(true)->insertGetId($param);
						$flow_step = array(
							'action_id' => $exid,
							'type' => 2,
							'flow_uids' => $param['check_admin_ids'],
							'create_time' => time()
						);
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					}
					
                    if ($exid) {
                        //相关内容多个数组;
                        $amountData = isset($param['amount']) ? $param['amount'] : '';
                        $remarksData = isset($param['remarks']) ? $param['remarks'] : '';
                        $cateData = isset($param['cate_id']) ? $param['cate_id'] : '';
                        if ($amountData) {
                            foreach ($amountData as $key => $value) {
                                if (!$value) {
                                    continue;
                                }
                                $data = [];
                                $data['exid'] = $exid;
                                $data['admin_id'] = $admin_id;
                                $data['amount'] = $amountData[$key];
                                $data['cate_id'] = $cateData[$key];
                                $data['remarks'] = $remarksData[$key];
                                $data['create_time'] = time();
                                if ($data['amount'] == 0) {
                                    Db::rollback();
                                    return to_assign(1, '第' . ($key + 1) . '条报销金额不能为零');
                                }
                                $eid = Db::name('ExpenseInterfix')->strict(false)->field(true)->insertGetId($data);
                            }
                        }						
                        add_log('add', $exid, $param);
                        Db::commit();
                        $dbRes = true;
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
                Db::rollback();
                    return to_assign(1, $e->getMessage());
                }
            }
            if ($dbRes == true) {
                return to_assign();
            } else {
                return to_assign(1, '保存失败');
            }
        }
        else{
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $expense = $this->detail($id);
                View::assign('expense', $expense);
            }
			$department = get_login_admin('did');
			//获取报销审批流程
			$flows = get_type_flows(6,$department);
            $expense_cate = Db::name('ExpenseCate')->where(['status' => 1])->select()->toArray();
            View::assign('user', get_admin($this->uid));
            View::assign('expense_cate', $expense_cate);
            View::assign('flows', $flows);
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = $this->detail($id);
		$flows = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
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
			$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
			if($step['flow_type'] == 4){
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>2,'step_id'=>$step['id'],'check_user_id'=>$this->uid])->count();
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
        $expense = $this->detail($id);
        if ($expense['check_status'] == 2) {
            return to_assign(1, "已审核的报销记录不能删除");
        }
        if ($expense['check_status'] == 3) {
            return to_assign(1, "已打款的报销记录不能删除");
        }
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('expense')->update($data) !== false) {
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
		$detail = Db::name('Expense')->where(['id' => $param['id']])->find();
		//当前审核节点详情
		$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		//审核通过
		if($param['status'] == 1){
			//多人会签审批
			if($step['flow_type'] == 4){
				//查询当前会签记录数
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>2,'step_id'=>$step['id']])->count();
				//当前会签记应有记录数
				$flow_count = explode(',', $step['flow_uids']);
				if(($check_count+1) >=count($flow_count)){
					$next_step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
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
						'type' => 2,
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
				$next_step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
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
			$res = Db::name('Expense')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'type' => 2,
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
			$res = Db::name('Expense')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'type' => 2,
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
			$res = Db::name('Expense')->strict(false)->field('check_step_sort,check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => 0,
					'check_user_id' => $this->uid,
					'type' => 2,
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
	
	//设置为已打款
    public function topay()
    {
        $param = get_params();
        if (request()->isAjax()) {
			//打款，数据操作
            $param['check_status'] = 5;
            $param['pay_admin_id'] = $this->uid;
            $param['pay_time'] = time();
            $res = ExpenseList::where('id', $param['id'])->strict(false)->field(true)->update($param);
            if ($res !== false) {
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }

}
