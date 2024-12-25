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

use app\api\BaseController;
use app\finance\model\Expense;
use app\finance\model\Invoice;
use app\finance\model\InvoiceIncome;
use app\finance\model\Ticket;
use app\finance\model\TicketPayment;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
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
	
	//报销设置为已打款
    public function topay()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthExpense($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有打款权限，请联系管理员或者HR");
			}
			//打款，数据操作
            $param['pay_status'] = 1;
            $param['pay_admin_id'] = $this->uid;
            $param['pay_time'] = time();
            $res = Expense::where('id', $param['id'])->strict(false)->field(true)->update($param);
            if ($res !== false) {
				add_log('topay', $param['id'],$param,'报销');
				//发送消息通知
				$detail = Expense::where(['id' => $param['id']])->find();
				$msg=[
					'from_uid'=>$this->uid,//发送人
					'to_uids'=>$detail['admin_id'],//接收人
					'template_id'=>'expense_pay',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$detail['id'],
						'title' => '报销'
					]
				];
				event('SendMessage',$msg);
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

}
