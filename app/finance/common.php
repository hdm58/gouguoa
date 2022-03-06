<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */
/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;
//读取开票主体
function finance_invoice_subject()
{
    $subject = Db::name('InvoiceSubject')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $subject;
}

//读取报销类型
function finance_expense_cate()
{
    $cate = Db::name('ExpenseCate')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $cate;
}