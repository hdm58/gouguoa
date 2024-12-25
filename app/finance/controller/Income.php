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

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Invoice as InvoiceModel;
use app\finance\model\InvoiceIncome;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Income extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new InvoiceModel();
    }
	
    public function datalist()
    {
		$uid = $this->uid;
		$auth = isAuthIncome($uid);
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            $where[] = ['delete_time', '=', 0];
            $where[] = ['check_status', '=', 2];
            $where[] = ['open_status', '=', 1];
			$where[] = ['invoice_type','>',0];
			if($auth == 0){
				$whereOr[] = ['admin_id','=',$this->uid];
				$dids_a = get_leader_departments($uid);	
				$dids_b = get_role_departments($uid);
				$dids = array_merge($dids_a, $dids_b);
				if(!empty($dids)){
					$whereOr[] = ['did','in',$dids];
				}
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['enter_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
            if (isset($param['enter_status']) && $param['enter_status']!='') {
                $where[] = ['enter_status', '=', $param['enter_status']];
            }
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
			View::assign('auth', $auth);
            return view();
        }
    }

    //查看
    public function add()
    {
        $param = get_params();
		$auth = isAuthIncome($this->uid);
        if (request()->isAjax()) {   
			if($auth == 0){
				return to_assign(1, "你没有到账管理权限，请联系管理员或者HR");
			}
            $invoice_id = $param['invoice_id'];   
            $admin_id = $this->uid;
            //计算已到账的金额
            $hasIncome = InvoiceIncome::where(['invoice_id'=>$invoice_id,'status'=>1])->sum('amount');
            //查询发票金额
            $invoiceAmount = $this->model->where(['id'=>$invoice_id])->value('amount');
            if($param['enter_type']==1){ //单个到账记录
                //相关内容多个数组
                $enterPriceData=isset($param['amount'])? $param['amount'] : '';
                $enterTimeData=isset($param['enter_time'])? $param['enter_time'] : '';
                $remarksData=isset($param['remarks'])? $param['remarks'] : '';

                //把合同协议关联的单个内容的发票入账明细重新添加
                if($enterPriceData){
                    $enter_price = 0;
                    $insert = [];
		            $time = time();
                    foreach ($enterPriceData as $key => $value) {
                        if (!$value ) continue;
                        $insert[] = [
                            'invoice_id' => $invoice_id,
						    'amount' 	=> $value,
						    'enter_time' => $enterTimeData[$key]? strtotime($enterTimeData[$key]) : 0,
						    'remarks' 	    => $remarksData[$key],
						    'admin_id' 	    => $admin_id,
						    'create_time'		=> $time
						];
                        $enter_price += $value*100;
                    }
                    if(($enter_price + $hasIncome*100)> $invoiceAmount*100){
                        return to_assign(1,'到账金额大于发票金额，不允许保存');
                    }
                    else{
                        $res = InvoiceIncome::strict(false)->field(true)->insertAll($insert);
                        if($res!==false){
                            if(($enter_price + $hasIncome*100) == $invoiceAmount*100){
                                //发票全部到账
                                $this->model->where(['id'=>$invoice_id])->update(['enter_status'=>2,'enter_amount'=>$invoiceAmount,'enter_time'=>time()]);
                            }
                            else if(($enter_price + $hasIncome*100) < $invoiceAmount*100){
                                $incomeTotal=($enter_price + $hasIncome*100)/100;
                                //发票部分到账
                                $this->model->where(['id'=>$invoice_id])->update(['enter_status'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                            }
                            add_log('add',$invoice_id,$param);
                            return to_assign();
                        }
                        else{
                            return to_assign(1,'保存失败');
                        }
                    }
                }
                else{
                    return to_assign(1,'提交的到账数据异常，请核对再提交');
                }         
            }
            else if($param['enter_type']==2){ //全部到账记录
                $enter_price = ($invoiceAmount*100-$hasIncome*100)/100;
                $data = [
                    'invoice_id' => $invoice_id,
                    'amount' => $enter_price,
                    'enter_time' => isset($param['enter_time'])? strtotime($param['enter_time']) : 0,
                    'remarks' => '一次性全部到账',
                    'admin_id' => $admin_id,
                    'create_time' => time()
                ];
                $res = InvoiceIncome::strict(false)->field(true)->insertGetId($data);
                if($res!==false){
                    //设置发票全部到账
                    $this->model->where(['id'=>$invoice_id])->update(['enter_status'=>2,'enter_amount'=>$invoiceAmount,'enter_time'=>time()]);
                    add_log('add',$invoice_id,$param);
                    return to_assign();
                }
            }
            else if ($param['enter_type']==3) {//全部反账记录
                //作废初始化发票到账数据
                $res = InvoiceIncome::where(['invoice_id'=>$invoice_id])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    //设置发票全部没到账
                    $this->model->where(['id'=>$invoice_id])->update(['enter_status'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    add_log('tovoid',$invoice_id,$param);
                    return to_assign();
                }                
            }
        }
        else{
			if($auth == 0){
				return view(EEEOR_REPORTING,['code'=>405,'warning'=>'无权限访问']);
			}
            $id = isset($param['id']) ? $param['id']: 0 ;
            $detail = $this->model->getById($id);
			if(empty($detail)){
				return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到记录']);
			}
			$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
			$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['file_array'] = $file_array;
			$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['other_file_array'] = $other_file_array;
			if($detail['open_status']>0){
				$detail['open_admin_name'] = Db::name('Admin')->where('id','=',$detail['open_admin_id'])->value('name');
			}
			$not_income =  ($detail['amount']*100 - $detail['enter_amount']*100)/100;
			$detail['not_income'] = sprintf("%.2f",$not_income);
			//已到账的记录
			$detail['income'] = InvoiceIncome::field('i.*,a.name as admin')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'LEFT')
				->where(['i.invoice_id'=>$id,'i.status'=>1])
				->order('i.enter_time desc')
				->select();
            View::assign('uid', $this->uid);
            View::assign('id', $id);
            View::assign('detail', $detail);
			if($detail['invoice_type'] == 0){
				return view('add_a');
			}
            return view();
        }
    }
    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = $this->model->getById($id);
		if(empty($detail)){
			throw new \think\exception\HttpException(406, '找不到记录');
		}
		$detail['not_income'] =  ($detail['amount']*100 - $detail['enter_amount']*100)/100;
		//已到账的记录
		$detail['income'] = InvoiceIncome::field('i.*,a.name as admin')
			->alias('i')
			->join('Admin a', 'a.id = i.admin_id', 'LEFT')
			->where(['i.invoice_id'=>$id,'i.status'=>1])
			->order('i.enter_time desc')
			->select();
			
		$detail['subject'] = Db::name('Enterprise')->where(['id' =>$detail['invoice_subject']])->value('title');
		$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
		$detail['file_array'] = $file_array;
		$other_file_array = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
		$detail['other_file_array'] = $other_file_array;
		if($detail['open_status']>0){
			$detail['open_admin_name'] = Db::name('Admin')->where('id','=',$detail['open_admin_id'])->value('name');
		}
        View::assign('uid', $this->uid);
        View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/finance/view_income');
		}
        return view();
    }

    //删除到账记录
    public function delete()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $income =InvoiceIncome::where(['id'=>$param['id']])->find();
            $invoice = $this->model->where(['id'=>$income['invoice_id']])->find();
            if($income){
                $res = InvoiceIncome::where(['id'=>$param['id']])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    if($income['amount']*100 == $invoice['amount']*100){
                        //发票全部反到账
                        $this->model->where(['id'=>$income['invoice_id']])->update(['enter_status'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    }
                    else if($income['amount']*100 < $invoice['amount']*100){
                        $incomeTotal=InvoiceIncome::where(['invoice_id'=>$income['invoice_id'],'status'=>1])->sum('amount');
                        //发票部分到账
                        $this->model->where(['id'=>$income['invoice_id']])->update(['enter_status'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                    }
                    add_log('enter',$income['invoice_id'],$invoice);
                    return to_assign();
                }
                else{
                    return to_assign(1,'操作失败');
                }
            }
        }
    }
	
	
	//回款记录
    public function record()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			$where[]=['status','=',1];
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['enter_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
			}
			$model = new InvoiceIncome();
			$list = $model->datalist($param,$where);
			
			$amount = $model::where($where)->sum('amount');					
			$totalRow['amount'] = sprintf("%.2f",$amount);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
}
