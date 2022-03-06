<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\oa\controller;

use app\base\BaseController;
use app\oa\model\Plan as PlanList;
use schedule\Schedule as ScheduleIndex;
use think\facade\Db;
use think\facade\View;

class Plan extends BaseController
{
    function index() {
        if (request()->isAjax()) {
            $param = get_params();
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime($param['start_time']) : 0;
            $end_time = isset($param['end_time']) ? strtotime($param['end_time']) : 0;
            $where = [];
            if ($start_time > 0 && $end_time > 0) {
                $where[] = ['a.start_time', 'between', [$start_time, $end_time]];
            }
            if (!empty($param['keywords'])) {
                $where[] = ['a.title', 'like', '%' . trim($param['keywords']) . '%'];
            }
            if (!empty($param['uid'])) {
                $where[] = ['a.admin_id', '=', $param['uid']];
            } else {
                $where[] = ['a.admin_id', '=', $this->uid];
            }
            $where[] = ['a.status', '=', 1];
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $plan = PlanList::where($where)
                ->field('a.*,u.name as create_admin')
                ->alias('a')
                ->join('admin u', 'u.id = a.admin_id', 'LEFT')
                ->order('a.id desc')
                ->paginate($rows, false)
                ->each(function ($item, $key) {
                    $item->remind_time = empty($item->remind_time) ? '-' : date('Y-m-d H:i', $item->remind_time);
                    $item->start_time = empty($item->start_time) ? '' : date('Y-m-d H:i', $item->start_time);
                    $item->end_time = empty($item->end_time) ? '': date('Y-m-d H:i', $item->end_time);
                    //$item->end_time = empty($item->end_time) ? '' : date('H:i', $item->end_time);
                });
            return table_assign(0, '', $plan);
        } else {
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
            $where1 = [];
            $where2 = [];

            $where1[] = ['status', '=', 1];
            $where1[] = ['admin_id', '=', $uid];
            $where1[] = ['start_time', '>=', strtotime($param['start'])];

            $where2[] = ['status', '=', 1];
            $where2[] = ['admin_id', '=', $uid];
            $where2[] = ['end_time', '<=', strtotime($param['end'])];

            $schedule = Db::name('Plan')
            ->where(function ($query) use ($where1) {
                $query->where($where1);
            })
            ->whereOr(function ($query) use ($where2) {
                $query->where($where2);
            })
            ->field('id,title,type,remind_type,start_time,end_time')
            ->select()->toArray();
            $events = [];
            $color_array=['#393D49','#FF5722','#FFB800','#1E9FFF','#12bb37'];
            foreach ($schedule as $k => $v) {
                $v['backgroundColor'] = $color_array[$v['type']];
                $v['borderColor'] = $color_array[$v['type']];
                $v['title'] = $v['title'];
                $v['start'] = date('Y-m-d H:i', $v['start_time']);
                $v['end'] = date('Y-m-d H:i', $v['end_time']);
                unset($v['start_time']);
                unset($v['end_time']);
                $events[] = $v;
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
            return view();
        }
    }

    //保存日志数据
    public function add()
    {
        $param = get_params();
        $admin_id = $this->uid;
        if (isset($param['start_time_a'])) {
            $param['start_time'] = strtotime($param['start_time_a'] . '' . $param['start_time_b']);
        }
        if (isset($param['end_time_a'])) {
            $param['end_time'] = strtotime($param['end_time_a'] . '' . $param['end_time_b']);
        }
        if ($param['end_time'] <= $param['start_time']) {
            return to_assign(1, "结束时间需要大于开始时间");
        }
		if ($param['start_time'] <= time()) {
            return to_assign(1, "开始时间需要大于当前时间");
        }
		if (isset($param['remind_type'])) {
			if($param['remind_type']==1){
				$param['remind_time'] = $param['start_time']-5*60;
			}
			if($param['remind_type']==2){
				$param['remind_time'] = $param['start_time']-15*60;
			}
			if($param['remind_type']==3){
				$param['remind_time'] = $param['start_time']-30*60;
			}
			if($param['remind_type']==4){
				$param['remind_time'] = $param['start_time']-60*60;
			}
			if($param['remind_type']==5){
				$param['remind_time'] = $param['start_time']-120*60;
			}
			if($param['remind_type']==6){
				$param['remind_time'] = $param['start_time']-1440*60;
			}
		}
        if ($param['id'] == 0) {
            $param['admin_id'] = $admin_id;
            $param['did'] = get_admin($admin_id)['did'];
            $param['create_time'] = time();
            $addid = Db::name('Plan')->strict(false)->field(true)->insertGetId($param);
            if ($addid > 0) {
                add_log('add', $addid, $param);
                return to_assign(0, '操作成功');
            } else {
                return to_assign(0, '操作失败');
            }
        } else {
            $param['update_time'] = time();
            $res = Db::name('Plan')->strict(false)->field(true)->update($param);
            if ($res !== false) {
                add_log('edit', $param['id'], $param);
                return to_assign(0, '操作成功');
            } else {
                return to_assign(0, '操作失败');
            }
        }
    }

    //删除工作记录
    public function delete()
    {
        $id = get_params("id");
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Plan')->update($data) !== false) {
            add_log('delete', $data['id'], $data);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }

    public function detail($id)
    {
        $id = get_params('id');
        $schedule = Db::name('Plan')->where(['id' => $id])->find();
        if (!empty($schedule)) {
            $schedule['remind_time'] = $schedule['remind_time'] == 0?'-':date('Y-m-d H:i', $schedule['remind_time']);
            $schedule['start_time_a'] = date('Y-m-d', $schedule['start_time']);
            $schedule['end_time_a'] = date('Y-m-d', $schedule['end_time']);
            $schedule['start_time_b'] = date('H:i', $schedule['start_time']);
            $schedule['end_time_b'] = date('H:i', $schedule['end_time']);
            $schedule['start_time'] = date('Y-m-d H:i', $schedule['start_time']);
            $schedule['end_time'] = date('Y-m-d H:i', $schedule['end_time']);
            $schedule['create_time'] = date('Y-m-d H:i:s', $schedule['create_time']);
            $schedule['user'] = Db::name('Admin')->where(['id' => $schedule['admin_id']])->value('name');
        }
        if (request()->isAjax()) {
            return to_assign(0, "", $schedule);
        } else {
            return $schedule;
        }
    }

    //读取日程弹层详情
    public function view()
    {
        $id = get_params('id');
        $schedule = $this->detail($id);
        if ($schedule) {
            View::assign('schedule', $schedule);
            return view();
        } else {
            echo '该日程安排不存在';
        }
    }

}
