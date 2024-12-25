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

namespace app\customer\controller;

use app\base\BaseController;
use app\customer\model\Customer as CustomerModel;
use app\customer\validate\CustomerValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Customer extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CustomerModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr = [];
			$uid = $this->uid;
			$tab = isset($param['tab']) ? $param['tab'] : 0;
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['customer_status'])) {
                $where[] = ['customer_status', '=', $param['customer_status']];
            }
			if (!empty($param['industry_id'])) {
                $where[] = ['industry_id', '=', $param['industry_id']];
            }
			if (!empty($param['source_id'])) {
                $where[] = ['source_id', '=', $param['source_id']];
            }
			if (!empty($param['grade_id'])) {
                $where[] = ['grade_id', '=', $param['grade_id']];
            }
			if (!empty($param['intent_status'])) {
                $where[] = ['intent_status', '=', $param['intent_status']];
            }
			if (!empty($param['follow_time'])) {
				$follow_time =explode('~', $param['follow_time']);
				$where[] = ['follow_time', 'between',[strtotime(urldecode($follow_time[0])),strtotime(urldecode($follow_time[1].' 23:59:59'))]];
            }
			if (!empty($param['next_time'])) {
                $next_time =explode('~', $param['next_time']);
				$where[] = ['next_time', 'between',[strtotime(urldecode($next_time[0])),strtotime(urldecode($next_time[1].' 23:59:59'))]];
            }
			$where[]=['delete_time','=',0];
			$where[]=['discard_time','=',0];
			
			if($tab == 0){
				if (!empty($param['uid'])) {
					$where[] = ['belong_uid', '=', $param['uid']];
				}
				else{
					//是否是客户管理员
					$auth = isAuth($uid,'customer_admin','conf_1');
					if($auth == 0){
						$whereOr[] = ['belong_uid','=',$uid];
						$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
						$dids_a = get_leader_departments($uid);
						$dids_b = get_role_departments($uid);
						$dids = array_merge($dids_a, $dids_b);
						if(!empty($dids)){
							$whereOr[] = ['belong_did','in',$dids];
						}
					}
				}
			}
			//我的客户
			if($tab == 1){
				$where[] = ['belong_uid','=',$uid];
			}
			//下属客户
			if($tab == 2){
				$where[] = ['belong_uid','<>',$uid];
				$dids_a = get_leader_departments($uid);
				if(!empty($dids_a)){
					$where[] = ['belong_did','in',$dids_a];
				}				
			}
			//分享客户
			if($tab == 3){
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('is_leader', isLeader($this->uid));
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
			try {
				validate(CustomerValidate::class)->scene($param['scene'])->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}		
            if (!empty($param['id']) && $param['id'] > 0) {
				$param['edit_id'] = $this->uid;
				$this->model->edit($param);
            } else {
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$sea = isset($param['sea']) ? $param['sea'] : 0;
			View::assign('sea', $sea);
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				return view('edit');
			}
			if($this->uid>1){
				View::assign('userinfo', get_admin($this->uid));
			}
			if(is_mobile()){
				return view('qiye@/customer/add');
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
		$detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
		if($detail['customer_status']>0){
			$detail['customer_status_name'] = Db::name('BasicCustomer')->where(['id' => $detail['customer_status']])->value('title');
		}
		else{
			$detail['customer_status_name']='-';
		}
		if($detail['intent_status']>0){
			$detail['intent_status_name'] = Db::name('BasicCustomer')->where(['id' => $detail['intent_status']])->value('title');
		}
		else{
			$detail['intent_status_name']='-';
		}
		
		$detail['industry'] = Db::name('Industry')->where(['id' => $detail['industry_id']])->value('title');
		$detail['source'] = Db::name('CustomerSource')->where(['id' => $detail['source_id']])->value('title');
		$detail['grade'] = Db::name('CustomerGrade')->where(['id' => $detail['grade_id']])->value('title');
		
		//附件
		$file_array = Db::name('CustomerFile')
			->field('cf.id,f.filepath,f.name,f.filesize,f.fileext,f.create_time,f.admin_id')
			->alias('cf')
			->join('File f', 'f.id = cf.file_id', 'LEFT')
			->order('cf.create_time asc')
			->where(array('cf.customer_id' => $id, 'cf.delete_time' => 0))
			->select()->toArray();
		$detail['file_array'] = $file_array;
		
		$role=0;
		if($detail['belong_uid'] == $this->uid && $detail['is_lock'] == 0){
			$role=1;
		}
		View::assign('detail', $detail);
		View::assign('role', $role);
		if(is_mobile()){
			return view('qiye@/customer/view');
		}
		return view();
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
