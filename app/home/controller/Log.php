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
use think\facade\Db;
use think\facade\View;

class Log extends BaseController
{
    //管理员操作日志
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['u.name|a.param_id|a.uid', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['action'])) {
                $where[] = ['a.action','=',$param['action']];
            }
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = DB::name('AdminLog')
                ->field("a.*,u.name")
				->alias('a')
				->join('Admin u', 'a.uid = u.id')
                ->order('a.create_time desc')
                ->where($where)
                ->paginate(['list_rows'=> $rows])
				->each(function($item, $key){
					$item['content'] = $item['name']. $item['action'] . '了' . $item['subject'];
					$item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
					$param_array = json_decode($item['param'], true);
					if(is_array($param_array)){
						$param_value = [];
						foreach ($param_array as $key => $value) {
							if (is_array($value)) {
								$value = implode(',', $value);
							}
							$param_value[] = $key . ':' . $value;
						}
						$item['param'] = implode(' & ',$param_value);
					}
					else{
						$item['param'] = $param_array;
					}
					return $item;
				});
            return table_assign(0, '', $list);
        } else {
			$type_action = get_config('log.type_action');
			View::assign('type_action', $type_action);
            return view();
        }
    }
}
