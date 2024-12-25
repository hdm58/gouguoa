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
			$sys_module = Db::name('AdminModule')->select()->toArray();
			View::assign('sys_module', $sys_module);
            return view();
        }
    }
	
	//新增/编辑模块
    public function add()
    {
		$param = get_params();
        if (request()->isAjax()) {
			if($this->uid!=1){
				return to_assign(1,'只有系统超级管理员才有权限新增或编辑模块！');
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ModuleCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                Db::name('AdminModule')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(ModuleCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $mid = Db::name('AdminModule')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $mid, $param);
            }
			return to_assign();
        } else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$module=[];
			if ($id > 0) {
                $module = Db::name('AdminModule')->where(['id' => $id])->find();
            }
			View::assign('id', $id);
			View::assign('module', $module);
            return view();
        }
    }
	
	//安装模块
    public function install()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限安装模块！');
		}
        $param = get_params();
		$name = $param['name'];
		$data = curl_post('https://www.gougucms.com/home/get_module/module',['name'=>$name]);
		$json_data = json_decode($data, true);
		if($json_data['code'] == 1){
			return to_assign(1,$json_data['msg']);
		}
		$detail = $json_data['data'];
		$rule = unserialize($detail['rule']);
		if(empty($rule)){
			return to_assign(1,'找不到该模块的信息');
		}
		$prefix = get_config('database.connections.mysql.prefix');
		
		$insert = [];
		$insert['title'] = $detail['title'];
		$insert['name'] = $detail['name'];
		$insert['type'] = $detail['type'];
		$insert['sourse'] = $detail['sourse'];
		$insert['create_time'] = time();
		try {
			validate(ModuleCheck::class)->scene('add')->check($insert);
		} catch (ValidateException $e) {
			// 验证失败 输出错误信息
			return to_assign(1, $e->getError());
		}
		//sql语句
		$sql_file = CMS_ROOT . '/app/'.$name.'/config/install.sql';
		$sql_array = [];
		if(file_exists($sql_file)){
			$sql = file_get_contents($sql_file);
			$sql_array = preg_split("/;[\r\n]+/", str_replace("oa_", $prefix, $sql));
		}	
		//var_dump($sql_array);exit;
		Db::startTrans();
        try {			
			// 导入sql数据并创建表
			if(!empty($sql_array)){
				foreach ($sql_array as $k => $v) {
					if (!empty($v)) {
						Db::execute($v);
					}
				}
			}			
			//如果安装过该模块，删除原来的菜单信息
			Db::name('AdminRule')->where('module',$name)->delete();
			$sort = Db::name('AdminRule')->where('pid',0)->max('sort');
			$this->add_rule($rule,0,$sort+1);			
			$mid = Db::name('AdminModule')->strict(false)->field(true)->insertGetId($insert);
			
			Db::commit();
		}
		catch (\Exception $e) {
			//回滚事务
			Db::rollback();
			return to_assign(1,'捕获到异常'.$e->getMessage());
		}
		
		//更新超级管理员的权限节点
		$rules = Db::name('AdminRule')->column('id');
		$admin_rules = implode(',',$rules);
		$res = Db::name('AdminGroup')->strict(false)->where('id',1)->update(['rules'=>$admin_rules,'update_time'=>time()]);		
		if($res!==false){
			// 删除后台节点缓存
            clear_cache('adminRules');
			add_log('install', $mid, $insert);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
	//递归插入菜单数据
	protected function add_rule($data, $pid=0,$sort=0)
	{
		foreach($data as $k => $v)
		{
			$rule=[
				'title'  => $v['title'],
				'name'   => $v['name'],
				'src'    => $v['src'],
				'module' => $v['module'],
				'menu'   => $v['menu'],
				'icon'   => $v['icon'],
				'pid'    => $pid,
				'sort'   => $sort,
				'create_time' => time()
			];
			$new_id = Db::name('AdminRule')->strict(false)->field(true)->insertGetId($rule);
			if(!empty($v['son'] && $new_id)){
				$this->add_rule($v['son'],$new_id);			
			}
		}
	}

    //删除
    public function del()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限删除模块！');
		}
        $param = get_params();
		$module = Db::name('AdminModule')->where('id',$param['id'])->find();
		if($module['type'] == 1){
			return to_assign(1,'系统模块不能删除');
		}
		$param['update_time']= time();
		$res = Db::name('AdminModule')->where('id',$param['id'])->delete();
		if($res!==false){
			add_log('delete', $module['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }

    //卸载
    public function uninstall()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限卸载模块！');
		}
        $param = get_params();
		$module = Db::name('AdminModule')->where('name',$param['name'])->find();
		if($module['type'] == 1){
			return to_assign(1,'系统模块不能卸载');
		}
		$param['update_time']= time();
		$res = Db::name('AdminModule')->where('name',$param['name'])->update(['status'=>0]);
		if($res!==false){
			Db::name('AdminRule')->strict(false)->where('module',$module['name'])->update(['status'=>0]);
			// 删除后台节点缓存
            clear_cache('adminRules');
			add_log('uninstall', $module['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
	//恢复
    public function recovery()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限恢复模块！');
		}
        $param = get_params();
		$module = Db::name('AdminModule')->where('name',$param['name'])->find();
		$param['update_time']= time();
		$res = Db::name('AdminModule')->where('name',$param['name'])->update(['status'=>1]);
		if($res!==false){
			Db::name('AdminRule')->strict(false)->where('module',$module['name'])->update(['status'=>1]);
			// 删除后台节点缓存
            clear_cache('adminRules');
			add_log('recovery', $module['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
	
	//数据权限列表
    public function data_auth()
    {
        if (request()->isAjax()) {
            $auth = Db::name('DataAuth')->select();
            return to_assign(0, '', $auth);
        } else {
            return view();
        }
    }
	
	//数据权限详情
    public function data_auth_detail()
    {
		$param = get_params();
        if (request()->isPost()) {
			$param['update_time'] = time();
            $res = Db::name('DataAuth')->strict(false)->field(true)->update($param);
            return to_assign();
        } else {
			$detail = Db::name('DataAuth')->where('name',$param['name'])->find();
			View::assign('detail', $detail);
            return view();
        }
    }
	
}
