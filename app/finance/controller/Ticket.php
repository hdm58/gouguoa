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
use app\finance\model\Ticket as TicketModel;
use app\finance\validate\TicketValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Ticket extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new TicketModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
		$uid = $this->uid;
		$auth = isAuth($uid,'finance_admin','conf_2');
        if (request()->isAjax()) {
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$dids_son = get_leader_departments($uid);
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
					$whereOr[] = ['did','in',$dids_son];
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
			//按时间检索
			if (!empty($param['create_time'])) {
				$create_time =explode('~', $param['create_time']);
				$where[] = ['create_time', 'between', [strtotime(urldecode($create_time[0])),strtotime(urldecode($create_time[1].' 23:59:59'))]];
			}
			if (!empty($param['open_time'])) {
				$open_time =explode('~', $param['open_time']);
				$where[] = ['open_time', 'between', [strtotime(urldecode($open_time[0])),strtotime(urldecode($open_time[1].' 23:59:59'))]];
			}
            if (isset($param['open_status']) && $param['open_status'] != "") {
                $where[] = ['open_status', '=', $param['open_status']];
            }
            if (isset($param['pay_status']) && $param['pay_status'] != "") {
                $where[] = ['pay_status', '=', $param['pay_status']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			if (!empty($param['keywords'])) {
                $where[] = ['invoice_title|code', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth',$auth);
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
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
			$id = isset($param['id']) ? $param['id'] : 0;
			if($param['purchase_id']>0){
				//计算合同已开票的金额
				$hasTicket = $this->model->where([['id','<>',$id],['purchase_id','=',$param['purchase_id']],['open_status','<',2],['delete_time','=',0]])->sum('amount');
				//查询合同金额
				$purchaseAmount = Db::name('Purchase')->where(['id'=>$param['purchase_id']])->value('cost');
				if(($param['amount']*10000 + $hasTicket*10000) > $purchaseAmount*10000){
					return to_assign(1,'收票金额大于关联采购合同金额，不允许保存，请核对');
				}
			}
			if (!empty($param['open_time'])) {
				$param['open_time'] = strtotime(urldecode($param['open_time']));
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(TicketValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(TicketValidate::class)->scene('add')->check($param);
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
					return view('qiye@/finance/add_ticket');
				}
				return view('edit');
			}
			if(is_mobile()){
				return view('qiye@/finance/add_ticket');
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
			View::assign('detail', $detail);
			View::assign('create_user', get_admin($detail['admin_id']));
			if(is_mobile()){
				return view('qiye@/finance/view_ticket');
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
	
	//收票记录
    public function record()
    {
		$uid = $this->uid;
		$auth = isAuth($uid,'finance_admin','conf_2');
        if (request()->isAjax()) {
			$param = get_params();
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$where = [];
			$where[]=['delete_time','=',0];
			$where[]=['check_status','=',2];
			$where[]=['invoice_type','>',0];
			if($auth==0){
				$where[] = ['admin_id', '=', $uid];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['open_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['open_status']) && $param['open_status'] != "") {
                $where[] = ['open_status', '=', $param['open_status']];
            }
			$list = $this->model->datalist($param,$where);
			
			$amount = $this->model::where($where)->sum('amount');					
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        } else {
			View::assign('auth',$auth);
            return view();
        }
    }
	
    /**
    * 无发票付款列表
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
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['pay_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['pay_status']) && $param['pay_status'] != "") {
                $where[] = ['pay_status', '=', $param['pay_status']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', isAuthPayment($this->uid));
            return view();
        }
    }
	
    /**
    * 无发票付款添加/编辑
    */
    public function add_a()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
			if (!empty($param['open_time'])) {
				$param['open_time'] = strtotime(urldecode($param['open_time']));
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(TicketValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(TicketValidate::class)->scene('add')->check($param);
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
					return view('qiye@/finance/add_ticket_a');
				}
				return view('edit_a');
			}
			if(is_mobile()){
				return view('qiye@/finance/add_ticket_a');
			}
			return view();
		}
    }
	
    /**
    * 无发票付款查看
    */
    public function view_a($id)
    {
		$detail = $this->model->getById($id);
		if (!empty($detail)) {
			$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
			$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['other_file_array'] = $other_file_array;
			View::assign('detail', $detail);
			View::assign('create_user', get_admin($detail['admin_id']));
			if(is_mobile()){
				return view('qiye@/finance/view_ticket_a');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到页面']);
		}
    }
	
   /**
    * 无发票付款删除
    */
    public function del_a()
    {
        $param = get_params();	
		if (request()->isDelete()) {
			$this->model->delById($param['id']);
		} else {
            return to_assign(1, "错误的请求");
        }
    }  

}
