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
class Note extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'a.end_time desc,a.sort desc,a.create_time desc' : $param['order'];
        try {
            $list = self::where($where)
			->field('a.*,c.title as cate')
            ->alias('a')
            ->join('NoteCate c', 'a.cate_id = c.id', 'LEFT')
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item->admin_name = Db::name('Admin')->where('id',$item->admin_id)->value('name');
				$item->start_time = empty($item->start_time) ? '-' : date('Y-m-d', $item->start_time);
                $item->end_time = empty($item->end_time) ? '-' : date('Y-m-d', $item->end_time);
				$item->create_time = to_date($item->create_time);
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
			$users= Db::name('Admin')->field('id as from_uid')->where(['status' => 1])->column('id');
			$msg=[
				'from_uid'=>$param['admin_id'],//发送人
				'to_uids'=>$users,//接收人
				'template_id'=>'note',//消息模板ID
				'content'=>[ //消息内容
					'create_time'=>date('Y-m-d H:i:s'),
					'title' => $param['title'],
					'action_id'=>$insertId
				]
			];
			event('SendMessage',$msg);
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

