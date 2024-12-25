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

namespace app\contract\model;

use think\facade\Db;
use think\Model;

class SupplierContact extends Model
{
	// 获取详情
    public function detail($id)
    {
        $detail = Db::name('SupplierContact')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['create_time'] = date('Y-m-d H:i:s', $detail['create_time']);
			$detail['supplier'] = Db::name('Supplier')->where(['id' => $detail['sid']])->value('title');
        }
        return $detail;
    }
}
