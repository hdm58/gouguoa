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
namespace app\Project\model;
use think\facade\Db;
use think\Model;

class ProjectDocument extends Model
{
    public function datalist($param,$where,$whereOr)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->order($order)
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key){
					$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
					if ($item->project_id > 0) {
						$item->project = Db::name('Project')->where(['id' => $item->project_id])->value('name');
					}
					else{						
						$item->project = '-';
					}
				});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
	}	
    //详情
    public function detail($id)
    {
        $detail = Db::name('ProjectDocument')->where(['id' => $id])->find();
        if (!empty($detail)) {
			$detail['project_name'] = '-';
            if ($detail['project_id'] > 0) {
                $detail['project_name'] = Db::name('Project')->where(['id' => $detail['project_id']])->value('name');
            }
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['times'] = time_trans($detail['create_time']);
            $detail['create_time'] = date('Y-m-d H:i:s', $detail['create_time']);
        }
        return $detail;
    }
}
