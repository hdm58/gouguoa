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

namespace app\oa\controller;

use app\base\BaseController;
use app\home\model\Leaves;
use app\home\model\Outs;
use app\home\model\Overtimes;
use app\home\model\Trips;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Attendance extends BaseController
{
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
		$tab = isset($param['tab']) ? $param['tab'] : 1;
        if (request()->isAjax()) {
			$where=[];
			if (!empty($param['create_time'])) {
				$create_time =explode('~', $param['create_time']);
				$where[] = ['create_time', 'between', [strtotime(urldecode($create_time[0])),strtotime(urldecode($create_time[1].' 23:59:59'))]];
			}
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			if (!empty($param['keywords'])) {
                $where[] = ['reason', 'like', '%' . $param['keywords'] . '%'];
            }
			$where[] = ['admin_id', '=',$this->uid];
			$where[]=['delete_time','=',0];
			$model = new Leaves();
			if($tab == 2){
				$model = new Trips();
			}
			if($tab == 3){
				$model = new Outs();
			}
			if($tab == 4){
				$model = new Overtimes();
			}
            $list = $model->datalist($param,$where);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('tab',$tab);
            return view('datalist'.$tab);
        }
    }
}
