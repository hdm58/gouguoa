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
		$dids = get_role_departments($uid);
        $total = [];
		
		$whereFinance= array();
		$whereFinanceOr = array();
		$whereFinance[] = ['delete_time', '=', 0];
		$whereFinance[] = ['check_status', '=', 2];
		$whereFinancerOr[] =['admin_id', '=', $uid];	
		if(!empty($dids)){
			$whereFinancerOr[] =['did', 'in', $dids];
		}	
		
        $total[] = array(
            'name' => '报销总数',
            'num' => Db::name('Expense')->where($whereFinance)
				->where(function ($query) use($whereFinancerOr) {
						$query->whereOr($whereFinancerOr);
					})
				->count()
        );
        $total[] = array(
            'name' => '开票总数',
            'num' => Db::name('Invoice')->where($whereFinance)
				->where(function ($query) use($whereFinancerOr) {
						$query->whereOr($whereFinancerOr);
					})
				->count()
        );
        $total[] = array(
            'name' => '收票总数',
            'num' => Db::name('Ticket')->where($whereFinance)
				->where(function ($query) use($whereFinancerOr) {
						$query->whereOr($whereFinancerOr);
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
            'id' => 169,
            'url' => '/adm/official/datalist',
        );
        $handle[] = array(
            'name' => '待审用章',
            'num' => Db::name('Seal')->where($whereHandle)->count(),
            'id' => 176,
            'url' => '/adm/seal/datalist',
        );
        $handle[] = array(
            'name' => '待审销售合同',
            'num' => Db::name('Contract')->where($whereHandle)->count(),
            'id' => 319,
            'url' => '/contract/contract/datalist',
        );
		$handle[] = array(
            'name' => '待审采购合同',
            'num' => Db::name('Purchase')->where($whereHandle)->count(),
            'id' => 323,
            'url' => '/contract/purchase/datalist',
        );
		$handle[] = array(
            'name' => '待审报销',
            'num' => Db::name('Expense')->where($whereHandle)->count(),
            'id' => 218,
            'url' => '/finance/expense/datalist',
        );
		$handle[] = array(
            'name' => '待审发票',
            'num' => Db::name('Invoice')->where($whereHandle)->where([['invoice_type','>',0]])->count(),
            'id' => 222,
            'url' => '/finance/invoice/datalist',
        );
		$handle[] = array(
            'name' => '待审收票',
            'num' => Db::name('ticket')->where($whereHandle)->where([['invoice_type','>',0]])->count(),
            'id' => 226,
            'url' => '/finance/ticket/datalist',
        );
		$handle[] = array(
            'name' => '待完成任务',
            'num' => Db::name('ProjectTask')->where([['director_uid', '=', $uid],['status', '<', 3],['delete_time', '=', 0]])->count(),
            'id' => 348,
            'url' => '/project/task/datalist',
        );
				
		$whereCustomer = array();
		$whereCustomerOr = array();
		$whereCustomer[] = ['delete_time', '=', 0];
		$whereCustomerOr[] =['belong_uid', '=', $uid];	
		if(!empty($dids)){
			$whereCustomerOr[] =['belong_did', 'in', $dids];
		}			
		$whereCustomerOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
		
		$customerCount = Db::name('Customer')->where($whereCustomer)
		->where(function ($query) use($whereCustomerOr) {
				$query->whereOr($whereCustomerOr);
			})
		->count();
		$total[] = array(
			'name' => '客户总数',
			'num' => $customerCount,
		);
		
		$whereContract = array();
		$whereContractOr = array();		
		$whereContract[] = ['delete_time', '=', 0];
		$whereContractOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
		$whereContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
		$whereContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
		$whereContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
		if(!empty($dids)){
			$whereContractOr[] =['did', 'in', $dids];
		}
		
		$contractCount = Db::name('Contract')->where($whereContract)
		->where(function ($query) use($whereContractOr) {
				$query->whereOr($whereContractOr);
			})
		->count();
		$total[] = array(
			'name' => '销售合同',
			'num' => $contractCount,
		);
		
		$purchaseCount = Db::name('Purchase')->where($whereContract)
		->where(function ($query) use($whereContractOr) {
				$query->whereOr($whereContractOr);
			})
		->count();
		$total[] = array(
			'name' => '采购合同',
			'num' => $purchaseCount,
		);
		
		$project_ids = Db::name('ProjectUser')->where(['uid' => $uid, 'delete_time' => 0])->column('project_id');
		$whereProject = [];
		$whereProject[] = ['delete_time', '=', 0];
		$whereProject[] = ['id', 'in', $project_ids];			
		$projectCount = Db::name('Project')->where($whereProject)->count();
		
		$whereOr = array();
		$map1 = [];
		$map2 = [];
		$map3 = [];
		$map4 = [];
		$map1[] = ['admin_id', '=', $uid];
		$map2[] = ['director_uid', '=', $uid];
		$map3[] = ['', 'exp', Db::raw("FIND_IN_SET({$uid},assist_admin_ids)")];
		$map4[] = ['project_id', 'in', $project_ids];
		
		$whereOr =[$map1,$map2,$map3];
		$taskCount = Db::name('ProjectTask')
			->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
			->where([['delete_time', '=', 0]])->count();
		
		$total[] = array(
			'name' => '项目总数',
			'num' => $projectCount,
		);
		$total[] = array(
			'name' => '任务总数',
			'num' => $taskCount,
		);
		
		$todue=[];
		$delay_day = valueAuth('contract_admin','conf_10');
		if(empty($delay_day)){
			$delay_day = 30;
		}
		$delay_time = time()+$delay_day*60*60*24;
		$mapContract = array();
		$mapContractOr = array();		
		$mapContract[] = ['delete_time', '=', 0];
		$mapContract[] = ['check_status', '=', 2];
		$mapContract[] = ['end_time','<',$delay_time];
		$mapContractOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
		$mapContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
		if(!empty($dids)){
			$mapContractOr[] =['did', 'in', $dids];
		}
		
        $todue[] = array(
            'name' => '快到期的销售合同',
            'num' =>  Db::name('Contract')->where($mapContract)
						->where(function ($query) use($mapContractOr) {
							$query->whereOr($mapContractOr);
						})->count(),
            'id' => 319,
            'url' => '/contract/contract/datalist',
        );
        $todue[] = array(
            'name' => '快到期的采购合同',
            'num' =>  Db::name('Purchase')->where($mapContract)
						->where(function ($query) use($mapContractOr) {
							$query->whereOr($mapContractOr);
						})->count(),
            'id' => 323,
            'url' => '/contract/purchase/datalist',
        );
		$delay_day_b = valueAuth('project_admin','conf_10');
		if(empty($delay_day_b)){
			$delay_day_b = 3;
		}
		$delay_day_b_time = time()+$delay_day_b*60*60*24;
		$todue[] = array(
            'name' => '快到期的项目',
            'num' =>  Db::name('Project')->where($whereProject)->where([['status','<',3],['end_time','<',$delay_day_b_time]])->count(),
            'id' => 343,
            'url' => '/project/index/datalist',
        );
        $todue[] = array(
            'name' => '快到期的任务',
            'num' =>  Db::name('ProjectTask')
			->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
			->where([['delete_time', '=', 0],['status','<',3],['end_time','<',$delay_day_b_time]])->count(),
            'id' => 348,
            'url' => '/project/task/datalist',
        );
		
		$position_id = Db::name('Admin')->where('id',$uid)->value('position_id');
		$adminGroup = Db::name('PositionGroup')->where(['pid' => $position_id])->column('group_id');
		$adminLayout = Db::name('AdminGroup')->where('id', 'in', $adminGroup)->column('layouts');
		$adminLayouts = [];
		foreach ($adminLayout as $k => $v) {
			$v = explode(',', $v);
			$adminLayouts = array_merge($adminLayouts, $v);
		}
		$layouts = get_config('layout');
		$layout_selected = [];
		foreach ($layouts as $key =>$vo) {
			if (!empty($adminLayouts) and in_array($vo['id'], $adminLayouts)) {
				$layout_selected[] = $vo;
			}
		}
		View::assign('layout_selected',$layout_selected);
        View::assign('total', $total);
        View::assign('handle', $handle);
        View::assign('todue', $todue);
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
                    $item->entry_time = empty($item->entry_time) ? '-' : date('Y-m-d', $item->entry_time);
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
}
