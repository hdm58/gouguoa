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

namespace app\customer\controller;

use app\base\BaseController;
use app\customer\model\Customer as CustomerModel;
use app\customer\validate\CustomerValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Clue extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CustomerModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
		$uid = $this->uid;
		$auth = isAuth($uid,'customer_admin','conf_1');
        if (request()->isAjax()) {
			$where=[];
			$whereOr = [];
			$dids_son = get_leader_departments($uid);
			$tab = isset($param['tab']) ? $param['tab'] : 0;
            if (!empty($param['keywords'])) {
                $where[] = ['name|address|clue_name|clue_moble|content|remark', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['follow_time'])) {
				$follow_time =explode('~', $param['follow_time']);
				$where[] = ['follow_time', 'between',[strtotime(urldecode($follow_time[0])),strtotime(urldecode($follow_time[1].' 23:59:59'))]];
            }
			if (!empty($param['next_time'])) {
                $next_time =explode('~', $param['next_time']);
				$where[] = ['next_time', 'between',[strtotime(urldecode($next_time[0])),strtotime(urldecode($next_time[1].' 23:59:59'))]];
            }
			$where[]=['is_clue','=',1];
			$where[]=['delete_time','=',0];
			
			//线索列表(我的线索+下属线索)
			if($tab == 0){
				if (!empty($param['uid'])) {
					$where[] = ['belong_uid', '=', $param['uid']];
				}
				else{
					if($auth == 0){
						$whereOr[] = ['belong_uid', '=', $uid];
						$whereOr[] = ['belong_did','in',$dids_son];
					}
					else{
						$where[] = ['belong_uid', '>', 0];
					}
				}
			}
			//我的线索
			if($tab == 1){
				$where[] = ['belong_uid', '=', $uid];
			}
			//下属线索
			if($tab == 2){
				$where[] = ['belong_did','in',$dids_son];
				$where[] = ['belong_uid', '<>', $uid];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', $auth);
			View::assign('leader', isLeader($uid));
            return view();
        }
    }
	
	/**
    * 公海数据列表
    */
    public function sealist()
    {
		$param = get_params();
		$uid = $this->uid;
        if (request()->isAjax()) {
			$where=[];
			$whereOr = [];
			$tab = isset($param['tab']) ? $param['tab'] : 0;
            if (!empty($param['keywords'])) {
                $where[] = ['name|clue_name|clue_mobile|address|content|remark', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['follow_time'])) {
				$follow_time =explode('~', $param['follow_time']);
				$where[] = ['follow_time', 'between',[strtotime(urldecode($follow_time[0])),strtotime(urldecode($follow_time[1].' 23:59:59'))]];
            }
			$where[]=['is_clue','=',1];
			$where[]=['delete_time','=',0];
			$where[] = ['belong_uid', '=', 0];
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', isAuth($uid,'customer_admin','conf_1'));
			View::assign('leader', isLeader($uid));
            return view();
        }
    }
    /**
    * 添加/编辑
    */
    public function add()
    {
		$param = get_params();	
        if (request()->isAjax()) {	
			try {
				validate(CustomerValidate::class)->scene($param['scene'])->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}		
            if (!empty($param['id']) && $param['id'] > 0) {
				$param['edit_id'] = $this->uid;
				$this->model->edit($param);
            } else {
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				if(is_mobile()){
					return view('qiye@/clue/add');
				}
				return view('edit');
			}
			if($this->uid>1){
				View::assign('userinfo', get_admin($this->uid));
			}
			if(is_mobile()){
				return view('qiye@/clue/add');
			}
			return view();
		}
    }
	
    /**
    * 查看
    */
    public function view($id)
    {
		$detail = $this->model->getById($id);
		$detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
		$detail['source'] = Db::name('CustomerSource')->where(['id' => $detail['source_id']])->value('title');
		$detail['follow_time'] = to_date($detail['follow_time']);
		$detail['next_time'] = to_date($detail['next_time']);
		View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/clue/view');
		}
		return view();
    }
	
   /**
    * 删除
    */
    public function del()
    {
		$param = get_params();
		if (request()->isDelete()) {
			$id = get_params("id");			
			$idArray = explode(',', strval($id));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'discard_time' => time()
				];
			}
			foreach ($list as $key => $v) {
				if (Db::name('Customer')->update($v) !== false) {
					add_log('delete', $param['id']);
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
    }   
	
	
	//移入公海
	public function to_sea()
    {
		if (request()->isAjax()) {
			$id = get_params("id");
			$idArray = explode(',', strval($id));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'belong_uid' => 0,
					'belong_did' => 0,
					'belong_time' => 0,
					'distribute_time' => 0,
					'share_ids' => '',
					'is_lock'=>0
				];
			}
			foreach ($list as $key => $v) {
				if (Db::name('Customer')->update($v) !== false) {
					add_log('tosea', $v['id']);
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	//获取线索
	public function to_get()
    {
		if (request()->isAjax()) {
			$max_num_total = Db::name('DataAuth')->where('name','customer_admin')->value('conf_4');			
			$count_total = Db::name('Customer')->where([['belong_uid','=',$this->uid],['is_clue','=',1]])->count();
			$id = get_params("id");
			$idArray = explode(',', strval($id));
			if($count_total+count($idArray) > $max_num_total){
				return to_assign(1, "您领取的线索数已到达上限，请把部分自已的线索移到线索池里");
			}
			foreach ($idArray as $key => $val) {
				$data = [
					'id' => $val,
					'belong_uid' => $this->uid,
					'belong_did' => $this->did,
					'belong_time' => time()
				];
				if (Db::name('Customer')->update($data) !== false) {
					add_log('get', $data['id'],[],'线索');
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	//线索移入废弃池
    public function to_trash()
    {
		if (request()->isAjax()) {
			$params = get_params();	
			$id = get_params("id");			
			$idArray = explode(',', strval($id));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'delete_time' => time()
				];
			}
			foreach ($list as $key => $v) {
				if (Db::name('Customer')->update($v) !== false) {
					add_log('totrash', $params['id'],[],'线索');
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//还原线索
    public function to_revert()
    {
		if (request()->isAjax()) {
			$params = get_params();		
			$id = get_params("id");			
			$idArray = explode(',', strval($id));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'delete_time' => 0
				];
			}
			foreach ($list as $key => $v) {
				if (Db::name('Customer')->update($v) !== false) {
					add_log('recovery', $params['id'],[],'线索');
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//分配线索
	public function to_allot()
    {
		if (request()->isAjax()) {
			$params = get_params();
			//是否是线索管理员
			$auth = isAuth($this->uid,'customer_admin','conf_1');
			if($auth==0){
				return to_assign(1, "只有客户管理员才有权限操作");
			}
			$id = $params["id"];
			$idArray = explode(',', strval($id));
			foreach ($idArray as $key => $val) {
				$data = [
					'id' => $val,
					'belong_uid' => $params['uid'],
					'belong_did' => $params['did'],
					'belong_time' => time(),
					'distribute_time' => time()
				];
				if (Db::name('Customer')->update($data) !== false) {
					add_log('allot', $data['id'],[],'线索');
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	//转移线索
	public function to_transfer()
    {
		if (request()->isAjax()) {
			$params = get_params();
			$id = $params["id"];
			$idArray = explode(',', strval($id));
			foreach ($idArray as $key => $val) {
				$data = [
					'id' => $val,
					'belong_uid' => $params['uid'],
					'belong_did' => $params['did'],
					'belong_time' =>time(),
					'distribute_time' => time()
				];
				if (Db::name('Customer')->update($data) !== false) {
					add_log('transfer', $data['id'],[],'线索');
				}
			}
			return to_assign(0, '操作成功');
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	

	//线索转客户
	public function to_customer()
    {
		$param = get_params();	
        if (request()->isAjax()) {	
			$detail= Db::name('Customer')->where(['id' => $param['id']])->find();
			if($detail['belong_uid']!=$this->uid){
				return to_assign(1, '你不是该线索的所属人，不支持操作');
			}
			$param['customer_time'] = time();
			$param['admin_id'] = $this->uid;
			$param['edit_id'] = $this->uid;
			$this->model->to_customer($param);	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail = $this->model->getById($id);
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/customer/to_customer');
			}
			return view();
		}
    }

}
