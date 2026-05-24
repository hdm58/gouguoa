<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 勾股OA http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
 
declare (strict_types = 1);

namespace app\service\controller;

use app\base\BaseController;
use app\service\model\Solutions as SolutionsModel;
use app\service\validate\SolutionsValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Solutions extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new SolutionsModel();
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
			if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
			if (!empty($param['cate_id'])) {
				$ids = get_cate_son('SolutionsCate',$param['cate_id'],1);
                $where[] = ['cate_id', 'in', $ids];
            }
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($where, $param);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('auth', isAuth($this->uid,'service_admin','conf_1'));	
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
                    validate(SolutionsValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['update_id'] = $this->uid;
				$this->model->edit($param);
            } else {
                try {
                    validate(SolutionsValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
				$param['update_id'] = $this->uid;
				$param['update_time'] = time();
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$types = isset($param['types']) ? $param['types'] : 1;
			$problems_id = isset($param['problems_id']) ? $param['problems_id'] : 0;
			View::assign('id', $id);
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				View::assign('types', $detail['types']);
				View::assign('problems_id', $detail['problems_id']);
				return view();
			}
			View::assign('problems_id', $problems_id);
			View::assign('types', $types);
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
			$detail['cate'] = Db::name('SolutionsCate')->where('id',$detail['cate_id'])->value('title');
			$detail['status_name'] = solutions_status_name($detail['status']);
			if($detail['check_id']>0){
				$detail['check_name'] = Db::name('Admin')->where('id',$detail['check_id'])->value('name');
			}
			if($detail['check_time'] > 0){
				$detail['check_time'] = to_date($detail['check_time']);
			}
			else{			
				$detail['check_time'] = '';
			}
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
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }     

}
