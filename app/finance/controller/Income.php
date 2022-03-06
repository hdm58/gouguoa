<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Invoice as InvoiceList;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Income extends BaseController
{
    public function get_list($param = [], $where = [])
    {
        $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
        $expense = InvoiceList::where($where)
            ->order('create_time asc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
                $item->user = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
                $item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
                $item->check_name = Db::name('Admin')->where(['id' => $item->check_admin_id])->value('name');
                $item->check_time = empty($item->check_time) ? '-' : date('Y-m-d H:i', $item->check_time);
                $item->open_name = Db::name('Admin')->where(['id' => $item->open_admin_id])->value('name');
                $item->open_time = empty($item->open_time) ? '-' : date('Y-m-d H:i', $item->open_time);
            });
        return $expense;
    }

    public function detail($id = 0)
    {
        $invoice = Db::name('Invoice')->where(['id' => $id])->find();
        if ($invoice) {
            $invoice['user'] = Db::name('Admin')->where(['id' => $invoice['admin_id']])->value('name');
            $invoice['department'] = Db::name('Department')->where(['id' => $invoice['did']])->value('title');
            $invoice['check_admin'] = Db::name('Admin')->where(['id' => $invoice['check_admin_id']])->value('name');
            $invoice['open_admin'] = Db::name('Admin')->where(['id' => $invoice['open_admin_id']])->value('name');
            if ($invoice['check_time'] > 0) {
                $invoice['check_time'] = empty($invoice['check_time']) ? '0' : date('Y-m-d H:i', $invoice['check_time']);
            }
            if ($invoice['open_time'] > 0) {
                $invoice['open_time'] = empty($invoice['open_time']) ? '0' : date('Y-m-d H:i', $invoice['open_time']);
            }
            else{
                $invoice['open_time'] = '-';
            }
            $invoice['not_income'] =  ($invoice['amount']*100 - $invoice['enter_amount']*100)/100;
            //已到账的记录
            $invoice['income'] = Db::name('InvoiceIncome')
            ->field('i.*,a.name as admin')
            ->alias('i')
            ->join('Admin a', 'a.id = i.admin_id', 'LEFT')
            ->where(['i.inid'=>$id,'i.status'=>1])
            ->order('i.id asc')
            ->select();
        }
        return $invoice;
    }

    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
            $where[] = ['status', '=', 1];
            $where[] = ['check_status', '=', 5];
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $where[] = ['enter_time', 'between', [$start_time, $end_time]];
            }
            if (isset($param['is_cash']) && $param['is_cash']!='') {
                $where[] = ['is_cash', '=', $param['is_cash']];
            }
            $invoice = $this->get_list($param, $where);
            return table_assign(0, '', $invoice);
        } else {
            return view();
        }
    }

    //查看
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {   
            $inid = $param['inid'];   
            $admin_id = $this->uid;
            //计算已到账的金额
            $hasIncome = Db::name('InvoiceIncome')->where(['inid'=>$inid,'status'=>1])->sum('amount');
            //查询发票金额
            $invoiceAmount = Db::name('Invoice')->where(['id'=>$inid])->value('amount');
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
                            'inid' => $inid,
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
                        $res = Db::name('InvoiceIncome')->strict(false)->field(true)->insertAll($insert);
                        if($res!==false){
                            if(($enter_price + $hasIncome*100) == $invoiceAmount*100){
                                //发票全部到账
                                Db::name('Invoice')->where(['id'=>$inid])->update(['is_cash'=>2,'enter_amount'=>$invoiceAmount,'enter_time'=>time()]);
                            }
                            else if(($enter_price + $hasIncome*100) < $invoiceAmount*100){
                                $incomeTotal=($enter_price + $hasIncome*100)/100;
                                //发票部分到账
                                Db::name('Invoice')->where(['id'=>$inid])->update(['is_cash'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                            }
                            add_log('add',$inid,$param);
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
                    'inid' => $inid,
                    'amount' => $enter_price,
                    'enter_time' => isset($param['enter_time'])? strtotime($param['enter_time']) : 0,
                    'remarks' => '一次性全部到账',
                    'admin_id' => $admin_id,
                    'create_time' => time()
                ];
                $res = Db::name('InvoiceIncome')->strict(false)->field(true)->insertGetId($data);
                if($res!==false){
                    //设置发票全部到账
                    Db::name('Invoice')->where(['id'=>$inid])->update(['is_cash'=>2,'enter_amount'=>$invoiceAmount,'enter_time'=>time()]);
                    add_log('add',$inid,$param);
                    return to_assign();
                }
            }
            else if ($param['enter_type']==3) {//全部反账记录
                //作废初始化发票到账数据
                $res = Db::name('InvoiceIncome')->where(['inid'=>$inid])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    //设置发票全部没到账
                    Db::name('Invoice')->where(['id'=>$inid])->update(['is_cash'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    add_log('tovoid',$inid,$param);
                    return to_assign();
                }                
            }
        }
        else{
            $id = isset($param['id']) ? $param['id']: 0 ;
            $detail = $this->detail($id);
            View::assign('uid', $this->uid);
            View::assign('id', $id);
            View::assign('detail', $detail);
            return view();
        }
    }
    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = $this->detail($id);
        View::assign('uid', $this->uid);
        View::assign('detail', $detail);
        return view();
    }

    //删除到账记录
    public function delete()
    {
        $param = get_params();
        if (request()->isAjax()) {
            //作废初始化发票到账数据
            $income = Db::name('InvoiceIncome')->where(['id'=>$param['id']])->find();
            $invoice = Db::name('Invoice')->where(['id'=>$income['inid']])->find();
            if($income){
                $res = Db::name('InvoiceIncome')->where(['id'=>$param['id']])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    if($income['amount']*100 == $invoice['amount']*100){
                        //发票全部反到账
                        Db::name('Invoice')->where(['id'=>$income['inid']])->update(['is_cash'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    }
                    else if($income['amount']*100 < $invoice['amount']*100){
                        $incomeTotal=Db::name('InvoiceIncome')->where(['inid'=>$income['inid'],'status'=>1])->sum('amount');
                        //发票部分到账
                        Db::name('Invoice')->where(['id'=>$income['inid']])->update(['is_cash'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                    }
                    add_log('enter',$income['inid'],$invoice);
                    return to_assign();
                }
                else{
                    return to_assign(1,'操作失败');
                }
            }
        }
    }
}
