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
use app\user\validate\BasicUserCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Basic extends BaseController
{	
	public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where=[];
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
            $list = Db::name('BasicUser')->where($where)->order('create_time asc')->select();
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }
    //新建编辑
    public function add()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(BasicUserCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = Db::name('BasicUser')->strict(false)->field('title,types,id,update_time')->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(BasicUserCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('BasicUser')->strict(false)->field('title,types,id,create_time')->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //设置
    public function set()
    {
		$param = get_params();
        $res = Db::name('BasicUser')->strict(false)->field('id,status')->update($param);
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
