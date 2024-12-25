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

namespace app\oa\model;
use think\Model;
use think\facade\Db;
class Schedule extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[], $where=[], $whereOr=[])
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'a.end_time desc' : $param['order'];
        try {
            $list = self::field('a.*,u.name,w.title as work_cate')
				->alias('a')
				->join('Admin u', 'a.admin_id = u.id', 'LEFT')
				->join('WorkCate w', 'w.id = a.cid', 'LEFT')
				->where($where)
				->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
				->order($order)
                ->paginate(['list_rows'=> $rows])
                ->each(function ($item, $key) {
					$item->labor_type_string = '案头工作';
					if($item->labor_type == 2){
						$item->labor_type_string = '外勤工作';
					}
					if($item->did > 0){
						$item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
					}
					else{
						$item->department='-';
					}
					if($item->tid > 0){
						$task = Db::name('ProjectTask')->where(['id' => $item->tid])->find();
						$item->task = $task['title'];
						$item->project = Db::name('Project')->where(['id' => $task['project_id']])->value('name');
					}
					$item->start_time_a = empty($item->start_time) ? '' : date('Y-m-d', $item->start_time);
					$item->start_time_b = empty($item->start_time) ? '' : date('H:i', $item->start_time);
					$item->end_time_a = empty($item->end_time) ? '' : date('Y-m-d', $item->end_time);
					$item->end_time_b = empty($item->end_time) ? '' : date('H:i', $item->end_time);
                    $item->start_time = empty($item->start_time) ? '' : date('Y-m-d H:i', $item->start_time);
                    $item->end_time = empty($item->end_time) ? '' : date('H:i', $item->end_time);
                });
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
}