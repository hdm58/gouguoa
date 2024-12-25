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
class OfficialDocs extends Model
{
	//密级程度
	public static $Secrets = ['','公开','秘密','机密'];
	
	//紧急程度
	public static $Urgency = ['','普通','紧急','加急'];
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
				$item->secrets_str = self::$Secrets[$item->secrets];
				$item->urgency_str = self::$Urgency[$item->urgency];
				$item->check_status_str = check_status_name($item->check_status);
				$item->draft_time = date('Y-m-d', $item->draft_time);
				$item->draft_name = Db::name('Admin')->where('id','=',$item->draft_uid)->value('name');
				$item->draft_dname = Db::name('Department')->where('id','=',$item->did)->value('title');
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
		$info['draft_name'] = Db::name('Admin')->where('id','=',$info['draft_uid'])->value('name');
		$info['draft_dame'] = Db::name('Department')->where('id','=',$info['did'])->value('title');
		
		$send_names = Db::name('Admin')->where([['id','in',$info['send_uids']]])->column('name');
		$info['send_names'] =implode(',' ,$send_names);
		
		$copy_names = Db::name('Admin')->where([['id','in',$info['copy_uids']]])->column('name');
		$info['copy_names'] =implode(',' ,$copy_names);
		
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

