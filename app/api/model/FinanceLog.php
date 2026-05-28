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
class FinanceLog extends Model
{
	 public static $COMPILE = [
		//'title'=>'交易流水简称','table'=>'数据表名称','amount_field'=>'金额字段名称','types'=>'类型:1收入,2支出','code'=>'交易流水号前缀'
		'income'=>['title'=>'业务收款','table'=>'InvoiceIncome','amount_field'=>'amount','types'=>"1",'code'=>'IN'],
		'payment'=>['title'=>'业务付款','table'=>'TicketPayment','amount_field'=>'amount','types'=>"2",'code'=>'PA'],
		'refund'=>['title'=>'业务退款','table'=>'IncomeRefund','amount_field'=>'amount','types'=>"2",'code'=>'RE'],
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
						if(empty($item['table_more'])){
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
    * 插入财务日志
    */
	public function add($name,$action_id)
	{
		//try {
			$session_admin = get_config('app.session_admin');
			$uid = \think\facade\Session::get($session_admin);
			$array = self::$COMPILE[$name];
			if(!empty($array)){
				$detail = Db::name($array['table'])->where('id',$action_id)->find();
				$field = $array['amount_field'];
				$res=false;
				if($array['types']==1){
					$res=Db::name('Account')->where('id',$detail['account_id'])->inc('amount', $detail[$field])->update();
				}
				if($array['types']==2){
					$res=Db::name('Account')->where('id',$detail['account_id'])->dec('amount', $detail[$field])->update();
				}
				$log_data = [
					'name' => $name,
					'types' => $array['types'],
					'action_id' => $action_id,
					'transaction_no' => get_codeno($array['code']),
					'amount' => $detail[$field],
					'enterprise_id' => $detail['enterprise_id'],
					'account_id' => $detail['account_id'],
					'fundscate_id' => $detail['fundscate_id'],
					'paytype_id' => $detail['paytype_id'],
					'admin_id' => $uid,
					'create_time' => time()
				];
				if($res!=false){
					self::strict(false)->field(true)->insert($log_data);
				}
			}
		//} catch(\Exception $e) {
		//	return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
		//}
    }
	
    /**
    * 删除财务日志
    */
	public function del($name,$action_id)
	{
		try {
			$session_admin = get_config('app.session_admin');
			$uid = \think\facade\Session::get($session_admin);
			$array = self::$COMPILE[$name];
			if(!empty($array)){
				$detail = Db::name($array['table'])->where('id',$action_id)->find();
				$field = $array['amount_field'];
				$res=false;
				if($array['types']==1){
					$res=Db::name('Account')->where('id',$detail['account_id'])->dec('amount', $detail[$field])->update();
				}
				if($array['types']==2){
					$res=Db::name('Account')->where('id',$detail['account_id'])->inc('amount', $detail[$field])->update();
				}
				if($res!=false){
					self::strict(false)->where(['types'=>$array['types'],'action_id'=>$action_id,'account_id' => $detail['account_id'],'delete_time'=>0])->update(['delete_time'=>time()]);
				}
			}
		} catch(\Exception $e) {
			return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
		}
    }
}

