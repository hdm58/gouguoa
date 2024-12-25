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
namespace systematic;
use think\facade\Config;
use think\facade\Cache;
use think\facade\Db;
/**
 * 系统类
 */
class Systematic
{	
    public function auth($uid)
    {
        if (!Cache::get('RulesSrc' . $uid) || !Cache::get('RulesSrc0')) {
            //用户所在权限组及所拥有的权限
            $groups = [];
            $position_id = Db::name('Admin')->where('id', $uid)->value('position_id');
            $groups = Db::name('PositionGroup')
                ->alias('a')
                ->join("AdminGroup g", "a.group_id=g.id", 'LEFT')
                ->where([['a.pid', '=', $position_id], ['g.status', '=', 1]])
                ->select()->toArray();
            //保存用户所属用户组设置的所有权限规则id
            $ids = [];
            foreach ($groups as $g) {
                $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
            }
            $ids = array_unique($ids);
            //读取所有权限规则
            $rules_all = Db::name('AdminRule')->field('src')->select()->toArray();
            //读取用户组所有权限规则
            $rules = Db::name('AdminRule')->where('id', 'in', $ids)->field('src')->select()->toArray();
            //循环规则，判断结果。
            $auth_list_all = [];
            $auth_list = [];
            foreach ($rules_all as $rule_all) {
                $auth_list_all[] = strtolower($rule_all['src']);
            }
            foreach ($rules as $rule) {
                $auth_list[] = strtolower($rule['src']);
            }
            //规则列表结果保存到Cache
            Cache::tag('adminRules')->set('RulesSrc0', $auth_list_all, 36000);
            Cache::tag('adminRules')->set('RulesSrc' . $uid, $auth_list, 36000);
        }
    }
	
	//读取文件配置
	public function getConfig($key)
	{
		return Config::get($key);
	}
}
