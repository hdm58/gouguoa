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

//获取项目重要性
function get_importance($id=0)
{
	$array = ['未设置','一般','重要','非常重要'];
	if($id==0){
		return $array;
	}
	else{
		$news_array=[];
		foreach($array as $key => $value){
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

//根据项目重要性名称
function importance_name($importance=0)
{
	$array = get_importance();
	return $array[$importance];
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

//设置项目成员
function set_project_uids($project_id)
{
    $project = Db::name('Project')->find($project_id);
    $uid_array = Db::name('ProjectUser')->where([['project_id','=',$project_id],['uid','>',0],['delete_time','=',0]])->column('uid');
	$uid_array[] = $project['admin_id'];
	$uid_array[] = $project['director_uid'];
    $step_uid_array = Db::name('ProjectStep')->where(['project_id'=>$project_id,'delete_time'=>0])->column('director_uid');
    $step_uids_array = Db::name('ProjectStep')->where(['project_id'=>$project_id,'delete_time'=>0])->column('uids');
	$result = array_merge($uid_array, $step_uid_array,$step_uids_array);
	$str = implode(',' , $result);
	$new_array = explode(',', $str);
	$uniqueArray = array_unique($new_array);
	$str = implode(',' , $uniqueArray);
    Db::name('Project')->where(['id'=>$project_id])->update(['uids'=>$str]);
	return $str;
}