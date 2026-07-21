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
use app\adm\model\Events as EventsModel;
use app\adm\validate\EventsCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Events extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new EventsModel();
    }
	
	public function datalist()
    {
        $list = Db::name('Events')->where('delete_time',0)->order('event_time desc')->select()->toArray();
		View::assign('list', $list);
        return view();
    }
    //新建编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['event_time'])) {
                $param['event_time'] = strtotime($param['event_time']);
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(EventsCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $this->model->edit($param);
            } else {
                try {
                    validate(EventsCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $this->model->add($param);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if($id>0){
                $detail = $this->model->getById($id);
                View::assign('detail', $detail);
            }
            return view();
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
}
