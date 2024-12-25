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

namespace app\contract\controller;

use app\base\BaseController;
use app\contract\model\Purchase as PurchaseModel;
use app\contract\validate\PurchaseValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Purchase extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PurchaseModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$uid = $this->uid;
            $where = [];
            $whereOr = [];
			$where[]=['delete_time','=',0];
			$where[]=['archive_time','=',0];
			$where[]=['stop_time','=',0];
			$where[]=['void_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|name|code', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=',$param['types']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=',$param['cate_id']];
            }
			if (!empty($param['check_status'])) {
                $where[] = ['check_status', '=',$param['check_status']];
            }
			if (!empty($param['sign_time'])) {
				$sign_time =explode('~', $param['sign_time']);
				$where[] = ['sign_time', 'between',[strtotime(urldecode($sign_time[0])),strtotime(urldecode($sign_time[1].' 23:59:59'))]];
            }
			if (!empty($param['end_time'])) {
				$end_time =explode('~', $param['end_time']);
				$where[] = ['end_time', 'between',[strtotime(urldecode($end_time[0])),strtotime(urldecode($end_time[1].' 23:59:59'))]];
            }
			if($tab == 0){
				if (!empty($param['uid'])) {
					$where[] = ['sign_uid', '=', $param['uid']];
				}
				else{
					//是否是客户管理员
					$auth = isAuth($uid,'customer_admin','conf_1');
					if($auth == 0){
						$whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
						$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
						$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
						$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
						$dids_a = get_leader_departments($uid);
						$dids_b = get_role_departments($uid);
						$dids = array_merge($dids_a, $dids_b);
						if(!empty($dids)){
							$whereOr[] = ['did','in',$dids];
						}
					}
				}
			}
			if($tab == 1){
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			}
			if($tab == 2){
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('is_leader', isLeader($this->uid));
			View::assign('is_auth', isAuth($this->uid,'contract_admin','conf_1'));
			View::assign('delay_num', valueAuth('contract_admin','conf_10'));
            return view();
        }
    }
	
	public function archivelist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid = $this->uid;
            $where = [];
            $whereOr = [];
			$where[]=['delete_time','=',0];
			$where[]=['archive_time','>',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|name|code', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=',$param['types']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=',$param['cate_id']];
            }
			if (!empty($param['sign_time'])) {
				$sign_time =explode('~', $param['sign_time']);
				$where[] = ['sign_time', 'between',[strtotime(urldecode($sign_time[0])),strtotime(urldecode($sign_time[1].' 23:59:59'))]];
            }
			if (!empty($param['end_time'])) {
				$end_time =explode('~', $param['end_time']);
				$where[] = ['end_time', 'between',[strtotime(urldecode($end_time[0])),strtotime(urldecode($end_time[1].' 23:59:59'))]];
            }
            $whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			
			$dids_a = get_leader_departments($uid);
			//是否是合同管理员
			$auth = isAuth($uid,'contract_admin','conf_1');
			if($auth == 1){
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
			}
			else{
				if(!empty($dids_a)){
					$whereOr[] = ['did','in',$dids_a];
				}
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('delay_num', valueAuth('contract_admin','conf_10'));
            return view();
        }
    }
	
	public function stoplist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid = $this->uid;
            $where = [];
            $whereOr = [];
			$where[]=['delete_time','=',0];
			$where[]=['stop_time','>',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|name|code', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=',$param['types']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=',$param['cate_id']];
            }
			if (!empty($param['sign_time'])) {
				$sign_time =explode('~', $param['sign_time']);
				$where[] = ['sign_time', 'between',[strtotime(urldecode($sign_time[0])),strtotime(urldecode($sign_time[1].' 23:59:59'))]];
            }
			if (!empty($param['end_time'])) {
				$end_time =explode('~', $param['end_time']);
				$where[] = ['end_time', 'between',[strtotime(urldecode($end_time[0])),strtotime(urldecode($end_time[1].' 23:59:59'))]];
            }
            $whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			
			$dids_a = get_leader_departments($uid);
			//是否是合同管理员
			$auth = isAuth($uid,'contract_admin','conf_1');
			if($auth == 1){
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
			}
			else{
				if(!empty($dids_a)){
					$whereOr[] = ['did','in',$dids_a];
				}
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
	public function voidlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid = $this->uid;
            $where = [];
            $whereOr = [];
			$where[]=['delete_time','=',0];
			$where[]=['void_time','>',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|name|code', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=',$param['types']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=',$param['cate_id']];
            }
			if (!empty($param['sign_time'])) {
				$sign_time =explode('~', $param['sign_time']);
				$where[] = ['sign_time', 'between',[strtotime(urldecode($sign_time[0])),strtotime(urldecode($sign_time[1].' 23:59:59'))]];
            }
			if (!empty($param['end_time'])) {
				$end_time =explode('~', $param['end_time']);
				$where[] = ['end_time', 'between',[strtotime(urldecode($end_time[0])),strtotime(urldecode($end_time[1].' 23:59:59'))]];
            }
            $whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			
			$dids_a = get_leader_departments($uid);
			//是否是合同管理员
			$auth = isAuth($uid,'contract_admin','conf_1');
			if($auth == 1){
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
			}
			else{
				if(!empty($dids_a)){
					$whereOr[] = ['did','in',$dids_a];
				}
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
			if (isset($param['sign_time'])) {
                $param['sign_time'] = strtotime($param['sign_time']);
            }
			if (isset($param['start_time'])) {
                $param['start_time'] = strtotime($param['start_time']);
            }
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime($param['end_time']);
				if ($param['end_time'] <= $param['start_time']) {
					return to_assign(1, "结束时间需要大于开始时间");
				}
            }
			if($param['scene'] == 'add' || $param['scene'] == 'edit'){
				$param['content'] = serialize([]);
				if($param['types']==2){			
					$purchased_title_data = isset($param['purchased_title']) ? $param['purchased_title'] : '';
					$purchased_id_data = isset($param['purchased_id']) ? $param['purchased_id'] : 0;
					$purchased_unit_data = isset($param['purchased_unit']) ? $param['purchased_unit'] : '';
					$purchased_specs_data = isset($param['purchased_specs']) ? $param['purchased_specs'] : '';
					$purchased_price_data = isset($param['purchased_price']) ? $param['purchased_price'] : '0.00';
					$purchased_num_data = isset($param['purchased_num']) ? $param['purchased_num'] : 1;
					$purchased_subtotal_data = isset($param['purchased_subtotal']) ? $param['purchased_subtotal'] : '0.00';
					$purchased_remark_data = isset($param['purchased_remark']) ? $param['purchased_remark'] : '';				
					$purchased = [];
					if(!empty($purchased_title_data)){
						foreach ($purchased_title_data as $key => $value) {
							if (!$value) {
								continue;
							}
							$data = [];
							$data['purchased_title'] = $purchased_title_data[$key];
							$data['purchased_id'] = $purchased_id_data[$key];
							$data['purchased_unit'] = $purchased_unit_data[$key];
							$data['purchased_specs'] = $purchased_specs_data[$key];
							$data['purchased_price'] = $purchased_price_data[$key];
							$data['purchased_num'] = $purchased_num_data[$key];
							$data['purchased_subtotal'] = $purchased_subtotal_data[$key];
							$data['purchased_remark'] = $purchased_remark_data[$key];
							$purchased[]=$data;
						}
					}
					$param['content'] = serialize($purchased);
				}	
				if($param['types']==3){			
					$service_title_data = isset($param['service_title']) ? $param['service_title'] : '';
					$service_date_data = isset($param['service_date']) ? $param['service_date'] : '';
					$service_price_data = isset($param['service_price']) ? $param['service_price'] : '0.00';
					$service_num_data = isset($param['service_num']) ? $param['service_num'] : 1;
					$service_subtotal_data = isset($param['service_subtotal']) ? $param['service_subtotal'] : '0.00';
					$service_remark_data = isset($param['service_remark']) ? $param['service_remark'] : '';				
					$service = [];
					if(!empty($service_title_data)){
						foreach ($service_title_data as $key => $value) {
							if (!$value) {
								continue;
							}
							$data = [];
							$data['service_title'] = $service_title_data[$key];
							$data['service_date'] = $service_date_data[$key];
							$data['service_price'] = $service_price_data[$key];
							$data['service_num'] = $service_num_data[$key];
							$data['service_subtotal'] = $service_subtotal_data[$key];
							$data['service_remark'] = $service_remark_data[$key];
							$service[]=$data;
						}
					}
					$param['content'] = serialize($service);
				}
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(PurchaseValidate::class)->scene($param['scene'])->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(PurchaseValidate::class)->scene($param['scene'])->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			if(is_mobile()){
				return view('qiye@/index/405',['msg' => '由于合同太多字段，手机端不方便操作，请到PC端新增合同']);
			}
			$id = isset($param['id']) ? $param['id'] : 0;
			$types = isset($param['types']) ? $param['types'] : 0;
			$is_supplier = Db::name('DataAuth')->where('name','contract_admin')->value('conf_6');
			$is_codeno = Db::name('DataAuth')->where('name','contract_admin')->value('conf_2');
			$is_purchased = Db::name('DataAuth')->where('name','contract_admin')->value('conf_7');
            View::assign('is_supplier', $is_supplier);
            View::assign('is_codeno', $is_codeno);
            View::assign('is_purchased', $is_purchased);
			if ($id>0) {
				$detail = $this->model->getById($id);
				if($detail['check_status'] == 1 || $detail['check_status'] == 2 || $detail['check_status'] == 3){
					return view(EEEOR_REPORTING,['code'=>403,'warning'=>'当前状态不支持编辑']);
				}
				$detail['content'] = unserialize($detail['content']);
				View::assign('detail', $detail);
				return view('edit');
			}
			$codeno='';
			if($is_codeno==1){
				$codeno = get_codeno(2);
			}
            View::assign('codeno', $codeno);
            View::assign('id', $id);
            View::assign('types', $types);
			if($types == 0){
				return view('add_types');
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
			if($detail['types'] > 1){
				$detail['content'] = unserialize($detail['content']);
			}
			$detail['status_name'] = check_status_name($detail['check_status']);
			$detail['cate_title'] = Db::name('ContractCate')->where(['id' => $detail['cate_id']])->value('title');
			$detail['subject_title'] = Db::name('Enterprise')->where(['id' => $detail['subject_id']])->value('title');
			//归档信息
			if($detail['archive_uid'] > 0){
				$detail['archive_name'] = Db::name('Admin')->where(['id' => $detail['archive_uid']])->value('name');
			}
			//中止信息
			if($detail['stop_uid'] > 0){
				$detail['stop_name'] = Db::name('Admin')->where(['id' => $detail['stop_uid']])->value('name');
			}
			//作废信息
			if($detail['void_uid'] > 0){
				$detail['void_name'] = Db::name('Admin')->where(['id' => $detail['void_uid']])->value('name');
			}
			$auth = isAuth($this->uid,'contract_admin','conf_1');
			View::assign('detail', $detail);
			View::assign('auth', $auth);
			if(is_mobile()){
				return view('qiye@/contract/purchase_view');
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
