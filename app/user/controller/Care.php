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
use app\user\model\Care as CareModel;
use app\user\validate\CareValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Care extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CareModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|thing|remark', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
			if (!empty($param['care_cate'])) {
                $where[] = ['care_cate', '=', $param['care_cate']];
            }
			if (!empty($param['uid'])) {
                $where[] = ['uid', '=', $param['uid']];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['care_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
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
			$param['care_time'] = isset($param['care_time']) ? strtotime($param['care_time']) : 0;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CareValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(CareValidate::class)->scene('add')->check($param);
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
				$detail['user_name'] = Db::name('Admin')->where('id',$detail['uid'])->value('name');
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
			$detail['cate'] = Db::name('CareCate')->where('id',$detail['care_cate'])->value('title');
			$detail['user_name'] = Db::name('Admin')->where('id',$detail['uid'])->value('name');
			$detail['admin_name'] = Db::name('Admin')->where('id',$detail['admin_id'])->value('name');
			View::assign('detail', $detail);
			return view();
		}
		else{
			throw new \think\exception\HttpException(404, '找不到页面');
		}
    }
	
   /**
    * 删除
    */
    public function del()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
        $this->model->delById($id);
    }  

}
