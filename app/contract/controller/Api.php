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
namespace app\contract\controller;

use app\api\BaseController;
use app\contract\model\Contract;
use app\contract\model\Purchase;
use app\contract\model\SupplierContact;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//获取销售合同协议
	public function get_contract()
    {
        $param = get_params();
		$uid = $this->uid;
		$where = array();
		$whereOr = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|name|code', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['check_status', '=', 2];		
		//是否是合同管理员
		$auth = isAuth($uid,'contract_admin','conf_1');
		if($auth == 0){
			$whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			$dids_a = get_leader_departments($uid);
			$dids_b = get_role_departments($uid);
			$dids = array_merge($dids_a, $dids_b);
			if(!empty($dids)){
				$whereOr[] = ['did','in',$dids];
			}
		}
		$model = new Contract();
        $list = $model->datalist($param,$where,$whereOr);
        return table_assign(0, '', $list);
    }

	//销售合同归档操作
    public function contract_archive()
    {
        if (request()->isPost()) {
			$param = get_params();
			$tips='操作成功，合同已转入到归档合同列表';
			if($param['archive_status'] == 1){
				$param['archive_uid'] = $this->uid;
				$param['archive_time'] = time();
			}
			else{
				$param['archive_uid'] = 0;
				$param['archive_time'] = 0;
				$tips='操作成功，合同已从归档合同列表转出';
			}
			if (Db::name('Contract')->strict(false)->update($param) !== false) {
				return to_assign(0, $tips);
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//销售合同中止等操作
    public function contract_stop()
    {
        if (request()->isPost()) {
			$param = get_params();
			$tips='操作成功，合同已转入到中止合同列表';
			if($param['stop_status'] == 1){
				$param['stop_uid'] = $this->uid;
				$param['stop_time'] = time();
			}
			else{
				$param['stop_uid'] = 0;
				$param['stop_time'] = 0;
				$tips='操作成功，合同已从中止合同列表转出';
			}
			if (Db::name('Contract')->strict(false)->update($param) !== false) {
				return to_assign(0, $tips);
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	//销售合同作废等操作
    public function contract_tovoid()
    {
        if (request()->isPost()) {
			$param = get_params();
			$tips='操作成功，合同已转入到作废合同列表';
			if($param['void_status'] == 1){
				$param['void_uid'] = $this->uid;
				$param['void_time'] = time();
			}
			else{
				$param['void_uid'] = 0;
				$param['void_time'] = 0;
				$tips='操作成功，合同已从作废合同列表转出';
			}
			if (Db::name('Contract')->strict(false)->update($param) !== false) {
				return to_assign(0, $tips);
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//获取产品分类数据
    public function get_productcate_tree()
    {
        $cate = get_base_data('ProductCate');
        $list = get_tree($cate, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }
	
	//获取销售产品列表
	public function get_product()
    {
        $param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('Product')->field('id,title,sale_price,purchase_price,base_price,unit,specs')->order('id asc')->where($where)->paginate(['list_rows'=> $rows]);
        table_assign(0, '', $list);
    }
	
	/*
	-------------------------------------------------分割线---------------------------------------------------------
	*/
	//获取采购合同协议
	public function get_purchase()
    {
        $param = get_params();
		$uid = $this->uid;
		$where = array();
		$whereOr = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|name|code', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['check_status', '=', 2];
		$auth = isAuth($uid,'contract_admin','conf_1');
		if($auth == 0){
			$whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			$dids_a = get_leader_departments($uid);
			$dids_b = get_role_departments($uid);
			$dids = array_merge($dids_a, $dids_b);
			if(!empty($dids)){
				$whereOr[] = ['did','in',$dids];
			}
		}
		$model = new Purchase();
        $list = $model->datalist($param,$where,$whereOr);
        return table_assign(0, '', $list);
    }

	//采购合同归档操作
    public function purchase_archive()
    {
        if (request()->isPost()) {
			$param = get_params();
			$tips='操作成功，合同已转入到归档合同列表';
			if($param['archive_status'] == 1){
				$param['archive_uid'] = $this->uid;
				$param['archive_time'] = time();
			}
			else{
				$param['archive_uid'] = 0;
				$param['archive_time'] = 0;
				$tips='操作成功，合同已从归档合同列表转出';
			}
			if (Db::name('Purchase')->strict(false)->update($param) !== false) {
				return to_assign(0, $tips);
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//采购合同中止等操作
    public function purchase_stop()
    {
        if (request()->isPost()) {
			$param = get_params();
			$tips='操作成功，合同已转入到中止合同列表';
			if($param['stop_status'] == 1){
				$param['stop_uid'] = $this->uid;
				$param['stop_time'] = time();
			}
			else{
				$param['stop_uid'] = 0;
				$param['stop_time'] = 0;
				$tips='操作成功，合同已从中止合同列表转出';
			}
			if (Db::name('Purchase')->strict(false)->update($param) !== false) {
				return to_assign(0, $tips);
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	//采购合同作废等操作
    public function purchase_tovoid()
    {
        if (request()->isPost()) {
			$param = get_params();
			$tips='操作成功，合同已转入到作废合同列表';
			if($param['void_status'] == 1){
				$param['void_uid'] = $this->uid;
				$param['void_time'] = time();
			}
			else{
				$param['void_uid'] = 0;
				$param['void_time'] = 0;
				$tips='操作成功，合同已从作废合同列表转出';
			}
			if (Db::name('Purchase')->strict(false)->update($param) !== false) {
				return to_assign(0, $tips);
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//获取采购品分类数据
    public function get_purchasedcate_tree()
    {
        $cate = get_base_data('PurchasedCate');
        $list = get_tree($cate, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }
	
	//获取供应商列表
	public function get_supplier()
    {
        $param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('Supplier')->field('id,title,address')->order('id asc')->where($where)->paginate(['list_rows'=> $rows])->each(function($item, $key){
			$contact = Db::name('SupplierContact')->where(['sid'=>$item['id'],'is_default'=>1])->find();
			if(!empty($contact)){
				$item['contact_name'] = $contact['name'];
				$item['contact_mobile'] = $contact['mobile'];
			}
			else{
				$item['contact_name'] = '';
				$item['contact_mobile'] = '';
			}
			return $item;
		});
        table_assign(0, '', $list);
    }
	
	//获取采购物品列表
	public function get_purchased()
    {
        $param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('Purchased')->field('id,title,purchase_price,sale_price,unit,specs')->order('id asc')->where($where)->paginate(['list_rows'=> $rows]);
        table_assign(0, '', $list);
    }	
	
	//获取供应商联系人数据
	public function get_supplier_contact()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['sid', '=', $param['supplier_id']];
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$content = SupplierContact::where($where)
			->order('create_time desc')
            ->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) {					
				$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
				$item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
			});
		return table_assign(0, '', $content);
    }
	
	//设置供应商联系人
	public function set_supplier_contact()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$detail= SupplierContact::where(['id' => $param['id']])->find();
			SupplierContact::where(['sid' => $detail['sid']])->strict(false)->field(true)->update(['is_default'=>0]);
			$res = SupplierContact::where(['id' => $param['id']])->update(['is_default'=>1]);
			if ($res) {
				add_log('edit', $param['id'], $param,'供应商联系人');
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }
}
