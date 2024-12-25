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

}
