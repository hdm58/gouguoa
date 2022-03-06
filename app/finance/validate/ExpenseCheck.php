<?php
namespace app\finance\validate;
use think\Validate;

class ExpenseCheck extends Validate
{
    protected $rule = [
        'code'       => 'require',
        'id'          => 'require',
        'status'      => 'require'
    ];

    protected $message = [
        'code.require'           => '报销凭证编号不能为空',
        'id.require'              => '缺少更新条件',
        'status.require'          => '状态为必选',
    ];

    protected $scene = [
        'add'       => ['code'],
        'edit'      => ['id', 'code']
    ];
}