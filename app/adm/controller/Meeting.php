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
