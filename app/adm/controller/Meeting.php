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

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\model\MeetingRecords as MeetingRecordsModel;
use app\adm\model\MeetingOrder;
use schedule\Schedule as ScheduleIndex;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Meeting extends BaseController
{	
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new MeetingRecordsModel();
    }
	
	
    /**
    * 预定数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
			$model = new MeetingOrder();
            $list = $model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
    /**
    * 添加/编辑预定
    */
    public function add()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$param['start_date'] = strtotime($param['start_date']);
			$param['end_date'] = strtotime($param['end_date']);
			if ($param['end_date'] <= $param['start_date']) {
                return to_assign(1, "结束时间需要大于开始时间");
            }
            $where1[] = ['delete_time', '=', 0];
            $where1[] = ['room_id', '=', $param['room_id']];
            $where1[] = ['start_date', 'between', [$param['start_date'], $param['end_date'] - 1]];

            $where2[] = ['delete_time', '=', 0];
            $where2[] = ['room_id', '=', $param['room_id']];
            $where2[] = ['start_date', '<=', $param['start_date']];
            $where2[] = ['start_date', '>=', $param['end_date']];

            $where3[] = ['delete_time', '=', 0];
            $where3[] = ['room_id', '=', $param['room_id']];
            $where3[] = ['end_date', 'between', [$param['start_date'] + 1, $param['end_date']]];

            $record = Db::name('MeetingOrder')
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
                return to_assign(1, "您所选的时间区间已有预定记录，请重新选时间");
            }
			$requirementData = isset($param['requirement']) ? $param['requirement'] : '';
            $param['requirements'] = implode(',', $requirementData);
            if (!empty($param['id']) && $param['id'] > 0) {
				$model = new MeetingOrder();
				$model->edit($param);
            } else {
				$param['admin_id'] = $this->uid;
				$param['did'] = $this->did;
				$model = new MeetingOrder();
                $model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$requirements = get_base_type_data('BasicAdm',2);
			if ($id>0) {
				$model = new MeetingOrder();
				$detail = $model->getById($id);
				$requirements_array = explode(',', $detail['requirements']);
				foreach ($requirements as &$val) {
					if (in_array($val['id'], $requirements_array)) {
						$val['checked'] = 1;
					} else {
						$val['checked'] = 0;
					}
				}
				View::assign('requirements', $requirements);
				View::assign('detail', $detail);
			}
			View::assign('requirements', $requirements);
			if(is_mobile()){
				return view('qiye@/approve/add_meeting');
			}			
			return view();
		}
    }
	
    /**
    * 查看预定
    */
    public function view($id)
    {
		$model = new MeetingOrder();
		$detail = $model->getById($id);
		View::assign('create_user', get_admin($detail['admin_id']));
		if (!empty($detail)) {
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/approve/view_meeting');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到页面']);
		}
    }
	
   /**
    * 删除预定
    */
    public function del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$model = new MeetingOrder();
			$model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   
	
	
	
	//会议室
    public function room()
    {
        if (request()->isAjax()) {
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('MeetingRoom')
				->field('mr.*,u.name as keep_name')
				->alias('mr')
				->join('Admin u', 'u.id = mr.keep_uid', 'left')
				->paginate(['list_rows'=> $rows]);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
    //会议室添加
    public function room_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('MeetingRoom')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('MeetingRoom')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
		} else {
			//keep_name
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = Db::name('MeetingRoom')->find($id);
				if($detail['keep_uid']>0){
					$detail['keep_name'] = Db::name('Admin')->where('id','=',$detail['keep_uid'])->value('name');
				}
                View::assign('detail', $detail);
				return view('room_edit');
			}
			return view();
        }
    }
	
    //会议室查看
    public function room_view()
    {
		$param = get_params();
		$detail = Db::name('MeetingRoom')->find($param['id']);
		if($detail['keep_uid']>0){
			$detail['keep_name'] = Db::name('Admin')->where('id','=',$detail['keep_uid'])->value('name');
		}
		View::assign('detail', $detail);
		return view();
    }
	
	//会议室查看
    public function room_use()
    {
		if (request()->isAjax()) {
            $param = get_params();
            $where = [];
			
			$where[] = ['start_date','<=',strtotime($param['end'])];
            $where[] = ['end_date','>=',strtotime($param['start'])];
            $where[] = ['delete_time', '=', 0];
            $where[] = ['check_status', '=', 2];
            $where[] = ['room_id', '=', $param['id']];

            $schedule = Db::name('MeetingOrder')
            ->where($where)
            ->field('id,title,start_date,end_date')
            ->select()->toArray();
            $events = [];
            $bg_array=['#ECECEC','#FFD3D3','#F6F6C7','#D7EBFF','#CCEBCC','#E9E9CB'];
            $border_array=['#CCCCCC','#FF9999','#E8E89B','#99CCFF','#99CC99','#CCCC99'];
            foreach ($schedule as $k => $v) {
                $v['backgroundColor'] = $bg_array[3];
                $v['borderColor'] = $border_array[3];
                $v['title'] = $v['title'];
                $v['start'] = date('Y-m-d H:i', $v['start_date']);
                $v['end'] = date('Y-m-d H:i', $v['end_date']);
                unset($v['start_date']);
                unset($v['end_date']);
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
			$param = get_params();
			$detail = Db::name('MeetingRoom')->find($param['id']);
			View::assign('detail', $detail);
			return view();
		}
    } 
	
    //会议室设置
    public function room_check()
    {
		$param = get_params();
        $res = Db::name('MeetingRoom')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }  
	
	//会议纪要
    public function records()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where=[];
			$whereOr = [];
			$uid = $this->uid;
            if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['anchor_id'])) {
                $where[] = ['anchor_id', '=', $param['anchor_id']];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['meeting_date', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
            }
            $where[] = ['delete_time', '=', 0];
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加会议纪要
    public function records_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['meeting_date'] = isset($param['meeting_date']) ? strtotime(urldecode($param['meeting_date'])) : 0;
            if (!empty($param['id']) && $param['id'] > 0) {
               $this->model->edit($param);
            } else {
                $param['admin_id'] = $this->uid;
                $this->model->add($param);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                View::assign('detail', $this->model->getById($id));
				return view('records_edit');
            }
            return view();
        }
    }

    //查看会议纪要
    public function records_view($id)
    {
		View::assign('detail', $this->model->getById($id));
        return view();
    }

    //删除会议纪要
    public function records_del()
    {
		if (request()->isDelete()) {
			$id = get_params("id");
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }
}
