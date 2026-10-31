<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
 
declare (strict_types = 1);

namespace app\analysis\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Project extends BaseController
{		
	/**
    * 项目数据分析
    */
    public function datalist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$year = isset($param['year']) ? $param['year'] : date('Y');
			$types = isset($param['types']) ? $param['types'] : 0;
			$year_start=strtotime("{$year}-01-01");
			$year_end=strtotime("{$year}-12-31 23:59:59");
			if($types==0){
				$projects = Db::name('Project')->where([['create_time','between',[$year_start,$year_end]],['delete_time','=',0]])->count();
				$projects_a = Db::name('Project')->where([['create_time','between',[$year_start,$year_end]],['delete_time','=',0],['status','=',3]])->count();
				$projects_b = '0.00';
				if($projects>0){
					$b = $projects_a*100 / $projects;
					$projects_b = number_format($b, 2);
				}

				$tasks = Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['delete_time','=',0]])->count();
				$tasks_a = Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['delete_time','=',0],['status','=',3]])->count();
			
				$delay_a = Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['status','=',3],['over_time', '>', Db::raw('end_time')]])->count();
				$delay_b = Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['status','in',[1,2]],['end_time', '<', time()]])->count();
				$delay = $delay_a+$delay_b;
				$normal = $tasks - $delay;
				
				$tasks_b = '0.00';
				$tasks_c = '0.00';
				$tasks_d = '0.00';
				if($tasks>0){
					$b = $tasks_a*100 / $tasks;
					$c = $delay*100 / $tasks;
					$d = $normal*100 / $tasks;
					$tasks_b = number_format($b, 2);
					$tasks_c = number_format($c, 2);
					$tasks_d = number_format($d, 2);
				}
				
				$result=[
					'projects'=>$projects,
					'projects_a'=>$projects_a,
					'projects_b'=>$projects_b,
					'tasks'=>$tasks,
					'tasks_a'=>$tasks_a,
					'tasks_b'=>$tasks_b,
					'tasks_c'=>$tasks_c,
					'tasks_d'=>$tasks_d,
					'delay'=>$delay,
					'normal'=>$normal
				];
				return to_assign(0, '', $result);
			}
			if($types==1){
				// 初始化月份列表
				$months = [];
				$tasks = [];
				for ($i = 1; $i <= 12; $i++) {
					$monthStr = sprintf("%02d", $i);
					$months[] = $i . "月";
					$month_start=strtotime("{$year}-{$monthStr}-01");
					$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
					$task = Db::name('ProjectTask')->where([['create_time','between',[$month_start,$month_end]],['delete_time','=',0]])->count();
					$tasks[] = $task;
				}
				$result = [
					'title' => [
						'text' => '每月任务分析'
					],
					'legend' => [
						'任务数'
					],
					'xaxis' => $months,
					'yaxis' => [
						[
							'type' => 'value',
							'axisLabel' => [
								'formatter' => '{value}'
							]
						]
					],
					'series' => [
						[
							'name' => '任务数量',
							'type' => 'bar',
							'barWidth'=>'50%',
							'data' => $tasks
						]
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==2){
				// 初始化月份列表
				$months = [];
				$hours = [];
				for ($i = 1; $i <= 12; $i++) {
					$monthStr = sprintf("%02d", $i);
					$months[] = $i . "月";
					$month_start=strtotime("{$year}-{$monthStr}-01");
					$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
					$hour = Db::name('Schedule')->where([['start_time','between',[$month_start,$month_end]],['delete_time','=',0]])->sum('labor_time');
					$hours[] = $hour;
				}
				$result = [
					'title' => [
						'text' => '每月工时分析'
					],
					'legend' => [
						'工时数'
					],
					'xaxis' => $months,
					'yaxis' => [
						[
							'type' => 'value',
							'axisLabel' => [
								'formatter' => '{value} 小时'
							]
						]
					],
					'series' => [
						[
							'name' => '工时',
							'type' => 'bar',
							'barWidth'=>'60%',
							'data' => $hours
						]
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==3){
				$data = [];
				$cates = get_base_data('WorkCate');
				foreach ($cates as $cate) {
					$data[] = [
						'name' => $cate['title'],
						'value' => Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['work_id','=',$cate['id']],['delete_time','=',0]])->count()
					];
				}

				$result = [
					'title' => [
						'text' => '任务工作类型'
					],
					'series' => [
						'data' => $data
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==4){
				$data = [];
				$priority_array = ['低','中','高','紧急'];
				foreach ($priority_array as $key => $value) {
					$priority = $key+1;
					$data[] = [
						'name' => $value,
						'value' => Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['priority','=',$priority],['delete_time','=',0]])->count()
					];
				}

				$result = [
					'title' => [
						'text' => '任务优先级'
					],
					'series' => [
						'data' => $data
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==5){
				$data = [];
				$status_array = ['未开始','进行中','已完成','已拒绝','已关闭'];
				foreach ($status_array as $key => $value) {
					$status = $key+1;
					$data[] = [
						'name' => $value,
						'value' => Db::name('ProjectTask')->where([['create_time','between',[$year_start,$year_end]],['status','=',$status],['delete_time','=',0]])->count()
					];
				}

				$result = [
					'title' => [
						'text' => '任务状态'
					],
					'series' => [
						'data' => $data
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==6){
				$data = [];
				$cates = get_base_data('WorkCate');
				foreach ($cates as $cate) {
					$data[] = [
						'name' => $cate['title'],
						'value' => Db::name('Schedule')->where([['start_time','between',[$year_start,$year_end]],['cid','=',$cate['id']],['delete_time','=',0]])->sum('labor_time')
					];
				}

				$result = [
					'title' => [
						'text' => '工作日志类型'
					],
					'series' => [
						'data' => $data
					]
				];
				return to_assign(0, '', $result);
			}
        }else{	
			//项目总数
			$project_count = Db::name('Project')->where(['delete_time'=>0])->count();
			$project_count_1 = Db::name('Project')->where(['delete_time'=>0,'status'=>1])->count();
			$project_count_2 = Db::name('Project')->where(['delete_time'=>0,'status'=>2])->count();
			$project_count_3 = Db::name('Project')->where(['delete_time'=>0,'status'=>3])->count();
			$project_count_4 = Db::name('Project')->where(['delete_time'=>0,'status'=>4])->count();
			
			//任务总数
			$task_count = Db::name('ProjectTask')->where(['delete_time'=>0])->count();
			$task_count_1 = Db::name('ProjectTask')->where(['delete_time'=>0,'status'=>1])->count();
			$task_count_2 = Db::name('ProjectTask')->where(['delete_time'=>0,'status'=>2])->count();
			$task_count_3 = Db::name('ProjectTask')->where(['delete_time'=>0,'status'=>3])->count();
			$task_count_4 = Db::name('ProjectTask')->where(['delete_time'=>0,'status'=>4])->count();
			
			//工时总数
			$schedule_count = Db::name('Schedule')->where(['delete_time'=>0])->count();
			$schedule_count_1 = Db::name('Schedule')->where(['delete_time'=>0,'labor_type'=>1])->count();
			$schedule_count_2 = Db::name('Schedule')->where(['delete_time'=>0,'labor_type'=>2])->count();
			
			$schedule_hour = Db::name('Schedule')->where(['delete_time'=>0])->sum('labor_time');
			$schedule_hour_1 = Db::name('Schedule')->where(['delete_time'=>0,'labor_type'=>1])->sum('labor_time');
			$schedule_hour_2 = Db::name('Schedule')->where(['delete_time'=>0,'labor_type'=>2])->sum('labor_time');
			
			View::assign('project_count', $project_count);
			View::assign('project_count_1', $project_count_1);
			View::assign('project_count_2', $project_count_2);
			View::assign('project_count_3', $project_count_3);
			View::assign('project_count_4', $project_count_4);
			View::assign('task_count', $task_count);
			View::assign('task_count_1', $task_count_1);
			View::assign('task_count_2', $task_count_2);
			View::assign('task_count_3', $task_count_3);
			View::assign('task_count_4', $task_count_4);
			View::assign('schedule_count', $schedule_count);
			View::assign('schedule_count_1', $schedule_count_1);
			View::assign('schedule_count_2', $schedule_count_2);
			View::assign('schedule_hour', $schedule_hour);
			View::assign('schedule_hour_1', $schedule_hour_1);
			View::assign('schedule_hour_2', $schedule_hour_2);
			return view();
		}
    }

}
