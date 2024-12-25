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

namespace app\user\controller;

use app\base\BaseController;
use app\user\model\LaborContract as LaborContractModel;
use app\user\validate\LaborContractValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Laborcontract extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new LaborContractModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
			if (!empty($param['cate'])) {
                $where[] = ['cate', '=', $param['cate']];
            }
			if (!empty($param['properties'])) {
                $where[] = ['properties', '=', $param['properties']];
            }
			if (!empty($param['uid'])) {
                $where[] = ['uid', '=', $param['uid']];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['sign_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
            }
            $list = $this->model->datalist($where, $param);
             return table_assign(0, '', $list);
        }
        else{
			$this->model::where([['end_time','<',time()],['status','=',1]])->update(['status'=>2]);
			View::assign('cate', $this->model::$laborcontract_cate);
			View::assign('types', $this->model::$laborcontract_types);
			View::assign('properties', $this->model::$laborcontract_properties);
			View::assign('status', $this->model::$laborcontract_status);
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
			$param['sign_time'] = isset($param['sign_time']) ? strtotime($param['sign_time']) : 0;
			$param['start_time'] = isset($param['start_time']) ? strtotime($param['start_time']) : 0;
			$param['end_time'] = isset($param['end_time']) ? strtotime($param['end_time']) : 0;
			$param['trial_end_time'] = isset($param['trial_end_time']) ? strtotime($param['trial_end_time']) : 0;
			if($param['end_time']<=$param['start_time']){
				return to_assign(1,'合同失效时间需要大于开始时间');
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(LaborContractValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if($param['end_time']<time() && $param['status']==1){
					$param['status'] = 2;
				}
				if($param['end_time']>time() && $param['status']==2){
					$param['status'] = 1;
				}
				$this->model->edit($param);
            } else {
                try {
                    validate(LaborContractValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if($param['end_time']<time()){
					$param['status'] = 2;
				}
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$cate_id = isset($param['cate_id']) ? $param['cate_id'] : 0;
			View::assign('properties', $this->model::$laborcontract_properties);
			if ($id>0) {
				$detail = $this->model->getById($id);
				$detail['user_name'] = Db::name('Admin')->where('id',$detail['uid'])->value('name');
				if($detail['file_ids'] !=''){
					$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['file_array'] = $file_array;
				}
				View::assign('detail', $detail);
				return view('edit');
			}
			$cate_title='';
			if($cate_id>0){
				$cate_title = $this->model::$laborcontract_cate[$cate_id-1]['title'];
			}
			View::assign('id', $id);
			View::assign('cate_id', $cate_id);
			View::assign('cate_title', $cate_title);
			return view();
		}
    }
	
    /**
    * 续签合同
    */
    public function add_renewal()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$param['sign_time'] = isset($param['sign_time']) ? strtotime($param['sign_time']) : 0;
			$param['start_time'] = isset($param['start_time']) ? strtotime($param['start_time']) : 0;
			$param['end_time'] = isset($param['end_time']) ? strtotime($param['end_time']) : 0;
			$param['trial_end_time'] = isset($param['trial_end_time']) ? strtotime($param['trial_end_time']) : 0;
			if($param['end_time']<=$param['start_time']){
				return to_assign(1,'合同失效时间需要大于开始时间');
			}
			if($param['end_time']<time()){
				$param['status'] = 3;
			}
			try {
				validate(LaborContractValidate::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['admin_id'] = $this->uid;
			$this->model->add($param);
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if($id>0){
				$detail = $this->model->getById($id);
				$detail['user_name'] = Db::name('Admin')->where('id',$detail['uid'])->value('name');
				View::assign('detail', $detail);
			}
			View::assign('properties', $this->model::$laborcontract_properties);
			return view();
		}
    }
	
    /**
    * 变更合同
    */
    public function add_change()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$param['sign_time'] = isset($param['sign_time']) ? strtotime($param['sign_time']) : 0;
			$param['start_time'] = isset($param['start_time']) ? strtotime($param['start_time']) : 0;
			$param['end_time'] = isset($param['end_time']) ? strtotime($param['end_time']) : 0;
			$param['trial_end_time'] = isset($param['trial_end_time']) ? strtotime($param['trial_end_time']) : 0;
			if($param['end_time']<=$param['start_time']){
				return to_assign(1,'合同失效时间需要大于开始时间');
			}
			if($param['end_time']<time()){
				$param['status'] = 3;
			}
			try {
				validate(LaborContractValidate::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['admin_id'] = $this->uid;
			$this->model->add($param);
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if($id>0){
				$detail = $this->model->getById($id);
				$detail['user_name'] = Db::name('Admin')->where('id',$detail['uid'])->value('name');
				View::assign('detail', $detail);
			}
			View::assign('properties', $this->model::$laborcontract_properties);
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
			$detail['cate_str'] = $this->model::$laborcontract_cate[$detail['cate']-1]['title'];
			$detail['types_str'] = $this->model::$laborcontract_types[$detail['types']];
			$detail['properties_str'] = $this->model::$laborcontract_properties[$detail['properties']];
			$detail['status_str'] = $this->model::$laborcontract_status[$detail['status']];
			$detail['user_name'] = Db::name('Admin')->where('id',$detail['uid'])->value('name');
			$detail['admin_name'] = Db::name('Admin')->where('id',$detail['admin_id'])->value('name');
			if($detail['file_ids'] !=''){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			if($detail['renewal_pid'] > 0){
				$detail['renewal_ptitle'] = Db::name('LaborContract')->where('id',$detail['renewal_pid'])->value('title');
			}
			if($detail['change_pid'] > 0){
				$detail['change_ptitle'] = Db::name('LaborContract')->where('id',$detail['change_pid'])->value('title');
			}
			View::assign('detail', $detail);
			View::assign('renewal', Db::name('LaborContract')->where(['renewal_pid'=>$id,'delete_time'=>0])->find());
			View::assign('change', Db::name('LaborContract')->where(['change_pid'=>$id,'delete_time'=>0])->find());
			return view();
		}
		else{
			throw new \think\exception\HttpException(404, '找不到页面');
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
    * 解除、恢复
    */
    public function set($id,$status)
    {
		if (request()->isAjax()) {
			$param = get_params();
			$renewal = Db::name('LaborContract')->where(['renewal_pid'=>$id,'delete_time'=>0])->count();
			$change = Db::name('LaborContract')->where(['change_pid'=>$id,'delete_time'=>0])->count();			
			if($renewal>0 || $change>0){
				return to_assign(1, "已续签或者已变更的合同不支持该操作");
			}
			$this->model::where('id', $id)->strict(false)->field(true)->update(['status'=>$status]);
			if($status==3){
				add_log('secure', $id, $param);
			}
			else{
				add_log('recovery', $id, $param);
			}
			return to_assign();
		} else {
            return to_assign(1, "错误的请求");
        }
    } 

}
