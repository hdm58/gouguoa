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
use app\performance\model\PerformanceRecords as PerformanceRecordsModel;
use app\performance\validate\RecordsValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Records extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PerformanceRecordsModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if($tab==0){
				$where[]=['uid','=',$this->uid];
			}
			if($tab==1){
				$where[]=['status','in',[1,3]];
				$map1 =[];
				$map1[]=['kpi_uid','=',$this->uid];
				$map1[]=['kpi_check_time','=',0];	
				$map2 =[];
				$map2[]=['action_uid','=',$this->uid];
				$map2[]=['action_check_time','=',0];	
				$map3 =[];
				$map3[]=['event_uid','=',$this->uid];		
				$map3[]=['event_check_time','=',0];		
				$whereOr =[$map1,$map2];
			}
			if($tab==2){
				$where[]=['status','=',2];
				$where[]=['check_uid','=',$this->uid];
			}
			if($tab==3){
				$where[]=['status','=',2];
				$map1 =[];
				$map1[]=['kpi_uid','=',$this->uid];
				$map2 =[];
				$map2[]=['action_uid','=',$this->uid];		
				$whereOr =[$map1,$map2];
			}
			if($tab==4){
				$where[]=['status','in',[3,4]];
				$where[]=['check_uid','=',$this->uid];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
			$where[]=['status','in',[1,3]];
			$map1 =[];
			$map1[]=['kpi_uid','=',$this->uid];
			$map1[]=['kpi_check_time','=',0];	
			$map2 =[];
			$map2[]=['action_uid','=',$this->uid];
			$map2[]=['action_check_time','=',0];	
			$map3 =[];
			$map3[]=['event_uid','=',$this->uid];		
			$map3[]=['event_check_time','=',0];		
			$whereOr =[$map1,$map2];
			
			$count_a = $this->model::where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})->count();
				
			$count_b = $this->model::where([['delete_time','=',0],['status','=',2],['check_uid','=',$this->uid]])->count();
			View::assign('tab', $tab);
			View::assign('count_a', $count_a);
			View::assign('count_b', $count_b);
            return view('datalist'.$tab);
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
                    validate(RecordsValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				//$param['status'] = 1;
				$this->model->edit($param);
            } else {
                try {
                    validate(RecordsValidate::class)->scene('add')->check($param);
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
    * 填写
    */
    public function edit()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$kpi_title = isset($param['kpi_title']) ? $param['kpi_title'] : '';
			$kpi_content = isset($param['kpi_content']) ? $param['kpi_content'] : '';
			$kpi_weight = isset($param['kpi_weight']) ? $param['kpi_weight'] : 0;
			$kpi_goal = isset($param['kpi_goal']) ? $param['kpi_goal'] : '';
			$kpi_standard = isset($param['kpi_standard']) ? $param['kpi_standard'] : '';
			$kpi_finish = isset($param['kpi_finish']) ? $param['kpi_finish'] : 0;
			$kpi_max = isset($param['kpi_max']) ? $param['kpi_max'] : 100;
			$kpi_frequency = isset($param['kpi_frequency']) ? $param['kpi_frequency'] : '';				
			$kpi_score = isset($param['kpi_score']) ? $param['kpi_score'] : '';				
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
					$data['kpi_finish'] = $kpi_finish[$key];
					$data['kpi_max'] = $kpi_max[$key];
					$data['kpi_frequency'] = $kpi_frequency[$key];
					$data['kpi_score'] = $kpi_score[$key];
					$kpi_serialize[]=$data;
				}
			}
			$param['kpi_serialize'] = serialize($kpi_serialize);
			
			//
			$action_title = isset($param['action_title']) ? $param['action_title'] : '';
			$action_weight = isset($param['action_weight']) ? $param['action_weight'] : 0;
			$action_standard = isset($param['action_standard']) ? $param['action_standard'] : '';
			$action_max = isset($param['action_max']) ? $param['action_max'] : 100;
			$action_task = isset($param['action_task']) ? $param['action_task'] : 0;
			$action_content = isset($param['action_content']) ? $param['action_content'] : '';			
			$action_frequency = isset($param['action_frequency']) ? $param['action_frequency'] : '';			
			$action_score = isset($param['action_score']) ? $param['action_score'] : '';			
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
					$data['action_task'] = $action_task[$key];
					$data['action_content'] = $action_content[$key];
					$data['action_frequency'] = $action_frequency[$key];
					$data['action_score'] = $action_score[$key];
					$action_serialize[]=$data;
				}
			}
			$param['action_serialize'] = serialize($action_serialize);
			
			//
			$event_title = isset($param['event_title']) ? $param['event_title'] : '';
			$event_standard = isset($param['event_standard']) ? $param['event_standard'] : '';		
			$event_num = isset($param['event_num']) ? $param['event_num'] : 0;		
			$event_content = isset($param['event_content']) ? $param['event_content'] : 0;		
			$event_score = isset($param['event_score']) ? $param['event_score'] : '';		
			$event_serialize = [];
			if(!empty($event_title)){
				foreach ($event_title as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['event_title'] = $event_title[$key];
					$data['event_standard'] = $event_standard[$key];
					$data['event_content'] = $event_content[$key];
					$data['event_num'] = $event_num[$key];
					$data['event_score'] = $event_score[$key];
					$event_serialize[]=$data;
				}
			}
			$param['event_serialize'] = serialize($event_serialize);
			
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(RecordsValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if(!empty($param['kpi_check_time']) && !empty($param['action_check_time']) && !empty($param['event_check_time'])){
					$param['status'] = 2;
				}
				$this->model->edit($param);
            } else {
                try {
                    validate(RecordsValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $this->model->add($param);
            }
		}
		else{
			$vip = isset($param['vip']) ? $param['vip'] : 0;
			$detail = $this->model->getById($param['id']);
			$is_kpi=0;
			$is_action=0;
			$is_event=0;
			if($detail['kpi_uid'] == $this->uid){
				$is_kpi=1;
			}
			if($detail['action_uid'] == $this->uid){
				$is_action=1;
			}
			if($detail['event_uid'] == $this->uid){
				$is_event=1;
			}
			View::assign('is_kpi', $is_kpi);
			View::assign('is_action', $is_action);
			View::assign('is_event', $is_event);
			View::assign('detail', $detail);
			if($vip>0){
				return view('edit_vip');
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
			$detail = $this->model->getById($id);
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/index/performance_view');
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
    public function del($id)
    {
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
