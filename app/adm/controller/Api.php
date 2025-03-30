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
namespace app\adm\controller;

use app\api\BaseController;
use app\adm\model\Property;
use app\adm\model\Car;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{	
	public function get_propertycate()
	{
        $cate = get_base_data('PropertyCate');
        return to_assign(0, '', $cate);
    }
	
    public function get_propertycate_tree()
    {
        $cate = get_base_data('PropertyCate');
        $list = get_tree($cate, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }
	//获取资产数据
	public function get_property()
    {
		$param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['p.title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['p.status', '=', 1];
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$model = new Property();
		$list = $model->datalist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取车辆信息
	public function get_car()
    {
		$param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['title|name', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$model = new Car();
		$list = $model->datalist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取车辆维修信息
	public function get_car_repair()
    {
		$param = get_params();
		$where = array();
		$where[] = ['cr.car_id','=',$param['car_id']];
		$where[] = ['cr.types','=',1];
		$where[] = ['cr.delete_time','=',0];
		$model = new Car();
		$list = $model->repairlist($where, $param);
		return table_assign(0, '', $list);
    }
	//获取车辆保养信息
	public function get_car_protect()
    {
		$param = get_params();
		$where = array();
		$where[] = ['cr.car_id','=',$param['car_id']];
		$where[] = ['cr.types','=',2];
		$where[] = ['cr.delete_time','=',0];
		$model = new Car();
		$list = $model->repairlist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取车辆费用信息
	public function get_car_fee()
    {
		$param = get_params();
		$where = array();
		$where[] = ['cf.car_id','=',$param['car_id']];
		$where[] = ['cf.delete_time','=',0];
		$model = new Car();
		$list = $model->feelist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取车辆费用信息
	public function get_car_mileage()
    {
		$param = get_params();
		$where = array();
		$where[] = ['car_id','=',$param['car_id']];
		$where[] = ['delete_time','=',0];
		$model = new Car();
		$list = $model->mileagelist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取会议室
    public function get_meeting_room()
    {
		$list = Db::name('MeetingRoom')->where('status',1)->paginate(['list_rows'=> 20]);
        return table_assign(0, '', $list);
    }
	
	//获取审核类型
    public function get_flow_item()
    {
        $param = get_params();
		$flows = Db::name('FlowItem')->where(['flow_cate'=>$param['cate'],'status'=>1])->select()->toArray();
		return to_assign(0, '', $flows);
	} 

	//修改公章状态
    public function set_seal_status()
    {
		$param = get_params();
		$res = Db::name('Seal')->where(['id'=>$param['id']])->update(['status'=>$param['status']]);
		if($res!==false){
			return to_assign();
		}
		else{
			return to_assign(1, '操作失败');
		}
	} 

	//测试demo
	public function work_flow()
    {
		return view('/flow/work_flow');
	}
	
    public function table()
    {
        $param = get_params();
		$prefix = config('database.connections.mysql.prefix');
		//查询指定表信息
        $table_info = Db::query('SHOW TABLE STATUS LIKE ' . "'" .$prefix.$param['name'] . "'");
		if(empty($table_info)){
			return view(EEEOR_REPORTING,['code'=>406,'warning'=>'找不到该数据表']);
		}
		$table_columns = Db::query("SHOW FULL COLUMNS FROM " .$prefix.$param['name']);
		$columns=[];
		foreach($table_columns as $key=>$val) {
			if (strpos($val['Type'], 'int') !== false || strpos($val['Type'], 'decimal') !== false) {
				$columns[]=$val;
			}
		}
		//var_dump($table_info);exit;
		//dd($table_columns);exit;
		View::assign('id', 0);
		View::assign('columns', $columns);
		return view('/flow/table');
    }
}
