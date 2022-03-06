<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\validate\ModuleCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Module extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $module = Db::name('AdminModule')->select();
            return to_assign(0, '', $module);
        } else {
            return view();
        }
    }

    //添加新增/编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$param['name'] = preg_replace('# #','',$param['name']);
            if ($param['id'] > 0) {
				$module = Db::name('AdminModule')->where('id',$param['id'])->find();
				if($module['type'] == 1){
					return to_assign(1,'系统模块不能编辑');
				}
                try {
                    validate(ModuleCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::name('AdminModule')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(ModuleCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $mid = Db::name('AdminModule')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $mid, $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if($id>0){
                $detail = Db::name('AdminModule')->where('id',$id)->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //禁用/启用
    public function disable()
    {
        $param = get_params();
		$module = Db::name('AdminModule')->where('id',$param['id'])->find();
		if($module['type'] == 1){
			return to_assign(1,'系统模块不能禁用');
		}
		$param['update_time']= time();
		$res = Db::name('AdminModule')->strict(false)->field('status,update_time')->update($param);
		if($res!==false){
			Db::name('AdminRule')->strict(false)->where('module',$module['name'])->field('status')->update(['status'=>$param['status']]);
			// 删除后台节点缓存
            clear_cache('adminRules');
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
}
