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
use backup\Backup;
use think\facade\Db;
use think\facade\View;

class Area extends BaseController
{
    //数据表列表
    public function datalist()
    {
        if (request()->isAjax()) {
            $area = Db::name('Area')->select();
			$list = generateTree($area);
            return to_assign(0, '', $list);
        }
		else{
			$db = new Backup();
			$is_area = $db->check_table('area');
			View::assign('is_area', $is_area);
			return view();
		}
    }

    //新增/编辑
    public function add()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$pid = isset($param['pid']) ? $param['pid'] : 0;
        if (request()->isAjax()) {
			$param['level'] = 1;
			if($pid > 0){
				$level = Db::name('Area')->where('id', $pid)->value('level');
				$param['level'] = $level+1;
			}
            if ($param['id'] > 0) {
                Db::name('Area')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                $param['create_time'] = time();
                $rid = Db::name('Area')->strict(false)->field(true)->insertGetId($param);
            }
            return to_assign();
        } else {
            if ($id > 0) {
                $detail = Db::name('Area')->where('id', $id)->find();
                $detail['pname'] = Db::name('Area')->where('id', $detail['pid'])->value('name');
                View::assign('detail', $detail);
            }
			$pname='';
			if($pid>0){
				$pname = Db::name('Area')->where('id', $pid)->value('name');
			}
            View::assign('id', $id);
            View::assign('pid', $pid);
            View::assign('pname', $pname);
            return view();
        }
    }

    //设置
    public function set()
    {
		if (request()->isPost()) {
            $db = new Backup();
			$is_area = $db->check_table('area');
			if($is_area==0){
				$oa_area = file_get_contents(CMS_ROOT . '/public/static/home/file/oa_area.sql');
				$res = $db->run_sql($oa_area);
				if($res){
					add_log('import', 0, []);
					return to_assign();
				}
				else{
					return to_assign(1, '数据写入失败，请联系官方！');
				}	
			}
        }
        else if (request()->isDelete()) {
            $params = get_params();
			$log_type = 'recovery';
			if($params['status']==0){
				$count = Db::name('Area')->where(["pid" => $params['id']])->count();
				if ($count > 0) {
					return to_assign(1, "该记录下还有子记录，无法禁用");
				}
				$log_type = 'disable';
			}
			if (Db::name('Area')->where(["id" => $params['id']])->update(['status'=>$params['status']]) !== false) {
				add_log($log_type, $params['id'], []);
				return to_assign();
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }

}
