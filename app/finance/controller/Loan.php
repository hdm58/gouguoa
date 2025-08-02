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
use app\finance\model\Loan as LoanModel;
use app\finance\validate\LoanValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Loan extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new LoanModel();
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
            $where = array();
            $whereOr = array();
			$where[]=['delete_time','=',0];
			if($tab == 0){
				//全部
				$auth = isAuthLoan($uid);
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
				//已打款的
				$where[] = ['pay_status', '=', 1];
				$auth = isAuthLoan($uid);
				if($auth == 0){
					$dids_a = get_leader_departments($uid);	
					$dids_b = get_role_departments($uid);
					$dids = array_merge($dids_a, $dids_b);
					if(!empty($dids)){
						$whereOr[] = ['did','in',$dids];
					}
					$whereOr[] = ['admin_id', '=', $this->uid];
				}
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['create_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
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
			if($param['project_id'] > 0){
				$project = Db::name('Project')->where(['id' => $param['project_id']])->find();
				if($project['status'] > 3){
					return to_assign(1, "不支持该操作：项目已申请提成，请先删除对应的提成申请记录");
				}
			}
            $param['loan_time'] = isset($param['loan_time']) ? strtotime(urldecode($param['loan_time'])) : 0;
            $param['plan_time'] = isset($param['plan_time']) ? strtotime(urldecode($param['plan_time'])) : 0;
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(LoanValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(LoanValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$is_codeno = Db::name('DataAuth')->where('name','finance_admin')->value('conf_9');
			View::assign('is_codeno', $is_codeno);
			View::assign('user', get_admin($this->uid));
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				if(is_mobile()){
					return view('qiye@/finance/add_loan');
				}
				return view('edit');
			}
			$codeno='';
			if($is_codeno==1){
				$codeno = get_codeno(4);
			}
            View::assign('codeno', $codeno);
			if(is_mobile()){
				return view('qiye@/finance/add_loan');
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
			$detail['expense'] = Db::name('Expense')->where(['delete_time'=>0,'loan_id'=>$id])->select()->toArray();
			View::assign('detail', $detail);
			View::assign('create_user', get_admin($detail['admin_id']));
			if(is_mobile()){
				return view('qiye@/finance/view_loan');
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
	
	//借支记录
    public function record()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			$where[]=['delete_time','=',0];
			$where[]=['check_status','=',2];
			if(isAuthLoan($this->uid)==0){
				$where[] = ['admin_id', '=', $this->uid];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['loan_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['pay_status']) && $param['pay_status'] != "") {
                $where[] = ['pay_status', '=', $param['pay_status']];
            }
			$list = $this->model->datalist($param,$where);
			
			$cost = $this->model::where($where)->sum('cost');					
			$totalRow['cost'] = sprintf("%.2f",$cost);
            return table_assign(0, '', $list);
        } else {
			View::assign('isAuthLoan',isAuthLoan($this->uid));
            return view();
        }
    }

}
