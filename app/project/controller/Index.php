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

use app\base\BaseController;
use app\project\model\Project as ProjectModel;
use app\project\validate\IndexValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
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
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
		$uid = $this->uid;
		$auth = isAuth($uid,'project_admin','conf_1');
		$tab = isset($param['tab']) ? $param['tab'] : 0;
        if (request()->isAjax()) {
			$where = array();
			$whereOr = array();
			$where[] = ['delete_time', '=', 0];
			if (!empty($param['status'])) {
				$where[] = ['status', '=', $param['status']];
			}
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['keywords'])) {
				$where[] = ['name|content', 'like', '%' . $param['keywords'] . '%'];
			}
			if (!empty($param['director_uid'])) {
				$where[] = ['director_uid', 'in', $param['director_uid']];
			}
			else{
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
			}
			if($tab == 0){

			}
			if($tab == 1){
				$where[] = ['status', '=', 2];
			}
			if($tab == 2){
				$time = time();
				$dalay_time = time()+7*86400;
				$where[] = ['status', '<', 3];
				$where[] = ['end_time', 'between', [$time,$dalay_time]];
			}
			if($tab == 3){
				$where[] = ['status', '<', 3];
				$where[] = ['end_time', '<', time()];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', $auth);
            return view();
        }
    }
	
    /**
    * 添加/编辑
    */
    public function add()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			if (isset($param['range_time'])) {
                $range_time = explode('到',$param['range_time']);
				$param['start_time'] = strtotime(urldecode(trim($range_time[0])));
				$param['end_time'] = strtotime(urldecode(trim($range_time[1])));
            }
			$step_title_data = isset($param['step_title']) ? $param['step_title'] : '';
			$step_director_uid_data = isset($param['step_director_uid']) ? $param['step_director_uid'] : '';
			$step_uids_data = isset($param['step_uids']) ? $param['step_uids'] : '';
			$step_cycle_time_data = isset($param['step_cycle_time']) ? $param['step_cycle_time'] : '';
			$step_remark_data = isset($param['step_remark']) ? $param['step_remark'] : '';
			$step_id_data = isset($param['step_id']) ? $param['step_id'] : 0;
			
			$step = [];
			$time_1 = $param['start_time'];
			$time_2 = $param['end_time'];
			if(!empty($step_title_data)){
				foreach ($step_title_data as $key => $value) {
					if (!$value) {
						continue;
					}				
					$step_cycle_time = explode('到',$step_cycle_time_data[$key]);
					$start_time = strtotime(urldecode(trim($step_cycle_time[0])));
					$end_time = strtotime(urldecode(trim($step_cycle_time[1])));
					if($start_time<$time_1){
						return to_assign(1, '第'.($key+1).'阶段的开始时间不能小于项目计划周期的开始时间');
						break;
					}
					if($end_time>$time_2){
						return to_assign(1, '第'.($key+1).'阶段的结束时间不能大于项目计划周期的结束时间');
						break;
					}
					$item = [];
					$item['title'] = $value;
					$item['director_uid'] = $step_director_uid_data[$key];
					$item['uids'] = $step_uids_data[$key];
					$item['sort'] = $key;
					$item['start_time'] = $start_time;
					$item['end_time'] = $end_time;
					$item['remark'] = $step_remark_data[$key];
					$item['id'] = $step_id_data[$key];
					$item['create_time'] = time();
					$step[]=$item;	
				}
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(IndexValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param,$step);
            } else {
                try {
                    validate(IndexValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['status'] = 2;
				$param['step_sort'] = 0;
				$param['admin_id'] = $this->uid;
                $this->model->add($param,$step);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				$detail['current_step'] = Db::name('ProjectStep')->where(['project_id' => $id, 'is_current' => 1,'delete_time'=>0])->value('sort');
				View::assign('detail', $detail);
				return view('edit');
			}
			return view();
		}
    }
	
    /**
    * 查看
    */
    public function view($id)
    {
		$detail = $this->model->getById($id);
		if (!empty($detail)) {
			$detail['status_name'] = status_name($detail['status']);
			$detail['cate'] = Db::name('ProjectCate')->where([['id', '=', $detail['cate_id']]])->value('title');
			$team_admin_ids = Db::name('ProjectUser')->where(['delete_time' => 0,'project_id'=>$id])->column('uid');
            $team_admin_names = Db::name('Admin')->where('id', 'in', $team_admin_ids)->column('name');
            $detail['team_admin_names'] = implode(',', $team_admin_names);
			
			$tids = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']], ['delete_time', '=', 0]])->column('id');
            $detail['schedules'] = Db::name('Schedule')->where([['tid', 'in', $tids], ['delete_time', '=', 0]])->count();
            $detail['hours'] = Db::name('Schedule')->where([['tid', 'in', $tids], ['delete_time', '=', 0]])->sum('labor_time');
            $detail['plan_hours'] = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']], ['delete_time', '=', 0]])->sum('plan_hours');
			
			$detail['tasks'] = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']],['delete_time', '=', 0]])->count();
            $detail['tasks_finish'] = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']],['status', '>', 2], ['delete_time', '=', 0]])->count();
            $detail['tasks_unfinish'] = $detail['tasks'] - $detail['tasks_finish'];
			
            $task_map = [];
            $task_map[] = ['project_id', '=', $detail['id']];
            $task_map[] = ['delete_time', '=', 0];
            //判断是否是创建者或者负责人
            $role = 0;
            if ($detail['admin_id'] == $this->uid) {
                $role = 1; //创建人
            }
            if ($detail['director_uid'] == $this->uid) {
                $role = 2; //负责人
            }
			$auth = isAuth($this->uid,'project_admin','conf_1');
			if ($auth == 1) {
                $role = 3; //项目管理员
            }

			//相关附件
			$file_array = Db::name('ProjectFile')
                ->field('mf.id,mf.topic_id,mf.admin_id,f.id as file_id,f.name,f.filesize,f.filepath,f.fileext,f.create_time,f.admin_id,a.name as admin_name')
                ->alias('mf')
                ->join('File f', 'mf.file_id = f.id', 'LEFT')
                ->join('Admin a', 'mf.admin_id = a.id', 'LEFT')
                ->order('mf.create_time desc')
                ->where(array('mf.topic_id' => $id, 'mf.module' => 'project'))
                ->select()->toArray();
		
			//阶段操作记录			
			$step_record = Db::name('ProjectStepRecord')
				->field('s.*,a.name as check_name,p.title')
				->alias('s')
				->join('Admin a', 'a.id = s.check_uid', 'LEFT')
				->join('ProjectStep p', 'p.id = s.step_id', 'LEFT')
				->order('s.check_time asc')
				->where(array('s.project_id' => $id))
				->select()->toArray();		
			foreach ($step_record as $kk => &$vv) {		
				$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
				$vv['status_str'] = '提交';
				if($vv['status'] == 1){
					$vv['status_str'] = '确认完成';
				}
				else if($vv['status'] == 2){
					$vv['status_str'] = '回退';
				}
				if($vv['status'] == 3){
					$vv['status_str'] = '撤销';
				}
				if($vv['content'] == ''){
					$vv['content'] = '无';
				}
			}
			
			//当前项目阶段
			$step = Db::name('ProjectStep')->where(array('project_id' => $id, 'is_current' => 1,'delete_time'=>0))->find();
			if(!empty($step)){
				$step['director_name'] = Db::name('Admin')->where(['id' => $step['director_uid']])->value('name');
				$unames = Db::name('Admin')->where([['id','in',$step['uids']]])->column('name');
				$step['unames'] = implode(',',$unames);	
			}
            View::assign('role', $role);	
            View::assign('file_array', $file_array);
            View::assign('step', $step);
			View::assign('step_record', $step_record);
            View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/project/view');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到页面']);
		}
    }
	
   /**
    * 删除
    */
    public function del()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
