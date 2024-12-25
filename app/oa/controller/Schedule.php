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

declare (strict_types = 1);

namespace app\oa\controller;

use app\base\BaseController;
use app\oa\model\Schedule as ScheduleModel;
use schedule\Schedule as ScheduleIndex;
use think\facade\Db;
use think\facade\View;

class Schedule extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ScheduleModel();
    }
	
    function datalist() {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];			
            $whereOr = [];	
			$uid= $this->uid;			
			if (!empty($param['keywords'])) {
				$where[] = ['a.title', 'like', '%' . trim($param['keywords']) . '%'];
			}
			if (!empty($param['labor_type'])) {
				$where[] = ['a.labor_type', '=',$param['labor_type']];
			}
			if (!empty($param['cid'])) {
				$where[] = ['a.cid', '=',$param['cid']];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['a.start_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            $where[] = ['a.delete_time', '=', 0];
			if (!empty($param['uid'])) {
				$where[] = ['a.admin_id', '=', $param['uid']];
			} else {
				$whereOr[] = ['a.admin_id', '=', $uid];
				$dids_a = get_leader_departments($uid);	
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['a.did','in',$dids];
				}
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
			View::assign('is_leader', isLeader($this->uid));
			View::assign('role_auth', isAuth($this->uid,'office_admin','conf_1'));
			View::assign('hour_auth', valueAuth('office_admin','conf_2'));
            return view();
        }
    }

    //工作记录
    public function calendar()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $uid = $this->uid;
            if (!empty($param['uid'])) {
                $uid = $param['uid'];
            }
            $where = [];
            $where[] = ['start_time', '>=', strtotime($param['start'])];
            $where[] = ['end_time', '<=', strtotime($param['end'])];
            $where[] = ['admin_id', '=', $uid];
            $where[] = ['delete_time', '=', 0];
            $schedule = Db::name('Schedule')->where($where)->field('id,title,labor_time,start_time,end_time')->select()->toArray();
            $events = [];
            $countEvents = [];
            foreach ($schedule as $k => $v) {
                $v['backgroundColor'] = '#12bb37';
                $v['borderColor'] = '#12bb37';
                $v['title'] = '[' . $v['labor_time'] . '工时] ' . $v['title'];
                $v['start'] = date('Y-m-d H:i', $v['start_time']);
                $v['end'] = date('Y-m-d H:i', $v['end_time']);
                $temData = date('Y-m-d', $v['start_time']);
                if (array_key_exists($temData, $countEvents)) {
                    $countEvents[$temData]['times'] += $v['labor_time'];
                } else {
                    $countEvents[$temData]['times'] = $v['labor_time'];
                    $countEvents[$temData]['start'] = date('Y-m-d', $v['start_time']);
                }
                unset($v['start_time']);
                unset($v['end_time']);
                $events[] = $v;
            }
            foreach ($countEvents as $kk => $vv) {
                $vv['backgroundColor'] = '#eeeeee';
                $vv['borderColor'] = '#eeeeee';
                $vv['title'] = '【当天总工时：' . $vv['times'] . '】';
                $vv['end'] = $vv['start'];
                $vv['id'] = 0;
                unset($vv['times']);
                $events[] = $vv;
            }
            $input_arrays = $events;
            $range_start = parseDateTime($param['start']);
            $range_end = parseDateTime($param['end']);
            $timeZone = null;
            if (isset($_GET['timeZone'])) {
                $timeZone = new DateTimeZone($_GET['timeZone']);
            }

            // Accumulate an output array of event data arrays.
            $output_arrays = array();
            foreach ($input_arrays as $array) {
                // Convert the input array into a useful Event object
                $event = new ScheduleIndex($array, $timeZone);
                // If the event is in-bounds, add it to the output
                if ($event->isWithinDayRange($range_start, $range_end)) {
                    $output_arrays[] = $event->toArray();
                }
            }
            return json($output_arrays);
        } else {
			View::assign('is_leader', isLeader($this->uid));
            return view();
        }
    }

    //保存日志数据
    public function add()
    {
        $param = get_params();
        $admin_id = $this->uid;
        if ($param['id'] == 0) {
			if (isset($param['start_time'])) {
                $param['start_time'] = strtotime($param['start_time']);
            }
			if (isset($param['end_time'])) {
                $param['end_time'] = strtotime($param['end_time']);
            }
            if (isset($param['end_time_a'])) {
                $param['end_time'] = strtotime($param['end_time_a'] . '' . $param['end_time_b']);
            }
            if (isset($param['start_time_a'])) {
                $param['start_time'] = strtotime($param['start_time_a'] . '' . $param['start_time_b']);
            }
            if (isset($param['end_time_a'])) {
                $param['end_time'] = strtotime($param['end_time_a'] . '' . $param['end_time_b']);
            }
			if($param['start_time']>time()){
				return to_assign(1, "开始时间不能大于现在时间");			
			}
            if ($param['end_time'] <= $param['start_time']) {
                return to_assign(1, "结束时间需要大于开始时间");
            }
			if (date('d',$param['end_time']) != date('d',$param['start_time'])) {
                return to_assign(1, "结束时间与开始时间必须是同一天");
            }
            $where1[] = ['delete_time', '=', 0];
            $where1[] = ['admin_id', '=', $admin_id];
            $where1[] = ['start_time', 'between', [$param['start_time'], $param['end_time'] - 1]];

            $where2[] = ['delete_time', '=', 0];
            $where2[] = ['admin_id', '=', $admin_id];
            $where2[] = ['start_time', '<=', $param['start_time']];
            $where2[] = ['start_time', '>=', $param['end_time']];

            $where3[] = ['delete_time', '=', 0];
            $where3[] = ['admin_id', '=', $admin_id];
            $where3[] = ['end_time', 'between', [$param['start_time'] + 1, $param['end_time']]];

            $record = Db::name('Schedule')
                ->where(function ($query) use ($where1) {
                    $query->where($where1);
                })
                ->whereOr(function ($query) use ($where2) {
                    $query->where($where2);
                })
                ->whereOr(function ($query) use ($where3) {
                    $query->where($where3);
                })
                ->count();
            if ($record > 0) {
                return to_assign(1, "您所选的时间区间已有工作记录，请重新选时间");
            }
            $param['labor_time'] = ($param['end_time'] - $param['start_time']) / 3600;
            $param['admin_id'] = $admin_id;
            $param['did'] = get_admin($admin_id)['did'];
            $param['create_time'] = time();
            $sid = Db::name('Schedule')->strict(false)->field(true)->insertGetId($param);
            if ($sid > 0) {
                add_log('add', $sid, $param);
                return to_assign(0, '操作成功');
            } else {
                return to_assign(0, '操作失败');
            }
        } else {
            $param['update_time'] = time();
            $res = Db::name('Schedule')->strict(false)->field(true)->update($param);
            if ($res !== false) {
                add_log('edit', $param['id'], $param);
                return to_assign(0, '操作成功');
            } else {
                return to_assign(0, '操作失败');
            }
        }
    }

    //删除工作记录
    public function del()
    {
        $id = get_params("id");
        $data['id'] = $id;
        $data['delete_time'] = time();
        if (Db::name('schedule')->update($data) !== false) {
            add_log('delete', $data['id'], $data);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }

    public function view()
    {
        $id = get_params('id');
        $schedule = Db::name('Schedule')->where(['id' => $id])->find();
        if (!empty($schedule)) {
            $schedule['start_time_1'] = date('H:i', $schedule['start_time']);
            $schedule['end_time_1'] = date('H:i', $schedule['end_time']);
            $schedule['start_time'] = date('Y-m-d', $schedule['start_time']);
            $schedule['end_time'] = date('Y-m-d', $schedule['end_time']);
            $schedule['create_time'] = date('Y-m-d H:i:s', $schedule['create_time']);
            $schedule['name'] = Db::name('Admin')->where(['id' => $schedule['admin_id']])->value('name');
			$schedule['labor_type_string'] = '案头工作';
			if($schedule['labor_type'] == 2){
				$schedule['labor_type_string'] = '外勤工作';
			}
			$schedule['department'] = get_admin($schedule['admin_id'])['department'];
			$schedule['work_cate'] = Db::name('WorkCate')->where(['id' => $schedule['cid']])->value('title');
			if($schedule['tid']>0){
				$task = Db::name('ProjectTask')->where(['id' => $schedule['tid']])->find();
				$schedule['task'] = $task['title'];
				$schedule['project'] = Db::name('Project')->where(['id' => $task['project_id']])->value('name');
			}
        }
        if (request()->isAjax()) {
            return to_assign(0, "", $schedule);
        } else {			
			View::assign('detail',$schedule);
			if(is_mobile()){
				return view('qiye@/index/schedule_view');
			}
            return $schedule;
        }
    }

}
