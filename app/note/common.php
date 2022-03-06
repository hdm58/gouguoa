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

//读取公告分类列表
function note_cate()
{
    $cate = Db::name('NoteCate')->order('id desc')->select()->toArray();
    return $cate;
}