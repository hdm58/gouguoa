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
namespace app\contract\model;
use think\model;
use think\facade\Db;
use app\api\model\EditLog;
class Purchase extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[], $where=[], $whereOr=[])
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
				$item->status_name = check_status_name($item->check_status);
				$types_title=purchase_types_name($item->types);
				$item->types_name = $types_title['title'];
				$item->cate_title = Db::name('ContractCate')->where(['id' => $item->cate_id])->value('title');
				$item->sign_name = Db::name('Admin')->where(['id' => $item->sign_uid])->value('name');
				$item->sign_time = date('Y-m-d', $item->sign_time);
				$item->interval_time = date('Y-m-d', $item->start_time) . ' 至 ' . date('Y-m-d', $item->end_time);
                $item->delay = count_days(date("Y-m-d"),date('Y-m-d', $item->end_time));
				if($item->keeper_uid>0){
					$item->keeper_name = Db::name('Admin')->where(['id' => $item->keeper_uid])->value('name');
				}
				else{
					$item->keeper_name='-';
				}
				if($item->archive_uid>0){
					$item->archive_name = Db::name('Admin')->where(['id' => $item->archive_uid])->value('name');
					$item->archive_time = date('Y-m-d', $item->archive_time);
				}
				else{
					$item->archive_name='-';
					$item->archive_time='-';
				}
				if($item->stop_uid>0){
					$item->stop_name = Db::name('Admin')->where(['id' => $item->stop_uid])->value('name');
					$item->stop_time = date('Y-m-d', $item->stop_time);
				}
				else{
					$item->stop_name='-';
					$item->stop_time='-';
				}
				if($item->void_uid>0){
					$item->void_name = Db::name('Admin')->where(['id' => $item->void_uid])->value('name');
					$item->void_time = date('Y-m-d', $item->void_time);
				}
				else{
					$item->void_name='-';
					$item->void_time='-';
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
			add_log('add', $insertId, $param);
			$log=new EditLog();
			$log->add('Purchase',$insertId);
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
			$old = self::find($param['id']);
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);
			add_log('edit', $param['id'], $param);
			$log=new EditLog();
			$log->edit('Purchase',$param['id'],$param,$old);
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
		$types_title=purchase_types_name($info['types']);
		$info['types_name'] = $types_title['title'];
		$info['sign_time'] = date('Y-m-d', $info['sign_time']);
		$info['start_time'] = date('Y-m-d', $info['start_time']);
		$info['end_time'] = date('Y-m-d', $info['end_time']);
		$info['sign_department'] = Db::name('Department')->where(['id' => $info['did']])->value('title');
		$info['sign_name'] = Db::name('Admin')->where(['id' => $info['sign_uid']])->value('name');
		$info['admin_name'] = Db::name('Admin')->where(['id' => $info['admin_id']])->value('name');
		if($info['prepared_uid']>0){
			$info['prepared_name'] = Db::name('Admin')->where(['id' => $info['prepared_uid']])->value('name');
		}
		else{
			$info['prepared_name']='';
		}
		if($info['keeper_uid']>0){
			$info['keeper_name'] = Db::name('Admin')->where(['id' => $info['keeper_uid']])->value('name');
		}
		else{
			$info['keeper_name']='';
		}
		if($info['share_ids'] !=''){
			$share_names = Db::name('Admin')->where([['id','in',$info['share_ids']]])->column('name');
			$info['share_names'] = implode(',',$share_names);
		}
		else{
			$info['share_names'] = '';
		}
		if($info['file_ids'] !=''){
			$file_array = Db::name('File')->where('id','in',$info['file_ids'])->select()->toArray();
			$info['file_array'] = $file_array;
		}
		else{
			$info['file_array'] = [];
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

