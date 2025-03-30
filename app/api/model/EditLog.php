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

namespace app\api\model;
use think\model;
use think\facade\Db;
class EditLog extends Model
{
	 public static $COMPILE = [
		//'title'=>'字段名称','action'=>'操作行为','table'=>'关联数据表','table_field'=>'关联数据表字段','table_more'=>'0单选数据，1多选数据','enumerate'=>[枚举数据],'time'=>'时间格式','suffix'=>'后缀'
		'Customer'=>[
			  'name'=>['title'=>'客户名称','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'source_id'=>['title'=>'客户来源','action'=>'','table'=>'CustomerSource','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'grade_id'=>['title'=>'客户等级','action'=>'','table'=>'CustomerGrade','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'industry_id'=>['title'=>'所属行业','action'=>'','table'=>'CustomerIndustry','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'services_id'=>['title'=>'客户意向','action'=>'','table'=>'CustomerServices','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'address'=>['title'=>'客户地址','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'customer_status'=>['title'=>'客户状态','action'=>'','table'=>'BasicCustomer','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'intent_status'=>['title'=>'意向状态','action'=>'','table'=>'BasicCustomer','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'belong_uid'=>['title'=>'所属人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'belong_did'=>['title'=>'所属部门','action'=>'','table'=>'Department','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'share_ids'=>['title'=>'共享人员','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'1','enumerate'=>[],'time'=>'','suffix'=>''],
			  'content'=>['title'=>'客户描述','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'market'=>['title'=>'主要经营业务','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'remark'=>['title'=>'备注信息','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax_bank'=>['title'=>'开户银行','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax_banksn'=>['title'=>'银行帐号','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax_num'=>['title'=>'纳税人识别号','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax_mobile'=>['title'=>'开票电话','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax_address'=>['title'=>'开票地址','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'is_lock'=>['title'=>'锁定状态','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未锁','已锁'],'time'=>'','suffix'=>'']
		],
		'Contract'=>[
			  'pid'=>['title'=>'母协议','action'=>'','table'=>'Contract','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'name'=>['title'=>'合同名称','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'code'=>['title'=>'合同编号','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'cate_id'=>['title'=>'合同类别','action'=>'','table'=>'ContractCate','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'subject_id'=>['title'=>'签约主体','action'=>'','table'=>'Enterprise','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'customer_id'=>['title'=>'签约客户','action'=>'','table'=>'Customer','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contact_name'=>['title'=>'客户代表','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contact_mobile'=>['title'=>'客户电话','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contact_address'=>['title'=>'客户地址','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'prepared_uid'=>['title'=>'合同制定人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'sign_uid'=>['title'=>'合同签订人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'keeper_uid'=>['title'=>'合同保管人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'share_ids'=>['title'=>'共享人员','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'1','enumerate'=>[],'time'=>'','suffix'=>''],
			  'did'=>['title'=>'合同所属部门','action'=>'','table'=>'Department','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			 // 'content'=>['title'=>'合同内容','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'remark'=>['title'=>'备注信息','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'types'=>['title'=>'合同性质','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未设置','普通合同','商品合同','服务合同'],'time'=>'','suffix'=>''],
			  'start_time'=>['title'=>'合同生效时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'end_time'=>['title'=>'合同失效时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'sign_time'=>['title'=>'合同签订时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'cost'=>['title'=>'合同金额','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax'=>['title'=>'税点','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'is_tax'=>['title'=>'是否含税','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未含税','含税'],'time'=>'','suffix'=>'']
		],
		'Purchase'=>[
			  'pid'=>['title'=>'母协议','action'=>'','table'=>'Contract','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'name'=>['title'=>'合同名称','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'code'=>['title'=>'合同编号','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'cate_id'=>['title'=>'合同类别','action'=>'','table'=>'ContractCate','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'subject_id'=>['title'=>'签约主体','action'=>'','table'=>'Enterprise','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'supplier_id'=>['title'=>'签约供应商','action'=>'','table'=>'Supplier','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contact_name'=>['title'=>'供应商代表','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contact_mobile'=>['title'=>'供应商电话','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contact_address'=>['title'=>'供应商地址','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'prepared_uid'=>['title'=>'合同制定人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'sign_uid'=>['title'=>'合同签订人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'keeper_uid'=>['title'=>'合同保管人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'share_ids'=>['title'=>'共享人员','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'1','enumerate'=>[],'time'=>'','suffix'=>''],
			  'did'=>['title'=>'合同所属部门','action'=>'','table'=>'Department','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  //'content'=>['title'=>'合同内容','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'remark'=>['title'=>'备注信息','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'types'=>['title'=>'合同性质','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未设置','普通采购','物品合同','服务采购'],'time'=>'','suffix'=>''],
			  'start_time'=>['title'=>'合同生效时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'end_time'=>['title'=>'合同失效时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'sign_time'=>['title'=>'合同签订时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'cost'=>['title'=>'合同金额','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'tax'=>['title'=>'税点','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'is_tax'=>['title'=>'是否含税','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未含税','含税'],'time'=>'','suffix'=>'']
		],
		'Project'=>[
			  'name'=>['title'=>'项目名称','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'code'=>['title'=>'项目编号','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'cate_id'=>['title'=>'项目类别','action'=>'','table'=>'ProjectCate','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'customer_id'=>['title'=>'关联客户','action'=>'','table'=>'Customer','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'contract_id'=>['title'=>'关联合同','action'=>'','table'=>'Contract','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'director_uid'=>['title'=>'项目经理','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'did'=>['title'=>'项目所属部门','action'=>'','table'=>'Department','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'content'=>['title'=>'项目描述','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'start_time'=>['title'=>'项目开始时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'end_time'=>['title'=>'项目结束时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'amount'=>['title'=>'项目金额','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'status'=>['title'=>'项目状态','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未设置','未开始','进行中','已完成','已关闭'],'time'=>'','suffix'=>'']
		],
		'Task'=>[
			  'title'=>['title'=>'任务主题','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'pid'=>['title'=>'父级任务','action'=>'','table'=>'ProjectTask','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'before_task'=>['title'=>'前置任务','action'=>'','table'=>'ProjectTask','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'project_id'=>['title'=>'关联项目','action'=>'','table'=>'Project','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'work_id'=>['title'=>'工作类型','action'=>'','table'=>'WorkCate','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'step_id'=>['title'=>'项目阶段','action'=>'','table'=>'ProjectStep','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'director_uid'=>['title'=>'负责人','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'did'=>['title'=>'任务所属部门','action'=>'','table'=>'Department','table_field'=>'title','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'assist_admin_ids'=>['title'=>'协助人员','action'=>'','table'=>'Admin','table_field'=>'name','table_more'=>'1','enumerate'=>[],'time'=>'','suffix'=>''],
			  'start_time'=>['title'=>'项目开始时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'end_time'=>['title'=>'预计结束时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'over_time'=>['title'=>'实际结束时间','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'Y-m-d','suffix'=>''],
			  'plan_hours'=>['title'=>'预估工时','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>''],
			  'done_ratio'=>['title'=>'完成进度','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>[],'time'=>'','suffix'=>'%'],
			  'priority'=>['title'=>'优先级','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未设置','低','中','高','紧急'],'time'=>'','suffix'=>''],
			  'status'=>['title'=>'任务状态','action'=>'','table'=>'','table_field'=>'','table_more'=>'0','enumerate'=>['未设置','待办的','进行中','已完成','已拒绝','已关闭'],'time'=>'','suffix'=>'']
		]
	 ];

	 
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[])
    {
        $page = empty($param['page']) ?1 : intval($param['page']);;
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$name=$param['name'];
		$action_id=$param['action_id'];
        try {
            $list = self::field('a.*, u.name as admin_name,u.thumb')
			->where(['a.name'=>$name,'a.action_id'=>$action_id])
			->alias('a')
			->join('Admin u','u.id = a.admin_id')
			->order('a.create_time desc')
			->page($page, $rows)
            ->select()->toArray();
			
			$field = self::$COMPILE[$name];
			foreach ($list as $k => &$v) {
				$v['action'] = '修改';
				$v['times'] = time_trans($v['create_time']);
				$v['create_time'] = to_date($v['create_time']);
				if($v['field'] == 'new'){
					continue;
				}
				$item = $field[$v['field']];
				if(isset($item)){
					$v['field_name'] = $item['title'];
					if(!empty($item['action'])){
						$v['action'] = $item['action'];
					}
					if(!empty($item['table']) && !empty($item['table_field'])){
						if(!empty($item['table_more'])){
							$v['old_content'] = Db::name($item['table'])->where('id',$v['old_content'])->value($item['table_field']);
							$v['new_content'] = Db::name($item['table'])->where('id',$v['new_content'])->value($item['table_field']);
						}else{
							$old_content = Db::name($item['table'])->where('id','in',$v['old_content'])->column($item['table_field']);
							$new_content = Db::name($item['table'])->where('id','in',$v['new_content'])->column($item['table_field']);
							if(!empty($old_content)){
								$v['old_content'] = implode(',',$old_content);
							}
							if(!empty($new_content)){
								$v['new_content'] = implode(',',$new_content);
							}
						}						
					}
					if(!empty($item['enumerate'])){
						$v['old_content'] = $item['enumerate'][$v['old_content']];
						$v['new_content'] = $item['enumerate'][$v['new_content']];
					}
					if(!empty($item['time'])){
						$v['old_content'] = to_date($v['old_content'],$item['time']);
						$v['new_content'] = to_date($v['new_content'],$item['time']);
					}
					if ($v['old_content'] == '' || $v['old_content'] == 0 || $v['old_content'] == null) {
						$v['old_content'] = '未设置';
					}
					else{
						$v['old_content'] = $v['old_content'].$item['suffix'];
					}
					if ($v['new_content'] == '' || $v['new_content'] == 0 || $v['new_content'] == null) {
						$v['new_content'] = '未设置';
					}
					else{
						$v['new_content'] = $v['new_content'].$item['suffix'];
					}
				}
			}
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }

    /**
    * 插入创建日志
    */
	public function add($name,$action_id)
	{
		try {
			$session_admin = get_config('app.session_admin');
			$uid = \think\facade\Session::get($session_admin);
			$log_data = [
				'name' => $name,
				'admin_id' => $uid,
				'field' => 'new',
				'action_id' => $action_id,
				'create_time' => time()
			];
			self::strict(false)->field(true)->insert($log_data);
		} catch(\Exception $e) {
			return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
		}
	}
    /**
    * 插入编辑日志
    */
    public function edit($name,$action_id,$new,$old)
    {
		$log_data = [];
		$array = self::$COMPILE[$name];
		$key_array = array_keys($array);;
		if(!empty($key_array)){
			try {
				$session_admin = get_config('app.session_admin');
				$uid = \think\facade\Session::get($session_admin);
				foreach ($new as $key => $value) {
					if (in_array($key, $key_array)) {
						if(isset($old[$key]) && ($old[$key]!=$value)){
							$log_data[] = array(
								'name' => $name,
								'admin_id' => $uid,
								'field' => $key,
								'action_id' => $new['id'],
								'old_content' => $old[$key],
								'new_content' => $value,
								'create_time' => time(),
							);
						}
					}
				}
				self::strict(false)->field(true)->insertAll($log_data);
			} catch(\Exception $e) {
				return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
			}
		}
    }
	
    /**
    * 插入删除日志
    */
	public function del($name,$action_id)
	{
		try {
			$session_admin = get_config('app.session_admin');
			$uid = \think\facade\Session::get($session_admin);
			$log_data = [
				'name' => $name,
				'admin_id' => $uid,
				'field' => 'delete',
				'action_id' => $action_id,
				'create_time' => time()
			];
			self::strict(false)->field(true)->insert($log_data);
		} catch(\Exception $e) {
			return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
		}
	}
}

