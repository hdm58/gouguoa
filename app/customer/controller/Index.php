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

namespace app\customer\controller;

use app\base\BaseController;
use app\customer\model\Customer as CustomerModel;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CustomerModel();
    }
	//抢客宝
    public function rush()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$param['order'] = 'rand()';
            $where = array();
            $where[] = ['delete_time', '=', 0];
            $where[] = ['discard_time', '=', 0];
            $where[] = ['belong_uid', '=', 0];
            $list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
			$time = strtotime(date('Y-m-d')." 00:00:00");
			$max_num = Db::name('DataAuth')->where('name','customer_admin')->value('conf_3');
			$count = Db::name('Customer')->where([['belong_time','>',$time],['belong_uid','=',$this->uid]])->count();
			View::assign('max_num', $max_num);
			View::assign('count', $count);
            return view();
        }
    }
	
	//公海客户
    public function sea()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['id|name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['industry_id'])) {
                $where[] = ['industry_id', '=', $param['industry_id']];
            }
			if (!empty($param['source_id'])) {
                $where[] = ['source_id', '=', $param['source_id']];
            }
			if (!empty($param['grade_id'])) {
                $where[] = ['grade_id', '=', $param['grade_id']];
            }
			if (!empty($param['follow_time'])) {
				$follow_time =explode('~', $param['follow_time']);
				$where[] = ['follow_time', 'between',[strtotime(urldecode($follow_time[0])),strtotime(urldecode($follow_time[1].' 23:59:59'))]];
            }
            $where[] = ['delete_time', '=', 0];
            $where[] = ['discard_time', '=', 0];
            $where[] = ['belong_uid', '=', 0];
			
            $list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//废池客户
    public function trash()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['id|name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['industry_id'])) {
                $where[] = ['industry_id', '=', $param['industry_id']];
            }
			if (!empty($param['source_id'])) {
                $where[] = ['source_id', '=', $param['source_id']];
            }
			if (!empty($param['grade_id'])) {
                $where[] = ['grade_id', '=', $param['grade_id']];
            }
            $where[] = ['delete_time', '>', 0];
            $where[] = ['discard_time', '=', 0];
            $where[] = ['belong_uid', '=', 0];
            $list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//移入公海
	public function to_sea()
    {
		if (request()->isAjax()) {
			$id = get_params("id");
			$data['id'] = $id;
			$data['belong_uid'] = 0;
			$data['belong_did'] = 0;
			$data['belong_time'] = 0;
			if (Db::name('Customer')->update($data) !== false) {
				add_log('tosea', $id);
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	//获取客户
	public function to_get()
    {
		if (request()->isAjax()) {
			$id = get_params("id");
			$time = strtotime(date('Y-m-d')." 00:00:00");
			$max_num = Db::name('DataAuth')->where('name','customer_admin')->value('conf_2');
			$count = Db::name('Customer')->where([['belong_time','>',$time],['belong_uid','=',$this->uid]])->count();
			if($count>=$max_num){
				return to_assign(1, "今日领取客户数已到达上限，请明天再来领取");
			}
			$data['id'] = $id;
			$data['belong_uid'] = $this->uid;
			$data['belong_did'] = $this->did;
			$data['belong_time'] = time();
			if (Db::name('Customer')->update($data) !== false) {
				add_log('tosea', $id);
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
	}	
	
	//客户移入废弃池
    public function to_trash()
    {
		if (request()->isAjax()) {
			$params = get_params();			
			$data['id'] = $params['id'];
			$data['delete_time'] = time();
			$log_data['action'] = 'totrash';
			if (Db::name('Customer')->update($data) !== false) {
				add_log('totrash', $params['id']);
				return to_assign();
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//还原客户
    public function to_revert()
    {
		if (request()->isAjax()) {
			$params = get_params();		
			$data['id'] = $params['id'];
			$data['delete_time'] = 0;
			if (Db::name('Customer')->update($data) !== false) {
				add_log('recovery', $params['id']);
				return to_assign();
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }
}
