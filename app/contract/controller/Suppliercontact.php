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

namespace app\supplier\controller;

use app\base\BaseController;
use app\supplier\model\SupplierContact;
use app\supplier\validate\SupplierContactCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Contact extends BaseController
{	
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.name|a.mobile|s.title', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['a.delete_time', '=', 0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = SupplierContact::where($where)
                ->field('a.*,s.title as supplier')
                ->alias('a')
                ->join('Supplier s', 'a.sid = s.id')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
                    $item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
				});
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }
    //添加
    public function add()
    {
		$param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(SupplierContactCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = Db::name('SupplierContact')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(SupplierContactCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$count= Db::name('SupplierContact')->where(['sid' => $param['sid'],'delete_time' => 0])->count();
				if($count == 0){
					$param['is_default'] = 1;	
				}
                $param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $insertId = Db::name('SupplierContact')->strict(false)->field(true)->insertGetId($param);
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
				return view('edit');
			}
			$supplier_name = Db::name('Supplier')->where('id',$supplier_id)->value('title');
            View::assign('supplier_id', $supplier_id);
            View::assign('supplier_name', $supplier_name);
            return view();
        }
	}
	
    //设置
    public function del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$contact = Db::name('SupplierContact')->where(['id' => $param['id']])->find();
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
