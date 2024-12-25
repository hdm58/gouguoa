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
namespace app\customer\model;
use think\model;
use think\facade\Db;
class CustomerTrace extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param,$where)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item->follow_time = date('Y-m-d H:i',$item->follow_time);
				$item->next_time = date('Y-m-d H:i',$item->next_time);
				$item->admin_name = Db::name('Admin')->where('id',$item->admin_id)->value('name');
				$item->type_name = Db::name('BasicCustomer')->where(['id' => $item->types])->value('title');
				$item->stage_name = Db::name('BasicCustomer')->where(['id' => $item->stage])->value('title');
				if($item->chance_id>0){
					$item->chance = Db::name('CustomerChance')->where(['id' => $item->chance_id])->value('title');
				}
				else{
					$item->chance='-';
				}
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
			$follow_time = self::order('follow_time', 'desc')->where(['delete_time'=>0,'cid'=>$param['cid']])->value('follow_time');
			$next_time = self::order('next_time', 'desc')->where(['delete_time'=>0,'cid'=>$param['cid']])->value('next_time');
			Db::name('Customer')->strict(false)->field(true)->update(['id'=>$param['cid'],'follow_time'=>$follow_time,'next_time'=>$next_time]);
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
			$follow_time = self::order('follow_time', 'desc')->where(['delete_time'=>0,'cid'=>$param['cid']])->value('follow_time');
			$next_time = self::order('next_time', 'desc')->where(['delete_time'=>0,'cid'=>$param['cid']])->value('next_time');
			Db::name('Customer')->strict(false)->field(true)->update(['id'=>$param['cid'],'follow_time'=>$follow_time,'next_time'=>$next_time]);
			add_log('edit', $param['id'], $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['return_id'=>$param['id']]);
    }
	
    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		$info['customer'] = Db::name('Customer')->where(['id' => $info['cid']])->value('name');
		$file_array=[];
		if(!empty($info['file_ids'])){
			$file_array = Db::name('File')->where('id','in',$info['file_ids'])->select()->toArray();
		}
		$info['file_array'] = $file_array;
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
		$cid = self::where('id', $id)->value('cid');
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
		$follow_time = self::order('follow_time', 'desc')->where(['delete_time'=>0,'cid'=>$cid])->value('follow_time');
		$next_time = self::order('next_time', 'desc')->where(['delete_time'=>0,'cid'=>$cid])->value('next_time');
		Db::name('Customer')->strict(false)->field(true)->update(['id'=>$cid,'follow_time'=>$follow_time,'next_time'=>$next_time]);
		return to_assign();
    }
}

