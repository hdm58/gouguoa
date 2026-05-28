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

namespace app\analysis\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Order extends BaseController
{	
    /**
    * 员工销售业绩
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title|mobile|address|tags|content', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['provinceid'])) {
                $where[] = ['provinceid', '=', $param['provinceid']];
            }
			if (!empty($param['cityid'])) {
                $where[] = ['cityid', '=', $param['cityid']];
            }
			if (!empty($param['distid'])) {
                $where[] = ['distid', '=', $param['distid']];
            }
            $list = Db::name('Admin')->where([['status','=',1],['id','>',1]])->select()->toArray();
			$res['data'] = $list;
            return table_assign(0, '', $res);
        }
        else{
            return view();
        }
    }
	
    /**
    * 订单排行
    */
    public function salelist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{
			
			return view();
		}
    }
	
    /**
    * 收款排行
    */
    public function incomelist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{
			
			return view();
		}
    }

}
