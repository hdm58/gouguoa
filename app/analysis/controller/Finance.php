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

class Finance extends BaseController
{		
    /**
    * 发票数据分析
    */
    public function invoicelist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{
			
			return view();
		}
    }
	
    /**
    * 收付款数据分析
    */
    public function paymentlist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{
			
			return view();
		}
    }

}
