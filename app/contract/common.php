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

//销售合同性质
function get_contract_types($check_status=0)
{
	$contract_types_array = [
		["id"=>1,"title"=>"普通合同"],
		["id"=>2,"title"=>"产品合同"],
		["id"=>3,"title"=>"服务合同"]
	];
	return $contract_types_array;
}

//根据销售合同性质读取销售合同性质名称
function contract_types_name($types=1)
{
	$contract_types_array = get_contract_types();
	return $contract_types_array[$types-1];
}

//采购合同性质
function get_purchase_types($check_status=0)
{
	$purchase_types_array = [
		["id"=>1,"title"=>"普通采购"],
		["id"=>2,"title"=>"物品采购"],
		["id"=>3,"title"=>"服务采购"]
	];
	return $purchase_types_array;
}

//根据采购合同性质读取采购合同性质名称
function purchase_types_name($types=1)
{
	$purchase_types_array = get_purchase_types();
	return $purchase_types_array[$types-1];
}