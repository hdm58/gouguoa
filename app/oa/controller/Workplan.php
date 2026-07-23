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

namespace app\oa\controller;

use app\base\BaseController;
use app\oa\model\WorkPlan as WorkPlanModel;
use app\oa\validate\WorkplanValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Workplan extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new WorkPlanModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid = $this->uid;
			$did = $this->did;
			$pid = $this->pid;
			$tab = isset($param['tab']) ? $param['tab'] : 0;
            $where = [];
            $whereOr = [];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title|content', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=',$param['types']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=',$param['cate_id']];
            }
			if (!empty($param['end_time'])) {
				$end_time =explode('~', $param['end_time']);
				$where[] = ['end_time', 'between',[strtotime(urldecode($end_time[0])),strtotime(urldecode($end_time[1].' 23:59:59'))]];
            }
			
			$map1=[
				['admin_id','=',$uid]
			];
			$map2=[
				['send_time', '>',0],
				['types', '=',1],
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")]
			];
			$map3=[
				['send_time', '>',0],
				['types', '=',2],
				['', 'exp', Db::raw("FIND_IN_SET('{$did}',dids)")]
			];
			$map4=[
				['send_time', '>',0],
				['types', '=',3],
				['', 'exp', Db::raw("FIND_IN_SET('{$pid}',pids)")]
			];
			$map5=[
				['send_time', '>',0],
				['types', '=',4]
			];
			
			if($tab == 0){
				$whereOr =[$map1,$map2,$map3,$map4,$map5];
			}
			if($tab == 1){
				$where[] = ['admin_id', '=', $uid];
			}
			if($tab == 2){
				$whereOr =[$map2,$map3,$map4,$map5];
			}
			if($tab == 3){
				$where[] = ['send_time', '>',0];
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',copy_uids)")];
			}
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
			View::assign('cate', get_base_type_data('BasicAdm',2));
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
			if($param['is_draft'] == 2){
				$param['send_time'] = time();
			}
			if(!empty($param['start_time'])){
				$param['start_time'] = strtotime($param['start_time']);
			}
			if(!empty($param['end_time'])){
				$param['end_time'] = strtotime($param['end_time']);
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(WorkplanValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(WorkplanValidate::class)->scene('add')->check($param);
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
			View::assign('cate', get_base_type_data('BasicAdm',2));
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
			View::assign('detail', $detail);
			if(is_mobile()){
				return view('qiye@/index/workplan_view');
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
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   

}
