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
use app\finance\model\IncomeRefund;
use app\finance\model\InvoiceIncome;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Refund extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new IncomeRefund();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
		$uid=$this->uid;
		//是否付款管理员
		$auth = isAuth($uid,'finance','conf_4');
        if (request()->isAjax()) {
			$param = get_params();
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$dids_son = get_leader_departments($uid);
            $where = array();
            $whereOr = array();
            $where[] = ['delete_time', '=', 0];
			if($tab == 0){
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
			if (!empty($param['back_time'])) {
				$back_time =explode('~', $param['back_time']);
				$where[] = ['back_time', 'between', [strtotime(urldecode($back_time[0])),strtotime(urldecode($back_time[1].' 23:59:59'))]];
			}
			if (!empty($param['fundscate_id'])) {
                $where[] = ['fundscate_id', '=', $param['fundscate_id']];
            }
            if (!empty($param['paytype_id'])) {
                $where[] = ['paytype_id', '=', $param['paytype_id']];
            }
			if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('is_leader', isLeader($uid));
			View::assign('auth', $auth);
            return view();
        }
    }
	
    public function record()
    {
		$param = get_params();
		$uid=$this->uid;
		//是否付款管理员
		$auth = isAuth($uid,'finance','conf_4');
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
			$where[] = ['check_status', '=', 2];			
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['back_time', 'between',[strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
            }
			if (!empty($param['uid'])) {
				$where[] = ['admin_id', '=', $param['uid']];
			}
			else{
				if($auth==0){
					$whereOr[] = ['admin_id', '=', $uid];
					$dids_son = get_leader_departments($uid);
					$whereOr[] = ['did','in',$dids_son];
				}
			}
            $list = $this->model->datalist($param,$where,$whereOr);
			$amount = $this->model::where($where)->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        }
        else{
			View::assign('is_leader', isLeader($uid));
			View::assign('auth', $auth);
            return view();
        }
    }
	
    public function add()
    {
		$param = get_params();	
		$IncomeModel = new InvoiceIncome();
        if (request()->isAjax()) {
			$income = $IncomeModel->getById($param['income_id']);
			if($income['types']>3){
				return to_assign(1, "只有意向金和定金才支持退款");
			}
			$has_back = Db::name('IncomeRefund')->where([['income_id','=',$param['income_id']],['delete_time','=',0],['id','<>',$param['id']]])->sum('amount');
			$back_amount = $has_back*1000+$param['amount']*1000;
			if($back_amount > $income['amount']*1000){
				return to_assign(1, "退回金额大于该收款金额，不支持操作");
			}
			if($back_amount < $income['amount']*1000){
				$param['back_status']=1;
			}else{
				$param['back_status']=2;
			}
			$param['back_amount'] = $back_amount/1000;
			if(isset($param['back_time'])){
				$param['back_time'] = strtotime($param['back_time']);
			}
            if (!empty($param['id']) && $param['id'] > 0) {
				$this->model->edit($param);
            } else {
				$param['admin_id'] = $this->uid;
				$param['did'] = $this->did;
                $this->model->add($param);
            }
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$income_id = isset($param['income_id']) ? $param['income_id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				$income_id = $detail['income_id'];
				View::assign('detail', $detail);
			}
			$income = $IncomeModel->getById($income_id);
			View::assign('income', $income);
			View::assign('income_id', $income_id);
			if(is_mobile()){
				return view('qiye@/finance/add_refund');
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
			if(is_mobile()){
				return view('qiye@/finance/view_refund');
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
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
