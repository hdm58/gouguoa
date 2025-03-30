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

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Invoice as InvoiceModel;
use app\finance\validate\InvoiceValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Invoice extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new InvoiceModel();
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
			$where[]=['invoice_type','>',0];
			if($tab == 0){
				$auth = isAuthInvoice($uid);
				if($auth == 0){
					$whereOr[] = ['admin_id', '=', $this->uid];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
					$dids_a = get_leader_departments($uid);	
					$dids_b = get_role_departments($uid);
					$dids = array_merge($dids_a, $dids_b);
					if(!empty($dids)){
						$whereOr[] = ['did','in',$dids];
					}
				}
			}
			if($tab == 1){
				//我创建的
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
			if($tab == 5){
				//已开具的
				$where[] = ['open_status', '=', 1];
			}
			if($tab == 6){
				//已作废的
				$where[] = ['open_status', '=', 2];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['open_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['open_status']) && $param['open_status'] != "") {
                $where[] = ['open_status', '=', $param['open_status']];
            }
            if (isset($param['enter_status']) && $param['enter_status'] != "") {
                $where[] = ['enter_status', '=', $param['enter_status']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
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
            if ($param['types'] == 1) {
                if (!$param['invoice_bank']) {
                    return to_assign(1, '开户银行不能为空');
                }
                if (!$param['invoice_account']) {
                    return to_assign(1, '银行账号不能为空');
                }
            }
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(InvoiceValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(InvoiceValidate::class)->scene('add')->check($param);
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
				if(is_mobile()){
					return view('qiye@/finance/add_invoice');
				}
				return view('edit');
			}
			if(is_mobile()){
				return view('qiye@/finance/add_invoice');
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
			$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
			$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['other_file_array'] = $other_file_array;
			if($detail['open_status']>0){
				$detail['open_admin_name'] = Db::name('Admin')->where('id','=',$detail['open_admin_id'])->value('name');
			}
			View::assign('detail', $detail);
			View::assign('create_user', get_admin($detail['admin_id']));
			if(is_mobile()){
				return view('qiye@/finance/view_invoice');
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
	
	//开票记录
    public function record()
    {
		$uid = $this->uid;
		$auth = isAuthInvoice($uid);
        if (request()->isAjax()) {
			$param = get_params();
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$where = [];
			$whereOr = [];
			$where[]=['delete_time','=',0];
			$where[]=['check_status','=',2];
			$where[]=['invoice_type','>',0];
			if($auth == 0){
				$dids_a = get_leader_departments($uid);	
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
			}
			if($tab == 0){
				//正常的
				$where[] = ['open_status', '<', 2];
			}
			if($tab == 1){
				//已作废的
				$where[] = ['open_status', '=', 2];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['open_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['open_status']) && $param['open_status'] != "") {
                $where[] = ['open_status', '=', $param['open_status']];
            }
			$list = $this->model->datalist($param,$where,$whereOr);
			
			$amount = $this->model::where($where)->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})->sum('amount');					
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list);
        } else {
			
			View::assign('authInvoice', $auth);
            return view();
        }
    }
	
	
   /**
    * 无发票回款列表
    */
    public function datalist_a()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid=$this->uid;
            $where = array();
            $whereOr = array();
			$where[]=['delete_time','=',0];
			$where[]=['invoice_type','=',0];
			
			$whereOr[] = ['admin_id', '=', $this->uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
			$auth = isAuthInvoice($uid);
			if($auth == 0){
				$dids_a = get_leader_departments($uid);	
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['enter_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['enter_status']) && $param['enter_status'] != "") {
                $where[] = ['enter_status', '=', $param['enter_status']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', isAuthIncome($this->uid));
            return view();
        }
    }
	
    /**
    * 无发票添加/编辑
    */
    public function add_a()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(InvoiceValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(InvoiceValidate::class)->scene('add')->check($param);
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
				if(is_mobile()){
					return view('qiye@/finance/add_invoice_a');
				}
				return view('edit_a');
			}
			if(is_mobile()){
				return view('qiye@/finance/add_invoice_a');
			}
			return view();
		}
    }
	
    /**
    * 无发票查看
    */
    public function view_a($id)
    {
		$detail = $this->model->getById($id);
		if (!empty($detail)) {
			$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
			$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['other_file_array'] = $other_file_array;
			if($detail['open_status']>0){
				$detail['open_admin_name'] = Db::name('Admin')->where('id','=',$detail['open_admin_id'])->value('name');
			}
			View::assign('detail', $detail);
			View::assign('create_user', get_admin($detail['admin_id']));
			if(is_mobile()){
				return view('qiye@/finance/view_invoice_a');
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
    public function del_a($id)
    {
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }

}
