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
use app\user\model\CareCate as CareCateModel;
use app\user\validate\CareCateValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Carecate extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CareCateModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
            $list = $this->model->select();
            return to_assign(0, '', $list);
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
                    validate(CareCateValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(CareCateValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getCateById($id);
			}
			return view();
		}
    }
    /**
    * 设置
    */
    public function set()
    {
		$param = get_params();
        $res = $this->model->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }
   

}
