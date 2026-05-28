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

class Customer extends BaseController
{	
    /**
    * 客户统计
    */
    public function analysis()
    {
		$param = get_params();
        if (request()->isAjax()) {

        }
        else{
            return view();
        }
    }
	
    /**
    * 客户分析
    */
    public function datalist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{
			
			return view();
		}
    }
	
    /**
    * 客户跟进
    */
    public function followlist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
        }else{			
			return view();
		}
    }

}
