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

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\model\OfficialDocs;
use app\adm\validate\CarValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Official extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new OfficialDocs();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr = [];
			$map1 = [];
			$map2 = [];
			$map3 = [];
			$map4 = [];
			$uid = $this->uid;
			$tab = isset($param['tab']) ? $param['tab'] : 0;
			
			//条件1
			$map1[] = ['admin_id','=',$uid];

			//条件2
			$map2[] = ['check_status','=',2];
			$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',send_uids)")];
				
			//条件3	
			$map3[] = ['check_status','=',2];
			$map3[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',copy_uids)")];
			
			//条件4
			$map4[] = ['check_status','=',2];
			$map4[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_uids)")];
			
			
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['secrets'])) {
                $where[] = ['secrets', '=', $param['secrets']];
            }
			if (!empty($param['urgency'])) {
                $where[] = ['urgency', '=', $param['urgency']];
            }
			if($tab == 0){
				$whereOr = [$map1,$map2];
			}
			if($tab == 1){
				$where[] = ['admin_id','=',$uid];
			}
			if($tab == 2){
				$where[] = ['check_status','=',2];
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',send_uids)")];
			}
			if($tab == 3){
				$where[] = ['check_status','=',2];
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',copy_uids)")];
			}
			if($tab == 4){
				$where[] = ['check_status','=',2];
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_uids)")];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('secrets', $this->model::$Secrets);
			View::assign('urgency', $this->model::$Urgency);
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
			if (isset($param['draft_time'])) {
                $param['draft_time'] = strtotime($param['draft_time']);
            }	
            if (!empty($param['id']) && $param['id'] > 0) {
				$this->model->edit($param);
            } else {
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if(is_mobile()){
				return view('qiye@/index/405',['msg' => '由于公文包含了富文本编辑，手机端不方便操作，请到PC端新增或编辑']);
			}
			if ($id>0) {
				$detail = $this->model->getById($id);
				if(!empty($detail['file_ids'])){
					$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['file_array'] = $file_array;
				}
                View::assign('detail', $detail);
				return view('edit');
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
			if(!empty($detail['file_ids'])){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			View::assign('detail', $detail);
			View::assign('auth_office', isAuth($this->uid,'office_admin','conf_1'));			
			if(is_mobile()){
				return view('qiye@/index/official_view');
			}
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['warning'=>'找不到页面']);
		}
    }
	
   /**
    * 删除
    */
    public function del()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }  
	
	//待审公文列表
    public function pending()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$uid = $this->uid;
			$where=[];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			$where[] = ['check_status', '=', 1];
			$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_uids)")];
			$list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//已审公文列表
    public function reviewed()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$uid = $this->uid;
			$where=[];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_history_uids)")];
			$list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
}
