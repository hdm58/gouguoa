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
use app\project\model\ProjectTask;
use app\api\model\Comment;
use app\oa\model\Schedule;
use app\api\model\EditLog;
use app\project\validate\TaskCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Task extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ProjectTask();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
		$uid = $this->uid;
		$auth = isAuth($uid,'project_admin','conf_1');
        if (request()->isAjax()) {
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$time = time();
			$time = time();
			$dalay_time = time()+7*86400;
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
			if (!empty($param['keywords'])) {
				$where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
			}
			if(!empty($param['project_id'])){
				$where[] = ['project_id', '=', $param['project_id']];			
			}
			if (!empty($param['director_uid'])) {
				$where[] = ['director_uid', 'in', $param['director_uid']];
			}
			else{
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
			if($tab==1){
				//进行中
				$where[] = ['status', '<', 3];
			}
			if($tab==2){
				//即将逾期
				$where[] = ['status', '<', 3];
				$where[] = ['end_time','between',[$time,$dalay_time]];
			}
			if($tab==3){
				//已逾期
				$where[] = ['status', '<', 3];
				$where[] = ['end_time','<',$time];
			}			
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$order = empty($param['order']) ? 'status asc,id desc' : $param['order'];
			$list = $this->model->datalist($param,$where,$whereOr);
			return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', $auth);
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime(urldecode($param['end_time']));
            }
			if (isset($param['status'])) {
                if ($param['status'] == 3) {
                    $param['over_time'] = time();
                    $param['done_ratio'] = 100;
                } else {
                    $param['over_time'] = 0;
					$param['done_ratio'] = 10;
                }
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $old = $this->model->detail($param['id']);
                try {
                    validate(TaskCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
				if(!empty($param['director_uid'])){
					$param['did'] = Db::name('Admin')->where(['id' => $param['director_uid']])->value('did');
				}
                $res = ProjectTask::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
					$log=new EditLog();
					$log->edit('Task',$param['id'],$param,$old);
                }
                return to_assign();
            } else {
                try {
                    validate(TaskCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
				if(empty($param['director_uid'])){
					$param['did'] = $this->did;
				}
				else{
					$param['did'] = Db::name('Admin')->where(['id' => $param['director_uid']])->value('did');
				}
                $insertId = ProjectTask::strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
					$log=new EditLog();
					$log->add('Task',$insertId);
					//发消息
					//event('SendMessage',$msg);
                }
                return to_assign();
            }
        } else {
            if (isset($param['project_id'])) {
                View::assign('project_id', $param['project_id']);
            }
            return view();
        }
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = (new ProjectTask())->detail($id);
		$role_uid = [$detail['admin_id'], $detail['director_uid']];
		$role_edit = 'view';
		if (in_array($this->uid, $role_uid)) {
			$role_edit = 'edit';
		}
		$project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
		$auth = isAuth($this->uid,'project_admin','conf_1');
		if (in_array($detail['project_id'], $project_ids) || in_array($this->uid, $role_uid) || in_array($this->uid, explode(",",$detail['assist_admin_ids'])) || $auth==1) {
			$file_array = Db::name('ProjectFile')
			->field('mf.id,mf.topic_id,mf.admin_id,mf.file_id,f.name,f.filesize,f.filepath,f.fileext,f.create_time,f.admin_id,a.name as admin_name')
			->alias('mf')
			->join('File f', 'mf.file_id = f.id', 'LEFT')
			->join('Admin a', 'mf.admin_id = a.id', 'LEFT')
			->order('mf.create_time desc')
			->where(array('mf.topic_id' => $id, 'mf.module' => 'task'))
			->select()->toArray();

			View::assign('detail', $detail);
			View::assign('file_array', $file_array);
			View::assign('role_edit', $role_edit);
			View::assign('id', $id);
			if(is_mobile()){
				return view('qiye@/project/task_view');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>405,'warning'=>'无权限访问']);
		}
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
			$auth = isAuth($this->uid,'project_admin','conf_1');
            $detail = Db::name('ProjectTask')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid && $auth==0) {
                return to_assign(1, "你不是该任务的创建人，无权限删除");
            }
            if (Db::name('ProjectTask')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	public function hour() {
        if (request()->isAjax()) {
            $param = get_params();
			$uid = $this->uid;
			$auth = isAuth($uid,'project_admin','conf_1');
            $tid = isset($param['tid']) ? $param['tid'] : 0;
            $where = [];
            $whereOr = [];
			
            //按时间检索
			if (!empty($param['range_time'])) {
				$range_time =explode('至', $param['range_time']);
				$where[] = ['a.start_time', 'between',[strtotime($range_time[0]),strtotime($range_time[1])]];
			}
			
			if ($tid>0) {
                $task_ids = Db::name('ProjectTask')->where(['delete_time' => 0, 'project_id' => $param['tid']])->column('id');
				$where[] = ['a.tid', 'in', $task_ids];
            }
			else{
				$where[] = ['a.tid', '>', 0];
				if (!empty($param['keywords'])) {
					$where[] = ['a.title', 'like', '%' . trim($param['keywords']) . '%'];
				}
				if($auth == 0){
					if (!empty($param['uid'])) {
						$where[] = ['a.admin_id', '=', $param['uid']];
					} else {
						$whereOr[] = ['a.admin_id', '=', $uid];
						$dids_a = get_leader_departments($uid);	
						$dids_b = get_role_departments($uid);
						$dids = array_merge($dids_a, $dids_b);
						if(!empty($dids)){
							$whereOr[] = ['a.did','in',$dids];
						}
					}
				}
			}
            $where[] = ['a.delete_time', '=', 0];
			
			$model = new Schedule();
			$list = $model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	public function comment() {
        if (request()->isAjax()) {
            $param = get_params();
			$param['admin_id'] = $this->uid;
			if (!empty($param['uid'])) {
				$where[] = ['admin_id', '=', $param['uid']];
			}
			if (!empty($param['keywords'])) {
				$where[] = ['content', 'like', '%' . trim($param['keywords']) . '%'];
			}
			if (!empty($param['module'])) {
				$where[] = ['module', '=', $param['module']];
			}
			if (!empty($param['topic_id'])) {
				$where['topic_id'] = $param['topic_id'];
			}			
            $where[] = ['delete_time', '=', 0];
			$model = new Comment();
            $list = $model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
}
