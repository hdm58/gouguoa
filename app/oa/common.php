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

//读取职位
function get_position()
{
    $position = Db::name('Position')->where(['status' => 1])->select()->toArray();
    return $position;
}

//读取印章类型
function oa_seal_cate()
{
    $list = Db::name('SealCate')->where(['status' => 1])->select()->toArray();
    return $list;
}

//读取会议室
function oa_meeting_cate()
{
    $list = Db::name('MeetingCate')->where(['status' => 1])->select()->toArray();
    return $list;
}

//读取车辆类型
function oa_car_cate()
{
    $list = Db::name('CarCate')->where(['status' => 1])->select()->toArray();
    return $list;
}

//读取费用类型
function oa_cost_cate()
{
    $list = Db::name('CostCate')->where(['status' => 1])->select()->toArray();
    return $list;
}
