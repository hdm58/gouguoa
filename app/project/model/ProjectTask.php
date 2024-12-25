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

namespace app\project\model;

use think\facade\Db;
use think\Model;

class ProjectTask extends Model
{	
    //列表
    function datalist($param=[],$where=[],$whereOr=[]) {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'status asc,id desc' : $param['order'];
        try {
            $list = self::where($where)
			->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
			->withoutField('content')
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item['director_name'] = '-';
				if ($item['director_uid'] > 0) {
					$item['director_name'] = Db::name('Admin')->where(['id' => $item['director_uid']])->value('name');
				}
				$assist_admin_names = Db::name('Admin')->where([['id', 'in', $item['assist_admin_ids']]])->column('name');
				if (empty($assist_admin_names)) {
                    $item['assist_admin_names'] = '-';
                } else {
                    $item['assist_admin_names'] = implode(',', $assist_admin_names);
                }
				$item['cate_name'] = Db::name('WorkCate')->where(['id' => $item['work_id']])->value('title');
				if ($item['project_id'] == 0) {
					$item['project_name'] = '-';
				} else {
					$item['project_name'] = Db::name('Project')->where(['id' => $item['project_id']])->value('name');
				}
				$item['delay'] = 0;
				if ($item['end_time'] > 0) {
					$item['end_time'] = date('Y-m-d', $item['end_time']);
					if ($item['over_time'] > 0 && $item['status'] < 4) {
						$item['delay'] = count_days($item['end_time'], date('Y-m-d', $item['over_time']));
					}
					if ($item['over_time'] == 0 && $item['status'] < 4) {
						$item['delay'] = count_days($item['end_time']);
					}
				}
				else{
					$item['delay'] = 9999;
					$item['end_time'] = '-';
				}
				$item['priority_name'] = priority_name($item['priority']);
				$item['status_name'] = status_task_name($item['status']);
				return $item;
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
    //详情
    public function detail($id)
    {
        $detail = Db::name('ProjectTask')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['project_name'] = '';
            if ($detail['project_id'] > 0) {
                $detail['project_name'] = Db::name('Project')->where(['id' => $detail['project_id']])->value('name');
            }
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['work_hours'] = Db::name('Schedule')->where(['delete_time' => 0, 'tid' => $detail['id']])->sum('labor_time');
            $detail['cate_name'] = Db::name('WorkCate')->where(['id' => $detail['work_id']])->value('title');
			
			$detail['director_name']= '';
			if($detail['director_uid'] > 0){
				$detail['director_name'] = Db::name('Admin')->where(['id' => $detail['director_uid']])->value('name');
			}
            $detail['logs'] = Db::name('EditLog')->where(['name' => 'Task', 'action_id' => $detail['id']])->count();
			$detail['comments'] = Db::name('Comment')->where(['module' => 'task', 'delete_time' => 0, 'topic_id' => $detail['id']])->count();
            $detail['assist_admin_names'] = '';
            if (!empty($detail['assist_admin_ids'])) {
                $assist_admin_names = Db::name('Admin')->where('id', 'in', $detail['assist_admin_ids'])->column('name');
                $detail['assist_admin_names'] = implode(',', $assist_admin_names);
            }
            $detail['priority_name'] = priority_name($detail['priority']);
			$detail['status_name'] = status_task_name($detail['status']);
            $detail['times'] = time_trans($detail['create_time']);
			$detail['delay'] = 0;
			if($detail['end_time']>0){
				$detail['end_time'] = date('Y-m-d', $detail['end_time']);
				if ($detail['over_time'] > 0 && $detail['status'] < 4) {
					$detail['delay'] = count_days($detail['end_time'], date('Y-m-d', $detail['over_time']));
				}
				if ($detail['over_time'] == 0 && $detail['status'] < 4) {
					$detail['delay'] = count_days($detail['end_time']);
				}
			}
			else{
				$detail['end_time'] = '';
			}
        }
        return $detail;
    }
}
