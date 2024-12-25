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

//获取项目状态
function get_status($id=0)
{
	$status_array = ['未设置','未开始','进行中','已完成','已关闭'];
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


//获取任务全部状态
function get_task_status($id=0)
{
	$status_task_array = ['未设置','未开始','进行中','已完成','已拒绝','已关闭'];
	if($id==0){
		return $status_task_array;
	}
	else{
		$news_array=[];
		foreach($status_task_array as $key => $value){
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

//根据任务状态读取审批状态名称
function status_task_name($status=0)
{
	$status_task_array = get_task_status();
	return $status_task_array[$status];
}

//获取任务紧急程度
function get_priority($id=0)
{
	$priority_array = ['未设置','低','中','高','紧急'];
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

//根据任务紧急程度名称
function priority_name($priority=0)
{
	$priority_array = get_priority();
	return $priority_array[$priority];
}

//是否是项目管理员,count>1即有权限
function isAuthProject($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'project_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_1)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}
//读取项目
function get_project($uid = 0)
{
    $map = [];
    $map[] = ['delete_time', '=', 0];
    if ($uid > 0) {
        $project_ids = Db::name('ProjectUser')->where(['uid' => $uid, 'delete_time' => 0])->column('project_id');
        $map[] = ['id', 'in', $project_ids];
    }
    $project = Db::name('Project')->where($map)->select()->toArray();
    return $project;
}

//任务分配情况统计
function plan_count($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $planTime = date("Y-m-d", $value['end_time']);
        if (empty($documents[$planTime])) {
            $documents[$planTime] = 1;
        } else {
            $documents[$planTime] += 1;
        }
    }
    return $documents;
}

//工时登记情况统计
function hour_count($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $hourTime = date("Y-m-d", $value['start_time']);
        if (empty($documents[$hourTime])) {
            $documents[$hourTime] = $value['labor_time'] + 0;
        } else {
            $documents[$hourTime] += $value['labor_time'];
        }
        $documents[$hourTime] = round($documents[$hourTime], 2);
    }
    return $documents;
}

//燃尽图统计
function cross_count($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $planTime = date("Y-m-d", $value['end_time']);
        if (empty($documents[$planTime])) {
            $documents[$planTime] = 1;
        } else {
            $documents[$planTime] += 1;
        }
    }
    return $documents;
}