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
use app\customer\model\CustomerContact as CustomerContactModel;
use app\customer\validate\ContactValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Contact extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CustomerContactModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid=$this->uid;
			$where=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|name|mobile|email', 'like', '%' . $param['keywords'] . '%'];
            }
			$map=[];
			$mapOr=[];
			$map[]=['delete_time','=',0];
			$map[]=['discard_time','=',0];
			
			$mapOr[] = ['belong_uid','=',$uid];
			$mapOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$dids_a = get_leader_departments($uid);
			//是否是客户管理员
			$auth = isAuth($uid,'customer_admin','conf_1');
			if($auth == 1){
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$mapOr[] = ['belong_did','in',$dids];
				}
			}
			else{
				if(!empty($dids_a)){
					$mapOr[] = ['belong_did','in',$dids_a];
				}
			}
			
			$cids = Db::name('Customer')
				->where($map)
				->where(function ($query) use($mapOr) {
					if (!empty($mapOr)){
						$query->whereOr($mapOr);
					}
				})->column('id');
			$where[] = ['cid', 'in',$cids];
            $list = $this->model->datalist($param,$where);
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
			if (isset($param['birthday'])) {
                $param['birthday'] = strtotime($param['birthday']);
            }
			$family=[];
			$family_name_data = isset($param['family_name']) ? $param['family_name'] : '';
			$family_relations_data = isset($param['family_relations']) ? $param['family_relations'] : '';
			$family_remarks_data = isset($param['family_remarks']) ? $param['family_remarks'] : '';			
			if(!empty($family_name_data)){
				foreach ($family_name_data as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['family_name'] = $family_name_data[$key];
					$data['family_relations'] = $family_relations_data[$key];
					$data['family_remarks'] = $family_remarks_data[$key];
					$family[]=$data;
				}
			}
			$param['family'] = serialize($family);		
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ContactValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(ContactValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$cid = isset($param['cid']) ? $param['cid'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				return view('edit');
			}
			if($cid>0){
				View::assign('customer_name', Db::name('Customer')->where('id',$cid)->value('name'));
			}
			View::assign('customer_id', $cid);
			View::assign('customer_id', $cid);
			if(is_mobile()){
				return view('qiye@/customer/contact_add');
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
				return view('qiye@/customer/contact_view');
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
