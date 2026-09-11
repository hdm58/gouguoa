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

namespace app\performance\controller;

use app\base\BaseController;
use app\performance\model\PerformanceUsers as PerformanceUsersModel;
use app\performance\validate\UsersValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Users extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PerformanceUsersModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['a.delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|u.name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['did'])) {
                $where[] = ['d.id', '=', $param['did']];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
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
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(UsersValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(UsersValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$template_id = isset($param['template_id']) ? $param['template_id'] : 0;
			$detail=[];
			if ($id>0) {
				$detail = $this->model->getById($id);
			}
			if($template_id>0){
				$detail['template_id']=$template_id;
				$detail['template_name']=Db::name('PerformanceTemplates')->where('id',$template_id)->value('title');
			}
			View::assign('detail', $detail);
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
			$template = Db::name('PerformanceTemplates')->where('id',$detail['template_id'])->find();
			$detail['kpi_score'] = $template['kpi_score'];
			$detail['action_score'] = $template['action_score'];
			$detail['kpi_array'] = unserialize($template['kpi_serialize']);
			$detail['action_array'] = unserialize($template['action_serialize']);
			$detail['event_array'] = unserialize($template['event_serialize']);
			View::assign('detail', $detail);
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
		$param = get_params();
		if (request()->isDelete()) {
			$this->model->delById(param['id']);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
