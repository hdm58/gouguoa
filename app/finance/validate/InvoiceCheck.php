<?php
namespace app\finance\validate;
use think\Validate;

class InvoiceCheck extends Validate
{
    protected $rule = [
        'amount'           => 'require|float',
        'invoice_type'     => 'require',
        'invoice_subject'  => 'require',
        'id'               => 'require'
    ];

    protected $message = [
        'amount.require'          => '开票金额不能为空',
        'amount.number'           => '开票金额只能为数字',
        'id.require'              => '缺少更新条件',
        'invoice_type.require'    => '请选择开票类型',
        'invoice_subject.require' => '请选择开票主体',
    ];

    protected $scene = [
        'add'       => ['amount','invoice_type','invoice_subject'],
        'edit'      => ['id', 'amount','invoice_type','invoice_subject']
    ];
}