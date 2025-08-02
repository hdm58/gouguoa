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
namespace app\disk\controller;

use app\api\BaseController;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
    //获取共享空间
    public function get_group()
    {
		$where=[];
		$whereOr=[];
		$where[] = ['delete_time','=',0];
		$uid=$this->uid;
		if($uid>1){
			$map1=[
				['admin_id','=',$uid],
			];
			$map2=[
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',director_uids)")],
			];
			$map3=[
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',group_uids)")],
			];
			$whereOr =[$map1,$map2,$map3];
		}
		$list = Db::name('DiskGroup')
			->where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->select()->toArray();
		return to_assign(0, '',$list);
	}
	
    /**
    * 空间成员列表
    */
    public function memberlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$group_uids = Db::name('DiskGroup')->where('id',$param['id'])->value('group_uids');
			$list['data']=[];
			if(!empty($group_uids)){
				$list['data'] = Db::name('Admin')
					->field('a.*,p.title as position, d.title as department')
					->alias('a')
					->join('Position p','p.id = a.position_id')
					->join('Department d','d.id = a.did')
					->where([['a.id','in',$group_uids],['a.status','=',1]])
					->select()->toArray();
			}
			table_assign(0,'', $list);
        }
        else{
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			View::assign('detail', $detail);
			return view();
        }
    }
	
    /**
    * 设置空间成员
    */
    public function memberset()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid=$this->uid;
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			$array = array_map('trim', explode(',', $detail['director_uids']));
			if($uid==1 || $detail['admin_id'] == $uid || in_array($uid, $array)){
				$res = Db::name('DiskGroup')->where('id',$param['id'])->update(['group_uids'=>$param['group_uids']]);
				if($res!==false){
					return to_assign(0,'操作成功',['return_id'=>$param['id']]);
				}
				else{
					return to_assign(1,'操作失败');
				}
			}else{
				return to_assign(1,'只要超级管理员、创建人、空间管理人员才有权限操作');
			}
        }
        else{
			// 禁止访问
			throw new \think\exception\HttpException(403, '禁止访问');
        }
    }
	
    /**
    * 空间管理员列表
    */
    public function adminlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$director_uids = Db::name('DiskGroup')->where('id',$param['id'])->value('director_uids');
			$list['data']=[];
			if(!empty($director_uids)){
				$list['data'] = Db::name('Admin')
					->field('a.*,p.title as position, d.title as department')
					->alias('a')
					->join('Position p','p.id = a.position_id')
					->join('Department d','d.id = a.did')
					->where([['a.id','in',$director_uids],['a.status','=',1]])
					->select()->toArray();
			}
			table_assign(0,'', $list);
        }
        else{
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			View::assign('detail', $detail);
			return view();
        }
    }
	
    /**
    * 设置空间管理员
    */
    public function adminset()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid=$this->uid;
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			if($uid==1 || $detail['admin_id'] == $uid){
				$res = Db::name('DiskGroup')->where('id',$param['id'])->update(['director_uids'=>$param['director_uids']]);
				if($res!==false){
					return to_assign(0,'操作成功',['return_id'=>$param['id']]);
				}
				else{
					return to_assign(1,'操作失败');
				}
			}else{
				return to_assign(1,'只要超级管理员和创建人才有权限操作');
			}
        }
        else{
			// 禁止访问
			throw new \think\exception\HttpException(403, '禁止访问');
        }
    }
}
