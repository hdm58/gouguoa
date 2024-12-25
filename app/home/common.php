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

use think\facade\Db;

//获取服务器信息
function get_system_info($key)
{
    $system = [
        'os' => PHP_OS,
        'php' => PHP_VERSION,
        'upload_max_filesize' => get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "不允许上传附件",
        'max_execution_time' => get_cfg_var("max_execution_time") . "秒 ",
    ];
    if (empty($key)) {
        return $system;
    } else {
        return $system[$key];
    }
}

//假期类型
function get_leaves_types($id=0)
{
	$types_array = ['未设置','事假','年假','调休假','病假','婚假','丧假','产假','陪产假','其他'];
	if($id==0){
		return $types_array;
	}
	else{
		$news_array=[];
		foreach($types_array as $key => $value){
			if($key>0){
				$news_array[]=array(
					'id'=>$key,
					'title'=>$value,
				);
			}
		}
		return $news_array;
	}
}

//根据假期类型读取名称
function leaves_types_name($types=0)
{
	$types_array = get_leaves_types();
	return $types_array[$types];
}

//读取后台菜单列表
function admin_menu()
{
    $menu = Db::name('AdminRule')->where(['menu' => 1,'status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $menu;
}

//读取权限节点列表
function admin_rule()
{
    $rule = Db::name('AdminRule')->where(['status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $rule;
}

//读取权限分组列表
function admin_group()
{
    $group = Db::name('AdminGroup')->order('id desc')->select()->toArray();
    return $group;
}

//读取指定权限分组菜单详情
function admin_group_info($id)
{
    $rule = Db::name('AdminGroup')->where(['id' => $id])->value('rules');
	$rules = explode(',', $rule);
    return $rules;
}

//读取模块列表
function admin_module()
{
    $group = Db::name('AdminModule')->order('id asc')->select()->toArray();
    return $group;
}
