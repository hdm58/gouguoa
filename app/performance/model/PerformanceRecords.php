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

namespace app\performance\model;
use think\model;
use think\facade\Db;
class PerformanceRecords extends Model
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
				$item['range_time'] = date('Y-m-d',$item['start_time']).' ~ '.date('Y-m-d',$item['end_time']);
				$item['performance'] = Db::name('Performance')->where('id',$item['performance_id'])->value('title');
				$user = Db::name('Admin')->where('id',$item['uid'])->find();
				$item['name'] = $user['name'];
				$item['department'] = Db::name('Department')->where('id',$user['did'])->value('title');
				$item['position'] = Db::name('Position')->where('id',$user['position_id'])->value('title');
				$item['kpi_uname'] = Db::name('Admin')->where('id',$item['kpi_uid'])->value('name');
				$item['action_uname'] = Db::name('Admin')->where('id',$item['action_uid'])->value('name');
				$item['event_uname'] = Db::name('Admin')->where('id',$item['event_uid'])->value('name');
				$item['check_uname'] = Db::name('Admin')->where('id',$item['check_uid'])->value('name');
				$item['status_name'] = status_name($item['status']);
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
			$param['update_time'] = time();
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
			//发送消息通知
			if(!empty($param['status'])){
				$detail =self::where('id', $param['id'])->find();
				$msg=[
					'from_uid'=>0,//发送人
					'to_uids'=>$detail['check_uid'],//接收人
					'template_id'=>'performance_check',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$detail['id'],
						'title' => '绩效待审核',
						'status' => '待审核'
					]
				];
				event('SendMessage',$msg);
			}
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
		$info['range_time'] = date('Y-m-d',$info['start_time']).' ~ '.date('Y-m-d',$info['end_time']);
		
		$user = Db::name('Admin')->where('id',$info['uid'])->find();
		$info['u_name'] = $user['name'];
		$info['department'] = Db::name('Department')->where('id',$user['did'])->value('title');
		$info['position'] = Db::name('Position')->where('id',$user['position_id'])->value('title');
		
		$info['template_name'] = Db::name('PerformanceTemplates')->where('id',$info['template_id'])->value('title');
		$info['template_score'] = Db::name('PerformanceTemplates')->where('id',$info['template_id'])->value('score');
		$info['kpi_uname'] = Db::name('Admin')->where('id',$info['kpi_uid'])->value('name');
		$info['action_uname'] = Db::name('Admin')->where('id',$info['action_uid'])->value('name');
		$info['event_uname'] = Db::name('Admin')->where('id',$info['event_uid'])->value('name');
		$info['check_uname'] = Db::name('Admin')->where('id',$info['check_uid'])->value('name');
		$info['kpi_array'] = unserialize($info['kpi_serialize']);
		$info['action_array'] = unserialize($info['action_serialize']);
		$info['event_array'] = unserialize($info['event_serialize']);
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

