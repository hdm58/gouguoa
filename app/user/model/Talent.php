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
class Talent extends Model
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
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$item['check_status_str'] = check_status_name($item['check_status']);
				if($item['check_status']==1 && !empty($item['check_uids'])){
					$check_users = Db::name('Admin')->where('id','in',$item['check_uids'])->column('name');
					$item['check_users'] = implode(',',$check_users);
				}
				else{
					$item['check_users']='-';
				}
				if(!empty($item['check_copy_uids'])){
					$check_copy_users = Db::name('Admin')->where('id','in',$item['check_copy_uids'])->column('name');
					$item['check_copy_users'] = implode(',',$check_copy_users);
				}
				else{
					$item['check_copy_users']='-';
				}
				if($item['entry_time']>0){
					$item['entry_time'] = date('Y-m-d',$item['entry_time']);
				}
				else{
					$item['entry_time'] = '-';
				}
				if($item['position_id']>0){
					$item['position']= Db::name('Position')->where('id',$item['position_id'])->value('title');
				}
				if($item['to_did']>0){
					$item['department']= Db::name('Department')->where('id',$item['to_did'])->value('title');
				}
				if($item['political']==1){
					$item['political'] = '中共党员';
				}
				else if($item['political']==2){
					$item['political'] = '团员';
				}
				else{
					$item['political'] = '其他';
				}
				$item['create_time'] = to_date($item['create_time']);
				$item['thumb'] = get_file($item['thumb']);
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
		return to_assign(0,'操作成功',['return_id'=>$param['id']]);
    }
	
    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		if($info['entry_time']>0){
			$info['entry_time'] = date('Y-m-d',$info['entry_time']);
		}
		else{
			$info['entry_time'] = '';
		}
		if($info['position_id']>0){
			$info['position']= Db::name('Position')->where('id',$info['position_id'])->value('title');
		}
		else{
			$info['position'] = '';
		}
		if($info['pid']>0){
			$info['pname']= Db::name('Admin')->where('id',$info['pid'])->value('name');
		}
		else{
			$info['pname'] = '';
		}
		if($info['position_name']>0){
			$info['position_name_str']= Db::name('BasicUser')->where('id',$info['position_name'])->value('title');
		}
		else{
			$info['position_name_str']='';
		}
		if($info['position_rank']>0){
			$info['position_rank_str']= Db::name('BasicUser')->where('id',$info['position_rank'])->value('title');
		}
		else{
			$info['position_rank_str']='';
		}
		if($info['to_did']>0){
			$info['department']= Db::name('Department')->where('id',$info['to_did'])->value('title');
		}
		else{
			$info['department']='';
		}
		if(!empty($info['to_dids'])){
			$departments= Db::name('Department')->where('id','in',$info['to_dids'])->column('title');
			$info['departments']= implode(',' ,$departments);
		}
		if(!empty($info['admin_id'])){
			$info['admin_name'] =  Db::name('Admin')->where([['id','=',$info['admin_id']]])->value('name');
		}
		$info['create_time'] = to_date($info['create_time']);
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

