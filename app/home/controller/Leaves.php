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

use app\api\BaseController;
use app\home\model\Leaves as LeavesModel;
use app\home\validate\LeavesValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Leaves extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new LeavesModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($where, $param);
            return table_assign(0, '', $list);
        }
        else{
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
			if(!empty($param['start_date'])){
				$param['start_date'] = strtotime($param['start_date']);
			}
			if(!empty($param['end_date'])){
				$param['end_date'] = strtotime($param['end_date']);
				if($param['end_date']<$param['start_date']){
					return to_assign(1, '结束时间不能小于开始时间');
				}
			}
            if (!empty($param['id']) && $param['id'] > 0) {
				$this->model->edit($param);
            } else {
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
			if(is_mobile()){
				return view('qiye@/approve/add_qingjia');
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
			$detail['start_span_name'] = '上午';
			$detail['end_span_name'] = '上午';
			if($detail['start_span'] == 2){
				$detail['start_span_name'] = '下午';
			}
			if($detail['end_span'] == 2){
				$detail['end_span_name'] = '下午';
			}
			$detail['types_name'] = leaves_types_name($detail['types']);
			View::assign('create_user', get_admin($detail['admin_id']));
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/approve/view_qingjia');
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
    public function del($id)
    {
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
