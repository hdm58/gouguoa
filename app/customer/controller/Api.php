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

use app\api\BaseController;
use app\customer\model\Customer;
use app\customer\model\CustomerTrace;
use app\customer\model\CustomerContact;
use app\customer\model\CustomerChance;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//获取客户列表
	public function get_customer()
    {
        $param = get_params();
		$uid = $this->uid;
		$where = array();
		$whereOr = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|name', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[]=['delete_time','=',0];
		$where[]=['discard_time','=',0];
		
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
		
		$model = new Customer();
		$list = $model->datalist($param,$where,$whereOr);
        table_assign(0, '', $list);
    }
	//分配客户
	public function distribute()
    {
		if (request()->isAjax()) {
			$params = get_params();
			//是否是客户管理员
			$auth = isAuth($this->uid,'customer_admin','conf_1');
			if($auth==0){
				return to_assign(1, "只有客户管理员才有权限操作");
			}
			$data['id'] = $params['id'];
			$data['belong_uid'] = $params['uid'];
			$data['belong_did'] = $params['did'];
			$data['distribute_time'] = time();
			if (Db::name('Customer')->update($data) !== false) {
				add_log('allot', $data['id'],[],'客户');
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	//锁住、解锁客户
	public function customer_lock()
    {
		if (request()->isAjax()) {
			$params = get_params();
			//是否是客户管理员
			$auth = isAuth($this->uid,'customer_admin','conf_1');
			if($auth==0){
				return to_assign(1, "只有客户管理员才有权限操作");
			}
			$data['id'] = $params['id'];
			$data['is_lock'] = $params['is_lock'];
			if (Db::name('Customer')->update($data) !== false) {
				if($data['is_lock']==1){
					add_log('lock', $data['id'],[],'客户');
				}
				else{
					add_log('unlock', $data['id'],[],'客户');
				}
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	
	//彻底删除客户
    public function delete()
    {
		if (request()->isDelete()) {
			$params = get_params();
			//是否是客户管理员
			$auth = isAuth($this->uid,'customer_admin','conf_1');
			if($auth==0){
				return to_assign(1, "只有客户管理员才有权限操作");
			}			
			$data['id'] = $params['id'];
			$data['delete_time'] = -1;
			if (Db::name('Customer')->update($data) !== false) {
				//删除客户联系人
				Db::name('CustomerContact')->where(['cid' => $params['id']])->update(['delete_time'=>time()]);
				//删除客户机会
				Db::name('CustomerChance')->where(['cid' => $params['id']])->update(['delete_time'=>time()]);
				add_log('delete', $params['id']);
				return to_assign();
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//跟进记录列表
	public function get_trace()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['cid', '=', $param['cid']];
		$model = new CustomerTrace();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }	
	
	//销售机会列表
	public function get_chance()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['cid', '=', $param['cid']];
		$model = new CustomerChance();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
	//获取联系人数据
	public function get_contact()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['cid', '=', $param['cid']];
		$model = new CustomerContact();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
	//设置联系人
	public function set_contact()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$detail= Db::name('CustomerContact')->where(['id' => $param['id']])->find();
			CustomerContact::where(['cid' => $detail['cid']])->update(['is_default'=>0]);
			$res = CustomerContact::where(['id' => $param['id']])->update(['is_default'=>1]);
			if ($res) {
				add_log('edit', $param['id'], $param,'客户联系人');
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }
	

    //添加附件
    public function add_file()
    {
        $param = get_params();
        $param['create_time'] = time();
        $param['admin_id'] = $this->uid;
        $fid = Db::name('CustomerFile')->strict(false)->field(true)->insertGetId($param);
        if ($fid) {
            return to_assign(0, '上传成功', $fid);
        }
    }
    
    //删除附件
    public function delete_file()
    {
        if (request()->isDelete()) {
			$id = get_params("id");
			$data['id'] = $id;
			$data['delete_time'] = time();
			if (Db::name('CustomerFile')->update($data) !== false) {
				$detail = Db::name('CustomerFile')->where('id', $id)->find();
				$file_name = Db::name('File')->where('id', $detail['file_id'])->value('name');
				return to_assign(0, "删除成功");
			} else {
				return to_assign(1, "删除失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//获取客户等级
	public function get_customer_grade()
    {
		$list = get_base_data('customer_grade');
		return to_assign(0, '', $list);
    }
	//获取客户状态
	public function get_customer_status()
    {
		$list = get_base_type_data('basic_customer',1);
		return to_assign(0, '', $list);
    }
	//获取客户意向
	public function get_intent_status()
    {
		$list = get_base_type_data('basic_customer',2);
		return to_assign(0, '', $list);
    }

}
