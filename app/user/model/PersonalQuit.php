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
namespace app\user\model;
use think\model;
use think\facade\Db;
class PersonalQuit extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
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
				$item['check_status_str'] = check_status_name($item['check_status']);
				$item['check_user'] = '-';
				if($item['check_status']==1 && !empty($item['check_uids'])){
					$check_user = Db::name('Admin')->where('id','in',$item['check_uids'])->column('name');
					$item['check_user'] = implode(',',$check_user);
				}
				$userinfo = Db::name('Admin')->where('id','=',$item->uid)->find();
				$item->name = $userinfo['name'];
				$item->department = Db::name('Department')->where('id',$userinfo['did'])->value('title');
				$item->position = Db::name('Position')->where('id',$userinfo['position_id'])->value('title');
				
				$item->admin_name = Db::name('Admin')->where('id','=',$item->admin_id)->value('name');
				$item->lead_admin_name = Db::name('Admin')->where('id','=',$item->lead_admin_id)->value('name');
				$item->connect_name = Db::name('admin')->where(['id' => $item->connect_id])->value('name');
				$item->quit_time = date('Y-m-d',$item->quit_time);
				$item->create_time = to_date($item->create_time);
				$connect_time_str = '-';
				if($item->status == 2){
					$connect_time_str = to_date($item->connect_time,'Y-m-d');
				}
				$item->connect_time_str = $connect_time_str;
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
		return to_assign(0,'操作成功',['return_id'=>$insertId]);
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
		return to_assign(0,'操作成功',['return_id'=> $param['id']]);
    }
	
    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		$userinfo =  Db::name('Admin')->where('id',$info['uid'])->find();
		$info['name'] = $userinfo['name'];
		$info['department'] = Db::name('Department')->where('id',$userinfo['did'])->value('title');
		$info['position'] = Db::name('Position')->where('id',$userinfo['position_id'])->value('title');
		
		$info['admin_name'] = Db::name('Admin')->where('id','=',$info['admin_id'])->value('name');
		$info['lead_admin_name'] = Db::name('Admin')->where('id','=',$info['lead_admin_id'])->value('name');
		$connect_uids_name = Db::name('Admin')->where([['id','in', $info['connect_uids']]])->column('name');
		$info['connect_names'] = implode(',', $connect_uids_name);
		$info['quit_time'] = date('Y-m-d', $info['quit_time']);
		$info['connect_name'] = Db::name('admin')->where(['id' => $info['connect_id']])->value('name');		
		$status_str = '未交接';
		$connect_time_str = '-';
		if($info['status'] == 2){
			$status_str = '已交接离职';
			$connect_time_str = to_date($info['connect_time'],'Y-m-d');
		}
		$info['status_str'] = $status_str;
		$info['connect_time_str'] = $connect_time_str;
		if(!empty($info['file_ids'])){
			$file_array = Db::name('File')->where('id','in',$info['file_ids'])->select();
			$info['file_array'] = $file_array;
		}
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

