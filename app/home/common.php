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


//读取后台菜单列表
function admin_menu()
{
    $menu = Db::name('AdminRule')->where(['menu' => 1,'status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $menu;
}

//读取权限节点列表
function admin_rule()
{
    $rule = Db::name('AdminRule')->where(['status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $rule;
}

//读取权限分组列表
function admin_group()
{
    $group = Db::name('AdminGroup')->order('id desc')->select()->toArray();
    return $group;
}

//读取指定权限分组菜单详情
function admin_group_info($id)
{
    $rule = Db::name('AdminGroup')->where(['id' => $id])->value('rules');
	$rules = explode(',', $rule);
    return $rules;
}

//读取模块列表
function admin_module()
{
    $group = Db::name('AdminModule')->order('id asc')->select()->toArray();
    return $group;
}

//读取公告分类子分类ids
function admin_note_cate_son($id = 0, $is_self = 1)
{
    $note = Db::name('NoteCate')->order('create_time asc')->select();
    $note_list = get_data_node($note, $id);
    $note_array = array_column($note_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $note_array[] = $id;
    }
    return $note_array;
}

//读取知识分类子分类ids
function admin_article_cate_son($id = 0, $is_self = 1)
{
    $article = Db::name('ArticleCate')->order('id desc')->select()->toArray();
    $article_list = get_data_node($article, $id);
    $article_array = array_column($article_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $article_array[] = $id;
    }
    return $article_array;
}
