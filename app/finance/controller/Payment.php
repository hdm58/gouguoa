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

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\TicketPayment;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Payment extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new TicketPayment();
    }
	
    public function datalist()
    {
		$uid = $this->uid;
		$auth = isAuthPayment($uid);
        if (request()->isAjax()) {
            $param = get_params();
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$dids_son = get_leader_departments($uid);
            $where = array();
            $whereOr = array();
            $where[] = ['delete_time', '=', 0];
			if($tab == 0){
				if($auth == 0){
					$whereOr[] = ['admin_id', '=', $this->uid];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
					$whereOr[] = ['did','in',$dids_son];
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
			//按时间检索
			if (!empty($param['create_time'])) {
				$create_time =explode('~', $param['create_time']);
				$where[] = ['create_time', 'between', [strtotime(urldecode($create_time[0])),strtotime(urldecode($create_time[1].' 23:59:59'))]];
			}
			if (!empty($param['pay_time'])) {
				$pay_time =explode('~', $param['pay_time']);
				$where[] = ['pay_time', 'between', [strtotime(urldecode($pay_time[0])),strtotime(urldecode($pay_time[1].' 23:59:59'))]];
			}
			if (!empty($param['fundscate_id'])) {
                $where[] = ['fundscate_id', '=', $param['fundscate_id']];
            }
            if (!empty($param['paytype_id'])) {
                $where[] = ['paytype_id', '=', $param['paytype_id']];
            }
			if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
			View::assign('auth', $auth);
            return view();
        }
    }

    //新增
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {   
			$id = isset($param['id']) ? $param['id'] : 0;
            if($param['ticket_id']>0); {
				//计算发票已付款的金额
				$hasPayment = $this->model->where([['id','<>',$id],['ticket_id','=',$param['ticket_id']],['status','in',[1,2]]])->sum('amount');
				//查询发票金额
				$ticketAmount = Db::name('Ticket')->where(['id'=>$param['ticket_id']])->value('amount');
				if(($param['amount']*10000 + $hasPayment*10000) > $ticketAmount*10000){
					return to_assign(1,'付款金额大于关联发票金额，不允许保存，请核对');
				}
			}
			if(!empty($param['pay_time'])){
				$param['pay_time']=strtotime($param['pay_time']);
			}
			if($id==0){
				$param['admin_id'] = $this->uid;
				$param['did'] = $this->did;
				$this->model->add($param);
			}
			else{
				$this->model->edit($param);
			}
		}
        else{
            $id = isset($param['id']) ? $param['id']: 0 ;
			if($id>0){
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
			}
			View::assign('id', $id);
			return view();
        }
    }
    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = $this->model->getById($id);
		if(empty($detail)){
			throw new \think\exception\HttpException(406, '找不到记录');
		}
		if($detail['status']==2){
			$detail['confirm_name'] = Db::name('Admin')->where('id','=',$detail['confirm_uid'])->value('name');
		}
        View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/finance/view_payment');
		}
        return view();
    }

    //删除付款记录
    public function del()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $payment =TicketPayment::where(['id'=>$param['id']])->find();
            $ticket = $this->model->where(['id'=>$payment['ticket_id']])->find();
            if($payment){
                $res = TicketPayment::where(['id'=>$param['id']])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    if($payment['amount']*100 == $ticket['amount']*100){
                        //发票全部反付款
                        $this->model->where(['id'=>$payment['ticket_id']])->update(['pay_status'=>0,'pay_amount'=>0,'pay_time'=>0]);
                    }
                    else if($payment['amount']*100 < $ticket['amount']*100){
                        $payTotal=TicketPayment::where(['ticket_id'=>$payment['ticket_id'],'status'=>1])->sum('amount');
                        //发票部分付款
                        $this->model->where(['id'=>$payment['ticket_id']])->update(['pay_status'=>1,'pay_amount'=>$payTotal,'pay_time'=>time()]);
                    }
                    add_log('pay',$payment['ticket_id'],$ticket);
                    return to_assign();
                }
                else{
                    return to_assign(1,'操作失败');
                }
            }
        }
    }
	
	
	//回款记录
    public function record()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			$whereOr = [];
			$where[]=['check_status','=',2];
			$where[]=['status','=',2];
			$where[]=['delete_time','=',0];
			//按时间检索
			if (!empty($param['create_time'])) {
				$create_time =explode('~', $param['create_time']);
				$where[] = ['create_time', 'between', [strtotime(urldecode($create_time[0])),strtotime(urldecode($create_time[1].' 23:59:59'))]];
			}
			if (!empty($param['pay_time'])) {
				$pay_time =explode('~', $param['pay_time']);
				$where[] = ['pay_time', 'between', [strtotime(urldecode($pay_time[0])),strtotime(urldecode($pay_time[1].' 23:59:59'))]];
			}
			$list = $this->model->datalist($param,$where,$whereOr);
			$amount = $this->model::where($where)->sum('amount');					
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        } else {
            return view();
        }
    }
}
