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

/**
 * 根据IP获取地址
 */
function get_address($ip)
{
    $res = file_get_contents("http://ip.360.cn/IPQuery/ipquery?ip=" . $ip);
    $res = json_decode($res, 1);
    if ($res && $res['errno'] == 0) {
        return explode("\t", $res['data'])[0];
    } else {
        return '';
    }
}

/**
 * 导出数据为excel表格
 * @param $data    一个二维数组,结构如同从数据库查出来的数组
 * @param $title   excel的第一行标题,一个数组,如果为空则没有标题
 * @param $filename 下载的文件名
 * @param exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
 */
function export_excel($data = array(), $title = array(), $filename = 'report')
{
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=" . $filename . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    //导出xls 开始
    if (!empty($title)) {
        foreach ($title as $k => $v) {
            $title[$k] = iconv("UTF-8", "GB2312", $v);
        }
        $title = implode("\t", $title);
        echo "$title\n";
    }
    if (!empty($data)) {
        foreach ($data as $key => $val) {
            foreach ($val as $ck => $cv) {
                $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
            }
            $data[$key] = implode("\t", $data[$key]);
        }
        echo implode("\n", $data);
    }
} 
 
//读取文章分类列表
function get_article_cate()
{
    $cate = \think\facade\Db::name('ArticleCate')->order('create_time asc')->select()->toArray();
    return $cate;
}