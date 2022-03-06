<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */
/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;

//读取印章类型
function oa_seal_cate()
{
    $list = Db::name('SealCate')->where(['status' => 1])->select()->toArray();
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
