<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */
//读取文章分类列表
function get_article_cate()
{
    $cate = \think\facade\Db::name('ArticleCate')->order('create_time asc')->select()->toArray();
    return $cate;
}