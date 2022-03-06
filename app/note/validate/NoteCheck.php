<?php
namespace app\note\validate;
use think\Validate;

class NoteCheck extends Validate
{
    protected $rule = [
        'title'       => 'require|unique:note',
        'id'          => 'require',
        'status'      => 'require'
    ];

    protected $message = [
        'title.require'           => '标题不能为空',
        'title.unique'            => '同样的记录已经存在!',
        'id.require'              => '缺少更新条件',
        'status.require'          => '状态为必选',
    ];

    protected $scene = [
        'add'       => ['title'],
        'edit'      => ['id', 'title']
    ];
}