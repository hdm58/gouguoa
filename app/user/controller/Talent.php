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

namespace app\user\controller;

use app\base\BaseController;
use app\user\model\Talent as TalentModel;
use app\user\validate\TalentValidate;
use think\exception\ValidateException;
use Overtrue\Pinyin\Pinyin;
use think\facade\Db;
use think\facade\View;

class Talent extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new TalentModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$param = get_params();
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			$uid = $this->uid;
            $where = array();
            $whereOr = array();
			$where[]=['delete_time','=',0];
			if($tab == 0){
				//全部
				$auth = isAuth($uid,'office_admin','conf_1');
				if($auth == 0){
					$whereOr[] = ['admin_id', '=', $this->uid];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
					$dids_a = get_leader_departments($uid);	
					$dids_b = get_role_departments($uid);
					$dids = array_merge($dids_a, $dids_b);
					if(!empty($dids)){
						$whereOr[] = ['did','in',$dids];
					}
				}
			}
			if($tab == 1){
				//我创建的
				$where[] = ['admin_id', '=', $this->uid];
			}
			if($tab == 2){
				//待我审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			}
			if($tab == 3){
				//我已审核的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			}
			if($tab == 4){
				//抄送给我的
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_copy_uids)")];
			}
			if (isset($param['check_status']) && $param['check_status'] != "") {
                $where[] = ['check_status', '=', $param['check_status']];
            }
			if (!empty($param['keywords'])) {
                $where[] = ['name|mobile', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['did'])) {
				$where[] = ['to_did', '=',$param['did']];
            }
			//按时间检索
			if (!empty($param['entry_time'])) {
				$entry_time =explode('~', $param['entry_time']);
				$where[] = ['entry_time', 'between', [strtotime(urldecode($entry_time[0])),strtotime(urldecode($entry_time[1]))]];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('is_auth', isAuth($this->uid,'office_admin','conf_1'));
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
            $param['entry_time'] = empty($param['entry_time']) ? '0':strtotime($param['entry_time']);
			$count_a = Db::name('Blacklist')->where([['idcard','=',$param['idcard']],['delete_time','=',0]])->count();
			$count_b = Db::name('Blacklist')->where([['mobile','=',$param['mobile']],['delete_time','=',0]])->count();
			if($count_a>0 || $count_b>0){
				return to_assign(1, '该员工的信息已被列入黑名单，不支持申请');
			}
			$count_c = Db::name('Admin')->where([['mobile','=',$param['mobile']],['status','in',['0,1']],['delete_time','=',0]])->count();
			$count_d = Db::name('Admin')->where([['email','=',$param['email']],['status','in',['0,1']],['delete_time','=',0]])->count();
			if($count_c>0){
				return to_assign(1, '该手机号已被其他员工占用，不支持申请');
			}
			if($count_d>0){
				return to_assign(1, '该邮箱已被其他员工占用，不支持申请');
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(IndexValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(IndexValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
				$param['did'] = $this->did;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
			}
			$position = Db::name('Position')->where([['status', '>=', 0]])->order('create_time asc')->select();
			View::assign('position', $position);
			View::assign('id', $id);
			if(is_mobile()){
				return view('qiye@/approve/add_talent');
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
		if (!empty($detail)) {
			$detail['department'] = Db::name('Department')->where('id', '=', $detail['to_did'])->value('title');
			$detail['position'] = Db::name('Position')->where('id', '=', $detail['position_id'])->value('title');
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/approve/view_talent');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到页面']);
		}
    }
	
   /**
    * 删除
    */
    public function del()
    {
		 $id = get_params("id");
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   
   /**
    * 转为正式员工
    */
    public function set()
    {
		$param = get_params();
		if (request()->isAjax()) {
			$param['update_time'] = time();
			$param['entry_time'] = strtotime($param['entry_time']);
            Db::name('Talent')->strict(false)->field(true)->update($param);
			$detail = Db::name('Talent')->where('id', $param['id'])->find();
			$count_a = Db::name('Admin')->where([['status', 'in', [0,1]],['mobile','=',$detail['mobile']],['delete_time','=',0]])->count();
			if($count_a>0){
				return to_assign(1, "同样的手机号码已经存在，请检查一下是否被离职或者禁用员工占用");
			}
            $detail['nickname'] = $detail['name'];
			$char = mb_substr($detail['name'], 0, 1, 'utf-8');
			$username = Pinyin::name($char,'none')->join('');
			$detail['username'] = $this->create_name($username,0);
			$detail['did'] = $detail['to_did'];
			$detail['salt'] = set_salt(20);
			$detail['reg_pwd'] = $param['reg_pwd'];
			$detail['thumb'] = get_file($detail['thumb']);
			$detail['pwd'] = set_password($detail['reg_pwd'], $detail['salt']);
			$detail['talent_id'] = $detail['id'];
			unset($detail['id']);
            $uid = Db::name('Admin')->strict(false)->field(true)->insertGetId($detail);
			if($uid!==false){
				Db::name('Talent')->where('id',$param ['id'])->update(['status'=>2]);
				add_log('add', $uid, $param);
				return to_assign();
			}
			else {
				return to_assign(1, "操作失败");
			}
		}
		else{
			$department = set_recursion(get_department());
			$position = Db::name('Position')->where([['status', '>=', 0]])->order('create_time asc')->select();
			$detail = $this->model->getById($param['id']);
			View::assign('position', $position);
			//初始化密码
            View::assign('reg_pwd', set_salt(6));
			View::assign('department', $department);
			View::assign('detail', $detail);
			return view();
		}
    }
    //生成登录名
    public function create_name($name,$id=0,$total=0,$old='')
    {
		$count = Db::name('Admin')->where([['username','=',$name],['id','<>',$id]])->count();
		if($total==0){
			$old = $name;
		}
		$total++;
		if($count>0){
			$newname = $old.$total;
			$name = $this->create_name($newname,$id,$total,$old);
		}
		return $name;
    }
}
