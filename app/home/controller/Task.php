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

namespace app\home\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Task extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $module = Db::name('TimingTask')->where(['delete_time' => 0])->select();
            return to_assign(0, '', $module);
        } else {
            return view();
        }
    }
	
	//新增/编辑模块
    public function add()
    {
		$param = get_params();
        if (request()->isAjax()) {
			if($this->uid!=1){
				return to_assign(1,'只有系统超级管理员才有权限新增或编辑定时任务！');
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                Db::name('TimingTask')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                $tid = Db::name('TimingTask')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $tid, $param);
            }
			return to_assign();
        } else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$task=[];
			if ($id > 0) {
                $task = Db::name('TimingTask')->where(['id' => $id])->find();
            }
			View::assign('id', $id);
			View::assign('task', $task);
            return view();
        }
    }
	
    //删除
    public function delete()
    {
		if($this->uid!=1){
			return to_assign(1,'只有超级管理员才有权限删除定时任务！');
		}
        $param = get_params();
		$res = Db::name('TimingTask')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if($res!==false){
			add_log('delete', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
}
