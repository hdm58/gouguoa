<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\validate\ConfCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Conf extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $where[] = ['status', '>=', 0];
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $content = Db::name('Config')
                ->where($where)
                ->paginate($rows, false, ['query' => $param]);
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }

    //添加/编辑配置项
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            try {
                validate(ConfCheck::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('config')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('Config')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $config = Db::name('Config')->where(['id' => $id])->find();
                View::assign('config', $config);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //删除配置项
    public function delete()
    {
        $id = get_params("id");
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Config')->update($data) !== false) {
            add_log('delete', $id, $data);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }

    //编辑配置信息
    public function edit()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $data['content'] = serialize($param);
            $data['update_time'] = time();
            $data['id'] = $param['id'];
            $res = Db::name('Config')->strict(false)->field(true)->update($data);
            $conf = Db::name('Config')->where('id', $param['id'])->find();
            clear_cache('system_config' . $conf['name']);
            if ($res) {
                add_log('edit', $param['id'], $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $conf = Db::name('Config')->where('id', $id)->find();
            $config = [];
            if ($conf['content']) {
                $config = unserialize($conf['content']);
            }
            View::assign('id', $id);
            View::assign('config', $config);
            return view($conf['name']);
        }
    }
}
