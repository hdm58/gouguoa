<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
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
                $where[] = ['name|rule_menu|param_id', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['action'])) {
                $where['action'] = $param['action'];
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
				if(is_array($param_array)){
					$param_value = '';
					foreach ($param_array as $key => $value) {
						if (is_array($value)) {
							$value = implode(',', $value);
						}
						$param_value .= $key . ':' . $value . '&nbsp;&nbsp;|&nbsp;&nbsp;';
					}
					$data['param'] = $param_value;
				}
				else{
					$data['param'] = $param_array;
				}
                $content->offsetSet($k, $data);
            }
            return table_assign(0, '', $content);
        } else {
			$type_action = get_config('log.type_action');
			View::assign('type_action', $type_action);
            return view();
        }
    }
}
