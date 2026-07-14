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
use app\finance\model\Account as AccountModel;
use app\finance\model\FinanceInjection as FinanceInjectionModel;
use app\api\model\FinanceLog;
use app\finance\validate\AccountValidate;
use app\finance\validate\InjectionValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Account extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new AccountModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
            $list = $this->model->where('delete_time',0)->select();
			foreach ($list as $key => &$value) {
				$value['enterprise'] = Db::name('Enterprise')->where('id',$value['enterprise_id'])->value('title');
				$injection_amount = Db::name('FinanceInjection')->where(['account_id'=>$value['id'],'delete_time'=>0])->sum('amount');
				$value['injection_amount'] = sprintf("%.2f", (float)$injection_amount);
				$amount = ($value['amount']*10000 + $value['initial_amount']*10000 + $injection_amount*10000)/10000;
				$value['amount'] = sprintf("%.2f", (float)$amount);
				
				$income_amount = Db::name('FinanceLog')->where(['account_id'=>$value['id'],'types'=>1,'delete_time'=>0])->sum('amount');
				$payout_amount = Db::name('FinanceLog')->where(['account_id'=>$value['id'],'types'=>2,'delete_time'=>0])->sum('amount');
				$value['income_amount'] = sprintf("%.2f", (float)$income_amount);
				$value['payout_amount'] = sprintf("%.2f", (float)$payout_amount);
			}

            return to_assign(0, '', $list);
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
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(AccountValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(AccountValidate::class)->scene('add')->check($param);
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
			$id = isset($param['id']) ? $param['id'] : 0;
			$type = isset($param['type']) ? $param['type'] : 0;
			$this->model->delById($id,$type);
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
			$res = $this->model->strict(false)->field('id,status')->update($param);
			if ($res) {
				if($param['status'] == 0){
					add_log('disable', $param['id'], $param);
				}
				else if($param['status'] == 1){
					add_log('recovery', $param['id'], $param);
				}
				return to_assign();
			}
			else{
				return to_assign(1, '操作失败');
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }
   
	/**
    * 注资数据列表
    */
    public function injection()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$model = new FinanceInjectionModel();
			$where=[];
			$where[]=['delete_time','=',0];
			if(!empty($param['account_id'])){
				$where[]=['account_id','=',$param['account_id']];
			}
            $list = $model->datalist($param,$where);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('account_id', $param['account_id']);
            return view();
        }
    }
	
    /**
    * 注资添加/编辑
    */
    public function injection_add()
    {
		$param = get_params();	
		$model = new FinanceInjectionModel();
        if (request()->isAjax()) {
			$param['income_time'] = isset($param['income_time']) ? strtotime(urldecode($param['income_time'])) : 0;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(InjectionValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$model->edit($param);
            } else {
                try {
                    validate(InjectionValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$account_id = isset($param['account_id']) ? $param['account_id'] : 0;
			if ($id>0) {
				$detail = $model->getById($id);
				$account_id = $detail['account_id'];
				View::assign('detail', $detail);
			}
			View::assign('account', $this->model->getById($account_id));
			return view();
		}
    }
	
	/**
    * 收入数据列表
    */
    public function income()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$where[]=['delete_time','=',0];
			$where[]=['types','=',1];
			if(!empty($param['account_id'])){
				$where[]=['account_id','=',$param['account_id']];
			}
			$model = new FinanceLog();
            $list = $model->datalist($param,$where);
            $amount = $model->where($where)->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
			return table_assign(0, '', $list,$totalRow);
        }
        else{
			View::assign('account_id', $param['account_id']);
            return view();
        }
    }
	
	/**
    * 支出数据列表
    */
	public function payout()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$where[]=['delete_time','=',0];
			$where[]=['types','=',2];
			if(!empty($param['account_id'])){
				$where[]=['account_id','=',$param['account_id']];
			}
			$model = new FinanceLog();
            $list = $model->datalist($param,$where);
			$amount = $model->where($where)->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        }
        else{
			View::assign('account_id', $param['account_id']);
            return view();
        }
    }
	

}
