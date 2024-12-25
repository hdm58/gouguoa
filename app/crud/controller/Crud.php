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

namespace app\crud\controller;

use app\api\BaseController;
use think\facade\Console;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Crud extends BaseController
{
    public function index()
    {
        return view();
    }

	//一键crud
	public function crud()
    {
		$uid = $this->uid;
		if($uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限使用一键crud功能！');
		}
		$param = get_params();
		$a = '-a'.$param['name'];
		$m = '-m'.$param['module'];
		$t = '-t'.$param['table'];
		$c = '-c'.$param['controller'];
		$y = '-y'.$param['types'];
		try {
			$output = Console::call('crud', [$a,$m,$t,$c,$y]);
			//return $output->fetch();
        } catch(\Exception $e) {
			return to_assign(1, $e->getMessage());
        }
    }
}
