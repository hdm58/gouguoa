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

use think\facade\Db;

//设置销项发票收款状态
function invoice_income_status($invoice_id){
    $invoice_amount = Db::name('Invoice')->where(['id'=>$invoice_id])->value('amount');
	//计算发票已收款的金额
    $hasIncome = Db::name('InvoiceIncome')->where(['invoice_id'=>$invoice_id,'delete_time'=>0])->sum('amount');
	if($hasIncome*10000 >= $invoice_amount*10000){
		//发票已全部回款
		Db::name('Invoice')->where(['id'=>$invoice_id])->update(['enter_status'=>2,'enter_amount'=>$hasIncome,'enter_time'=>time()]);
	}
	else if($hasIncome > 0 && $hasIncome*10000 < $invoice_amount*10000){
		//发票部分回款
		Db::name('Invoice')->where(['id'=>$invoice_id])->update(['enter_status'=>1,'enter_amount'=>$hasIncome,'enter_time'=>time()]);
	}
	else if($hasIncome == 0){
		//发票未回款
		Db::name('Invoice')->where(['id'=>$invoice_id])->update(['enter_status'=>0,'enter_amount'=>$hasIncome,'enter_time'=>0]);
	}
}

//设置进项发票付款状态
function ticket_payment_status($ticket_id){
	$ticket_amount = Db::name('Ticket')->where(['id'=>$ticket_id])->value('amount');
	//计算发票已付款的金额
	$hasPayment = Db::name('TicketPayment')->where([['ticket_id','=',$ticket_id],['delete_time','=',0]])->sum('amount');
	if($hasPayment*10000 >= $ticket_amount*10000){
		//发票已全部付款
		Db::name('Ticket')->where(['id'=>$ticket_id])->update(['pay_status'=>2,'pay_amount'=>$hasPayment]);
	}
	else if($hasPayment > 0 && $hasPayment*10000 < $ticket_amount*10000){
		//发票部分付款
		Db::name('Ticket')->where(['id'=>$ticket_id])->update(['pay_status'=>1,'pay_amount'=>$hasPayment]);
	}
	else if($hasPayment == 0){
		//发票未付款
		Db::name('Ticket')->where(['id'=>$ticket_id])->update(['pay_status'=>0,'pay_amount'=>$hasPayment,'pay_time'=>0]);
	}
}