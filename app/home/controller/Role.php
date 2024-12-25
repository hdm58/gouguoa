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
use app\home\model\AdminGroup;
use app\home\validate\GroupCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Role extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['id|title|desc', 'like', '%' . $param['keywords'] . '%'];
            }
			$list = Db::name('AdminGroup')->where($where)->order('create_time asc')->select();
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $ruleData = isset($param['rule']) ? $param['rule'] : 0;
            $layoutData = isset($param['layout']) ? $param['layout'] : 0;
            $menuData = isset($param['mobile_menu']) ? $param['mobile_menu'] : 0;
            $barData = isset($param['mobile_bar']) ? $param['mobile_bar'] : 0;
			if($ruleData==0){
				return to_assign(1, '权限节点至少选择一个');
			}
			if($layoutData==0){
				return to_assign(1, '首页展示模块至少选择一个');
			}
            $param['rules'] = implode(',', $ruleData);
            $param['layouts'] = implode(',', $layoutData);
            $param['mobile_menu'] = implode(',', $menuData);
            $param['mobile_bar'] = implode(',', $barData);
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(GroupCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                //为了系统安全id为1的系统所有者管理组不允许修改
                if ($param['id'] == 1) {
                    return to_assign(1, '为了系统安全,该管理组不允许修改');
                }
                Db::name('AdminGroup')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(GroupCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $gid = Db::name('AdminGroup')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $gid, $param);
            }
            //清除菜单\权限缓存
            clear_cache('adminMenu');
            clear_cache('MobileRules');
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $rule = admin_rule();
			$layouts = get_config('layout');
			$mobile_bar = Db::name('MobileBar')->where([['status','=',1]])->field('id,url,title,icon')->select()->toArray();
			$mobile_menu = Db::name('MobileTypes')->where(['status'=>1])->select()->toArray();		

			
            if ($id > 0) {
                $rules = admin_group_info($id);
                $role_rule = create_tree_list(0, $rule, $rules);
                $role = Db::name('AdminGroup')->where(['id' => $id])->find();				
                View::assign('role', $role);
				
				$layout_selected = explode(',', $role['layouts']);
				foreach ($layouts as $key =>&$vo) {
					if (!empty($layout_selected) and in_array($vo['id'], $layout_selected)) {
						$vo['checked'] = true;
					} else {
						$vo['checked'] = false;
					}
				}
				
				$mobile_bar_selected = explode(',', $role['mobile_bar']);
				foreach ($mobile_bar as $key =>&$vo) {
					if (!empty($mobile_bar_selected) and in_array($vo['id'], $mobile_bar_selected)) {
						$vo['checked'] = true;
					} else {
						$vo['checked'] = false;
					}
				}
				
				$mobile_menu_selected = explode(',', $role['mobile_menu']);
				foreach ($mobile_menu as &$row) {
					$list = Db::name('MobileMenu')->where([['types','=',$row['id']],['status','=',1]])->select()->toArray();
					foreach ($list as $key =>&$vo) {
						if (!empty($mobile_menu_selected) and in_array($vo['id'], $mobile_menu_selected)) {
							$vo['checked'] = true;
						} else {
							$vo['checked'] = false;
						}
					}	
					$row['list'] = $list;
				}

            } else {
                $role_rule = create_tree_list(0, $rule, []);
				foreach ($layouts as $key =>&$vo) {
					$vo['checked'] = false;
				}
				
				foreach ($mobile_bar as $key =>&$vo) {
					$vo['checked'] = false;
				}
				
				foreach ($mobile_menu as &$row) {
					$list = Db::name('MobileMenu')->where([['types','=',$row['id']],['status','=',1]])->select()->toArray();
					foreach ($list as $key =>&$vo) {
						$vo['checked'] = false;
					}	
					$row['list'] = $list;
				}
            }	
            View::assign('role_rule', $role_rule);			
            View::assign('layout', $layouts);
            View::assign('mobile_bar', $mobile_bar);
            View::assign('mobile_menu', $mobile_menu);
            View::assign('id', $id);
            return view();
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            if ($id == 1) {
                return to_assign(1, "该组是系统所有者，无法删除");
            }
            $count = Db::name('PositionGroup')->where(["group_id" => $id])->count();
            if ($count > 0) {
                return to_assign(1, "该权限组还在使用，请去除使用者关联再删除");
            }
            if (Db::name('AdminGroup')->delete($id) !== false) {
                add_log('delete', $id, []);
                return to_assign(0, "删除权限组成功");
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
}
