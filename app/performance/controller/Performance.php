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
 
declare (strict_types = 1);

namespace app\performance\controller;

use app\base\BaseController;
use app\performance\model\Performance as PerformanceModel;
use app\performance\validate\IndexValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Performance extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PerformanceModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
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
			if (!empty($param['range_time'])) {
				$range_time = explode('~', $param['range_time']);
				$param['start_time'] = strtotime(urldecode($range_time[0]));
				$param['end_time'] = strtotime(urldecode($range_time[1]));
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(IndexValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(IndexValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }				
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail=[];
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				return view('edit');
			}
			else{
				$users = Db::name('PerformanceUsers')->where(['delete_time'=>0])->select()->toArray();
				foreach ($users as $key => &$value) {
					$user = Db::name('Admin')->where('id',$value['uid'])->find();
					$value['uid'] = $user['id'];
					$value['u_name'] = $user['name'];
					$value['department'] = Db::name('Department')->where('id',$user['did'])->value('title');
					$value['position'] = Db::name('Position')->where('id',$user['position_id'])->value('title');
					$value['kpi_uname'] = Db::name('Admin')->where('id',$value['kpi_uid'])->value('name');
					$value['action_uname'] = Db::name('Admin')->where('id',$value['action_uid'])->value('name');
					$value['event_uname'] = Db::name('Admin')->where('id',$value['event_uid'])->value('name');
					$value['check_uname'] = Db::name('Admin')->where('id',$value['check_uid'])->value('name');
				}			
				$detail['users'] = $users;
			}
			View::assign('detail', $detail);
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
			if($detail['status']>3){
				$detail['count_a'] = Db::name('PerformanceRecords')->where([['performance_id','=',$id,'delete_time','=',0,'status','=',4]])->count();
				$detail['count_b'] = Db::name('PerformanceRecords')->where([['performance_id','=',$id,'delete_time','=',0,'status','<',4]])->count();
			}
			$detail['range_time'] = to_date($detail['start_time'],'Y-m-d').' 至 '.to_date($detail['end_time'],'Y-m-d');
			View::assign('detail', $detail);
			View::assign('role_auth', isAuth($this->uid,'human_admin','conf_2'));
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
		if (request()->isDelete()) {
			$this->model->delById($param['id']);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
