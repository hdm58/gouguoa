<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\user\controller;

use app\base\BaseController;
use app\user\model\Admin as AdminList;
use app\user\validate\AdminCheck;
use avatars\MDAvatars;
use Overtrue\Pinyin\Pinyin;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class User extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['id|username|name|nickname|mobile|desc', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['status', '<', 2];
            if (isset($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
            if (!empty($param['type'])) {
                $where[] = ['type', '=', $param['type']];
            }
            if (!empty($param['did'])) {
                $department_array = get_department_son($param['did']);
                $where[] = ['did', 'in', $department_array];
            }
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $admin = AdminList::where($where)
                ->order('id desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
                    $item->position = Db::name('Position')->where(['id' => $item->position_id])->value('title');
                    $item->entry_time = empty($item->entry_time) ? '-' : date('Y-m-d', $item->entry_time);
                    $item->last_login_time = empty($item->last_login_time) ? '-' : date('Y-m-d H:i', $item->last_login_time);
                    $item->last_login_ip = empty($item->last_login_ip) ? '-' : $item->last_login_ip;
                });
            return table_assign(0, '', $admin);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['entry_time'] = strtotime($param['entry_time']);
            $param['nickname'] = $param['name'];
            $pinyin = new Pinyin();
            $username = $pinyin->name($param['name'], PINYIN_UMLAUT_V);
            $param['username'] = implode('', $username);
            if (!empty($param['id']) && $param['id'] > 0) {
				$count = Db::name('Admin')->where([['username', '=', $param['username']], ['id', '<>', $param['id']]])->count();
				if ($count > 0) {
					$param['username'] = implode('', $username) . $count;
				}
                try {
                    validate(AdminCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                // 启动事务
                Db::startTrans();
                try {
                    Db::name('Admin')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                    if (!isset($param['thumb']) || $param['thumb'] == '') {
                        $char = mb_substr($param['name'], 0, 1, 'utf-8');
                        Db::name('Admin')->where('id', $param['id'])->update(['thumb' => $this->to_avatars($char)]);
                    }
                    add_log('edit', $param['id'], $param);
                    //清除菜单\权限缓存
                    clear_cache('adminMenu');
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return to_assign(1, '提交失败:' . $e->getMessage());
                }
            } else {
                $count = Db::name('Admin')->where('username', $param['username'])->count();
                if ($count > 0) {
                    $param['username'] = implode('', $username) . $count;
                }
                try {
                    validate(AdminCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['salt'] = set_salt(20);
                $param['pwd'] = set_password($param['reg_pwd'], $param['salt']);
                // 启动事务
                Db::startTrans();
                try {
                    $uid = Db::name('Admin')->strict(false)->field(true)->insertGetId($param);
                    if (!isset($param['thumb']) || $param['thumb'] == '') {
                        $char = mb_substr($param['name'], 0, 1, 'utf-8');
                        Db::name('Admin')->where('id', $uid)->update(['thumb' => $this->to_avatars($char)]);
                    }
                    add_log('add', $uid, $param);
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return to_assign(1, '提交失败:' . $e->getMessage());
                }
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $department = set_recursion(get_department());
            $position = Db::name('Position')->where('status', '>=', 0)->order('create_time asc')->select();
            if ($id > 0) {
                $detail = get_admin($id);
                View::assign('detail', $detail);
            } else {
                //初始化密码
                $reg_pwd = set_salt(6);
                View::assign('reg_pwd', $reg_pwd);
            }
            View::assign('department', $department);
            View::assign('position', $position);
            View::assign('id', $id);
            return view();
        }
    }
    //生成头像
    public function to_avatars($char)
    {
        $defaultData = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'S', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            '零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '拾',
            '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        if (isset($char)) {
            $Char = $char;
        } else {
            $Char = $defaultData[mt_rand(0, count($defaultData) - 1)];
        }
        $OutputSize = min(512, empty($_GET['size']) ? 36 : intval($_GET['size']));

        $Avatar = new MDAvatars($Char, 256, 1);
        $avatar_name = '/avatars/avatar_256_' . set_salt(10) . time() . '.png';
        $path = get_config('filesystem.disks.public.url') . $avatar_name;
        $res = $Avatar->Save('.' . $path, 256);
        $Avatar->Free();
        return $path;
    }

    //查看
    public function view()
    {
        $id = get_params('id');
        $detail = get_admin($id);
        //查询所有菜单和权限节点
        $menu = Db::name('AdminRule')->where(['menu' => 1])->order('sort asc,id asc')->select()->toArray();
        $rule = Db::name('AdminRule')->order('sort asc,id asc')->select()->toArray();

        //查询用户拥有的查单和节点
        $user_groups = Db::name('PositionGroup')
            ->alias('a')
            ->join("AdminGroup g", "a.group_id=g.id", 'LEFT')
            ->where([['a.pid', '=', $detail["position_id"]], ['g.status', '=', 1]])
            ->select()
            ->toArray();
        $groups = $user_groups ?: [];

        $rules = [];
        foreach ($groups as $g) {
            $rules = array_merge($rules, explode(',', trim($g['rules'], ',')));
        }
        $rules = array_unique($rules);

        //数据嵌套
        $role_rule = create_tree_list(0, $rule, $rules);

        View::assign('role_rule', $role_rule);
        View::assign('detail', $detail);
        add_log('view', get_params('id'));
        return view();
    }
    //禁用,恢复
    public function set()
    {
        $type = get_params("type");
        $ids = get_params("ids");
        $idArray = explode(',', $ids);
        $list = [];
        foreach ($idArray as $key => $val) {
            if ($val == 1) {
                continue;
            }
            $list[] = [
                'status' => $type,
                'id' => $val,
                'update_time' => time(),
            ];
        }
        foreach ($list as $key => $v) {
            if (Db::name('Admin')->update($v) !== false) {
                if ($type == 0) {
                    add_log('disable', $v['id']);
                } else if ($type == 1) {
                    add_log('recovery', $v['id']);
                }
            }
        }
        return to_assign(0, '操作成功');
    }

    //重置密码
    public function reset_psw()
    {
        $id = get_params("id");
		if($id == 1){
			return to_assign(1, '该账号是超级管理员，不允许重置');
		}
        $new_pwd = set_salt(6);
        $salt = set_salt(20);
        $data = [
            'reg_pwd' => $new_pwd,
            'salt' => $salt,
            'pwd' => set_password($new_pwd, $salt),
            'id' => $id,
            'update_time' => time(),
        ];
        if (Db::name('Admin')->update($data) !== false) {
            add_log('reset', $id);
            return to_assign(0, '操作成功');
        } else {
            return to_assign(1, '操作失败');
        }
    }

    //管理员操作日志
    public function log()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['name|rule_menu|param_id', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['title_cate'])) {
                $where['title'] = $param['title_cate'];
            }
            if (!empty($param['rule_menu'])) {
                $where['rule_menu'] = $param['rule_menu'];
            }
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $content = DB::name('AdminLog')
                ->field("id,uid,name,action,title,content,rule_menu,ip,param_id,param,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') create_time")
                ->order('create_time desc')
                ->where($where)
                ->paginate($rows, false, ['query' => $param]);
            $content->toArray();
            foreach ($content as $k => $v) {
                $data = $v;
                $param_array = json_decode($v['param'], true);
                $param_value = '';
                foreach ($param_array as $key => $value) {
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $param_value .= $key . ':' . $value . '&nbsp;&nbsp;|&nbsp;&nbsp;';
                }
                $data['param'] = $param_value;
                $content->offsetSet($k, $data);
            }
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }
}
