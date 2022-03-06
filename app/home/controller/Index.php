<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $admin_id = $this->uid;
            //发票待审核统计
            $invoice_map_check[] = ['check_status','<',2];
            $invoice_map_check[] = ['', 'exp', Db::raw("FIND_IN_SET('{$admin_id}',check_admin_ids)")];
            $invoice_map_check[] = ['status','=',1];
            $invoice_count_check = Db::name('Invoice')->where($invoice_map_check)->count();
            $statistics['invoice_html_check'] = '<a data-id="129" data-title="待审核发票" data-src="finance/invoice/list" class="site-menu-active"> 您有<font style="color:#FF0000">'.$invoice_count_check.'</font>条发票申请待审核</a>';          
            if($invoice_count_check==0){
                $statistics['invoice_html_check'] = '';
            }

            //发票待开具统计
            $invoice_map_open[] = ['open_time','=',0];
            $invoice_map_open[] = ['open_admin_id','=',$admin_id];
            $invoice_map_open[] = ['status','=',1];
            $invoice_count_open = Db::name('Invoice')->where($invoice_map_open)->count();
            $statistics['invoice_html_open'] = '<a data-id="130" data-title="发票开具" data-src="finance/invoice/checkedlist" class="site-menu-active"> 您有<font style="color:#FF0000">'.$invoice_count_open.'</font>条发票待开具</a>';       
            if($invoice_count_open==0){
                $statistics['invoice_html_open'] = '';
            }

            //待审核的报销统计
            $expense_map_check[] = ['check_status','<',2];
			$expense_map_check[] = ['', 'exp', Db::raw("FIND_IN_SET('{$admin_id}',check_admin_ids)")];
            $expense_map_check[] = ['status','=',1];
            $expense_count_check =  Db::name('Expense')->where($expense_map_check)->count();
            $statistics['expense_html_check'] = '<a data-id="120" data-title="待我审批的报销" data-src="finance/expense/list" class="site-menu-active"> 您有<font style="color:#FF0000">'.$expense_count_check.'</font>条报销单待审核</a>';
           // $statistics['expense_count_check'] = $expense_count_check;            
            if($expense_count_check==0){
                $statistics['expense_html_check'] = '';
            }

            //未读消息统计
            $msg_map[] = ['to_uid','=',$admin_id];
            $msg_map[] = ['read_time','=',0];
            $msg_map[] = ['status','=',1];
            $msg_count = Db::name('Message')->where($msg_map)->count();
            $statistics['msg_html'] = '<a data-id="27" data-title="收件箱" data-src="/home/message/inbox.html" class="site-menu-active"> 您有<font style="color:#FF0000">'.$msg_count.'</font>条未读消息</a>';
            $statistics['msg_num'] = $msg_count;            
            if($msg_count==0){
                $statistics['msg_html'] = '';
            }

            foreach ($statistics as $key => $value) {
                if (!$value ) unset($statistics[$key]); 
            }
            return to_assign(0,'ok',$statistics);
        }
        else{
			$admin = get_login_admin();
			if (get_cache('menu' . $admin['id'])) {
				$list = get_cache('menu' . $admin['id']);
			} else {
				$adminGroup = Db::name('PositionGroup')->where(['pid' => $admin['position_id']])->column('group_id');
				$adminMenu = Db::name('AdminGroup')->where('id', 'in', $adminGroup)->column('rules');
				$adminMenus = [];
				foreach ($adminMenu as $k => $v) {
					$v = explode(',', $v);
					$adminMenus = array_merge($adminMenus, $v);
				}
				$menu = Db::name('AdminRule')->where(['menu' => 1,'status'=>1])->where('id', 'in', $adminMenus)->order('sort asc')->select()->toArray();
				$list = list_to_tree($menu);
				\think\facade\Cache::tag('adminMenu')->set('menu' . $admin['id'], $list);
			}
            View::assign('menu', $list);
            return View();
        }
    }

    public function main()
    {
        $install = false;
        if (file_exists(CMS_ROOT . 'app/install')) {
            $install = true;
        }
        $adminCount = Db::name('Admin')->where('status', '1')->count();
        $articleCount = Db::name('Article')->where('status', '1')->count();
        $approveCount = Db::name('Approve')->count();
        $expenseCount = Db::name('Expense')->count();
        $invoiceCount = Db::name('Invoice')->count();
        View::assign('install', $install);
        View::assign('adminCount', $adminCount);
        View::assign('articleCount', $articleCount);
        View::assign('approveCount', $approveCount);
        View::assign('expenseCount', $expenseCount);
        View::assign('invoiceCount', $invoiceCount);
        return View();
    }

    public function errorShow()
    {
        echo '错误';
    }

}
