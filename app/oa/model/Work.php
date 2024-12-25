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

class Work extends Model
{
    //获取发送汇报列表
    public function get_send($param = [],$where = [])
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'create_time desc' : $param['order'];
			try {
				$list = self::where($where)
				->order($order)
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key) {
					if($item['start_date']>0){
						$item['start_date'] = date('Y-m-d',$item['start_date']);
					}
					if($item['end_date']>0){
						$item['end_date'] = date('Y-m-d',$item['end_date']);
					}
					$to_names = Db::name('Admin')->where('status', 1)->where('id', 'in', $item['to_uids'])->column('name');
					$item['to_names'] = implode(",", $to_names);
					$item['files'] = Db::name('File')->where('id', 'in', $item['file_ids'])->count();
				});
				return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
	//详情
	public function detail($id)
    {
		$detail = self::find($id);
		if($detail['types']>1){
			$detail['range_date'] = date('Y-m-d',$detail['start_date']).' ~ '.date('Y-m-d',$detail['end_date']);
		}
		else{
			$detail['range_date'] = date('Y-m-d',$detail['start_date']);
		}
		$to_unames = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['to_uids'])->column('name');
		$detail['to_unames'] = implode(",", $to_unames);
		$detail['file_array'] = Db::name('File')->where([['id','in',$detail['file_ids']]])->select()->toArray();
		return $detail;
    }
}
