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
use app\performance\model\PerformanceTemplates as PerformanceTemplatesModel;
use app\performance\validate\TemplatesValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Templates extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PerformanceTemplatesModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();	
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
			
			$kpi_title = isset($param['kpi_title']) ? $param['kpi_title'] : '';
			$kpi_content = isset($param['kpi_content']) ? $param['kpi_content'] : '';
			$kpi_weight = isset($param['kpi_weight']) ? $param['kpi_weight'] : 0;
			$kpi_goal = isset($param['kpi_goal']) ? $param['kpi_goal'] : '';
			$kpi_standard = isset($param['kpi_standard']) ? $param['kpi_standard'] : '';
			$kpi_max = isset($param['kpi_max']) ? $param['kpi_max'] : 100;
			$kpi_frequency = isset($param['kpi_frequency']) ? $param['kpi_frequency'] : '';				
			$kpi_serialize = [];
			if(!empty($kpi_title)){
				foreach ($kpi_title as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['kpi_title'] = $kpi_title[$key];
					$data['kpi_content'] = $kpi_content[$key];
					$data['kpi_weight'] = $kpi_weight[$key];
					$data['kpi_goal'] = $kpi_goal[$key];
					$data['kpi_standard'] = $kpi_standard[$key];
					$data['kpi_max'] = $kpi_max[$key];
					$data['kpi_frequency'] = $kpi_frequency[$key];
					$kpi_serialize[]=$data;
				}
			}
			$param['kpi_serialize'] = serialize($kpi_serialize);
			
			//
			$action_title = isset($param['action_title']) ? $param['action_title'] : '';
			$action_weight = isset($param['action_weight']) ? $param['action_weight'] : 0;
			$action_standard = isset($param['action_standard']) ? $param['action_standard'] : '';
			$action_max = isset($param['action_max']) ? $param['action_max'] : 100;
			$action_frequency = isset($param['action_frequency']) ? $param['action_frequency'] : '';			
			$action_serialize = [];
			if(!empty($action_title)){
				foreach ($action_title as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['action_title'] = $action_title[$key];
					$data['action_weight'] = $action_weight[$key];
					$data['action_standard'] = $action_standard[$key];
					$data['action_max'] = $action_max[$key];
					$data['action_frequency'] = $action_frequency[$key];
					$action_serialize[]=$data;
				}
			}
			$param['action_serialize'] = serialize($action_serialize);
			
			//
			$event_title = isset($param['event_title']) ? $param['event_title'] : '';
			$event_standard = isset($param['event_standard']) ? $param['event_standard'] : '';		
			$event_serialize = [];
			if(!empty($event_title)){
				foreach ($event_title as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['event_title'] = $event_title[$key];
					$data['event_standard'] = $event_standard[$key];
					$event_serialize[]=$data;
				}
			}
			$param['event_serialize'] = serialize($event_serialize);
			
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(TemplatesValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(TemplatesValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
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
			$users = Db::name('PerformanceUsers')->where(['template_id'=>$detail['id'],'delete_time'=>0])->select()->toArray();
			foreach ($users as $key => &$value) {
				$user = Db::name('Admin')->where('id',$value['uid'])->find();
				$value['u_name'] = $user['name'];
				$value['department'] = Db::name('Department')->where('id',$user['did'])->value('title');
				$value['position'] = Db::name('Position')->where('id',$user['position_id'])->value('title');
				$value['kpi_uname'] = Db::name('Admin')->where('id',$value['kpi_uid'])->value('name');
				$value['action_uname'] = Db::name('Admin')->where('id',$value['action_uid'])->value('name');
				$value['event_uname'] = Db::name('Admin')->where('id',$value['event_uid'])->value('name');
				$value['check_uname'] = Db::name('Admin')->where('id',$value['check_uid'])->value('name');
			}			
			$detail['users'] = $users;
			
			View::assign('detail', $detail);
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
		if (request()->isDelete()) {
			$param = get_params();
			$count_cate = Db::name('PerformanceUsers')->where(['template_id'=>$param['id'],'delete_time'=>0])->count();
			if ($count_cate > 0) {
				return to_assign(1, "该模板已经关联员工使用，请取消关联再删除");
			}
			$this->model->delById($param['id']);
		} else {
            return to_assign(1, "错误的请求");
        }
    }

    /**
    * 设置
    */
    public function set()
    {
		if (request()->isAjax()) {
			$param = get_params();
			if($param['status'] == 0){
				$count_cate = Db::name('PerformanceUsers')->where(['template_id'=>$param['id'],'delete_time'=>0])->count();
				if ($count_cate > 0) {
					return to_assign(1, "该模板已经关联员工使用，请取消关联再禁用");
				}
				$this->model->strict(false)->field('id,status')->update($param);
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				$res = $this->model->strict(false)->field('id,status')->update($param);
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
