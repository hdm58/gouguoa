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
use app\finance\model\Ticket as TicketModel;
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
        $this->model = new TicketModel();
    }
	
    public function datalist()
    {
		$auth = isAuthPayment($this->uid);
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            $where[] = ['delete_time', '=', 0];
            $where[] = ['check_status', '=', 2];
            $where[] = ['open_status', '=', 1];
			$where[] = ['invoice_type','>',0];
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['pay_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['pay_status']) && $param['pay_status']!='') {
                $where[] = ['pay_status', '=', $param['pay_status']];
            }
			if($auth == 0){
				$where[] = ['admin_id','=',$this->uid];
			}
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
			View::assign('auth', $auth);
            return view();
        }
    }

    //查看
    public function add()
    {
        $param = get_params();
		$auth = isAuthPayment($this->uid);
        if (request()->isAjax()) {   
			if($auth == 0){
				return to_assign(1, "你没有付款管理权限，请联系管理员或者HR");
			}
            $ticket_id = $param['ticket_id'];   
            $admin_id = $this->uid;
            //计算已付款的金额
            $hasPay = TicketPayment::where(['ticket_id'=>$ticket_id,'status'=>1])->sum('amount');
            //查询发票金额
            $ticketAmount = $this->model->where(['id'=>$ticket_id])->value('amount');
            if($param['pay_type']==1){ //单个付款记录
                //相关内容多个数组
                $payPriceData=isset($param['amount'])? $param['amount'] : '';
                $payTimeData=isset($param['pay_time'])? $param['pay_time'] : '';
                $remarksData=isset($param['remarks'])? $param['remarks'] : '';

                //把合同协议关联的单个内容的发票入账明细重新添加
                if($payPriceData){
                    $pay_price = 0;
                    $insert = [];
		            $time = time();
                    foreach ($payPriceData as $key => $value) {
                        if (!$value ) continue;
                        $insert[] = [
                            'ticket_id' => $ticket_id,
						    'amount' 	=> $value,
						    'pay_time' => $payTimeData[$key]? strtotime($payTimeData[$key]) : 0,
						    'remarks' 	    => $remarksData[$key],
						    'admin_id' 	    => $admin_id,
						    'create_time'		=> $time
						];
                        $pay_price += $value*100;
                    }
                    if(($pay_price + $hasPay*100)> $ticketAmount*100){
                        return to_assign(1,'付款金额大于发票金额，不允许保存');
                    }
                    else{
                        $res = TicketPayment::strict(false)->field(true)->insertAll($insert);
                        if($res!==false){
                            if(($pay_price + $hasPay*100) == $ticketAmount*100){
                                //发票全部付款
                                $this->model->where(['id'=>$ticket_id])->update(['pay_status'=>2,'pay_amount'=>$ticketAmount,'pay_time'=>time()]);
                            }
                            else if(($pay_price + $hasPay*100) < $ticketAmount*100){
                                $payTotal=($pay_price + $hasPay*100)/100;
                                //发票部分付款
                                $this->model->where(['id'=>$ticket_id])->update(['pay_status'=>1,'pay_amount'=>$payTotal,'pay_time'=>time()]);
                            }
                            add_log('add',$ticket_id,$param);
                            return to_assign();
                        }
                        else{
                            return to_assign(1,'保存失败');
                        }
                    }
                }
                else{
                    return to_assign(1,'提交的付款数据异常，请核对再提交');
                }         
            }
            else if($param['pay_type']==2){ //全部付款记录
                $pay_price = ($ticketAmount*100-$hasPay*100)/100;
                $data = [
                    'ticket_id' => $ticket_id,
                    'amount' => $pay_price,
                    'pay_time' => isset($param['pay_time'])? strtotime($param['pay_time']) : 0,
                    'remarks' => '一次性全部付款',
                    'admin_id' => $admin_id,
                    'create_time' => time()
                ];
                $res = TicketPayment::strict(false)->field(true)->insertGetId($data);
                if($res!==false){
                    //设置发票全部付款
                    $this->model->where(['id'=>$ticket_id])->update(['pay_status'=>2,'pay_amount'=>$ticketAmount,'pay_time'=>time()]);
                    add_log('add',$ticket_id,$param);
                    return to_assign();
                }
            }
            else if ($param['pay_type']==3) {//全部反账记录
                //作废初始化发票付款数据
                $res = TicketPayment::where(['ticket_id'=>$ticket_id])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    //设置发票全部没付款
                    $this->model->where(['id'=>$ticket_id])->update(['pay_status'=>0,'pay_amount'=>0,'pay_time'=>0]);
                    add_log('tovoid',$ticket_id,$param);
                    return to_assign();
                }                
            }
        }
        else{
			if($auth == 0){
				return view(EEEOR_REPORTING,['code'=>405,'warning'=>'无权限访问']);
			}
            $id = isset($param['id']) ? $param['id']: 0 ;
            $detail = $this->model->getById($id);
			if(empty($detail)){
				return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到记录']);
			}
			$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
			$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['file_array'] = $file_array;
			$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['other_file_array'] = $other_file_array;
			if($detail['open_status']>0){
				$detail['open_admin_name'] = Db::name('Admin')->where('id','=',$detail['open_admin_id'])->value('name');
			}
			$not_pay =  ($detail['amount']*100 - $detail['pay_amount']*100)/100;
			$detail['not_pay'] = sprintf("%.2f",$not_pay);
			//已付款的记录
			$detail['payment'] = TicketPayment::field('i.*,a.name as admin')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'LEFT')
				->where(['i.ticket_id'=>$id,'i.status'=>1])
				->order('i.pay_time desc')
				->select();
            View::assign('uid', $this->uid);
            View::assign('id', $id);
            View::assign('detail', $detail);
			if($detail['invoice_type'] == 0){
				return view('add_a');
			}
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
		$detail['not_pay'] =  ($detail['amount']*100 - $detail['pay_amount']*100)/100;
		//已付款的记录
		$detail['payment'] = TicketPayment::field('i.*,a.name as admin')
			->alias('i')
			->join('Admin a', 'a.id = i.admin_id', 'LEFT')
			->where(['i.ticket_id'=>$id,'i.status'=>1])
			->order('i.pay_time desc')
			->select();
			
		$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
		$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
		$detail['file_array'] = $file_array;
		$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
		$detail['other_file_array'] = $other_file_array;
		if($detail['open_status']>0){
			$detail['open_admin_name'] = Db::name('Admin')->where('id','=',$detail['open_admin_id'])->value('name');
		}
        View::assign('uid', $this->uid);
        View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/finance/view_payment');
		}
        return view();
    }

    //删除付款记录
    public function delete()
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
			$where[]=['status','=',1];
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['pay_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
			$model = new TicketPayment();
			$list = $model->datalist($param,$where);
			
			$amount = $model::where($where)->sum('amount');					
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
}
