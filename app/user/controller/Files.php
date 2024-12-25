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
use app\user\model\Admin as AdminList;
use think\facade\Db;
use think\facade\View;

class Files extends BaseController
{
    public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.username|a.name|a.nickname|a.mobile|a.desc', 'like', '%' . $param['keywords'] . '%'];
            }
			if (isset($param['status']) && $param['status']!='') {
                $where[] = ['a.status', '=', $param['status']];
            }
			else{
				$where[] = ['a.status', '<', 2];
			}
            if (!empty($param['political'])) {
                $where[] = ['a.political', '=', $param['political']];
            }
			if (!empty($param['position_name'])) {
                $where[] = ['a.position_name', '=', $param['position_name']];
            }
			if (!empty($param['position_rank'])) {
                $where[] = ['a.position_rank', '=', $param['position_rank']];
            }
            if (!empty($param['did'])) {
				$admin_array = Db::name('DepartmentAdmin')->where('department_id',$param['did'])->column('admin_id');
				$map1=[
					['a.id','in',$admin_array],
				];
				$map2=[
					['a.did', '=', $param['did']],
				];
				$whereOr =[$map1,$map2];
            }
			$where[] = ['a.id', '>', 1];
			$admin = AdminList::alias('a')
				->with('departments')
				->field('a.*,p.title as position,d.title as department')
				->join('Department d', 'd.id = a.did','left')
				->join('Position p', 'p.id = a.position_id','left')
				->where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->paginate(intval($this->pageSize))
				->order('a.id desc')
                ->each(function ($item, $key) {
					//遍历次要部门数据
					$departments = $item->departments->toArray();
					if(empty($departments)){
						$item->departments = '-';
					}
					else{
						$item->departments = split_array_field($departments,'title');
					}
                    $item->entry_time = empty($item->entry_time) ? '-' : date('Y-m-d', $item->entry_time);
                    $item->birthday = empty($item->birthday) ? '-' : date('Y-m-d', $item->birthday);
                    $item->last_login_time = empty($item->last_login_time) ? '-' : date('Y-m-d H:i', $item->last_login_time);
                    $item->last_login_ip = empty($item->last_login_ip) ? '-' : $item->last_login_ip;
					if($item->political==1){
						$item->political = '中共党员';
					}
					else if($item->political==2){
						$item->political = '团员';
					}
					else{
						$item->political = '-';
					}
                });
            return table_assign(0, '', $admin);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
		$id = isset($param['id'])?$param['id']:0;
        if (request()->isAjax()) {
            $param['birthday'] = empty($param['birthday']) ? '0':strtotime($param['birthday']);
            $param['graduate_day'] = empty($param['graduate_day']) ? '0':strtotime($param['graduate_day']);
            $param['work_date'] = empty($param['work_date']) ? '0':strtotime($param['work_date']);
			if($id == 1){
				return to_assign(1, '超级管理员信息不支持编辑');
			}
			$res = Db::name('Admin')->where(['id' => $id])->strict(false)->field(true)->update($param);
			if($res!==false){
				
				//教育经历
				$edu_start_time = isset($param['edu_start_time']) ? $param['edu_start_time'] : '';
				$edu_end_time = isset($param['edu_end_time']) ? $param['edu_end_time'] : '';
				$edu_title = isset($param['edu_title']) ? $param['edu_title'] : '';
				$edu_speciality = isset($param['edu_speciality']) ? $param['edu_speciality'] : '';
				$edu_education = isset($param['edu_education']) ? $param['edu_education'] : '';
				$edu_remark = isset($param['edu_remark']) ? $param['edu_remark'] : '';
				$edu_id = isset($param['edu_id']) ? $param['edu_id'] : 0;			

				if ($edu_start_time) {
					foreach ($edu_start_time as $key => $value) {
						if (!$value && $value !=' ') {
							continue;
						}
						$data = [];
						$data['start_time'] = $value;
						$data['end_time'] = $edu_end_time[$key];
						$data['title'] = $edu_title[$key];
						$data['speciality'] = $edu_speciality[$key];
						$data['education'] = $edu_education[$key];
						$data['remark'] = $edu_remark[$key];
						$data['admin_id'] = $id;
						$data['types'] = 1;
						if($edu_id[$key]>0){
							$data['id'] = $edu_id[$key];
							$data['update_time'] = time();
							Db::name('AdminProfiles')->strict(false)->field(true)->update($data);
						}
						else{
							$data['create_uid'] = $this->uid;
							$data['create_time'] = time();
							$data['update_time'] = time();
							$eid = Db::name('AdminProfiles')->strict(false)->field(true)->insertGetId($data);
						}						
					}
				}
			
			
				//工作经历			
				$work_start_time = isset($param['work_start_time']) ? $param['work_start_time'] : '';
				$work_end_time = isset($param['work_end_time']) ? $param['work_end_time'] : '';
				$work_title = isset($param['work_title']) ? $param['work_title'] : '';
				$work_position = isset($param['work_position']) ? $param['work_position'] : '';
				$work_remark = isset($param['work_remark']) ? $param['work_remark'] : '';
				$work_id = isset($param['work_id']) ? $param['work_id'] : 0;
			
				if ($work_start_time) {
					foreach ($work_start_time as $key => $value) {
						if (!$value && $value !=' ') {
							continue;
						}
						$data = [];
						$data['start_time'] = $value;
						$data['end_time'] = $work_end_time[$key];
						$data['title'] = $work_title[$key];
						$data['position'] = $work_position[$key];
						$data['remark'] = $work_remark[$key];
						$data['admin_id'] = $id;
						$data['types'] = 2;
						if($work_id[$key]>0){
							$data['id'] = $work_id[$key];
							$data['update_time'] = time();
							Db::name('AdminProfiles')->strict(false)->field(true)->update($data);
						}
						else{
							$data['create_uid'] = $this->uid;
							$data['create_time'] = time();
							$data['update_time'] = time();
							$eid = Db::name('AdminProfiles')->strict(false)->field(true)->insertGetId($data);
						}						
					}
				}
			
				//相关证书
				$certificate_start_time = isset($param['certificate_start_time']) ? $param['certificate_start_time'] : '';
				$certificate_title = isset($param['certificate_title']) ? $param['certificate_title'] : '';
				$certificate_authority = isset($param['certificate_authority']) ? $param['certificate_authority'] : '';
				$certificate_remark = isset($param['certificate_remark']) ? $param['certificate_remark'] : '';
				$certificate_id = isset($param['certificate_id']) ? $param['certificate_id'] : 0;

				if ($certificate_start_time) {
					foreach ($certificate_start_time as $key => $value) {
						if (!$value && $value !=' ') {
							continue;
						}
						$data = [];
						$data['start_time'] = $value;
						$data['title'] = $certificate_title[$key];
						$data['authority'] = $certificate_authority[$key];
						$data['remark'] = $certificate_remark[$key];
						$data['admin_id'] = $id;
						$data['types'] = 3;
						if($certificate_id[$key]>0){
							$data['id'] = $certificate_id[$key];
							$data['update_time'] = time();
							Db::name('AdminProfiles')->strict(false)->field(true)->update($data);
						}
						else{
							$data['create_uid'] = $this->uid;
							$data['create_time'] = time();
							$data['update_time'] = time();
							$eid = Db::name('AdminProfiles')->strict(false)->field(true)->insertGetId($data);
						}						
					}
				}
				
				//计算机技能
				$skills_title = isset($param['skills_title']) ? $param['skills_title'] : '';
				$skills_know = isset($param['skills_know']) ? $param['skills_know'] : '';
				$skills_remark = isset($param['skills_remark']) ? $param['skills_remark'] : '';
				$skills_id = isset($param['skills_id']) ? $param['skills_id'] : 0;

				if ($skills_title) {
					foreach ($skills_title as $key => $value) {
						if (!$value && $value !=' ') {
							continue;
						}
						$data = [];
						$data['title'] = $skills_title[$key];
						$data['know'] = $skills_know[$key];
						$data['remark'] = $skills_remark[$key];
						$data['admin_id'] = $id;
						$data['types'] = 4;
						if($skills_id[$key]>0){
							$data['id'] = $skills_id[$key];
							$data['update_time'] = time();
							Db::name('AdminProfiles')->strict(false)->field(true)->update($data);
						}
						else{
							$data['create_uid'] = $this->uid;
							$data['create_time'] = time();
							$data['update_time'] = time();
							$eid = Db::name('AdminProfiles')->strict(false)->field(true)->insertGetId($data);
						}						
					}
				}
				
				//语言能力
				$language_title = isset($param['language_title']) ? $param['language_title'] : '';
				$language_know = isset($param['language_know']) ? $param['language_know'] : '';
				$language_remark = isset($param['language_remark']) ? $param['language_remark'] : '';
				$language_id = isset($param['language_id']) ? $param['language_id'] : 0;

				if ($language_title) {
					foreach ($language_title as $key => $value) {
						if (!$value && $value !=' ') {
							continue;
						}
						$data = [];
						$data['title'] = $language_title[$key];
						$data['know'] = $language_know[$key];
						$data['remark'] = $language_remark[$key];
						$data['admin_id'] = $id;
						$data['types'] = 5;
						if($language_id[$key]>0){
							$data['id'] = $language_id[$key];
							$data['update_time'] = time();
							Db::name('AdminProfiles')->strict(false)->field(true)->update($data);
						}
						else{
							$data['create_uid'] = $this->uid;
							$data['create_time'] = time();
							$data['update_time'] = time();
							$eid = Db::name('AdminProfiles')->strict(false)->field(true)->insertGetId($data);
						}						
					}
				}
				
				add_log('edit', $id, $param);
				return to_assign();
			}
			else{
				return to_assign(1, '提交失败');
			}
        } else {
            $detail = get_admin($id);
			$detail['pname'] = Db::name('Admin')->where('id',$detail['pid'])->value('name');
			$detail['position'] = Db::name('Position')->where('id',$detail['position_id'])->value('title');
			$detail['department'] = Db::name('Department')->where('id',$detail['did'])->value('title');
			$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$id)->column('department_id');
			$department_names = Db::name('Department')->whereIn('id',$department_ids)->column('title');
			$detail['department_names'] = implode(',',$department_names);
			if($detail['birthday']>0){
				$detail['birthday'] = date('Y-m-d',$detail['birthday']);
			}
			else{
				$detail['birthday'] = '';
			}
			if($detail['graduate_day']>0){
				$detail['graduate_day'] = date('Y-m-d',$detail['graduate_day']);
			}
			else{
				$detail['graduate_day'] = '';
			}
			if($detail['work_date']>0){
				$detail['work_date'] = date('Y-m-d',$detail['work_date']);
			}
			else{
				$detail['work_date'] = '';
			}
			
			if($detail['file_ids'] !=''){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			
			$edu = Db::name('AdminProfiles')->where(['types'=>1,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$work = Db::name('AdminProfiles')->where(['types'=>2,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$certificate = Db::name('AdminProfiles')->where(['types'=>3,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$skills = Db::name('AdminProfiles')->where(['types'=>4,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$language = Db::name('AdminProfiles')->where(['types'=>5,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
            View::assign('edu', $edu);
            View::assign('work', $work);
            View::assign('skills', $skills);
            View::assign('certificate', $certificate);
            View::assign('language', $language);
            View::assign('detail', $detail);
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
		$param = get_params();
		$id = isset($param['id'])?$param['id']:0;
		$detail = get_admin($id);
		$detail['pname'] = Db::name('Admin')->where('id',$detail['pid'])->value('name');
		$detail['position'] = Db::name('Position')->where('id',$detail['position_id'])->value('title');
		$detail['department'] = Db::name('Department')->where('id',$detail['did'])->value('title');
		$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$id)->column('department_id');
		$department_names = Db::name('Department')->whereIn('id',$department_ids)->column('title');
		$detail['department_names'] = implode(',',$department_names);
		if($detail['birthday']>0){
			$detail['birthday'] = date('Y-m-d',$detail['birthday']);
		}
		else{
			$detail['birthday'] = '';
		}
		if($detail['graduate_day']>0){
			$detail['graduate_day'] = date('Y-m-d',$detail['graduate_day']);
		}
		else{
			$detail['graduate_day'] = '';
		}
		if($detail['work_date']>0){
			$detail['work_date'] = date('Y-m-d',$detail['work_date']);
		}
		else{
			$detail['work_date'] = '';
		}
		if($detail['file_ids'] !=''){
			$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['file_array'] = $file_array;
		}
		$edu = Db::name('AdminProfiles')->where(['types'=>1,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
		$work = Db::name('AdminProfiles')->where(['types'=>2,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
		$certificate = Db::name('AdminProfiles')->where(['types'=>3,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
		$skills = Db::name('AdminProfiles')->where(['types'=>4,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
		$language = Db::name('AdminProfiles')->where(['types'=>5,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
		View::assign('edu', $edu);
		View::assign('work', $work);
		View::assign('skills', $skills);
		View::assign('certificate', $certificate);
		View::assign('language', $language);
		View::assign('detail', $detail);
		View::assign('id', $id);
		return view();
    }
}
