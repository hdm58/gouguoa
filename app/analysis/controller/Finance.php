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
 
declare (strict_types = 1);

namespace app\analysis\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use app\api\model\FinanceLog;
use think\facade\Db;
use think\facade\View;

class Finance extends BaseController
{		
    /**
    * 发票数据分析
    */
    public function invoicelist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{
			
			return view();
		}
    }
	
    /**
    * 收付款数据分析
    */
    public function paymentlist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
 
        }else{			
			return view();
		}
    }
	
    /**
    * 收入流水
    */
    public function incomelist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$where=[];
			$where[]=['delete_time','=',0];
			$where[]=['types','=',1];
			if(!empty($param['create_time'])){
				if (!empty($param['create_time'])) {
					$create_time =explode('~', $param['create_time']);
					$where[] = ['create_time', 'between',[strtotime(urldecode($create_time[0])),strtotime(urldecode($create_time[1].' 23:59:59'))]];
				}
			}
			$model = new FinanceLog();
			$list = $model->datalist($param,$where);
			$amount = $model->where($where)->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        }else{			
			return view();
		}
    }

	/**
    * 收入流水
    */
    public function payoutlist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$where=[];
			$where[]=['delete_time','=',0];
			$where[]=['types','=',2];
			if(!empty($param['create_time'])){
				if (!empty($param['create_time'])) {
					$create_time =explode('~', $param['create_time']);
					$where[] = ['create_time', 'between',[strtotime(urldecode($create_time[0])),strtotime(urldecode($create_time[1].' 23:59:59'))]];
				}
			}
			$model = new FinanceLog();
			$list = $model->datalist($param,$where);
			$amount = $model->where($where)->sum('amount');
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list,$totalRow);
        }else{			
			return view();
		}
    }
	
	/**
    * 财务台账分析
    */
    public function accountlist()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$year = isset($param['year']) ? $param['year'] : date('Y');
			$types = isset($param['types']) ? $param['types'] : 0;
			$year_start=strtotime("{$year}-01-01");
			$year_end=strtotime("{$year}-12-31 23:59:59");
			
			if($types==1){
				// 初始化月份列表
				$months = [];
				$income = [];
				for ($i = 1; $i <= 12; $i++) {
					$monthStr = sprintf("%02d", $i);
					$months[] = $i . "月";
					$month_start=strtotime("{$year}-{$monthStr}-01");
					$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
					$income_amount = Db::name('FinanceLog')->where([['action_time','between',[$month_start,$month_end]],['types','=',1],['delete_time','=',0]])->sum('amount');
					$income[] = $income_amount;
				}
				$result = [
					'title' => [
						'text' => '每月支出对比'
					],
					'legend' => [
						'月收入'
					],
					'xaxis' => $months,
					'yaxis' => [
						[
							'type' => 'value',
							'axisLabel' => [
								'formatter' => '{value} 元'
							]
						]
					],
					'series' => [
						[
							'name' => '支出金额',
							'type' => 'bar',
							'barWidth'=>'60%',
							'data' => $income
						]
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==2){
				// 初始化月份列表
				$months = [];
				$payout = [];
				for ($i = 1; $i <= 12; $i++) {
					$monthStr = sprintf("%02d", $i);
					$months[] = $i . "月";
					$month_start=strtotime("{$year}-{$monthStr}-01");
					$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
					$payout_amount = Db::name('FinanceLog')->where([['action_time','between',[$month_start,$month_end]],['types','=',2],['delete_time','=',0]])->sum('amount');
					$payout[] = $payout_amount;
				}
				$result = [
					'title' => [
						'text' => '每月支出对比'
					],
					'legend' => [
						'月收入'
					],
					'xaxis' => $months,
					'yaxis' => [
						[
							'type' => 'value',
							'axisLabel' => [
								'formatter' => '{value} 元'
							]
						]
					],
					'series' => [
						[
							'name' => '支出金额',
							'type' => 'bar',
							'barWidth'=>'60%',
							'data' => $payout
						]
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==3){
				$data = [];
				$cates = get_base_data('PayType');
				foreach ($cates as $cate) {
					$data[] = [
						'name' => $cate['title'],
						'value' => Db::name('FinanceLog')->where([['action_time','between',[$year_start,$year_end]],['paytype_id','=',$cate['id']],['types','=',1],['delete_time','=',0]])->sum('amount')
					];
				}

				$result = [
					'title' => [
						'text' => '收入收款方式分析'
					],
					'series' => [
						'data' => $data
					]
				];
				return to_assign(0, '', $result);
			}
			if($types==4){
				$data = [];
				$cates = get_base_data('PayType');
				foreach ($cates as $cate) {
					$data[] = [
						'name' => $cate['title'],
						'value' => Db::name('FinanceLog')->where([['action_time','between',[$year_start,$year_end]],['paytype_id','=',$cate['id']],['types','=',2],['delete_time','=',0]])->sum('amount')
					];
				}

				$result = [
					'title' => [
						'text' => '支出付款方式分析'
					],
					'series' => [
						'data' => $data
					]
				];
				return to_assign(0, '', $result);
			}
			
        }else{	
			//注资金额
			$injection_amount = Db::name('FinanceInjection')->where(['delete_time'=>0])->sum('amount');
			$injection = sprintf("%.2f", (float)$injection_amount);
			
			//账号初始化金额
			$initial_amount = Db::name('Account')->where(['delete_time'=>0])->sum('initial_amount');
			$initial = sprintf("%.2f", (float)$initial_amount);
			
			//账号当前金额
			$account_amount = Db::name('Account')->where(['delete_time'=>0])->sum('amount');
			$total_amount = ($account_amount*10000 + $initial_amount*10000 + $injection_amount*10000)/10000;
			$amount = sprintf("%.2f", (float)$total_amount);
			
			//总收入
			$income_amount = Db::name('FinanceLog')->where(['types'=>1,'delete_time'=>0])->sum('amount');
			$income = sprintf("%.2f", (float)$income_amount);
			
			//总支出
			$payout_amount = Db::name('FinanceLog')->where(['types'=>2,'delete_time'=>0])->sum('amount');
			$payout = sprintf("%.2f", (float)$payout_amount);
			
			$enterprise = Db::name('Enterprise')->select()->toArray();
			foreach($enterprise as $k => &$value){
				$account = Db::name('Account')->where(['enterprise_id'=>$value['id'],'delete_time'=>0])->select()->toArray();
				//注资金额
				$injection_amount = Db::name('FinanceInjection')->where(['enterprise_id'=>$value['id'],'delete_time'=>0])->sum('amount');
				$value['injection'] = sprintf("%.2f", (float)$injection_amount);
				
				//账号初始化金额
				$initial_amount = Db::name('Account')->where(['enterprise_id'=>$value['id'],'delete_time'=>0])->sum('initial_amount');
				$value['initial'] = sprintf("%.2f", (float)$initial_amount);
				
				//账号当前金额
				$account_amount = Db::name('Account')->where(['enterprise_id'=>$value['id'],'delete_time'=>0])->sum('amount');
				//账号当前金额
				$total_amount = ($account_amount*10000 + $initial_amount*10000 + $injection_amount*10000)/10000;
				$value['total'] = sprintf("%.2f", (float)$total_amount);
				
				//总收入
				$income_amount = Db::name('FinanceLog')->where(['enterprise_id'=>$value['id'],'types'=>1,'delete_time'=>0])->sum('amount');
				$value['income'] = sprintf("%.2f", (float)$income_amount);
				
				//总支出
				$payout_amount = Db::name('FinanceLog')->where(['enterprise_id'=>$value['id'],'types'=>2,'delete_time'=>0])->sum('amount');
				$value['payout'] = sprintf("%.2f", (float)$payout_amount);
				
				foreach($account as $kk => &$val){
					//注资金额
					$injection_amount = Db::name('FinanceInjection')->where(['account_id'=>$val['id'],'delete_time'=>0])->sum('amount');
					$val['injection'] = sprintf("%.2f", (float)$injection_amount);
					
					//账号当前金额
					$total_amount = ($val['amount']*10000 + $val['initial_amount']*10000 + $injection_amount*10000)/10000;
					$val['total'] = sprintf("%.2f", (float)$total_amount);
					
					//总收入
					$income_amount = Db::name('FinanceLog')->where(['account_id'=>$val['id'],'types'=>1,'delete_time'=>0])->sum('amount');
					$val['income'] = sprintf("%.2f", (float)$income_amount);
					
					//总支出
					$payout_amount = Db::name('FinanceLog')->where(['account_id'=>$val['id'],'types'=>2,'delete_time'=>0])->sum('amount');
					$val['payout'] = sprintf("%.2f", (float)$payout_amount);
				}
				$value['account'] = $account;
				$value['row'] = count($account)+2;
			}
			
			View::assign('injection', $injection);
			View::assign('initial', $initial);
			View::assign('amount', $amount);
			View::assign('income', $income);
			View::assign('payout', $payout);
			View::assign('enterprise', $enterprise);
			return view();
		}
    }

}
