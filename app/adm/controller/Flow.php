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
use app\adm\validate\FlowCateCheck;
use app\adm\validate\FlowCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Flow extends BaseController
{	

    public function datalist()
    {
        if (request()->isAjax()) {
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('Flow')
				->field('f.*,fc.title as cate,fm.title as module')
				->alias('f')
                ->join('FlowCate fc', 'fc.id = f.cate_id', 'left')
                ->join('FlowModule fm', 'fm.id = fc.module_id', 'left')
				->order('id desc')->paginate(['list_rows'=> $rows])
				->each(function($item, $key){
					if(!empty($item['department_ids'])){
						$item['departments']=get_department_name($item['department_ids']);
					}
					else{
						$item['departments']='全部';
					}
					if(!empty($item['copy_uids'])){
						$copy_unames=Db::name('Admin')->where([['status','=',1],['id','in',$item['copy_uids']]])->column('name');
						$item['copy_unames']=implode(',',$copy_unames);
					}
					else{
						$item['copy_unames']='-';
					}
					return $item;
				});
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加新增/编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$param['flow_list'] = '';
			$flow_list=[];
			//固定审批流
			if($param['check_type']==2){
				$roleData = isset($param['check_role']) ? $param['check_role'] : '';
				$positionIdData = isset($param['check_position_id']) ? $param['check_position_id'] : '';
				$checkUidsData = isset($param['check_uids_a']) ? $param['check_uids_a'] : '';
				$checkTypesData = isset($param['check_types']) ? $param['check_types'] : '';
				foreach ($roleData as $key => $value) {
					if (!$value) {
						continue;
					}
					if($value==3 && $positionIdData[$key]==''){
						return to_assign(1, '第'.($key+1).'行的指定岗位职称未选择');
						break;
					}
					if($value>3 && $checkUidsData[$key]==''){
						return to_assign(1, '第'.($key+1).'行的指定人未选择');
						break;
					}
					$item = [];
					$item['check_role'] = $value;
					$item['check_position_id'] = $positionIdData[$key];
					$item['check_uids'] = $checkUidsData[$key];
					$item['check_types'] = $checkTypesData[$key];
					$flow_list[]=$item;	
				}
				$param['flow_list'] = serialize($flow_list);
			}
			//可回退审批流
			if($param['check_type']==3){
				$flowNameData = isset($param['flow_name']) ? $param['flow_name'] : '';
				$checkUidsData = isset($param['check_uids_b']) ? $param['check_uids_b'] : '';
				foreach ($flowNameData as $key => $value) {
					if (!$value) {
						continue;
					}
					if($checkUidsData[$key]==''){
						return to_assign(1, '第'.($key+1).'行的指定人未选择');
						break;
					}
					$item = [];
					$item['check_role'] = 5;
					$item['flow_name'] = $value;
					$item['check_uids'] = $checkUidsData[$key];
					$flow_list[]=$item;	
				}
				if(empty($flow_list)){
					return to_assign(1, '审批流程信息未完善');
				}
				$param['flow_list'] = serialize($flow_list);
			}
			
            if ($param['id'] > 0) {
                try {
                    validate(FlowCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::name('Flow')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(FlowCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $mid = Db::name('Flow')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $mid, $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $check_type = 1;
            if($id>0){
                $detail = Db::name('Flow')->where('id',$id)->find();
                $flow_list = unserialize($detail['flow_list']);
				if(!empty($flow_list)){
					foreach ($flow_list as $key => &$val) {
						$val['check_unames'] ='';
						$val['check_position_title'] ='';
						if($val['check_role']==3){
							$val['check_position_title'] = Db::name('Position')->where('id', '=', $val['check_position_id'])->value('title');
						}
						if($val['check_role']>3){
							$check_unames = Db::name('Admin')->where('id', 'in', $val['check_uids'])->column('name');
							$val['check_unames'] = implode(',', $check_unames);
						}
					}
				}
				$detail['flow_list'] = $flow_list;
				$detail['copy_unames'] ='';
				if($detail['copy_uids']!=''){
					$copy_unames = Db::name('Admin')->where('id', 'in', $detail['copy_uids'])->column('name');
					$detail['copy_unames'] = implode(',', $copy_unames);
				}
				$check_type = $detail['check_type'];
				//var_dump($flow_list);exit;
                View::assign('detail', $detail);
            }
            View::assign('check_type', $check_type);
            View::assign('id', $id);
            return view();
        }
    }

    //禁用/启用
    public function check()
    {
        $param = get_params();
		$param['update_time']= time();
		$res = Db::name('Flow')->strict(false)->field('status,update_time')->update($param);
		if($res!==false){
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
	//审批模块
    public function modulelist()
    {
        if (request()->isAjax()) {
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('FlowModule')
				->order('sort desc,id desc')
				->paginate(['list_rows'=> $rows]);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
    //添加模块
    public function module_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('FlowModule')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('FlowModule')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = Db::name('FlowModule')->find($id);
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }
	
    //设置
    public function module_check()
    {
		$param = get_params();
        $res = Db::name('FlowModule')->strict(false)->field('id,status')->update($param);
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
	
	//审批类型列表
    public function catelist()
    {
        if (request()->isAjax()) {
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('FlowCate')
				->field('fc.*,fm.title as module')
				->alias('fc')
				->join('FlowModule fm', 'fm.id = fc.module_id', 'left')
				->order('fc.sort desc,fc.id desc')
				->paginate(['list_rows'=> $rows])
				->each(function($item, $key){
					if(!empty($item['department_ids'])){
						$item['departments']=get_department_name($item['department_ids']);
					}
					else{
						$item['departments']='全部';
					}
					return $item;
				});
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
    //添加审批类型
    public function cate_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(FlowCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = Db::name('FlowCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(FlowCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('FlowCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = Db::name('FlowCate')->find($id);
				if(!empty($detail['department_ids'])){
					$detail['departments']=get_department_name($detail['department_ids']);
				}
				else{
					$detail['departments']='全部';
				}
				if($detail['template_id']>0){
					$detail['template_title'] = Db::name('Template')->where('id',$detail['template_id'])->value('title');
				}
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }
	
    //审批类型设置
    public function item_check()
    {
		$param = get_params();
        $res = Db::name('FlowCate')->strict(false)->field('id,status')->update($param);
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
