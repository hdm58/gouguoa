<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
declare (strict_types = 1);
namespace app\user\controller;

use app\api\BaseController;
use app\user\model\Department as DepartmentModel;
use think\facade\Db;

class Api extends BaseController
{
    //删除档案记录相关
    public function del_profiles()
    {
        $id = get_params("id");
		if (Db::name('AdminProfiles')->where('id', $id)->update(['delete_time'=>time()]) !== false) {
			return to_assign(0, "删除成功");
		} else {
			return to_assign(1, "删除失败");
		}
    }

	//一键调部门
    public function change_check()
    {
        $id = get_params("id");
        $data['id'] = $id;
		$data['connect_time'] = time();
        $data['status'] = 2;
		$detail = Db::name('DepartmentChange')->where('id', $id)->find();
        if (Db::name('DepartmentChange')->update($data) !== false) {
			Db::name('Admin')->where('id', $detail['uid'])->update(['did' => $detail['to_did']]);
			Db::name('DepartmentAdmin')->where(['admin_id'=>$detail['uid'],'department_id'=>$detail['to_did']])->delete();			
			$info = Db::name('Admin')->where('id', $detail['uid'])->find();
			$model = new DepartmentModel();
			$auth_dids = $model->get_auth_departments($info);
			$son_dids = $model->get_son_departments($info);
			Db::name('Admin')->where('id',$detail['uid'])->update(['auth_dids'=>$auth_dids,'son_dids'=>$son_dids]);
            return to_assign(0, "操作成功");
        } else {
            return to_assign(1, "操作失败");
        }
    }
	
	//一键交接资料
    public function leave_check()
    {
        $id = get_params("id");
        $data['id'] = $id;
        $data['connect_time'] = time();
        $data['status'] = 2;
		$detail = Db::name('PersonalQuit')->where('id', $id)->find();
        $uid =  $detail['uid'];
        $connect_uid = $detail['connect_id'];
        if (Db::name('PersonalQuit')->update($data) !== false) {
			//项目负责人
            Db::name('Project')->where([['director_uid','=',$uid],['status','<',3]])->update(['director_uid' => $connect_uid]);
			//任务负责人
            Db::name('ProjectTask')->where([['director_uid','=',$uid],['status','<',3]])->update(['director_uid' => $connect_uid]);			
			//客户所属人
			$did = Db::name('Admin')->where('id', $connect_uid)->value('did');
            Db::name('Customer')->where([['belong_uid','=',$uid]])->update(['belong_uid' => $connect_uid,'belong_did'=>$did]);
			//合同
            Db::name('Contract')->where([['admin_id','=',$uid],['check_status','<',3]])->update(['admin_id' => $connect_uid]);
			Db::name('Admin')->where('id', $uid)->update(['status' => 2]);
            add_log('hand', $id);
            return to_assign(0, "交接成功");
        } else {
            return to_assign(1, "交接失败");
        }
    }
	
