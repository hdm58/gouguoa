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
use app\finance\model\InvoiceIncome;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Income extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new InvoiceIncome();
    }
	
    public function datalist()
    {
		$uid = $this->uid;
		$auth = isAuth($uid,'finance_admin','conf_4');
        if (request()->isAjax()) {
            $param = get_params();
			$tab = isset($param['tab']) ? $param['tab'] : 0;
            $where = array();
            $whereOr = array();
            $where[] = ['delete_time', '=', 0];
			if($tab == 0){
				if($auth == 0){
					$dids_son = get_leader_departments($uid);
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
			if (!empty($param['enter_time'])) {
				$enter_time =explode('~', $param['enter_time']);
				$where[] = ['enter_time', 'between', [strtotime(urldecode($enter_time[0])),strtotime(urldecode($enter_time[1].' 23:59:59'))]];
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
			$amount = $this->model->where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        } else {
			View::assign('auth', $auth);
            return view();
        }
    }

    //新增
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {   
			$id = isset($param['id']) ? $param['id'] : 0;
            if($param['invoice_id']>0){
				//计算发票已回款的金额
				$hasIncome = $this->model->where([['id','<>',$id],['invoice_id','=',$param['invoice_id']],['status','in',[1,2]],['delete_time','=',0]])->sum('amount');
				//查询发票金额
				$invoiceAmount = Db::name('Invoice')->where(['id'=>$param['invoice_id']])->value('amount');
				if(($param['amount']*10000 + $hasIncome*10000) > $invoiceAmount*10000){
					return to_assign(1,'收款金额大于关联发票金额，不允许保存，请核对');
				}
			}
			if($param['contract_id']>0){
				//计算合同已回款的金额
				$hasIncome = $this->model->where([['id','<>',$id],['contract_id','=',$param['contract_id']],['status','in',[1,2]],['delete_time','=',0]])->sum('amount');
				//查询合同金额
				$contractAmount = Db::name('Contract')->where(['id'=>$param['contract_id']])->value('cost');
				if(($param['amount']*10000 + $hasIncome*10000) > $contractAmount*10000){
					return to_assign(1,'收款金额大于关联销售合同金额，不允许保存，请核对');
				}
			}
			
			if(!empty($param['enter_time'])){
				$param['enter_time']=strtotime($param['enter_time']);
			}
			if($id==0){
				$param['admin_id'] = $this->uid;
				$param['did'] = $this->did;
				$this->model->add($param);
			}
			else{
				$this->model->edit($param);
			}
		}
        else{
            $id = isset($param['id']) ? $param['id']: 0 ;
			if($id>0){
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
			}
			View::assign('id', $id);
			if(is_mobile()){
				return view('qiye@/finance/add_income');
			}
			return view();
        }
    }
    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = $this->model->getById($id);
		if(empty($detail)){
			throw new \think\exception\HttpException(406, '找不到记录');
		}
		if($detail['status']==2){
			$detail['confirm_name'] = Db::name('Admin')->where('id','=',$detail['confirm_uid'])->value('name');
		}
		$detail['refund'] = Db::name('IncomeRefund')->field('i.*,a.name as admin')
					->alias('i')
					->join('Admin a', 'a.id = i.admin_id', 'LEFT')
					->where([['i.income_id','=',$id],['i.delete_time','=',0]])
					->order('i.back_time desc')
					->select();
        View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/finance/view_income');
		}
        return view();
    }

    //删除到账记录
    public function del()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $income =InvoiceIncome::where(['id'=>$param['id']])->find();
            $invoice = $this->model->where(['id'=>$income['invoice_id']])->find();
            if($income){
                $res = InvoiceIncome::where(['id'=>$param['id']])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    if($income['amount']*100 == $invoice['amount']*100){
                        //发票全部反到账
                        $this->model->where(['id'=>$income['invoice_id']])->update(['enter_status'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    }
                    else if($income['amount']*100 < $invoice['amount']*100){
                        $incomeTotal=InvoiceIncome::where(['invoice_id'=>$income['invoice_id'],'status'=>1])->sum('amount');
                        //发票部分到账
                        $this->model->where(['id'=>$income['invoice_id']])->update(['enter_status'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                    }
                    add_log('enter',$income['invoice_id'],$invoice);
                    return to_assign();
                }
                else{
                    return to_assign(1,'操作失败');
                }
            }
        }
    }
	
	
	//回款记录
    public function record()
    {
		$uid = $this->uid;
		$auth = isAuth($uid,'finance_admin','conf_4');
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			$whereOr = [];
			$where[]=['check_status','=',2];
			$where[]=['status','=',2];
			$where[]=['delete_time','=',0];
			if (!empty($param['uid'])) {
				$where[] = ['admin_id', '=', $param['uid']];
			}
			else{
				if($auth==0){
					$dids_son = get_leader_departments($uid);
					$whereOr[] = ['admin_id', '=', $uid];
					$whereOr[] = ['did','in',$dids_son];
				}
			}
			//按时间检索
			if (!empty($param['confirm_time'])) {
				$confirm_time =explode('~', $param['confirm_time']);
				$where[] = ['confirm_time', 'between', [strtotime(urldecode($confirm_time[0])),strtotime(urldecode($confirm_time[1].' 23:59:59'))]];
			}
			if (!empty($param['enter_time'])) {
				$enter_time =explode('~', $param['enter_time']);
				$where[] = ['enter_time', 'between', [strtotime(urldecode($enter_time[0])),strtotime(urldecode($enter_time[1].' 23:59:59'))]];
			}
			$list = $this->model->datalist($param,$where,$whereOr);
			$amount = $this->model->where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        } else {
			View::assign('auth',$auth);
            return view();
        }
    }
}
