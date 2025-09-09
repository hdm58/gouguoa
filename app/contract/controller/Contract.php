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
use app\contract\model\Contract as ContractModel;
use app\contract\validate\ContractValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Contract extends BaseController
{
	/**
     * 构造函数
     */
	protected $model; 
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ContractModel();
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
			if (isset($param['check_status']) && $param['check_status']!='') {
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
					//是否是合同管理员
					$auth = isAuth($uid,'contract_admin','conf_1');
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
			//是否是合同管理员
			$auth = isAuth($uid,'contract_admin','conf_1');
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
			//是否是合同管理员
			$auth = isAuth($uid,'contract_admin','conf_1');
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
			//是否是合同管理员
			$auth = isAuth($uid,'contract_admin','conf_1');
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
					$product_title_data = isset($param['product_title']) ? $param['product_title'] : '';
					$product_id_data = isset($param['product_id']) ? $param['product_id'] : 0;
					$product_unit_data = isset($param['product_unit']) ? $param['product_unit'] : '';
					$product_specs_data = isset($param['product_specs']) ? $param['product_specs'] : '';
					$product_price_data = isset($param['product_price']) ? $param['product_price'] : '0.00';
					$product_num_data = isset($param['product_num']) ? $param['product_num'] : 1;
					$product_subtotal_data = isset($param['product_subtotal']) ? $param['product_subtotal'] : '0.00';
					$product_remark_data = isset($param['product_remark']) ? $param['product_remark'] : '';				
					$product = [];
					if(!empty($product_title_data)){
						foreach ($product_title_data as $key => $value) {
							if (!$value) {
								continue;
							}
							$data = [];
							$data['product_title'] = $product_title_data[$key];
							$data['product_id'] = $product_id_data[$key];
							$data['product_unit'] = $product_unit_data[$key];
							$data['product_specs'] = $product_specs_data[$key];
							$data['product_price'] = $product_price_data[$key];
							$data['product_num'] = $product_num_data[$key];
							$data['product_subtotal'] = $product_subtotal_data[$key];
							$data['product_remark'] = $product_remark_data[$key];
							$product[]=$data;
						}
					}
					$param['content'] = serialize($product);
				}	
				if($param['types']==3){			
					$service_title_data = isset($param['service_title']) ? $param['service_title'] : '';
					$service_id_data = isset($param['service_id']) ? $param['service_id'] : 0;
					$service_time_a_data = isset($param['service_time_a']) ? $param['service_time_a'] : '';
					$service_time_b_data = isset($param['service_time_b']) ? $param['service_time_b'] : '';
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
							$data['service_id'] = $service_id_data[$key];
							if(!empty($service_time_a_data)){
								$data['service_date'] = $service_time_a_data[$key].'到'.$service_time_b_data[$key];
							}
							else{
								$data['service_date'] = $service_date_data[$key];
							}
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
                    validate(ContractValidate::class)->scene($param['scene'])->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if(isset($param['chance_id']) && $param['chance_id']>0){
					$has_chance = Db::name('Contract')->where([['chance_id','=',$param['chance_id']],['delete_time','=',0],['id','<>',$param['id']]])->count();
					if($has_chance>0){
						return to_assign(1, '所选的机会线索已关联有销售合同，不支持关联多合同');
					}
				}
				$this->model->edit($param);
            } else {
                try {
                    validate(ContractValidate::class)->scene($param['scene'])->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if($param['chance_id']>0){
					$has_chance = Db::name('Contract')->where(['chance_id'=>$param['chance_id'],'delete_time'=>0])->count();
					if($has_chance>0){
						return to_assign(1, '所选的机会线索已关联有销售合同，不支持关联多合同');
					}
				}
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$types = isset($param['types']) ? $param['types'] : 0;
			$is_customer = Db::name('DataAuth')->where('name','contract_admin')->value('conf_3');
			$is_codeno = Db::name('DataAuth')->where('name','contract_admin')->value('conf_2');
			$is_product = Db::name('DataAuth')->where('name','contract_admin')->value('conf_4');
			$is_service = Db::name('DataAuth')->where('name','contract_admin')->value('conf_5');
            View::assign('is_customer', $is_customer);
            View::assign('is_codeno', $is_codeno);
			View::assign('is_product', $is_product);
			View::assign('is_service', $is_service);
			if ($id>0) {
				$detail = $this->model->getById($id);
				if($detail['check_status'] == 1 || $detail['check_status'] == 2 || $detail['check_status'] == 3){
					return view(EEEOR_REPORTING,['code'=>403,'warning'=>'当前状态不支持编辑']);
				}
				if($detail['types'] > 1){
					$content_array = unserialize($detail['content']);
					if($detail['types']==3){
						foreach ($content_array as $key => &$value) {
							if(!empty($value['service_date'])){
								$service_date = explode('到', $value['service_date']);
								$value['service_time_a'] = $service_date[0];
								$value['service_time_b'] = $service_date[1];
							}
						}
					}
					$detail['content_array'] = $content_array;
				}
				View::assign('types', $detail['types']);
				View::assign('codeno', $detail['code']);
				View::assign('detail', $detail);
				if(is_mobile()){
					return view('qiye@/contract/contract_add');
				}
				return view('edit');
			}
			$codeno='';
			if($is_codeno==1){
				$codeno = get_codeno(1);
			}
            View::assign('codeno', $codeno);
            View::assign('id', $id);
            View::assign('types', $types);
			if($types == 0){
				return view('add_types');
			}
			if(is_mobile()){
				return view('qiye@/contract/contract_add');
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
				$content_array = unserialize($detail['content']);
				if($detail['types']==3){
					foreach ($content_array as $key => &$value) {
						if(!empty($value['service_date'])){
							$service_date = explode('到', $value['service_date']);
							$value['service_time_a'] = $service_date[0];
							$value['service_time_b'] = $service_date[1];
						}
					}
				}
				$detail['content_array'] = $content_array;
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
			
			//开票和回款信息
			if($detail['check_status'] == 2){
				$detail['invoice'] = Db::name('Invoice')->field('i.*,a.name as admin')
					->alias('i')
					->join('Admin a', 'a.id = i.admin_id', 'LEFT')
					->where([['i.contract_id','=',$id],['i.open_status','<',2],['i.invoice_type','>',0],['i.delete_time','=',0]])
					->order('i.create_time desc')
					->select();
					
				$has_invoice = Db::name('invoice')->where([['contract_id','=',$id],['open_status','<',2],['invoice_type','>',0],['delete_time','=',0]])->sum('amount');
				$detail['has_invoice'] = sprintf("%.2f",$has_invoice);
				$detail['no_invoice'] = sprintf("%.2f",($detail['cost']*100 - $has_invoice*100)/100);	
					
					
				$invoice_ids = 	Db::name('Invoice')->where([['contract_id','=',$id],['open_status','<',2],['delete_time','=',0]])->column('id');
				$detail['income'] = Db::name('InvoiceIncome')->field('i.*,a.name as admin')
					->alias('i')
					->join('Admin a', 'a.id = i.admin_id', 'LEFT')
					->where([['i.invoice_id','in',$invoice_ids],['i.status','=',1]])
					->order('i.enter_time desc')
					->select();
					
				$has_income = Db::name('InvoiceIncome')->where([['invoice_id','in',$invoice_ids],['status','=',1]])->sum('amount');
				$detail['has_income'] = sprintf("%.2f",$has_income);
				$detail['no_income'] = sprintf("%.2f",($detail['cost']*100 - $has_income*100)/100);
			}
			
			if($detail['seal_ids'] !=''){
				$file_array = Db::name('File')->where('id','in',$detail['seal_ids'])->select()->toArray();
				$detail['seal_array'] = $file_array;
			}
			else{
				$detail['seal_array'] = [];
			}
			
			$auth = isAuth($this->uid,'contract_admin','conf_1');
			View::assign('detail', $detail);
			View::assign('auth', $auth);
			if(is_mobile()){
				return view('qiye@/contract/contract_view');
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
