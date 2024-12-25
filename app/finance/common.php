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

//是否是报销打款管理员,count>1即有权限
function isAuthExpense($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_1)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//是否是发票管理员,count>1即有权限
function isAuthInvoice($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_2)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//是否是到账管理员,count>1即有权限
function isAuthIncome($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_3)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//是否是付款管理员,count>1即有权限
function isAuthPayment($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_4)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}