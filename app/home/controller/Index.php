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

namespace app\home\controller;

use app\base\BaseController;
use app\home\model\AdminLog;
use app\user\validate\AdminCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{	
    public function index()
    {
		$mobile = is_mobile();
		if($mobile){
			return redirect('/qiye/index/index');
		}
		$admin = Db::name('Admin')->where('id',$this->uid)->find();
		if (get_cache('menu' . $this->uid)) {
			$list = get_cache('menu' . $this->uid);
		} else {
			$adminGroup = Db::name('PositionGroup')->where(['pid' => $admin['position_id']])->column('group_id');
			$adminMenu = Db::name('AdminGroup')->where('id', 'in', $adminGroup)->column('rules');
			$adminMenus = [];
			foreach ($adminMenu as $k => $v) {
				$v = explode(',', $v);
				$adminMenus = array_merge($adminMenus, $v);
			}
			$menu = Db::name('AdminRule')->where(['menu' => 1, 'status' => 1])->where('id', 'in', $adminMenus)->order('sort asc,id asc')->select()->toArray();
			$list = list_to_tree($menu);
			\think\facade\Cache::tag('adminMenu')->set('menu' . $this->uid, $list);
		}
		View::assign('menu', $list);
		View::assign('admin',$admin);
		View::assign('system',get_system_config('system'));
		View::assign('web',get_system_config('web'));
		return View();
    }

    public function main()
    {
        $install = false;
        if (file_exists(CMS_ROOT . 'app/install')) {
            $install = true;
        }
		$uid = $this->uid;
		$dids_son = get_leader_departments($uid);
        $total = [];
		
		$mapExpense = make_where($uid,'Expense');
		$whereExpense = $mapExpense['where'];
		$whereExpenseOr = $mapExpense['whereOr'];
        $total[] = array(
            'name' => '报销总数',
            'num' => Db::name('Expense')->where($whereExpense)
				->where(function ($query) use($whereExpenseOr) {
				if (!empty($whereExpenseOr))
					$query->whereOr($whereExpenseOr);
				})
				->count()
        );
		
		$mapInvoice = make_where($uid,'Invoice');
		$whereInvoice = $mapInvoice['where'];
		$whereInvoiceOr = $mapInvoice['whereOr'];
        $total[] = array(
            'name' => '开票总数',
            'num' => Db::name('Invoice')->where($whereInvoice)
				->where(function ($query) use($whereInvoiceOr) {
				if (!empty($whereInvoiceOr))		
					$query->whereOr($whereInvoiceOr);
				})
				->count()
        );
		
		$mapTicket = make_where($uid,'Ticket');
		$whereTicket = $mapTicket['where'];
		$whereTicketOr = $mapTicket['whereOr'];
        $total[] = array(
            'name' => '收票总数',
            'num' => Db::name('Ticket')->where($whereTicket)
				->where(function ($query) use($whereTicketOr) {
				if (!empty($whereTicketOr))	
					$query->whereOr($whereTicketOr);
				})
				->count()
        );
		
		$mapCustomer = make_where($uid,'Customer');
		$whereCustomer = $mapCustomer['where'];
		$whereCustomerOr = $mapCustomer['whereOr'];
		$total[] = array(
			'name' => '客户总数',
			'num' => Db::name('Customer')->where($whereCustomer)
					->where(function ($query) use($whereCustomerOr) {
					if (!empty($whereCustomerOr))	
						$query->whereOr($whereCustomerOr);
					})
					->count()
		);
		
		$mapContract = make_where($uid,'Contract');
		$whereContract = $mapContract['where'];
		$whereContractOr = $mapContract['whereOr'];
		$total[] = array(
			'name' => '销售合同',
			'num' => Db::name('Contract')->where($whereContract)
					->where(function ($query) use($whereContractOr) {
					if (!empty($whereContractOr))	
						$query->whereOr($whereContractOr);
					})
					->count()
		);

		$mapPurchase = make_where($uid,'Purchase');
		$wherePurchase = $mapPurchase['where'];
		$wherePurchaseOr = $mapPurchase['whereOr'];
		$total[] = array(
			'name' => '采购合同',
			'num' => Db::name('Purchase')->where($wherePurchase)
					->where(function ($query) use($wherePurchaseOr) {
					if (!empty($wherePurchaseOr))	
						$query->whereOr($wherePurchaseOr);
					})
					->count()
		);
		
		$mapProject = make_where($uid,'Project');
		$whereProject = $mapProject['where'];
		$whereProjectOr = $mapProject['whereOr'];
		$total[] = array(
			'name' => '项目总数',
			'num' => Db::name('Project')->where($whereProject)
					->where(function ($query) use($whereProjectOr) {
					if (!empty($whereProjectOr))	
						$query->whereOr($whereProjectOr);
					})
					->count()
		);
		
		$mapProjectTask = make_where($uid,'ProjectTask');
		$whereProjectTask = $mapProjectTask['where'];
		$whereProjectTaskOr = $mapProjectTask['whereOr'];
		$total[] = array(
			'name' => '任务总数',
			'num' => Db::name('ProjectTask')->where($whereProjectTask)
					->where(function ($query) use($whereProjectTaskOr) {
					if (!empty($whereProjectTaskOr))		
						$query->whereOr($whereProjectTaskOr);
					})
					->count()
		);

		$whereHandle = [];
		$whereHandle[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
		$whereHandle[] = ['delete_time', '=', 0];
		$handle=[];
		
        $handle[] = array(
            'name' => '待审公文',
            'num' =>  Db::name('OfficialDocs')->where($whereHandle)->count(),
            'id' => 197,
            'url' => '/adm/official/datalist',
        );
        $handle[] = array(
            'name' => '待审用章',
            'num' => Db::name('Seal')->where($whereHandle)->count(),
            'id' => 204,
            'url' => '/adm/seal/datalist',
        );
        $handle[] = array(
            'name' => '待审销售合同',
            'num' => Db::name('Contract')->where($whereHandle)->count(),
            'id' => 349,
            'url' => '/contract/contract/datalist',
        );
		$handle[] = array(
            'name' => '待审采购合同',
            'num' => Db::name('Purchase')->where($whereHandle)->count(),
            'id' => 353,
            'url' => '/contract/purchase/datalist',
        );
		$handle[] = array(
            'name' => '待审报销',
            'num' => Db::name('Expense')->where($whereHandle)->count(),
            'id' => 423,
            'url' => '/finance/expense/datalist',
        );
		$handle[] = array(
            'name' => '待审借支',
            'num' => Db::name('Loan')->where($whereHandle)->count(),
            'id' => 427,
            'url' => '/finance/loan/datalist',
        );
		$handle[] = array(
            'name' => '待审销项发票',
            'num' => Db::name('Invoice')->where($whereHandle)->where([['invoice_type','>',0]])->count(),
            'id' => 431,
            'url' => '/finance/invoice/datalist',
        );
		$handle[] = array(
            'name' => '待审进项收票',
            'num' => Db::name('Ticket')->where($whereHandle)->where([['invoice_type','>',0]])->count(),
            'id' => 435,
            'url' => '/finance/ticket/datalist',
        );
		$handle[] = array(
            'name' => '待审收款',
            'num' => Db::name('InvoiceIncome')->where($whereHandle)->count(),
            'id' => 439,
            'url' => '/finance/income/datalist',
        );
		$handle[] = array(
            'name' => '待审付款',
            'num' => Db::name('TicketPayment')->where($whereHandle)->count(),
            'id' => 443,
            'url' => '/finance/payment/datalist',
        );
		$handle[] = array(
            'name' => '待审退款',
            'num' => Db::name('IncomeRefund')->where($whereHandle)->count(),
            'id' => 447,
            'url' => '/finance/refund/datalist',
        );
		$handle[] = array(
            'name' => '待完成任务',
            'num' => Db::name('ProjectTask')->where([['director_uid', '=', $uid],['status', '<', 3],['delete_time', '=', 0]])->count(),
            'id' => 378,
            'url' => '/project/task/datalist',
        );
		
		$links = Db::name('Links')->where('delete_time',0)->order('sort desc')->select();
		
		$position_id = $this->pid;
		$layouts_array = get_config('layout');
		$position_id = Db::name('Admin')->where('id',$uid)->value('position_id');
		$layouts= Db::name('Position')->where(['id' => $position_id])->value('layouts');
		if(!empty($layouts)){
			$layouts_array = unserialize($layouts);
			usort($layouts_array, function($a, $b) {
				return intval($a['sort']) - intval($b['sort']);
			});
		}
		
		$todue=[];
		$delay_day = valueAuth('contract_admin','conf_10');
		if(empty($delay_day)){
			$delay_day = 30;
		}
		$delay_time = time()+$delay_day*60*60*24;

        $todue[] = array(
            'name' => '快到期的销售合同',
            'num' =>  Db::name('Contract')->where($whereContract)
					->where([['check_status','=',2],['end_time','<',$delay_time]])
					->where(function ($query) use($whereContractOr) {
					if (!empty($whereContractOr))	
						$query->whereOr($whereContractOr);
					})->count(),
            'id' => 349,
            'url' => '/contract/contract/datalist',
        );
        $todue[] = array(
            'name' => '快到期的采购合同',
            'num' =>  Db::name('Purchase')->where($wherePurchase)
						->where([['check_status','=',2],['end_time','<',$delay_time]])
						->where(function ($query) use($wherePurchaseOr) {
							$query->whereOr($wherePurchaseOr);
						})->count(),
            'id' => 353,
            'url' => '/contract/purchase/datalist',
        );
		$delay_day_b = valueAuth('project_admin','conf_10');
		if(empty($delay_day_b)){
			$delay_day_b = 3;
		}
		$delay_day_b_time = time()+$delay_day_b*60*60*24;
		$todue[] = array(
            'name' => '快到期的项目',
            'num' =>  Db::name('Project')->where($whereProject)->where($whereProject)
					->where([['status','<',3],['end_time','<',$delay_day_b_time]])
					->where(function ($query) use($whereProjectOr) {
					if (!empty($whereProjectOr))
						$query->whereOr($whereProjectOr);
					})
					->count(),
            'id' => 343,
            'url' => '/project/index/datalist',
        );
        $todue[] = array(
            'name' => '快到期的任务',
            'num' =>  Db::name('ProjectTask')
			->where($whereProjectTask)
			->where([['status','<',3],['end_time','<',$delay_day_b_time]])
			->where(function ($query) use ($whereProjectTaskOr) {
				if (!empty($whereProjectTaskOr))
					$query->whereOr($whereProjectTaskOr);
				})
			->count(),
            'id' => 378,
            'url' => '/project/task/datalist',
        );
		
		View::assign('layouts',$layouts_array);
        View::assign('total', $total);
        View::assign('handle', $handle);
        View::assign('todue', $todue);
        View::assign('links', $links);
        View::assign('install', $install);
        View::assign('TP_VERSION', \think\facade\App::version());
        return View();
    }
	
	//权限不足
	public function role()
    {
		return View('../../base/view/common/roletemplate');
	}
	//通讯录
	public function contacts_book()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.username|a.name|a.nickname|a.mobile|a.desc', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['did'])) {
				$admin_array = Db::name('DepartmentAdmin')->where('department_id',$param['did'])->column('admin_id');
				$map1=[
					['a.id','in',$admin_array],
				];
				$map2=[
					['a.did', '=', $param['did']],
				];
				$whereOr =[$map1,$map2];
            }
            $where[] = ['a.status', '=', 1];
            $where[] = ['a.id', '>', 1];
			$admin = \app\user\model\Admin::alias('a')
				->with('departments')
				->field('a.*,p.title as position,d.title as department')
				->join('Department d', 'd.id = a.did','left')
				->join('Position p', 'p.id = a.position_id','left')
				->where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->paginate(intval($this->pageSize))
				->order('a.id desc')
                ->each(function ($item, $key) {
					//遍历次要部门数据
					$departments = $item->departments->toArray();
					if(empty($departments)){
						$item->departments = '-';
					}
					else{
						$item->departments = split_array_field($departments,'title');
					}
					if($item->is_hide ==1){
						$item->mobile = hidetel($item->mobile);
						$item->email = hidetel($item->email);
					}
                    $item->entry_time = empty($item->entry_time) ? '-' : to_date($item->entry_time,'Y-m-d',);
                });
            return table_assign(0, '', $admin);
        } else {
            return view();
        }
    }
	

    //修改个人信息
    public function edit_personal()
    {
		if (request()->isAjax()) {
            $param = get_params();
            $uid = $this->uid;
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($param);
            return to_assign();
        }
		else{
			View::assign('admin',get_admin($this->uid));
			return view();
		}
    }

    //修改密码
    public function edit_password()
    {
		if (request()->isAjax()) {			
			$param = get_params();			
            try {
                validate(AdminCheck::class)->scene('editPwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $uid = $this->uid;
			
			$admin = Db::name('Admin')->where(['id' => $uid])->find();
			$old_psw = set_password($param['old_pwd'], $admin['salt']);
			if ($admin['pwd'] != $old_psw) {
				return to_assign(1, '旧密码错误');
			}

			$salt = set_salt(20);
			$new_pwd = set_password($param['pwd'], $salt);
			$data = [
				'reg_pwd' => '',
				'salt' => $salt,
				'pwd' => $new_pwd,
				'update_time' => time(),
			];
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($data);
            return to_assign();
        }
		else{
			View::assign('admin',get_admin($this->uid));
			return view();
		}
    }
	
    //系统操作日志
    public function log_list()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$log = new AdminLog();
			$content = $log->get_log_list($param);
			return table_assign(0, '', $content);
		}else{
			return view();
		}
    }
	
	//设置theme
	public function set_theme()
    {
        if (request()->isAjax()) {
            $param = get_params();
			Db::name('Admin')->where('id',$this->uid)->update(['theme'=>$param['theme']]);
            return to_assign();
        }
		else{
			return to_assign(1,'操作错误');
		}
    }
	
    //办公工具
    public function get_links()
    {
		if (request()->isAjax()) {
			$links = Db::name('Links')->where('delete_time',0)->order('sort desc')->select();
			return to_assign(0, '', $links);
		}else{
			return view();
		}
    }
}
