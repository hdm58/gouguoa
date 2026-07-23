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
use think\model;
use think\facade\Db;
class WorkPlan extends Model
{
	//参与人类型
	public static $Types = ['','员工','部门','岗位','全部'];
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
				$item['types_str'] = self::$Types[$item->types];
				$item['cate'] = Db::name('BasicAdm')->where('id',$item['cate_id'])->value('title');
				$copy_names = Db::name('Admin')->where('id',$item['copy_uids'])->column('name');
				$item['copy_names'] = implode(',' ,$copy_names);
				$director_names = Db::name('Admin')->where('id',$item['director_uids'])->column('name');
				$item['director_names'] = implode(',' ,$director_names);
				$endorse_names = Db::name('Admin')->where('id',$item['endorse_uids'])->column('name');
				$item['endorse_names'] = implode(',' ,$endorse_names);
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$item['start_time'] = to_date($item['start_time'],'Y-m-d');
				$item['end_time'] = to_date($item['end_time'],'Y-m-d');
				$item['create_time'] = to_date($item['create_time']);
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
		$info['file_array'] = Db::name('File')->where([['id','in',$info['file_ids']]])->select()->toArray();
		$info['types_str'] = self::$Types[$info->types];
		$info['cate'] = Db::name('BasicAdm')->where('id',$info['cate_id'])->value('title');
		$copy_names = Db::name('Admin')->where('id','in',$info['copy_uids'])->column('name');
		$info['copy_names'] = implode(',' ,$copy_names);
		$director_names = Db::name('Admin')->where('id','in',$info['director_uids'])->column('name');
		$info['director_names'] = implode(',' ,$director_names);
		$endorse_names = Db::name('Admin')->where('id','in',$info['endorse_uids'])->column('name');
		$info['endorse_names'] = implode(',' ,$endorse_names);
		if($info['types'] == 1){
			$unames = Db::name('Admin')->where('id','in',$info['uids'])->column('name');
			$info['unames'] = implode(',' ,$unames);			
		}
		if($info['types'] == 2){
			$dnames = Db::name('Department')->where('id','in',$info['dids'])->column('title');
			$info['dnames'] = implode(',' ,$dnames);			
		}
		if($info['types'] == 3){
			$pnames = Db::name('Position')->where('id','in',$info['pids'])->column('title');
			$info['pnames'] = implode(',' ,$pnames);			
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

