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

/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;

//客户查看编辑数据权限判断
function customer_auth($uid,$customer_id)
{
	$customer =  Db::name('Customer')->where(['id' => $customer_id])->find();
	if($customer['belong_uid']==0){
		return $customer;
	}
	//是否是客户管理员
    $auth = isAuth($uid,'customer_admin','conf_1');
	$role= 0;
	if($auth==1){
		//可见部门数据
		$dids = get_role_departments($uid);
		if(in_array($customer['belong_did'],$dids)){
			$role= 1;
		}
	}
	else if($auth==0){
		$auth_array=[];
		if(!empty($customer['share_ids'])){
			$share_ids = explode(",",$customer['share_ids']);
			$auth_array = array_merge($auth_array,$share_ids);
		}	
		array_push($auth_array,$customer['belong_uid']);
		//部门负责人
		$dids = get_leader_departments($uid);
		if(in_array($uid,$auth_array) || in_array($customer['belong_did'],$dids)){
			$role= 1;
		}
	}
	if($role == 0){
		throw new \think\exception\HttpException(405, '无权限访问');
	}
	else{
		return $customer;
	}
}

//读取联系人
function customer_contact($cid)
{
    $contact = Db::name('CustomerContact')->where(['delete_time' => 0,'cid'=>$cid])->select()->toArray();
    return $contact;
}

//读取销售机会
function customer_chance($cid)
{
    $chance = Db::name('CustomerChance')->where(['delete_time' => 0,'cid'=>$cid])->select()->toArray();
    return $chance;
}