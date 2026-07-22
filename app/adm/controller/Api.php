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
use app\adm\model\CarUse;
use app\adm\model\SalaryRecords;
use app\api\model\FinanceLog;
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
	
	//获取车辆记录
	public function get_car_use()
    {
		$param = get_params();
		$where = array();
		$where[] = ['car_id','=',$param['car_id']];
		$where[] = ['check_status','=',2];
		$where[] = ['delete_time','=',0];
		$model = new CarUse();
		$list = $model->datalist($param,$where);
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
	
	//修改用车状态
    public function set_car_status()
    {
		$param = get_params();
		$detail = Db::name('CarUse')->where(['id'=>$param['id']])->find();
		$driver = $detail['admin_id'];
		if($param['status']==1){
			if($detail['status'] == 1){
				return to_assign(1, '该申请已经设置在用状态');
			}
			$param['out_admin'] = $this->uid;
			$param['out_time'] = time();
		}
		if($param['status']==2){
			if($detail['status'] == 2){
				return to_assign(1, '该申请已经设置还车状态');
			}
			$driver=0;
			$param['back_admin'] = $this->uid;
			$param['back_time'] = time();
		}
		$res = Db::name('CarUse')->update($param);
		if($res!==false){
			Db::name('Car')->where('id',$detail['car_id'])->update(['driver'=>$driver]);
			return to_assign();
		}
		else{
			return to_assign(1, '操作失败');
		}
	} 
	
	//获取已生成的文号
    public function doc_number()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (isset($param['base_id']) && $param['base_id']!='') {
                $where[] = ['dn.base_id','=',$param['base_id']];
            }
			if (!empty($param['year'])) {
                $where[] = ['dn.year','=',$param['year']];
            }
			$where[] = ['dn.delete_time','=',0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = DB::name('DocNumber')
                ->field("dn.*,a.name as admin_name")
				->alias('dn')
				->join('Admin a', 'dn.admin_id = a.id')
				->join('DocBase db', 'dn.base_id = db.id')
                ->order('dn.create_time desc')
                ->where($where)
                ->paginate(['list_rows'=> $rows])
				->each(function ($item, $key){
					$item['title'] = '-';
					if($item['official_id']>0){
						$item['title'] = Db::name('Official')->where('id',$item['official_id'])->value('title');
					}
					if($item['received_id']>0){
						$item['title'] = Db::name('Received')->where('id',$item['received_id'])->value('title');
					}
					$item['update_time'] = date('Y-m-d H:i:s', $item['update_time']);
					return $item;
				});
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//获取产品分类数据
    public function get_regulationcate_tree()
    {
        $cate = get_base_data('RegulationCate');
        $list = get_tree($cate, 0, 2);
        $data['trees'] = $list;
        return json($data);
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
	
	//获取个人工资记录列表
	public function get_records()
    {
        $param = get_params();
		$where = array();
		$where[] = ['salary_id', '=', $param['salary_id']];
		$where[] = ['delete_time', '=', 0];
		$model = new SalaryRecords();
		$list = $model->datalist($param,$where);
        return table_assign(0, '', $list);
    }
	
	//发放审核月工资单
    public function salary_pay()
    {
		$param = get_params();
		$auth = isAuth($this->uid,'office_admin','conf_3');
		if($auth == 0){
			return to_assign(1, "你无权限操作");
		}
		$detail = Db::name('Salary')->where('id',$param['salary_id'])->find();
		if($detail['salary']==0){
			return to_assign(1, "该月工资单工资为0，不支持发放");
		}
		if(isset($param['pay_time'])){
			$param['pay_time'] = strtotime(urldecode($param['pay_time']));
		}
		$param['pay_uid'] = $this->uid;
		$param['status'] = 1;
		$res = Db::name('Salary')->where('id',$param['salary_id'])->strict(false)->update($param);
        if ($res!==false) {
			Db::name('SalaryRecords')->where('salary_id',$param['salary_id'])->update(['status'=>1,'pay_time'=>$param['pay_time']]);			
			$log=new FinanceLog();
			//注入收入流水
			$log->add('salary',$param['salary_id']);
			$log->add('social',$param['salary_id']);
			$log->add('gongjijin',$param['salary_id']);
			$log->add('tax',$param['salary_id']);
			
			$records = Db::name('SalaryRecords')->where('salary_id',$param['salary_id'])->select()->toArray();
			foreach($records as $key=>$val) {
				//发送消息通知
				$msg=[
					'from_uid'=>$this->uid,//发送人
					'to_uids'=>$val['uid'],//接收人
					'template_id'=>'salary',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$val['id'],
						'title' => '工资发放',
						'amount' => $val['total_payment']
					]
				];
				event('SendMessage',$msg);
			}
			
            return to_assign();
        }
		else{
			return to_assign(1, "操作失败");
		}
	}
}
