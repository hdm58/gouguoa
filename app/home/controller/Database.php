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

namespace app\home\controller;

use app\base\BaseController;
use backup\Backup;
use think\facade\Session;
use think\facade\View;

class Database extends BaseController
{
    //数据表列表
    public function database()
    {
        if (request()->isAjax()) {
            // 数据信息
            $db = new Backup();
            $list = $db->dataList();
            // 计算总大小
            $total = 0;
            foreach ($list as $k => $v) {
                $total += $v['data_length'];
                $list[$k]['data_size'] = $v['data_length'];
                $list[$k]['data_length'] = format_bytes($v['data_length']);
            }
            // 提示信息
            $dataTips = '数据库中共有<strong> ' . count($list) . '</strong> 张表，共计 <strong>' . format_bytes($total) . '</strong>大小。';
            $data['data'] = $list;
            return table_assign(0, $dataTips, $data);
        }
        return view();
    }

    //备份数据
    public function backup()
    {
        $db = new Backup();
        if (request()->isPost()) {
            $tables = get_params('tables');
            $fileinfo = $db->getFile();
            //检查是否有正在执行的任务
            $lock = "{$fileinfo['filepath']}backup.lock";
            if (is_file($lock)) {
                return to_assign(2, '检测到有一个备份任务未完成');
            } else {
                //创建锁文件
                file_put_contents($lock, time());
            }
            // 检查备份目录是否可写
            if (!is_writeable($fileinfo['filepath'])) {
                return to_assign(1, '备份目录不存在或不可写，请检查后重试');
            }

            //缓存锁文件
            Session::set('lock', $lock);
            //缓存备份文件信息
            Session::set('backup_file', $fileinfo['file']);
            //缓存要备份的表
            Session::set('backup_tables', $tables);
            //创建备份文件
            if (false !== $db->Backup_Init()) {
                return to_assign(0, '初始化成功，开始备份...', ['tab' => ['id' => 0, 'start' => 0, 'table' => $tables[0]]]);
            } else {
                return to_assign(1, '初始化失败，备份文件创建失败');
            }
        } else if (request()->isGet()) {
            $tables = Session::get('backup_tables');
            $file = Session::get('backup_file');
            $id = get_params('id');
            $start = get_params('start');
            $start = $db->setFile($file)->backup($tables[$id], $start);
            if (false === $start) {
                return to_assign(1, '备份出错');
            } else if (0 === $start) {
                if (isset($tables[++$id])) {
                    return to_assign(0, '备份完成', ['tab' => ['id' => $id, 'start' => 0, 'table' => $tables[$id - 1]]]);
                } else { //备份完成，清空缓存
                    unlink(Session::get('lock'));
                    Session::delete('backup_tables');
                    Session::delete('backup_file');
                    add_log('bak');
                    return to_assign(0, '备份完成', ['tab' => ['start' => 'ok', 'table' => $tables[$id - 1]]]);
                }
            }
        } else {
            return to_assign(1, '参数错误！');
        }
    }

    //优化表
    public function optimize($tables = null)
    {
        $db = new Backup();
        //return to_assign(0, $db->optimize($tables));
        if ($db->optimize($tables)) {
            add_log('optimize');
            return to_assign(0, '数据表优化完成');
        } else {
            return to_assign(1, '数据表优化出错请重试');
        }
    }

    //修复表
    public function repair($tables = null)
    {
        $db = new Backup();
        //return to_assign(0, $db->repair($tables));
        if ($db->repair($tables)) {
            add_log('repair');
            return to_assign(0, '数据表修复完成');
        } else {
            return to_assign(1, '数据表修复出错请重试');
        }
    }

    //备份文件列表
    public function backuplist()
    {
        $db = new Backup();
        $list = $db->fileList();
        $fileinfo = $db->getFile();
        $lock = "{$fileinfo['filepath']}backup.lock";
        $lock_time = 0;
        if (is_file($lock)) {
            $lock_time = file_get_contents($lock);
        }
        $listNew = [];
        $indx = 0;
        foreach ($list as $k => $v) {
            $listNew[$indx]['time'] = $k;
            $listNew[$indx]['timespan'] = $v['time'];
            $listNew[$indx]['data'] = $v;
            $indx++;
        }
        $list = $listNew;
        array_multisort(array_column($list, 'time'), SORT_DESC, $list);
        return view('', ['list' => $list, 'lock_time' => $lock_time]);
    }

    //数据还原
    public function import($time = 0, $part = null, $start = null)
    {
        $db = new Backup();
        $time = (int) $time;
        if (is_numeric($time) && is_null($part) && is_null($start)) {
            $list = $db->getFile('timeverif', $time);
            if (is_array($list)) {
                Session::set('backup_list', $list);
                return to_assign(0, '初始化完成，开始还原...', array('part' => 1, 'start' => 0, 'time' => $time));
            } else {
                return to_assign(1, '备份文件可能已经损坏,请检查');
            }
        } else if (is_numeric($part) && is_numeric($start)) {
            $list = Session::get('backup_list');
            $part = (int) $part;
            $start = (int) $start;
            $start = $db->setFile($list)->import($start, $time, $part);
            if (false === $start) {
                return to_assign(1, '还原数据出错,请重试');
            } elseif (0 === $start) {
                if (isset($list[++$part])) {
                    $data = array('part' => $part, 'start' => 0, 'time' => $time);
                    return to_assign(0, "正在还原...卷{$part}，请勿关闭当前页面", $data);
                } else {
                    Session::delete('backup_list');
                    return to_assign(0, '还原数据成功');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0], 'time' => $time);
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    return to_assign(0, "正在还原...卷{$part} ({$rate}%)，请勿关闭当前页面", $data);
                } else {
                    $data['gz'] = 1;
                    return to_assign(0, "正在还原...卷{$part}，请勿关闭当前页面", $data);
                }
                return to_assign(0, "正在还原...卷{$part}，请勿关闭当前页面");
            }
        } else {
            return to_assign(1, "参数错误");
        }
    }
    /**
     * 删除备份文件
     */
    public function del($time = 0, $lock = 0)
    {
        $db = new Backup();
        if ($lock == 1) {
            $fileinfo = $db->getFile();
            $lock = "{$fileinfo['filepath']}backup.lock";
            if (is_file($lock)) {
                $time = file_get_contents($lock);
                unlink($lock);
            }
        }
        if ($db->delFile((int) $time)) {
            add_log('delete');
            return to_assign(0, '删除成功');
        } else {
            return to_assign(0, '删除失败，请检查权限');
        }
    }
    /**
     * 下载备份文件
     */
    public function downfile($time = 0, $part = 0)
    {
        $db = new Backup();
        add_log('down');
        $db->downloadFile((int) $time, $part - 1);
    }

}
