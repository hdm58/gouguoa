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

namespace app\user\controller;

use app\base\BaseController;
use app\user\model\Admin as AdminList;
use app\user\model\Department as DepartmentModel;
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
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.username|a.name|a.nickname|a.mobile|a.desc', 'like', '%' . $param['keywords'] . '%'];
            }
            if (isset($param['status']) && $param['status']!='') {
                $where[] = ['a.status', '=', $param['status']];
            }
			else{
				$where[] = ['a.status', '<', 2];
			}
            if (!empty($param['type'])) {
                $where[] = ['a.type', '=', $param['type']];
            }
            if (!empty($param['did'])) {
				$admin_array = Db::name('DepartmentAdmin')->where('department_id',$param['did'])->column('admin_id');
				$map1=[
					['a.id','in',$admin_array],
				];
				$map2=[
					['a.did', '=', $param['did']],
				];
				$whereOr =[$map1,$map2];
            }
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$admin = AdminList::alias('a')
				->with('departments')
				->field('a.*,p.title as position,d.title as department')
				->join('Department d', 'd.id = a.did','left')
				->join('Position p', 'p.id = a.position_id','left')
				->where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->paginate(['list_rows'=> $rows])
				->order('a.id desc')
                ->each(function ($item, $key) {
					//遍历次要部门数据
					$departments = $item->departments->toArray();
					if(empty($departments)){
						$item->departments = '-';
					}
					else{
						$item->departments = split_array_field($departments,'title');
					}
                    $item->entry_time = empty($item->entry_time) ? '-' : date('Y-m-d', $item->entry_time);
                    $item->last_login_time = empty($item->last_login_time) ? '-' : date('Y-m-d H:i', $item->last_login_time);
                    $item->last_login_ip = empty($item->last_login_ip) ? '-' : $item->last_login_ip;
                });
            return table_assign(0, '', $admin);
        } else {
            return view();
        }
    }

    //生成登录名
    public function create_name($name,$id=0,$total=0,$old='')
    {
		$count = Db::name('Admin')->where([['username','=',$name],['id','<>',$id]])->count();
		if($total==0){
			$old = $name;
		}
		$total++;
		if($count>0){
			$newname = $old.$total;
			$name = $this->create_name($newname,$id,$total,$old);
		}
		return $name;
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$id = isset($param['id'])?$param['id']:0;
            $param['entry_time'] = strtotime($param['entry_time']);
            $param['nickname'] = $param['name'];
            if ($id > 0) {
				if($id == 1){
					return to_assign(1, '超级管理员信息不支持编辑');
				}
                try {
                    validate(AdminCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$detail = get_admin($param['id']);
				$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$param['id'])->column('department_id');
				$detail['department_ids'] = implode(',',$department_ids);
                // 启动事务
                Db::startTrans();
                try {
                    Db::name('Admin')->where(['id' => $id])->strict(false)->field(true)->update($param);
					if($detail['department_ids'] != $param['department_ids']){
						Db::name('DepartmentAdmin')->where('admin_id',$id)->whereIn('department_id', $detail['department_ids'])->delete();
						if(!empty($param['department_ids'])){
							$dids = explode(',',$param['department_ids']);
							foreach ($dids as $did) {
								Db::name('DepartmentAdmin')->insert(['admin_id'=>$param['id'],'department_id'=>$did,'create_time' => time()]);
							}
						}
					}
                    if(empty($param['thumb'])){
                        $char = mb_substr($param['name'], 0, 1, 'utf-8');
                        Db::name('Admin')->where('id', $id)->update(['thumb' => $this->to_avatars($char)]);
                    }
					$info = Db::name('Admin')->where('id', $id)->find();
					$model = new DepartmentModel();
					$auth_dids = $model->get_auth_departments($info);
					$son_dids = $model->get_son_departments($info);
					Db::name('Admin')->where('id',$id)->update(['auth_dids'=>$auth_dids,'son_dids'=>$son_dids]);
                    add_log('edit', $id, $param);
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
				$username = Pinyin::name($param['name'],'none')->join('');
				$param['username'] = $this->create_name($username,$id);
                try {
                    validate(AdminCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['salt'] = set_salt(20);
                $param['pwd'] = set_password($param['reg_pwd'], $param['salt']);
                // 启动事务
                Db::startTrans();
                try {
                    $uid = Db::name('Admin')->strict(false)->field(true)->insertGetId($param);
					if(!empty($param['department_ids'])){
						$dids = explode(',',$param['department_ids']);
						foreach ($dids as $did) {
							Db::name('DepartmentAdmin')->insert(['admin_id'=>$uid,'department_id'=>$did,'create_time' => time()]);
						}
					}
                    if(empty($param['thumb'])){
                        $char = mb_substr($param['name'], 0, 1, 'utf-8');
                        Db::name('Admin')->where('id', $uid)->update(['thumb' => $this->to_avatars($char)]);
                    }
					$info = Db::name('Admin')->where('id', $uid)->find();
					$model = new DepartmentModel();
					$auth_dids = $model->get_auth_departments($info);
					$son_dids = $model->get_son_departments($info);
					Db::name('Admin')->where('id',$uid)->update(['auth_dids'=>$auth_dids,'son_dids'=>$son_dids]);
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
                $detail['pname'] =  Db::name('Admin')->where('id',$detail['pid'])->value('name');
				$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$param['id'])->column('department_id');
				$detail['department_ids'] = implode(',',$department_ids);
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
		$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$id)->column('department_id');
		$department_names = Db::name('Department')->whereIn('id',$department_ids)->column('title');
		$detail['department_names'] = implode(',',$department_names);
		$detail['pname'] =  Db::name('Admin')->where('id',$detail['pid'])->value('name');
        //查询所有菜单和权限节点
        $menu = Db::name('AdminRule')->where(['menu' => 1])->order('sort asc,id asc')->select()->toArray();
        $rule = Db::name('AdminRule')->order('sort asc,id asc')->select()->toArray();

        //查询用户拥有的菜单和节点
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
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = DB::name('AdminLog')
                ->field("id,uid,name,action,title,content,rule_menu,ip,param_id,param,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') create_time")
                ->order('create_time desc')
                ->where($where)
                ->paginate(['list_rows'=> $rows]);
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
