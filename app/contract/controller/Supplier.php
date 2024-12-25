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

use app\base\BaseController;
use app\contract\model\Supplier as SupplierModel;
use app\contract\model\SupplierContact;
use app\contract\validate\SupplierValidate;
use app\contract\validate\SupplierContactValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Supplier extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new SupplierModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $where = [];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
    //新建编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(SupplierValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = Db::name('Supplier')->strict(false)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(SupplierValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('Supplier')->strict(false)->insertGetId($param);
                if ($insertId) {
					if(!empty($param['c_name'])){
						$contact = [
							'name' => $param['c_name'],
							'mobile' => $param['c_mobile'],
							'sex' => $param['c_sex'],
							'sid' => $insertId,
							'is_default' => 1,
							'create_time' => time(),
							'admin_id' => $this->uid
						];
						Db::name('SupplierContact')->strict(false)->field(true)->insert($contact);
					}
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if($id>0){
                $detail = Db::name('Supplier')->where('id',$id)->find();
				$detail['file_array'] = Db::name('File')->where('id','in',$detail['file_ids'])->select();
                View::assign('detail', $detail);
				return view('edit');
            }
            return view();
        }
    }
	
    //查看
    public function view()
    {
        $param = get_params();
		$detail = Db::name('Supplier')->where('id',$param['id'])->find();
		$detail['file_array'] = Db::name('File')->where('id','in',$detail['file_ids'])->select();
		View::assign('detail', $detail);
		return view();
    }
	
    //设置
    public function set()
    {
		$param = get_params();
        $res = Db::name('Supplier')->strict(false)->field('id,status')->update($param);
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
			return to_assign(0, '操作失败');
		}
    }  
	
	//删除
    public function del()
    {
		if (request()->isDelete()) {
			$params = get_params();
			$count = Db::name('Purchase')->where(['supplier_id' => $params['id'],'delete_time'=>0])->count();
			if($count>0){
				return to_assign(1, "该供应商有采购合同存在，不准删除");
			}
			$data['id'] = $params['id'];
			$data['delete_time'] = time();
			if (Db::name('Supplier')->update($data) !== false) {
				//删除客户联系人
				Db::name('SupplierContact')->where(['sid' => $params['id']])->update(['delete_time'=>time()]);
				return to_assign();
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }
	
	
    //添加供应商联系人
    public function contact_add()
    {
		$param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(SupplierContactValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = SupplierContact::strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(SupplierContactValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$count= SupplierContact::where(['sid' => $param['sid'],'delete_time' => 0])->count();
				if($count == 0){
					$param['is_default'] = 1;	
				}
                $param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $insertId = SupplierContact::strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $supplier_id = isset($param['sid']) ? $param['sid'] : 0;
            $id = isset($param['id']) ? $param['id'] : 0;
			if ($id > 0) {
				View::assign('detail', (new SupplierContact())->detail($id));
				return view('contact_edit');
			}
			$supplier_name = Db::name('Supplier')->where('id',$supplier_id)->value('title');
            View::assign('supplier_id', $supplier_id);
            View::assign('supplier_name', $supplier_name);
            return view();
        }
	}
	
    //删除供应商联系人
    public function contact_del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$contact = SupplierContact::where(['id' => $param['id']])->find();
			if($contact['is_default'] == 1){
				return to_assign(1, '供应商的首要联系人不能删除');
			}
			if($contact['admin_id'] != $this->uid){
				return to_assign(1, '你不是该联系人的创建人，无权限删除');
			}
            $param['delete_time'] = time();
			$res = SupplierContact::strict(false)->field(true)->update($param);
			if ($res) {
				add_log('edit', $param['id'], $param);
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }  
}
