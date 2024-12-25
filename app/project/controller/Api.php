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
declare (strict_types = 1);
namespace app\project\controller;

use app\api\BaseController;
use app\project\model\Project as ProjectModel;
use app\project\model\ProjectTask;
use app\oa\model\Schedule;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ProjectModel();
    }
    //获取项目列表
    public function get_project()
    {
		$param = get_params();
		$uid = $this->uid;
		$auth = isAuth($uid,'project_admin','conf_1');
		$where = array();
		$whereOr = array();
		$where[] = ['delete_time', '=', 0];		
		if($auth == 0){
			$whereOr[] = ['director_uid', '=', $uid];
			$project_ids = Db::name('ProjectUser')->where(['uid' => $uid, 'delete_time' => 0])->column('project_id');
			$whereOr[] = ['id', 'in', $project_ids];
			$dids_a = get_leader_departments($uid);	
			$dids_b = get_role_departments($uid);
			$dids = array_merge($dids_a, $dids_b);
			if(!empty($dids)){
				$whereOr[] = ['did','in',$dids];
			}
		}
		if (!empty($param['keywords'])) {
			$where[] = ['name|content', 'like', '%' . $param['keywords'] . '%'];
		}
		$list = $this->model->datalist($param,$where,$whereOr);
		return table_assign(0, '', $list);
    }
	
    //获取任务列表
    public function get_task()
    {
		$param = get_params();
		$uid = $this->uid;
		$where = [];
		$whereOr = [];
		$where[] = ['delete_time', '=', 0];
        if (!empty($param['status'])) {
            $where[] = ['status', '=', $param['status']];
        }
        if (!empty($param['priority'])) {
            $where[] = ['priority', '=', $param['priority']];
        }
        if (!empty($param['work_id'])) {
            $where[] = ['work_id', '=', $param['work_id']];
        }
        if (!empty($param['director_uid'])) {
            $where[] = ['director_uid', 'in', $param['director_uid']];
        }
        if (!empty($param['keywords'])) {
            $where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
        }
		if(!empty($param['project_id'])){
			$where[] = ['project_id', '=', $param['project_id']];			
		}
		else{
			$auth = isAuth($uid,'project_admin','conf_1');
			if($auth == 0){
				$whereOr[] = ['admin_id', '=', $uid];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',assist_admin_ids)")];
				$dids_a = get_leader_departments($uid);	
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
				if (empty($param['director_uid'])) {
					$whereOr[] = ['director_uid', '=', $uid];
				}
			}
		}
		
		$model = new ProjectTask();
        $list = $model->datalist($param,$where,$whereOr);
        return table_assign(0, '', $list);
    }
	
	//获取项目概况数据
	public function get_chart_data()
    {
        $param = get_params();
        $tasks = Db::name('ProjectTask')->field('id,plan_hours,end_time,status,over_time')->order('end_time asc')->where([['project_id', '=', $param['project_id']],['end_time','>',0],['delete_time', '=', 0]])->select()->toArray();

        $task_count = count($tasks);
        $task_count_ok = Db::name('ProjectTask')->where([['project_id', '=', $param['project_id']], ['delete_time', '=', 0],['status', '>', 2]])->count();
        $task_delay = 0;
        if ($task_count > 0) {
            foreach ($tasks as $k => $v) {
                if (($v['status'] < 3) && ($v['end_time'] < time() - 86400)) {
                    $task_delay++;
                }
                if (($v['status'] == 3) && ($v['end_time'] < $v['over_time'] - 86400)) {
                    $task_delay++;
                }
            }
        }
        $task_pie = [
            'count' => $task_count,
            'count_ok' => $task_count_ok,
            'delay' => $task_delay,
            'ok_lv' => $task_count == 0 ? 100 : round($task_count_ok * 100 / $task_count, 2),
            'delay_lv' => $task_count == 0 ? 100 : round($task_delay * 100 / $task_count, 2),
        ];

        $date_tasks = [];
        if ($tasks) {
            $date_tasks = plan_count($tasks);
        }

        $tasks_ok = Db::name('ProjectTask')->field('id,over_time as end_time')->order('over_time asc')->where([['over_time', '>', 0], ['delete_time', '=', 0], ['project_id', '=', $param['project_id']]])->select()->toArray();
        $date_tasks_ok = [];
        if ($tasks_ok) {
            $date_tasks_ok = plan_count($tasks_ok);
        }
        $tids = Db::name('ProjectTask')->where(['delete_time' => 0, 'project_id' => $param['project_id']])->column('id');
        $schedules = Db::name('Schedule')->where([['tid', 'in', $tids], ['delete_time', '=', 0]])->select()->toArray();
        $date_schedules = [];
        if ($schedules) {
            $date_schedules = hour_count($schedules);
        }
        $res['task_pie'] = $task_pie;
        $res['date_tasks'] = $date_tasks;
        $res['date_tasks_ok'] = $date_tasks_ok;
        $res['date_schedules'] = $date_schedules;
        to_assign(0, '', $res);
    }	

    //添加附件
    public function add_file()
    {
        $param = get_params();
        $param['create_time'] = time();
        $param['admin_id'] = $this->uid;
        $fid = Db::name('ProjectFile')->strict(false)->field(true)->insertGetId($param);
        if ($fid) {
            return to_assign(0, '上传成功', $fid);
        }
    }
    
    //删除
    public function delete_file()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('ProjectFile')->where('id', $id)->find();
            if (Db::name('ProjectFile')->where('id', $id)->delete() !== false) {
                $file_name = Db::name('File')->where('id', $detail['file_id'])->value('name');
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
    //工作记录列表
    public function schedule()
    {
		$param = get_params();
		$task_ids = Db::name('ProjectTask')->where(['delete_time' => 0, 'project_id' => $param['tid']])->column('id');
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['a.title', 'like', '%' . $param['keywords'] . '%'];
		}
		if (!empty($param['uid'])) {
			$where[] = ['a.admin_id', '=', $param['uid']];
		}
		if (!empty($task_ids)) {
			$where[] = ['a.tid', 'in', $task_ids];
		}
		$where[] = ['a.delete_time', '=', 0];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$list = Schedule::where($where)
			->field('a.*,u.name,d.title as department,t.title as task,p.name as project,w.title as work_cate')
			->alias('a')
			->join('Admin u', 'a.admin_id = u.id', 'LEFT')
			->join('Department d', 'u.did = d.id', 'LEFT')
			->join('ProjectTask t', 'a.tid = t.id', 'LEFT')
			->join('WorkCate w', 'w.id = t.cate', 'LEFT')
			->join('Project p', 't.project_id = p.id', 'LEFT')
			->order('a.end_time desc')
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) {
				$item->start_time_a = empty($item->start_time) ? '' : date('Y-m-d', $item->start_time);
				$item->start_time_b = empty($item->start_time) ? '' : date('H:i', $item->start_time);
				$item->end_time_a = empty($item->end_time) ? '' : date('Y-m-d', $item->end_time);
				$item->end_time_b = empty($item->end_time) ? '' : date('H:i', $item->end_time);

				$item->start_time = empty($item->start_time) ? '' : date('Y-m-d H:i', $item->start_time);
				$item->end_time = empty($item->end_time) ? '' : date('H:i', $item->end_time);
			});
		return table_assign(0, '', $list);
    }
	
    //查看工作记录详情
    public function schedule_detail($id)
    {
        $id = get_params('id');
        $schedule = Schedule::where(['id' => $id])->find();
        if (!empty($schedule)) {
            $schedule['start_time_1'] = date('H:i', $schedule['start_time']);
            $schedule['end_time_1'] = date('H:i', $schedule['end_time']);
            $schedule['start_time'] = date('Y-m-d', $schedule['start_time']);
            $schedule['end_time'] = date('Y-m-d', $schedule['end_time']);
            $schedule['user'] = Db::name('Admin')->where(['id' => $schedule['admin_id']])->value('name');
            $schedule['department'] = Db::name('Department')->where(['id' => $schedule['did']])->value('title');
        }
        if (request()->isAjax()) {
            return to_assign(0, "", $schedule);
        } else {
            return $schedule;
        }
    }

    //任务的工作记录列表
    public function task_schedule()
    {
        $param = get_params();
        $where = array();
        $where['a.tid'] = $param['tid'];
        $where['a.delete_time'] = 0;
        $list = Db::name('Schedule')
            ->field('a.*,u.name')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->order('a.create_time desc')
            ->where($where)
            ->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['start_time'] = empty($v['start_time']) ? '' : date('Y-m-d H:i', $v['start_time']);
            $list[$k]['end_time'] = empty($v['end_time']) ? '' : date('H:i', $v['end_time']);
        }
        return to_assign(0, '', $list);
    }


	public function project_user()
    {
        $param = get_params();
		$project = Db::name('Project')->where(['id' => $param['tid']])->find();
		$users = Db::name('ProjectUser')
				->field('pu.*,a.name,a.mobile,p.title as position,d.title as department')
				->alias('pu')
				->join('Admin a', 'pu.uid = a.id', 'LEFT')
				->join('Department d', 'a.did = d.id', 'LEFT')
				->join('Position p', 'a.position_id = p.id', 'LEFT')
				->order('pu.id asc')
				->where(['pu.project_id' => $param['tid']])
				->select()->toArray();
		if(!empty($users)){
			foreach ($users as $k => &$v) {
				$v['role'] = 0; //普通项目成员
				if ($v['uid'] == $project['admin_id']) {
					$v['role'] = 1; //项目创建人
				}
				if ($v['uid'] == $project['director_uid']) {
					$v['role'] = 2; //项目负责人
				}

				$v['create_time'] = date('Y-m-d', (int) $v['create_time']);
				if($v['delete_time'] > 0){
					$v['delete_time'] = date('Y-m-d', (int) $v['delete_time']);
				}
				else{
					$v['delete_time'] = '-';
				}

				$tids = Db::name('ProjectTask')->where([['project_id','=',$param['tid']],['delete_time','=',0]])->column('id');
				$schedule_map = [];
        		$schedule_map[] = ['tid','in',$tids];
        		$schedule_map[] = ['delete_time','=',0];
        		$schedule_map[] = ['admin_id','=',$v['uid']];
        		$v['schedules'] = Db::name('Schedule')->where($schedule_map)->count();
        		$v['labor_times'] = Db::name('Schedule')->where($schedule_map)->sum('labor_time');

				$task_map = [];
				$task_map[] = ['project_id','=',$param['tid']];
				$task_map[] = ['delete_time', '=', 0];

				$task_map1 = [
					['admin_id', '=', $v['uid']],
				];
				$task_map2 = [
					['director_uid', '=', $v['uid']],
				];
				$task_map3 = [
					['', 'exp', Db::raw("FIND_IN_SET('{$v['uid']}',assist_admin_ids)")],
				];

				//任务总数
				$v['tasks_total'] = Db::name('ProjectTask')
				->where(function ($query) use ($task_map1, $task_map2, $task_map3) {
					$query->where($task_map1)->whereor($task_map2)->whereor($task_map3);
				})
				->where($task_map)->count();
				//已完成任务
				$task_map[] = ['status', '>', 2]; //已完成
				$v['tasks_finish'] = Db::name('ProjectTask')->where(function ($query) use ($task_map1, $task_map2, $task_map3) {
					$query->where($task_map1)->whereor($task_map2)->whereor($task_map3);
				})
				->where($task_map)->count();
				//未完成任务
				$v['tasks_unfinish'] = $v['tasks_total'] - $v['tasks_finish'];
				$v['tasks_pensent'] = "100％";
				if ($v['tasks_total'] > 0) {
					$v['tasks_pensent'] = round($v['tasks_finish'] / $v['tasks_total'] * 100, 2) . "％";
				}
			}
		}
        to_assign(0, '', $users);
    }

	//新增项目成员
    public function add_user()
    {
        $param = get_params();
        if (request()->isPost()) {
			$has = Db::name('ProjectUser')->where(['uid' => $param['uid'],'project_id'=>$param['project_id']])->find();
			if(!empty($has)){
				to_assign(1, '该员工已经是项目成员');
			}
			$project = Db::name('Project')->where(['id' => $param['project_id']])->find();
			if($this->uid == $project['admin_id'] || $this->uid == $project['director_uid']){
				$param['admin_id'] = $this->uid;
				$param['create_time'] = time();
				$res = Db::name('ProjectUser')->strict(false)->field(true)->insert($param);
				if ($res) {
					to_assign();
				}				
			}else{
				to_assign(1, '只有项目创建者和负责人才有权限新增项目成员');
			}
		}
	}

	//移除项目成员
	public function remove_user()
	{
		$param = get_params();
		if (request()->isDelete()) {
			$detail = Db::name('ProjectUser')->where(['id' => $param['id']])->find();
			$project = Db::name('Project')->where(['id' => $detail['project_id']])->find();
			if($this->uid == $project['admin_id'] || $this->uid == $project['director_uid']){
				if($detail['uid'] == $project['admin_id']){
					to_assign(1, '该项目成员是项目的创建者，不能移除');
				}
				if($detail['uid'] == $project['director_uid']){
					to_assign(1, '该项目成员是项目的负责人，需要去除负责人权限才能移除');
				}
				$param['delete_time'] = time();
				if (Db::name('ProjectUser')->update($param) !== false) {				
					return to_assign(0, "移除成功");
				} else {
					return to_assign(1, "移除失败");
				}
			}else{
				to_assign(1, '只有项目创建者和负责人才有权限移除项目成员');
			}
		}else{
			return to_assign(1, "错误的请求");
		}
	}
	//恢复项目成员
	public function recover_user()
	{
		$param = get_params();
		if (request()->isPost()) {
			$detail = Db::name('ProjectUser')->where(['id' => $param['id']])->find();
			$project = Db::name('Project')->where(['id' => $detail['project_id']])->find();
			if($this->uid == $project['admin_id'] || $this->uid == $project['director_uid']){
				$param['delete_time'] = 0;
				if (Db::name('ProjectUser')->update($param) !== false) {				
					return to_assign(0, "恢复成功");
				} else {
					return to_assign(1, "恢复失败");
				}
			}else{
				to_assign(1, '只有项目创建者和负责人才有权限恢复项目成员');
			}
		}else{
			return to_assign(1, "错误的请求");
		}
	}
	
    //关闭项目
    public function close()
    {
        if (request()->isPost()) {
            $id = get_params("id");
            $project = Db::name('Project')->where('id', $id)->find();
			if($project['status'] == 3){
				return to_assign(1, "已完成的项目当不能关闭");
			}
			$auth = isAuth($this->uid,'project_admin','conf_1');
			if($project['admin_id'] == $this->uid || $project['director_uid'] == $this->uid || $auth==1){
				if (Db::name('Project')->where('id', $id)->update(['status'=>4]) !== false) {
					add_log('close', $id, [],'项目');
					return to_assign(0, "操作成功");
				} else {
					return to_assign(1, "操作失败");
				}
			}
			else{
				return to_assign(1, "只有项目管理员、项目创建人、项目负责人才有权限关闭");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//关闭项目
    public function open()
    {
        if (request()->isPost()) {
            $id = get_params("id");
            $project = Db::name('Project')->where('id', $id)->find();
			if($project['status'] == 3){
				return to_assign(1, "已完成的项目当不能开启");
			}
			$auth = isAuth($this->uid,'project_admin','conf_1');
			if($project['admin_id'] == $this->uid || $project['director_uid'] == $this->uid || $auth==1){
				if (Db::name('Project')->where('id', $id)->update(['status'=>2]) !== false) {
					add_log('open', $id, [],'项目');
					return to_assign(0, "操作成功");
				} else {
					return to_assign(1, "操作失败");
				}
			}
			else{
				return to_assign(1, "只有项目管理员、项目创建人、项目负责人才有权限开启");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//删除已有的项目步骤
    public function step_del()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $step = Db::name('ProjectStep')->where('id', $id)->find();
			if($step['is_current'] == 1){
				return to_assign(1, "项目当前所在步骤不能删除");
			}
			$project = Db::name('Project')->where('id', $step['project_id'])->find();
			$auth = isAuth($this->uid,'project_admin','conf_1');
			if($project['admin_id'] == $this->uid || $project['director_uid'] == $this->uid || $auth==1){
				if (Db::name('ProjectStep')->where('id', $id)->update(['delete_time'=>time()]) !== false) {
					return to_assign(0, "删除成功");
				} else {
					return to_assign(1, "删除失败");
				}
			}
			else{
				return to_assign(1, "只有项目管理员、项目创建人、项目负责人才有权限删除");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//审核
    public function step_check()
    {
        $param = get_params();
		$detail = Db::name('Project')->where(['id' => $param['id']])->find();
		if($detail['status'] > 2){
			return to_assign(1, "不支持该操作：项目已完成或者已关闭");
		}
		//当前审核节点详情
		$step = Db::name('ProjectStep')->where(['project_id'=>$detail['id'],'is_current' => 1,'delete_time'=>0])->find();
		if ($this->uid != $step['director_uid']){		
			return to_assign(1,'您没权限操作');
		}
		//审核通过
		if($param['check'] == 1){
			$next_step = Db::name('ProjectStep')->where([['project_id','=',$detail['id']],['sort','>',$step['sort']],['delete_time','=',0]])->order('sort', 'asc')->find();
			if(!empty($next_step)){
				Db::name('ProjectStep')->where('id', $step['id'])->strict(false)->field(true)->update(['is_current'=>0]);
				Db::name('ProjectStep')->where('id', $next_step['id'])->strict(false)->field(true)->update(['is_current'=>1]);
				$param['status'] = 2;
			}
			else{
				//不存在下一步审核，审核结束
				$param['status'] = 3;
				Db::name('ProjectStep')->where('id', $step['id'])->strict(false)->field(true)->update(['is_current'=>0]);
			}		
			//审核通过数据操作
			Db::name('Project')->strict(false)->field('status')->update($param);
			$checkData=array(
				'project_id' => $detail['id'],
				'step_id' => $step['id'],
				'check_uid' => $this->uid,
				'check_time' => time(),
				'status' => $param['check'],
				'create_time' => time()
			);
			Db::name('ProjectStepRecord')->strict(false)->field(true)->insertGetId($checkData);
			add_log('check', $param['id'], $param,'项目阶段');
			return to_assign();
		}
		//回退审核
		else if($param['check'] == 2){
			//获取上一步的审核信息
			$prev_step = Db::name('ProjectStep')->where([['project_id','=',$detail['id']],['sort','<',$step['sort']],['delete_time','=',0]])->order('sort', 'desc')->find();
			if(!empty($prev_step)){
				//存在上一步审核
				Db::name('ProjectStep')->where('id', $step['id'])->strict(false)->field(true)->update(['is_current'=>0]);
				Db::name('ProjectStep')->where('id', $prev_step['id'])->strict(false)->field(true)->update(['is_current'=>1]);
				$checkData=array(
					'project_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_uid' => $this->uid,
					'check_time' => time(),
					'status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				Db::name('ProjectStepRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('refue', $param['id'], $param,'项目阶段');
				return to_assign();
			}
			else{
				return to_assign(1,'已经是第一个阶段了');
			}
		}				
    }
	
	//获取项目类别
	public function get_project_cate()
    {
		$list = get_base_data('project_cate');
		return to_assign(0, '', $list);
    }
	
	public function project_edit()
    {
		$param = get_params();
		$this->model->apiedit($param);
    }
}
