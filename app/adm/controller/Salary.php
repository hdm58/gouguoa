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

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\model\Salary as SalaryModel;
use app\adm\model\SalaryRecords as SalaryRecordsModel;
use app\adm\validate\SalaryValidate;
use app\adm\validate\RecordsValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Salary extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new SalaryModel();
    }

    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
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
			if (isset($param['month_time'])) {
				$param['month_time'] = strtotime($param['month_time']);
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(SalaryValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(SalaryValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail=[];
			if ($id>0) {
				$detail = $this->model->getById($id);
			}
			else{
				$detail['exclude_uids'] = valueAuth('human_admin','conf_6');
				$exclude_names = Db::name('Admin')->where('id', 'in', $detail['exclude_uids'])->column('name');
				$detail['exclude_names'] = implode(',', $exclude_names);
			}
			View::assign('detail', $detail);
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
			$detail['count'] = Db::name('SalaryRecords')->where([['salary_id','=',$id],['delete_time','=',0]])->sum('total_payment');
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
    public function del($id)
    {
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

	/**
    * 新增/编辑
    */
    public function records_add()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			if (empty($param['salary_position'])){
				$param['salary_position'] = 0;
			}
			if (empty($param['salary_performance'])){
				$param['salary_performance'] = 0;
			}
			if (empty($param['salary_quanqin'])){
				$param['salary_quanqin'] = 0;
			}
			if (empty($param['salary_overwork'])){
				$param['salary_overwork'] = 0;
			}
			if (empty($param['salary_meal'])){
				$param['salary_meal'] = 0;
			}
			if (empty($param['salary_phone'])){
				$param['salary_phone'] = 0;
			}
			if (empty($param['salary_traffic'])){
				$param['salary_traffic'] = 0;
			}
			if (empty($param['salary_house'])){
				$param['salary_house'] = 0;
			}
			if (empty($param['salary_fuli'])){
				$param['salary_fuli'] = 0;
			}
			if (empty($param['salary_protecting'])){
				$param['salary_protecting'] = 0;
			}
			if (empty($param['salary_bonus'])){
				$param['salary_bonus'] = 0;
			}
			if (empty($param['deduct_belate'])){
				$param['deduct_belate'] = 0;
			}
			if (empty($param['deduct_leave'])){
				$param['deduct_leave'] = 0;
			}
			if (empty($param['deduct_absenteeism'])){
				$param['deduct_absenteeism'] = 0;
			}
			if (empty($param['deduct_tax'])){
				$param['deduct_tax'] = 0;
			}
			if (empty($param['deduct_social'])){
				$param['deduct_social'] = 0;
			}
			if (empty($param['deduct_gongjijin'])){
				$param['deduct_gongjijin'] = 0;
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(RecordsValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$model = new SalaryRecordsModel();
				$model->edit($param);
            } else {
                try {
                    validate(RecordsValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['month_time'] = Db::name('Salary')->where('id',$param['salary_id'])->value('month_time');
				$param['admin_id'] = $this->uid;
				$model = new SalaryRecordsModel();
                $model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$salary_id = isset($param['salary_id']) ? $param['salary_id'] : 0;
			if ($id>0) {
				$model = new SalaryRecordsModel();
				$detail = $model->getById($id);
				$salary_id = $detail['salary_id'];
				View::assign('detail', $detail);
			}
			View::assign('salary_id', $salary_id);			
			return view();
		}
    }
	
    /**
    * 查看
    */
    public function records_view($id)
    {
		$model = new SalaryRecordsModel();
		$detail = $model->getById($id);
		if (!empty($detail)) {
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/index/salary_view');
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
    public function records_del($id)
    {
		if (request()->isDelete()) {
			$model = new SalaryRecordsModel();
			$model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   


}
