<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 勾股OA http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
declare (strict_types = 1);
namespace app\service\controller;
use app\service\model\Problems as ProblemsModel;
use app\service\model\Solutions;

use app\api\BaseController;
use think\facade\Db;

class Api extends BaseController
{
    public function get_problemscate()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where=[];
			$where[]=['status','=',1];
			$where[]=['delete_time','=',0];
            $list = Db::name('ProblemsCate')->where($where)->select();
			$res['data'] = $list;
            return table_assign(0, '', $res);
        }
    }
	
   /**
    * 方案审批
    */
    public function solutions_check()
    {
		$param = get_params();
		$param['check_id'] = $this->uid;
		$param['check_time'] = time();
		$auth = isAuth($this->uid,'service_admin','conf_1');
		if($auth == 0){
			return to_assign(1, "只有售后模块的管理员才有权限操作"); 
		}
		$model=new solutions();
		$model->edit($param);
    } 
}
