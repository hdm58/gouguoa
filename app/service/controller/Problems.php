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
use app\service\model\Problems as ProblemsModel;
use app\service\validate\ProblemsValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Problems extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ProblemsModel();
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
			if (!empty($param['emergent'])) {
                $where[] = ['emergent', '=', $param['emergent']];
            }
			if (!empty($param['priority'])) {
                $where[] = ['priority', '=', $param['priority']];
            }
			if (!empty($param['director_id'])) {
                $where[] = ['director_id', '=', $param['director_id']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
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
			if (isset($param['problem_time'])) {
				if($param['problem_time']!=''){
					$param['problem_time'] = strtotime(urldecode($param['problem_time']));
				}
				else{
					$param['problem_time']=0;
				}
			}
			if($param['status'] == 4){
				$param['finish_time']=time();				
			}
			if($param['status'] == 5){
				$param['over_time']=time();				
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ProblemsValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(ProblemsValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
			}
			View::assign('id', $id);
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
			$detail['cate'] = Db::name('ProblemsCate')->where('id',$detail['cate_id'])->value('title');
			$detail['status_name'] = status_name($detail['status']);
			$detail['priority_name'] = priority_name($detail['priority']);
			$detail['emergent_name'] = emergent_name($detail['emergent']);
			$solutions = Db::name('Solutions')->where(['problems_id'=>$detail['id'],'types'=>1,'delete_time'=>0])->find();
			$methods = Db::name('Solutions')->where(['problems_id'=>$detail['id'],'types'=>2,'delete_time'=>0])->find();
			
			View::assign('solutions', $solutions);
			View::assign('methods', $methods);
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
