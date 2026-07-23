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

namespace app\oa\model;
use think\Model;
use think\facade\Db;
class Plan extends Model
{
	//参与人类型
	public static $Types = ['','普通','重要/紧急','重要/不紧急','不重要/紧急','不重要/不紧急'];
	public static $RemindType = ['','不提醒','提前5分钟','提前15分钟','提前30分钟','提前1小时','提前2小时','提前1天'];
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[],$where=[],$whereOr=[])
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
				$item['type_str'] = self::$Types[$item->type];
				$unames = Db::name('Admin')->where('id','in',$item['uids'])->column('name');;
				$item['unames'] = implode(',' ,$unames);
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$item['start_time'] = to_date($item['start_time'],'Y-m-d H:i');
				$item['end_time'] = to_date($item['end_time'],'Y-m-d H:i');
				$item['remind_time'] = to_date($item['remind_time'],'Y-m-d H:i');
				$item['create_time'] = to_date($item['create_time']);
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
}