<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
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
		'loan'=>['title'=>'日常借支','table'=>'Loan','amount_field'=>'cost','types'=>"2",'code'=>'LO'],
		'loan_back'=>['title'=>'借支归还','table'=>'Loan','amount_field'=>'cost','types'=>"1",'code'=>'LC'],
		'expense'=>['title'=>'日常报销','table'=>'Expense','amount_field'=>'pay_amount','types'=>"2",'code'=>'EX'],
		'salary'=>['title'=>'员工工资','table'=>'Salary','amount_field'=>'salary','types'=>"2",'code'=>'SA'],
		'social'=>['title'=>'员工社保','table'=>'Salary','amount_field'=>'social','types'=>"2",'code'=>'SO'],
		'gongjijin'=>['title'=>'员工公积金','table'=>'Salary','amount_field'=>'gongjijin','types'=>"2",'code'=>'GJ'],
		'tax'=>['title'=>'员工个税','table'=>'Salary','amount_field'=>'tax','types'=>"2",'code'=>'TA'],
	 ];

	 
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[],$where=[])
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        try {
            $list = self::where($where)
			->order('create_time desc')
			->paginate(['list_rows'=> $rows])
            ->each(function ($item, $key){
 				$types = self::$COMPILE[$item->name];
				$item->title = $types['title'];
				$item->enterprise = Db::name('Enterprise')->where('id','=',$item->enterprise_id)->value('title');
				$item->account = Db::name('Account')->where('id','=',$item->account_id)->value('title');
				$item->paytype = Db::name('PayType')->where('id','=',$item->paytype_id)->value('title');
				$item->fundscate='-';
				if($item->fundscate_id>0){
					$item->fundscate = Db::name('FundsCate')->where('id','=',$item->fundscate_id)->value('title');
				}
				$item->admin_name = Db::name('Admin')->where('id','=',$item->admin_id)->value('name');
				$item->create_time = to_date($item->create_time); 
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }

    /**
    * 插入财务流水
    */
	public function add($name='',$action_id=0,$amount=0)
	{
		try {
			$session_admin = get_config('app.session_admin');
			$uid = \think\facade\Session::get($session_admin);
			$array = self::$COMPILE[$name];
			if(!empty($array)){
				$detail = Db::name($array['table'])->where('id',$action_id)->find();
				$field = $array['amount_field'];
				if($amount>0){
					$detail[$field] = $amount;
				}
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
					'action_time' => time(),
					'transaction_no' => get_codeno($array['code']),
					'amount' => $detail[$field],
					'enterprise_id' => $detail['enterprise_id'],
					'account_id' => $detail['account_id'],
					'paytype_id' => $detail['paytype_id'],
					'fundscate_id' => empty($detail['fundscate_id'])?'0':$detail['fundscate_id'],
					'admin_id' => $uid,
					'create_time' => time()
				];
				if($res!=false){
					self::strict(false)->field(true)->insert($log_data);
				}
			}
		} catch(\Exception $e) {
			return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
		}
    }
	
    /**
    * 删除财务流水
    */
	public function del($name='',$action_id=0,$amount=0)
	{
		try {
			$session_admin = get_config('app.session_admin');
			$uid = \think\facade\Session::get($session_admin);
			$array = self::$COMPILE[$name];
			if(!empty($array)){
				$detail = Db::name($array['table'])->where('id',$action_id)->find();
				$field = $array['amount_field'];
				if($amount>0){
					$detail[$field] = $amount;
				}
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

