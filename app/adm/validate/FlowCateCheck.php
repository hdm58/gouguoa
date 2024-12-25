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

namespace app\adm\validate;

use think\Validate;
use think\facade\Db;

class FlowCateCheck extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$table_array = ['invoice','ticket','approve'];
		if(in_array($data['check_table'],$table_array)){
			return true;
		}
		else{
			$count = Db::name('FlowCate')->where([['check_table','=',$data['check_table']],['id','<>',$id]])->count();
			return $count == 0 ? true : false;
		}
	}
	
    protected $rule = [
        'title' => 'require',
        'name' => 'require|alphaDash|unique:flow_cate',
        'check_table' => 'require|alphaDash|checkOne',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '审批类型名称不能为空',
        'name.require' => '审批类型标识不能为空',
        'name.unique' => '同样的审批类型标识已经存在',
		'name.alphaDash' => '审批类型标识只能是小写字母',
        'check_table.require' => '数据表不能为空',
        'check_table.alphaDash' => '数据表只能是小写字母、数字下划线_',
        'check_table.checkOne' => '同样的数据表已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title','check_table'],
        'edit' => ['id', 'title','check_table'],
    ];
}
