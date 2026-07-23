<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
declare (strict_types = 1);
namespace app\finance\controller;

use app\api\BaseController;
use app\finance\model\Account;
use app\finance\model\Loan;
use app\finance\model\Expense;
use app\finance\model\Invoice;
use app\finance\model\InvoiceIncome;
use app\finance\model\Ticket;
use app\finance\model\TicketPayment;
use app\finance\model\IncomeRefund;
use app\api\model\FinanceLog;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
    //获取获取借支
    public function get_loan()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['admin_id', '=', $this->uid];		
		$where[] = ['balance_status', '<', 2];		
		$where[] = ['status', '=', 2];
		$where[] = ['check_status', '=', 2];
		$where[] = ['back_status', '=', 0];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$list = Db::name('Loan')->where($where)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) {
				$item['create_time'] = to_date($item['create_time'],'Y-m-d');
				$item['pay_time'] = to_date($item['pay_time'],'Y-m-d');
				$item['un_balance_cost'] = ($item['cost']*100 - $item['balance_cost']*100)/100;
				if($item['balance_status']==0){
					$item['balance_name']='待冲抵';
				}
				if($item['balance_status']==1){
					$item['balance_name']='部分冲抵';
				}
				return $item;
			});
		return table_assign(0, '', $list);
    }
	
    //获取获取报销
    public function get_expense()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		if(!empty($param['project_id'])){
			$where[] = ['project_id', '=', $param['project_id']];
		}
		$model = new Expense();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
    //获取企业主体
    public function get_enterprise()
    {
		$param = get_params();
		$where = array();
		$where[] = ['status', '=', 1];
		$list = Db::name('Enterprise')->where($where)->select()->toArray();
		return to_assign(0, '', $list);
    }
	//获取资金账户
    public function get_account()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['status', '=', 1];
		if(!empty($param['enterprise_id'])){
			$where[] = ['enterprise_id', '=', $param['enterprise_id']];
		}
		$model = new Account();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
    //获取支付方式
    public function get_paytype()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['status', '=', 1];
		$list = Db::name('PayType')->where($where)->select()->toArray();
		return to_assign(0, '', $list);
    }

	//获取销项发票
    public function get_invoice()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['open_status', '=', 2];
		if(!empty($param['project_id'])){
			$where[] = ['project_id', '=', $param['project_id']];
		}
		$model = new Invoice();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
    //获取进项发票
    public function get_ticket()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['open_status', '=', 2];
		if(!empty($param['project_id'])){
			$where[] = ['project_id', '=', $param['project_id']];
		}
		$model = new Ticket();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
    //删除报销项
    public function del_expense_interfix()
    {
        $id = get_params("id");
        $admin_id = Db::name('ExpenseInterfix')->where('id', $id)->value('admin_id');
        if ($admin_id == $this->uid) {
            if (Db::name('ExpenseInterfix')->where('id', $id)->delete() !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "您不是申请人，没权限删除该报销数据");
        }
    }
	
    //开具发票
    public function open()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有开票权限，请联系管理员或者HR");
			}
			$status = Invoice::where(['id' => $param['id']])->value('check_status');
            if ($status == 2) {
                $param['open_status'] = 1;
                $param['open_admin_id'] = $this->uid;
            }
			if(isset($param['open_time'])){
				$param['open_time'] = strtotime(urldecode($param['open_time']));
			}
            $res = Invoice::where('id', $param['id'])->strict(false)->field('code,open_status,open_time,open_admin_id,delivery')->update($param);
            if ($res !== false) {
				add_log('open', $param['id'],$param,'发票');
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //作废发票
    public function tovoid()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有作废发票权限，请联系管理员或者HR");
			}
			$count = InvoiceIncome::where(['invoice_id'=>$param['id'],'status'=>1])->count();
			if($count>0){
				return to_assign(1, "发票已经新增有回款记录，请先反回款后再作废发票");
			}
			else{
				$param['update_time'] = time();
				$param['open_status'] = 2;
			}
            $res = Invoice::where('id', $param['id'])->strict(false)->field('update_time,open_status')->update($param);
            if ($res !== false) {
                return to_assign();
                add_log('tovoid', $param['id'],$param,'发票');
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }

    //反作废发票
    public function novoid()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有反作废发票权限，请联系管理员或者HR");
			}
			$param['open_status'] = 1;
			$param['update_time'] = time();
            $res = Invoice::where('id', $param['id'])->strict(false)->field('update_time,open_status')->update($param);
            if ($res !== false) {
                return to_assign();
				add_log('novoid', $param['id'],$param,'发票');
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //上传电子发票
    public function upload_invoice()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有开票权限，请联系管理员或者HR");
			}
			$param['update_time'] = time();
            $res = Invoice::where('id', $param['id'])->strict(false)->field('update_time,other_file_ids')->update($param);
            if ($res !== false) {
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
	
    //作废收票
    public function tovoid_ticket()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有作废发票权限，请联系管理员或者HR");
			}
			$count = TicketPayment::where(['ticket_id'=>$param['id'],'status'=>1])->count();
			if($count>0){
				return to_assign(1, "发票已经新增有付款记录，请先反付款后再作废发票");
			}
			else{
				$param['update_time'] = time();
				$param['open_status'] = 2;
			}
            $res = Ticket::where('id', $param['id'])->strict(false)->field('update_time,open_status')->update($param);
            if ($res !== false) {
                return to_assign();
                add_log('tovoid', $param['id'],$param,'发票');
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }

    //反作废收票
    public function novoid_ticket()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有反作废发票权限，请联系管理员或者HR");
			}
			$param['open_status'] = 1;
			$param['update_time'] = time();
            $res = Ticket::where('id', $param['id'])->strict(false)->field('update_time,open_status')->update($param);
            if ($res !== false) {
                return to_assign();
				add_log('novoid', $param['id'],$param,'发票');
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //上传
    public function upload_ticket()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有发票权限，请联系管理员或者HR");
			}
			$param['update_time'] = time();
            $res = Ticket::where('id', $param['id'])->strict(false)->field('update_time,other_file_ids')->update($param);
            if ($res !== false) {
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	

	//借支设置为已归还
    public function loan_back()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuth($this->uid,'finance_admin','conf_6');
			if($auth == 0){
				return to_assign(1, "你没有归还到账操作权限，请联系管理员或者HR");
			}
			//打款，数据操作
            $param['back_status'] = 1;
            $param['back_admin_id'] = $this->uid;
            $param['back_time'] = time();
            $res = Loan::where('id', $param['id'])->strict(false)->field('back_time,back_admin_id,back_status,account_id,paytype_id')->update($param);
            if ($res !== false) {
				add_log('toback', $param['id'],$param,'借支');
				$detail = Loan::where(['id' => $param['id']])->find();
				$amount = ($detail['cost']*10000-$detail['balance_cost']*10000)/10000;
				if($amount>0){
					$log=new FinanceLog();
					//注入收入流水
					$log->add('loan_back',$param['id'],$amount);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'loan_back',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '借支归还',
							'status' => '归还确认',
							'amount' => $amount
						]
					];
					event('SendMessage',$msg);
				}
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //确认收款
    public function confirm_income()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuth($this->uid,'finance_admin','conf_6');
			if($auth == 0){
				return to_assign(1, "你没有到账操作权限，请联系管理员或者HR");
			}
			$detail = InvoiceIncome::where('id', $param['id'])->find();
			if($detail['status']==2){
				return to_assign(1, "该记录已确认，请勿重复操作");
			}
			$param['confirm_uid'] = $this->uid;
			$param['confirm_time'] = time();
			$param['status'] = 2;
            $res = InvoiceIncome::where('id', $param['id'])->strict(false)->field('confirm_uid,confirm_time,status,account_id,paytype_id')->update($param);
            if ($res !== false) {
                add_log('confirm', $param['id'],$param,'业务收款');
				$log=new FinanceLog();
				$log->add('income',$param['id']);
				//发送消息通知
				$msg=[
					'from_uid'=>$this->uid,//发送人
					'to_uids'=>$detail['admin_id'],//接收人
					'template_id'=>'invoice_income',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$detail['id'],
						'title' => '业务收款',
						'status' => '到账确认',
						'amount' => $detail['amount']
					]
				];
				event('SendMessage',$msg);
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }

    //反确认收款
    public function unconfirm_income()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuth($this->uid,'finance_admin','conf_6');
			if($auth == 0){
				return to_assign(1, "你没有反确认到账操作权限，请联系管理员或者HR");
			}
			$detail = InvoiceIncome::where('id', $param['id'])->find();
			if($detail['status']==1){
				return to_assign(1, "该记录已反确认，请勿重复操作");
			}
			$count = Db::name('IncomeRefund')->where([['income_id','=',$param['id']],['delete_time','=',0]])->count();
			if($count>0){
				return to_assign(1, "该记录已有退款记录，不支持反确认操作");
			}
			$param['confirm_uid'] = 0;
			$param['confirm_time'] = 0;
			$param['status'] = 1;
            $res = InvoiceIncome::where('id', $param['id'])->strict(false)->field('confirm_uid,confirm_time,status,account_id,paytype_id')->update($param);
            if ($res !== false) {
                add_log('unconfirm', $param['id'],$param,'业务收款');
				$log=new FinanceLog();
				$log->del('income',$param['id']);
				//发送消息通知
				$msg=[
					'from_uid'=>$this->uid,//发送人
					'to_uids'=>$detail['admin_id'],//接收人
					'template_id'=>'invoice_income',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$detail['id'],
						'title' => '业务收款',
						'status' => '到账反确认',
						'amount' => $detail['amount']
					]
				];
				event('SendMessage',$msg);
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //确认付款
    public function confirm_payment()
    {
        $param = get_params();
		$table=$param['table'];
		$detail = Db::name($table)->where(['id' => $param['id']])->find();
        if (request()->isAjax()) {
			$auth = isAuth($this->uid,'finance_admin','conf_7');
			if($auth == 0){
				return to_assign(1, "你没有打款操作权限，请联系管理员或者HR");
			}
			if($detail['status']==2){
				return to_assign(1, "该记录已确认打款，请勿重复操作");
			}
			$param['confirm_uid'] = $this->uid;
			$param['confirm_time'] = time();
			$param['status'] = 2;
            $res = Db::name($table)->where('id', $param['id'])->strict(false)->field('confirm_uid,confirm_time,status,account_id,paytype_id')->update($param);
			
            if ($res !== false) {
                add_log('confirm', $param['id'],$param,'打款确认');
				$log=new FinanceLog();
				if($table == 'Loan'){
					//注入打款流水
					$log->add('loan',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'loan_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '借支打款',
							'status' => '打款确认',
							'amount' => $detail['cost']
						]
					];
					event('SendMessage',$msg);
				}
				if($table == 'Expense'){
					//注入打款流水
					$log->add('expense',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'expense_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '报销打款',
							'status' => '打款确认',
							'amount' => $detail['cost']
						]
					];
					event('SendMessage',$msg);
				}
				if($table == 'TicketPayment'){
					//注入打款流水
					$log->add('payment',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'ticket_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '业务付款',
							'status' => '打款确认',
							'amount' => $detail['amount']
						]
					];
					event('SendMessage',$msg);
				}
				if($table == 'IncomeRefund'){
					//注入打款流水
					$log->add('refund',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'refund_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '业务退款',
							'status' => '打款确认',
							'amount' => $detail['amount']
						]
					];
					event('SendMessage',$msg);
				}
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
		else{
			$detail = Db::name($table)->where(['id' => $param['id']])->find();
			$detail['enterprise'] = Db::name('Enterprise')->where(['id' => $detail['enterprise_id']])->value('title');
			$account = Db::name('Account')->where(['enterprise_id' => $detail['enterprise_id'],'status'=>1])->select();
			View::assign('detail', $detail);
			View::assign('account', $account);
			return view('confirm_'.$table);
		}
    }

    //反确认付款
    public function unconfirm_payment()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuth($this->uid,'finance_admin','conf_7');
			if($auth == 0){
				return to_assign(1, "你没有反确认付款操作权限，请联系管理员或者HR");
			}
			$table=$param['table'];
			$detail = Db::name($table)->where(['id' => $param['id']])->find();
			if($detail['status']==1){
				return to_assign(1, "该记录已反确认，请勿重复操作");
			}
			$param['confirm_uid'] = 0;
			$param['confirm_time'] = 0;
			$param['status'] = 1;
            $res = Db::name($table)->where('id', $param['id'])->strict(false)->field('confirm_uid,confirm_time,status')->update($param);
			
            if ($res !== false) {
                add_log('unconfirm', $param['id'],$param,'打款反确认');
				$log=new FinanceLog();
				if($table == 'Loan'){
					//删除打款流水
					$log->del('loan',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'loan_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '借支打款',
							'status' => '打款反确认',
							'amount' => $detail['cost']
						]
					];
					event('SendMessage',$msg);
				}
				if($table == 'Expense'){
					//删除打款流水
					$log->del('expense',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'expense_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '报销打款',
							'status' => '打款反确认',
							'amount' => $detail['cost']
						]
					];
					event('SendMessage',$msg);
				}
				if($table == 'TicketPayment'){
					//删除打款流水
					$log->del('payment',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'ticket_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '业务付款',
							'status' => '打款反确认',
							'amount' => $detail['amount']
						]
					];
					event('SendMessage',$msg);
				}
				if($table == 'IncomeRefund'){
					//删除打款流水
					$log->del('refund',$param['id']);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,//发送人
						'to_uids'=>$detail['admin_id'],//接收人
						'template_id'=>'refund_payment',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$detail['id'],
							'title' => '业务退款',
							'status' => '打款反确认',
							'amount' => $detail['amount']
						]
					];
					event('SendMessage',$msg);
				}
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
		}
    }
}
