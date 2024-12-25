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
namespace app\project\model;
use think\model;
use think\facade\Db;
use app\api\model\EditLog;
class Project extends Model
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
			 ->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item['title'] = $item['name'];
				$item->director_name = Db::name('Admin')->where(['id' => $item->director_uid])->value('name');
				$item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
				$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
				$item->status_name = status_name($item->status);
				$item->range_time = date('Y-m-d',$item->start_time). ' 至 ' .date('Y-m-d',$item->end_time);
				$item->delay = count_days(date("Y-m-d"),date('Y-m-d', $item->end_time));
				$item->cate = Db::name('ProjectCate')->where('id', $item->cate_id)->value('title');
				$task_map = [];
                $task_map[] = ['project_id', '=', $item->id];
                $task_map[] = ['delete_time', '=', 0];
                //任务总数
                $item->tasks_total = Db::name('ProjectTask')->where($task_map)->count();
                //已完成任务
                $task_map[] = ['status', '>', 2];
                $item->tasks_finish = Db::name('ProjectTask')->where($task_map)->count();
                //未完成任务
				$item->tasks_unfinish = $item->tasks_total - $item->tasks_finish;
				$item->tasks_pensent = "0.00％";
				if ($item->tasks_total > 0) {
                    $item->tasks_pensent = round($item->tasks_finish / $item->tasks_total * 100, 2) . "％";
                }
				$step = Db::name('ProjectStep')->where(['project_id' => $item->id,'is_current' => 1,'delete_time'=>0])->find();
				if(!empty($step)){
					$item->step_director = Db::name('Admin')->where(['id'=>$step['director_uid']])->value('name');
					$item->step = $step['title'] . '『' . $item->step_director. '』';
				}
				else{						
					$item->step = '-';
				}
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
    public function apilist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)->field('id,status,name as title')->order($order)->paginate(['list_rows'=> $rows]);
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }

    /**
    * 添加数据
    * @param $param
    */
    public function add($param,$step)
    {
		$insertId = 0;
		// 启动事务
		Db::startTrans();
        try {
			$param['create_time'] = time();
			$insertId = self::strict(false)->field(true)->insertGetId($param);
			//项目成员
			$project_users = $param['admin_id'];
			if (!empty($param['director_uid'])){
				$project_users.=",".$param['director_uid'];
			}
			if (!empty($param['team_admin_ids'])){
				$project_users.=",".$param['team_admin_ids'];
			}
			$project_array = explode(",",(string)$project_users);
			$project_array = array_unique($project_array);
			$project_user_array=[];
			foreach ($project_array as $k => $v) {
				if (is_numeric($v)) {
					$project_user_array[]=array(
						'uid'=>$v,
						'admin_id'=>$param['admin_id'],
						'project_id'=>$insertId,
						'create_time'=>time(),
					);
				}
			}
			Db::name('ProjectUser')->strict(false)->field(true)->insertAll($project_user_array);
			//项目阶段
			foreach ($step as $key => &$value) {
				if($key==0){
					$value['is_current'] = 1;
				}
				else{
					$value['is_current'] = 0;
				}
				$value['project_id'] = $insertId;
			}
			Db::name('ProjectStep')->strict(false)->field(true)->insertAll($step);	
			add_log('add', $insertId, $param);
			$log=new EditLog();
			$log->add('Project',$insertId);
			// 提交事务
			Db::commit();
        } catch(\Exception $e) {
			// 回滚事务
			Db::rollback();
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['return_id'=>$insertId]);
    }

    /**
    * 编辑信息
    * @param $param
    */
    public function edit($param,$step)
    {
		// 启动事务
		Db::startTrans();
        try {
            $param['update_time'] = time();
			$old = self::find($param['id']);
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);
			//项目阶段
			foreach ($step as $key => $value) {
				$value['project_id'] = $param['id'];
				if($value['id'] == 0){
					Db::name('ProjectStep')->strict(false)->field(true)->insert($value);
				}
				else{
					$value['update_time'] = time();
					Db::name('ProjectStep')->strict(false)->field(true)->update($value);
				}
			}			
			add_log('edit', $param['id'], $param);
			$log=new EditLog();
			$log->edit('Project',$param['id'],$param,$old);
			// 提交事务
			Db::commit();
        } catch(\Exception $e) {
			// 回滚事务
			Db::rollback();
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['return_id'=>$param['id']]);
    }
	
    public function apiedit($param)
    {
		try {
            $param['update_time'] = time();
			$old = self::find($param['id']);
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);		
			add_log('edit', $param['id'], $param);
			$log=new EditLog();
			$log->edit('Project',$param['id'],$param,$old);
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
		$info['admin_name'] = Db::name('Admin')->where(['id' => $info['admin_id']])->value('name');
		$info['director_name'] = Db::name('Admin')->where(['id' => $info['director_uid']])->value('name');
		$info['department'] = Db::name('Department')->where(['id' => $info['did']])->value('title');
		$info['contract_name'] = Db::name('Contract')->where(['id' => $info['contract_id']])->value('name');
		//项目阶段			
		$step_array = Db::name('ProjectStep')
			->field('s.*,a.name as director_name')
			->alias('s')
			->join('Admin a', 'a.id = s.director_uid', 'LEFT')
			->order('s.sort asc')
			->where(array('s.project_id' => $id, 's.delete_time' => 0))
			->select()->toArray();
		foreach ($step_array as $kk => &$vv) {
			$unames = Db::name('Admin')->where([['id','in',$vv['uids']]])->column('name');
			$vv['unames'] = implode(',',$unames);	
		}
		$info['step_array'] = $step_array;
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

