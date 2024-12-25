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
use app\contract\validate\PurchasedCateValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Purchasedcate extends BaseController
{	
	//类别
    public function datalist()
    {
        if (request()->isAjax()) {
            $cate = Db::name('PurchasedCate')->order('create_time asc')->select();
			$list = generateTree($cate);
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //分类添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(PurchasedCateValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $note_array = get_cate_son('PurchasedCate',$param['id']);
                if (in_array($param['pid'], $note_array)) {
                    return to_assign(1, '父级分类不能是该分类本身或其子分类');
                } else {
                    $param['update_time'] = time();
                    $res = Db::name('PurchasedCate')->strict(false)->field(true)->update($param);
                    if($res){
                        add_log('edit', $param['id'], $param);
                        return to_assign();
                    }
                }
            } else {
                try {
                    validate(PurchasedCateValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('PurchasedCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
			$cate = Db::name('PurchasedCate')->order('id desc')->select()->toArray();
			$cates = set_recursion($cate);
            if ($id > 0) {
                $detail = Db::name('PurchasedCate')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('pid', $pid);
            View::assign('cates', $cates);
            return view();
        }
    }

    //删除分类
    public function del()
    {
        $id = get_params("id");
        $cate_count = Db::name('PurchasedCate')->where(["pid" => $id])->count();
        if ($cate_count > 0) {
            return to_assign(1, "该分类下还有子分类，无法删除");
        }
        $product_count = Db::name('Product')->where(["cate_id" => $id,'delete_time'=>0])->count();
        if ($product_count > 0) {
            return to_assign(1, "该分类下还有产品，无法删除");
        }
        if (Db::name('PurchasedCate')->delete($id) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除分类成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
   
   
}
