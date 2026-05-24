<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 勾股OA http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;
//获取全部状态
function get_status($id=0)
{
	$status_array = ['未设置','未开始','进行中','已分配','已解决','已关闭'];
	if($id==0){
		return $status_array;
	}
	else{
		$news_array=[];
		foreach($status_array as $key => $value){
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

//根据状态读取审批状态名称
function status_name($status=0)
{
	$status_array = get_status();
	return $status_array[$status];
}

//获取优先级
function get_priority($id=0)
{
	$priority_array = ['未设置','低','中','高'];
	if($id==0){
		return $priority_array;
	}
	else{
		$news_array=[];
		foreach($priority_array as $key => $value){
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

//根据优先级名称
function priority_name($priority=0)
{
	$priority_array = get_priority();
	return $priority_array[$priority];
}

//获取紧急程度
function get_emergent($id=0)
{
	$emergent_array = ['未设置','低','中','高'];
	if($id==0){
		return $emergent_array;
	}
	else{
		$news_array=[];
		foreach($emergent_array as $key => $value){
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

//根据程度名称
function emergent_name($emergent=0)
{
	$emergent_array = get_emergent();
	return $emergent_array[$emergent];
}

//获取全部状态
function get_solutions_status($id=0)
{
	$status_array = ['未设置','待批准','已批准','未批准'];
	if($id==0){
		return $status_array;
	}
	else{
		$news_array=[];
		foreach($status_array as $key => $value){
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

//根据状态读取审批状态名称
function solutions_status_name($status=0)
{
	$status_array = get_solutions_status();
	return $status_array[$status];
}