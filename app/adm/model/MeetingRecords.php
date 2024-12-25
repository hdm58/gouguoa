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

namespace app\adm\model;
use think\model;
use think\facade\Db;
class MeetingRecords extends Model
{
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
					$item['anchor'] = Db::name('Admin')->where(['id' => $item['anchor_id']])->value('name');
					$item['meeting_date'] = empty($item['meeting_date']) ? '-' : date('Y-m-d', $item['meeting_date']);
					$item['did_name'] = Db::name('Department')->where(['id' => $item['did']])->value('title');
					$item['recorder_name'] = Db::name('Admin')->where(['id' => $item['recorder_id']])->value('name');
				});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }

    /**
    * 添加数据
    * @param $param
    */
    public function add($param)
    {
		$insertId = 0;
        try {
			$param['create_time'] = time();
			$insertId = self::strict(false)->field(true)->insertGetId($param);
			add_log('add', $insertId, $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['aid'=>$insertId]);
    }

    /**
    * 编辑信息
    * @param $param
    */
    public function edit($param)
    {
        try {
            $param['update_time'] = time();
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);
			add_log('edit', $param['id'], $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign();
    }	

    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		$info['recorder_name'] = Db::name('Admin')->where(['id' => $info['recorder_id']])->value('name');
		$info['anchor_name'] = Db::name('Admin')->where(['id' => $info['anchor_id']])->value('name');
		$info['room'] = Db::name('MeetingRoom')->where(['id' => $info['room_id']])->value('title');
		$info['did_name'] = Db::name('Department')->where(['id' => $info['did']])->value('title');
		$join_names = Db::name('Admin')->where([['id','in',$info['join_uids']]])->column('name');
		$info['join_names'] =implode(',' ,$join_names);
		$sign_names = Db::name('Admin')->where([['id','in',$info['sign_uids']]])->column('name');
		$info['sign_names'] =implode(',' ,$sign_names);
		$share_names = Db::name('Admin')->where([['id','in',$info['share_uids']]])->column('name');
		$info['share_names'] =implode(',' ,$share_names);
		return $info;
    }

    /**
    * 删除信息
    * @param $id
    * @param $type
    * @return array
    */
    public function delById($id,$type=0)
    {
		if($type==0){
			//逻辑删除
			try {
				$param['delete_time'] = time();
				self::where('id', $id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				self::destroy($id);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }
}
