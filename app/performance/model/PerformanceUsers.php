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
class PerformanceUsers extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[],$where=[],$whereOr=[])
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'a.id desc' : $param['order'];
        try {
            $list = self::field('a.*, u.name as u_name, d.title as department, p.title as position')
			->where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->alias('a')
			->join('Admin u','u.id = a.uid')
			->join('Department d','u.did = d.id')
			->join('Position p','p.id = u.position_id')
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$template = Db::name('PerformanceTemplates')->where('id',$item['template_id'])->find();
				$item['template_name'] = $template['title'];
				$item['score'] = $template['score'];				
				$item['kpi_score'] = $template['kpi_score'];				
				$item['action_score'] = $template['action_score'];			
				$item['kpi_uname'] = Db::name('Admin')->where('id',$item['kpi_uid'])->value('name');
				$item['action_uname'] = Db::name('Admin')->where('id',$item['action_uid'])->value('name');
				$item['event_uname'] = Db::name('Admin')->where('id',$item['event_uid'])->value('name');
				$item['check_uname'] = Db::name('Admin')->where('id',$item['check_uid'])->value('name');
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
			$uids = explode(',', strval($param['uid']));
			$list = [];
			foreach ($uids as $key => $val) {
				$param['uid']=$val;
				$insertId = self::strict(false)->field(true)->insertGetId($param);
				add_log('add', $insertId, $param);
			}			
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

