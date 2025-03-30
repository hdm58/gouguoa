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

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\model\Seal as SealModel;
use app\adm\validate\CarValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Seal extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new SealModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$uid=$this->uid;
            $where = array();
            $whereOr = array();
			$where[]=['delete_time','=',0];
			if($tab == 0){
				//全部
				$whereOr[] = ['admin_id', '=', $this->uid];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
			}
			if($tab == 1){
				//创建的
				$where[] = ['admin_id', '=', $this->uid];
			}
			if($tab == 2){
				//待我审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			}
			if($tab == 3){
				//我已审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			}
			if($tab == 4){
				//抄送给我的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
			}
            if (!empty($param['seal_cate_id'])) {
                $where[] = ['seal_cate_id', '=', $param['seal_cate_id']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($where,$whereOr, $param);
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
			if (!empty($param['use_time'])) {
                $param['use_time'] = strtotime($param['use_time']);
            }
			if (!empty($param['start_time'])) {
                $param['start_time'] = strtotime($param['start_time']);
            }
			else{
				$param['start_time'] = 0;
			}
			if (!empty($param['end_time'])) {
                $param['end_time'] = strtotime($param['end_time']);
				if($param['end_time']<$param['start_time']){
					return to_assign(1, "结束借用日期需要大于等于印章借用日期");
				}
            }
			else{
				$param['end_time'] = 0;
			}
            if (!empty($param['id']) && $param['id'] > 0) {
				$this->model->edit($param);
            } else {
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				if(!empty($detail['file_ids'])){
					$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['file_array'] = $file_array;
				}
				if($detail['check_status']==0 || $detail['check_status']==4){
					View::assign('detail', $detail);
					if(is_mobile()){
						return view('qiye@/approve/add_seal');
					}
					return view('edit');
				}
				return view(EEEOR_REPORTING,['code'=>403,'warning'=>'当前状态不支持编辑']);
			}
			if(is_mobile()){
				return view('qiye@/approve/add_seal');
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
			if(!empty($detail['file_ids'])){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			View::assign('detail', $detail);
			View::assign('create_user', get_admin($detail['admin_id']));
			if(is_mobile()){
				return view('qiye@/approve/view_seal');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['warning'=>'找不到页面']);
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
	
	//用章记录
    public function record()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			$whereOr = [];
			$where[]=['delete_time','=',0];
			$where[]=['check_status','=',2];
            if (!empty($param['seal_cate_id'])) {
                $where[] = ['seal_cate_id', '=', $param['seal_cate_id']];
            }
			if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
			$list = $this->model->datalist($where,$whereOr, $param);
            return table_assign(0, '', $list);
        } else {
			View::assign('status', ['未使用','已使用','已归还']);
            return view();
        }
    }
}
