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

namespace app\adm\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Propertybrand extends BaseController
{	
	//资产品牌
    public function datalist()
    {
        if (request()->isAjax()) {
            $cate = Db::name('PropertyBrand')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }

    //添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
				$res = Db::name('PropertyBrand')->strict(false)->field(true)->update($param);
				if($res){
					add_log('edit', $param['id'], $param);
					return to_assign();
				}
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('PropertyBrand')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = Db::name('PropertyBrand')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //禁用启用
    public function check()
    {
		$param = get_params();
        $res = Db::name('PropertyBrand')->strict(false)->field('id,status')->update($param);
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
   
   
}
