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
}
