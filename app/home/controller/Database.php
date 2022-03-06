<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use backup\Backup;
use think\facade\Db;
use think\facade\View;

class Database extends BaseController
{
    protected $db = '', $datadir;
    public function initialize()
    {
        parent::initialize();
        $this->config = array(
            'path' => './backup/', // 数据库备份路径
            'part' => 20971520, // 数据库备份卷大小
            'compress' => 0, // 数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level' => 9, // 数据库备份文件压缩级别 1普通 4 一般  9最高
        );
        $this->db = new Backup($this->config);
    }

    // 数据列表
    public function database()
    {
        if (request()->isAjax()) {
            // 数据信息
            $list = $this->db->dataList();
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

    // 备份
    public function backup()
    {
        $tables = get_params('id');
        if (!empty($tables)) {
            $tables = explode(',', $tables);
            foreach ($tables as $table) {
                $this->db->setFile()->backup($table, 0);
            }
            add_log('add');
            return to_assign(0, '备份成功！');
        } else {
            return to_assign(1, '请选择要备份的表');
        }
    }

    // 优化
    public function optimize()
    {
        $tables = get_params('id');
        if (empty($tables)) {
            return to_assign(0, '请选择要优化的表');
        }
        $tables = explode(',', $tables);
        if ($this->db->optimize($tables)) {
            add_log('edit');
            return to_assign(0, '数据表优化成功！');
        } else {
            return to_assign(1, '数据表优化出错请重试');
        }
    }

    // 修复
    public function repair()
    {
        $tables = get_params('id');
        if (empty($tables)) {
            return to_assign(1, '请选择要修复的表');
        }
        $tables = explode(',', $tables);
        if ($this->db->repair($tables)) {
            add_log('edit');
            return to_assign(0, '数据表修复成功');
        } else {
            return to_assign(1, '数据表修复出错请重试');
        }
    }

    // 还原列表
    public function backuplist()
    {
        // 数据信息
        $list = $this->db->fileList();
        $listNew = [];
        $indx = 0;
        foreach ($list as $k => $v) {
            $listNew[$indx]['time'] = $k;
            $listNew[$indx]['data'][] = $v;
            $indx++;
            // $listNew[$k]['list'] = $list[$k];
        }
        $list = $listNew;
        array_multisort(array_column($list, 'time'), SORT_DESC, $list);
        return view('', ['list' => $list]);
    }

    // 执行还原数据库操作
    public function import(int $id)
    {
        $list = $this->db->getFile('timeverif', $id);
        $this->db->setFile($list)->import(1);
        add_log('save');
        return to_assign(0, '还原成功！');
    }

    // 下载
    public function downfile(string $name)
    {
        $file_name = $name; //得到文件名
        header("Content-type:text/html;charset=utf-8");
        $file_name = iconv("utf-8", "gb2312", $file_name); // 转换编码
        $file_sub_path = $this->config['path']; //确保文件在这个路径下面，换成你文件所在的路径
        $file_path = $file_sub_path . $file_name;
        # 将反斜杠 替换成正斜杠
        $file_path = str_replace('\\', '/', $file_path);
        if (!file_exists($file_path)) {
            $this->error($file_path);exit; //如果提示这个错误，很可能你的路径不对，可以打印$file_sub_path查看
        }
        $fp = fopen($file_path, "r"); // 以可读的方式打开这个文件
        # 如果出现图片无法打开，可以在这个位置添加函数
        ob_clean(); # 清空擦掉，输出缓冲区。
        $file_size = filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:" . $file_size);
        Header("Content-Disposition: attachment; filename = " . $file_name);
        $buffer = 1024000;
        $file_count = 0;
        while (!feof($fp) && $file_count < $file_size) {
            $file_con = fread($fp, $buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        fclose($fp); //关闭这个打开的文件
    }

    // 删除sql文件
    public function del(string $id)
    {
        if (request()->isAjax()) {
            if (strpos($id, ',') !== false) {
                $idArr = explode(',', $id);
                foreach ($idArr as $k => $v) {
                    $this->db->delFile($v);
                }
                add_log('delete');
                return to_assign(0, "删除成功");
            }
            if ($this->db->delFile($id)) {
                add_log('delete');
                return to_assign(0, "删除成功");
            } else {
                return to_assign(1, "备份文件删除失败，请检查文件权限");
            }
        }
    }

}
