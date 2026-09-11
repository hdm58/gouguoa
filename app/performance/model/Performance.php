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
class Performance extends Model
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
				$item['range_time'] = date('Y-m-d',$item['start_time']).' 至 '.date('Y-m-d',$item['end_time']);
				if($item['status']==0){
					$item['status_name'] = '未发布绩效单';
				}
				else{
					$item['status_name'] = '已发布';
				}		
				$item['create_time'] = to_date($item['create_time'],'Y-m-d H:i:s');				
				$item['count_a'] = Db::name('PerformanceRecords')->where([['performance_id','=',$item['id']],['delete_time','=',0],['status','=',4]])->count();
				$item['count_b'] = Db::name('PerformanceRecords')->where([['performance_id','=',$item['id']],['delete_time','=',0],['status','<',4]])->count();
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
			$uid_array = isset($param['uid']) ? $param['uid'] : 0;
			$template_id_array = isset($param['template_id']) ? $param['template_id'] : 0;
			$performance_array = [];
			if(!empty($uid_array)){
				$insertId = self::strict(false)->field(true)->insertGetId($param);
				foreach ($uid_array as $key => $value) {
					$template = Db::name('PerformanceTemplates')->where('id',$template_id_array[$key])->find();
					$template_users = Db::name('PerformanceUsers')->where(['uid'=>$uid_array[$key],'template_id'=>$template_id_array[$key]])->find();
					if(!empty($template) && !empty($template_users)){
						$data = [];
						$data['uid'] = $uid_array[$key];
						$data['performance_id'] = $insertId;
						$data['template_id'] = $template_id_array[$key];
						$data['start_time'] = $param['start_time'];
						$data['end_time'] = $param['end_time'];
						$data['kpi_serialize'] = $template['kpi_serialize'];
						$data['kpi_score'] = $template['kpi_score'];
						$data['kpi_uid'] = $template_users['kpi_uid'];
						$data['action_serialize'] = $template['action_serialize'];
						$data['action_uid'] = $template_users['action_uid'];
						$data['action_score'] = $template['action_score'];
						$data['event_serialize'] = $template['event_serialize'];
						$data['event_score'] = $template['event_score'];
						$data['event_uid'] = $template_users['event_uid'];
						$data['check_uid'] = $template_users['check_uid'];
						$data['score'] = $template['score'];
						
						$performance_array[]=$data;
					}
				}
				if(!empty($performance_array)){
					Db::name('PerformanceRecords')->insertAll($performance_array);
				}
			}
			add_log('add', $insertId, $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：没有参与考核的员工'.$e->getMessage());
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
			Db::name('PerformanceRecords')->where('performance_id','=',$param['id'])->update(['start_time'=>$param['start_time'],'end_time'=>$param['end_time']]);
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
				Db::name('PerformanceRecords')->where('performance_id','=',$id)->update(['delete_time'=>time()]);				
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				self::destroy($id);
				Db::name('PerformanceRecords')->where('performance_id','=',$id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }
}

