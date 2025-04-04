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

use think\facade\Db;

function get_pfolder($id)
{
	$folder = Db::name('Disk')->where('id',$id)->find();
	if($folder && $folder['pid'] > 0){
		$pfolders = get_pfolder($folder['pid']);
		$pfolders[] = $folder;
		return $pfolders;
	}
	else{
		if(empty($folder)){
			return [];
		}
		return [$folder];
	}	
}