	//人事分析报表
    public function statistics()
    {
        $param = get_params();
		$types = isset($param['types']) ? $param['types'] : 0;
		//部门
		if($types==0){
			$departments = Db::name('Department')->where([['status','=',1]])->select()->toArray();
			$departments_x = [];
			$departments_y = [];
			foreach ($departments as $key => &$val) {
				$departments_x[] = $val['title'];
				$departments_y[] = Db::name('Admin')->where([['status','=',1],['id','>',1],['did','=',$val['id']]])->count();
			}
			// 构建部门人员
			$result = [
					'title' => [
						'text' => '各部门人员'
					],
					'xaxis' => $departments_x,
					'yaxis' => [
						[
							'type' => 'value',
							'axisLabel' => [
								'formatter' => '{value} 人'
							]
						]
					],
					'series' => [
						[
							'name' => '',
							'type' => 'bar',
							'barWidth'=>'50%',
							'data' => $departments_y
						]
					]
				];
			return to_assign(0, '', $result);
		}
		//性别
		if($types==1){
			$sex = [];
			$sex[]=[
				'name'=>'未知',
				'value'=> Db::name('Admin')->where('sex', 0)->where([['status','=',1],['id','>',1]])->count()
			];
			$sex[]=[
				'name'=>'男',
				'value'=> Db::name('Admin')->where('sex', 1)->where([['status','=',1],['id','>',1]])->count()
			];
			$sex[]=[
				'name'=>'女',
				'value'=> Db::name('Admin')->where('sex', 2)->where([['status','=',1],['id','>',1]])->count()
			];
			
			$result = [
				'title' => [
					'text' => '员工性别'
				],
				'series' => [
					'data' => $sex
				]
			];
			return to_assign(0, '', $result);
		}
		//年龄
		if($types==2){
			$a=0;
			$b=0;
			$c=0;
			$d=0;
			$e=0;
			$users = Db::name('Admin')->where([['status','=',1],['birthday','<>',0],['id','>',1]])->select()->toArray();
			foreach ($users as $key =>$val) {
				$age = calculateAge($val['birthday']);
				if($age>=16 && $age<=20){
					$a=$a+1;
				}
				if($age>=21 && $age<=30){
					$b=$b+1;
				}
				if($age>=31 && $age<=40){
					$c=$c+1;
				}
				if($age>=41 && $age<=50){
					$d=$d+1;
				}
				if($age>50){
					$e=$e+1;
				}
			}
			
			$age = [
				[
					'name'=>'16-20岁',
					'value'=> $a
				],
				[
					'name'=>'21-30岁',
					'value'=> $b
				],
				[
					'name'=>'31-40岁',
					'value'=> $c
				],[
					'name'=>'41-50岁',
					'value'=> $d
				],[
					'name'=>'50岁以上',
					'value'=> $e
				]
			];
			$result = [
				'title' => [
					'text' => '员工年龄'
				],
				'series' => [
					'data' => $age
				]
			];
			return to_assign(0, '', $result);
		}
		if($types==3){
			$staff_a = Db::name('Admin')->where([['status','=',1],['is_staff','=',1],['id','>',1]])->count();
			$staff_b = Db::name('Admin')->where([['status','=',1],['is_staff','=',2],['id','>',1]])->count();
			$staff_c = Db::name('Admin')->where([['status','=',1],['is_staff','=',3],['id','>',1]])->count();
			$staff = [
				[
					'name'=>'企业员工',
					'value'=> $staff_a
				],
				[
					'name'=>'劳务派遣',
					'value'=> $staff_b
				],
				[
					'name'=>'兼职员工',
					'value'=> $staff_c
				]
			];
			$result = [
				'title' => [
					'text' => '员工类型'
				],
				'series' => [
					'data' => $staff
				]
			];
			return to_assign(0, '', $result);
		}
    }
	
	//入职离职分析报表
    public function in_out()
    {
        $param = get_params();
		$year = date('Y');
		if (!empty($param['year_time'])) {
			$year = $param['year_time'];
		}
		//入职离职
		$months = [];
		$data_in =[];
		$data_out =[];
		for($m=1;$m<13;$m++){
			$month=$year.'-'.$m;
			$data=[];
			if($m<9){
				$month=$year.'-0'.$m;
			}
			$months[] = $month;
			$data_in[] = Db::name('Admin')->whereMonth('entry_time', $month)->where([['status','=',1],['id','>',1]])->count();
			$data_out[] = Db::name('PersonalQuit')->whereMonth('quit_time', $month)->where([['delete_time','=',0],['check_status','=',2]])->count();		
		}
		$result = [
			'title' => [
				'text' => $year.'年入职/离职数据分析'
			],
			'xaxis' => $months,
			'yaxis' => [
				[
					'type' => 'value',
					'axisLabel' => [
						'formatter' => '{value}'
					]
				]
			],
			'series' => [
				[
					'name' => '入职',
					'type' => 'bar',
					'barWidth'=>'30%',
					'data' => $data_in
				],
				[
					'name' => '离职',
					'type' => 'bar',
					'barWidth'=>'30%',
					'data' => $data_out
				]
			]
		];
		return to_assign(0, '', $result);
	}
}
