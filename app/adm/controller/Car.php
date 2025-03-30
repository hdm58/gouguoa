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
use app\adm\model\Car as CarModel;
use app\adm\validate\CarValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Car extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CarModel();
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
			if (isset($param['insure_time'])) {
                $param['insure_time'] = strtotime($param['insure_time']);
            }
			if (isset($param['review_time'])) {
                $param['review_time'] = strtotime($param['review_time']);
            }	
			if (isset($param['buy_time'])) {
                $param['buy_time'] = strtotime($param['buy_time']);
            }			
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CarValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(CarValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				if(!empty($detail['file_ids'])){
					$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['file_array'] = $file_array;
				}
				if($detail['driver']>0){
					$detail['driver_name'] = Db::name('Admin')->where('id','=',$detail['driver'])->value('name');
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
			if($detail['driver']>0){
				$detail['driver_name'] = Db::name('Admin')->where('id','=',$detail['driver'])->value('name');
			}
			$latestMileage = Db::name('CarMileage')->where(['car_id'=>$detail['id'],'delete_time'=>0])->max('mileage');
			if(empty($latestMileage)){
				$latestMileage = $detail['mileage'];
			}
			$detail['latestMileage'] = $latestMileage;
			View::assign('detail', $detail);
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
	
	//维修记录列表
    public function repair_list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['c.title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['cr.repair_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
            }
			$where[] = ['cr.types','=',1];
			$where[] = ['cr.delete_time','=',0];
            $model = new Car();
			$list = $this->model->repairlist($where, $param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
    //维修记录添加&编辑
    public function repair_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['repair_time'])) {
                $param['repair_time'] = strtotime($param['repair_time']);
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
				$res = Db::name('CarRepair')->strict(false)->field(true)->update($param);
				if($res){
					add_log('edit', $param['id'], $param);
					return to_assign();
				}
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('CarRepair')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $cid = isset($param['cid']) ? $param['cid'] : 0;
            if ($id > 0) {
                $detail = Db::name('CarRepair')->where(['id' => $id])->find();
                $detail['handled_name'] = Db::name('Admin')->where('id',$detail['handled'])->value('name');
                $detail['car'] = Db::name('Car')->where('id',$detail['car_id'])->value('title');
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
                View::assign('detail', $detail);
				return view('repair_edit');
            }
			if($cid>0){
				View::assign('car', $this->model->getById($cid));
			}
            View::assign('cid', $cid);
            View::assign('id', $id);
            return view();
        }
    }
	
    //维修记录查看
    public function repair_view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
		$detail = Db::name('CarRepair')->where(['id' => $id])->find();
		$detail['handled_name'] = Db::name('Admin')->where('id',$detail['handled'])->value('name');
        $detail['car'] = Db::name('Car')->where('id',$detail['car_id'])->value('title');
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		View::assign('detail', $detail);
        return view();
    }
	
     //维修记录删除
    public function repair_del()
    {
		$param = get_params();
        $res = Db::name('CarRepair')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if ($res) {
			add_log('delete', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }  
	
	
	//保养记录列表
    public function protect_list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['c.title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['cr.repair_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
            }
			$where[] = ['cr.types','=',2];
			$where[] = ['cr.delete_time','=',0];
			$list = $this->model->repairlist($where, $param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
    //保养记录添加&编辑
    public function protect_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['repair_time'])) {
                $param['repair_time'] = strtotime($param['repair_time']);
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
				$res = Db::name('CarRepair')->strict(false)->field(true)->update($param);
				if($res){
					add_log('edit', $param['id'], $param);
					return to_assign();
				}
            } else {
                $param['create_time'] = time();
                $param['types'] = 2;
                $insertId = Db::name('CarRepair')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $cid = isset($param['cid']) ? $param['cid'] : 0;
            if ($id > 0) {
                $detail = Db::name('CarRepair')->where(['id' => $id])->find();
                $detail['handled_name'] = Db::name('Admin')->where('id',$detail['handled'])->value('name');
                $detail['car'] = Db::name('Car')->where('id',$detail['car_id'])->value('title');
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
                View::assign('detail', $detail);
				return view('protect_edit');
            }
			if($cid>0){
				View::assign('car', $this->model->getById($cid));
			}
            View::assign('cid', $cid);
            View::assign('id', $id);
            return view();
        }
    }
	
    //保养记录查看
    public function protect_view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
		$detail = Db::name('CarRepair')->where(['id' => $id])->find();
		$detail['handled_name'] = Db::name('Admin')->where('id',$detail['handled'])->value('name');
        $detail['car'] = Db::name('Car')->where('id',$detail['car_id'])->value('title');
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		View::assign('detail', $detail);
        return view();
    }
	
     //保养记录删除
    public function protect_del()
    {
		$param = get_params();
        $res = Db::name('CarRepair')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if ($res) {
			add_log('delete', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }  
	
	//费用记录列表
    public function fee_list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['cf.title|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['cf.fee_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
            }
			if (!empty($param['types'])) {
                $where[] = ['cf.types','=',$param['types']];
            }
			$where[] = ['cf.delete_time','=',0];
			$list = $this->model->feelist($where, $param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
    //费用记录添加&编辑
    public function fee_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['fee_time'])) {
                $param['fee_time'] = strtotime($param['fee_time']);
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
				$res = Db::name('CarFee')->strict(false)->field(true)->update($param);
				if($res){
					add_log('edit', $param['id'], $param);
					return to_assign();
				}
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('CarFee')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $cid = isset($param['cid']) ? $param['cid'] : 0;
            if ($id > 0) {
                $detail = Db::name('CarFee')->where(['id' => $id])->find();
                $detail['handled_name'] = Db::name('Admin')->where('id',$detail['handled'])->value('name');
                $detail['car'] = Db::name('Car')->where('id',$detail['car_id'])->value('title');
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
                View::assign('detail', $detail);
				return view('fee_edit');
            }
			if($cid>0){
				View::assign('car', $this->model->getById($cid));
			}
            View::assign('cid', $cid);
            View::assign('id', $id);
            return view();
        }
    }
	
    //费用记录查看
    public function fee_view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
		$detail = Db::name('CarFee')->where(['id' => $id])->find();
		$detail['handled_name'] = Db::name('Admin')->where('id',$detail['handled'])->value('name');
        $detail['car'] = Db::name('Car')->where('id',$detail['car_id'])->value('title');
        $detail['types_str'] = Db::name('basicAdm')->where('id',$detail['types'])->value('title');
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		View::assign('detail', $detail);
        return view();
    }
	
    //费用记录删除
    public function fee_del()
    {
		$param = get_params();
        $res = Db::name('CarFee')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if ($res) {
			add_log('delete', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }  
	
	//获取里程数记录
	public function mileage_list()
    {
        $param = get_params();
		$where = array();
		$where[] = ['car_id', '=', $param['car_id']];
		$where[] = ['delete_time', '=', 0];
		$this->model->mileagelist($where, $param);
        table_assign(0, '', $list);
    }
	
	//增加里程数记录
	public function mileage_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['mileage_time'])) {
                $param['mileage_time'] = strtotime($param['mileage_time']);
            }
			$latestMileage = Db::name('CarMileage')->where([['id','<>',$param['id']],['delete_time','=',0]])->order('mileage_time', 'desc')->value('mileage');
			$mileage = Db::name('Car')->where('id',$param['car_id'])->value('mileage');
			if(empty($latestMileage)){
				$latestMileage = $mileage;
			}
			if($param['mileage'] < $latestMileage){
				 return to_assign(1,'新增的里程数，不能小于现有里程数');
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('CarMileage')->strict(false)->field(true)->update($param);
                return to_assign();
            } else {
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $insertId = Db::name('CarMileage')->strict(false)->field(true)->insertGetId($param);
                return to_assign();
            }
        }
    }
	
    //里程数记录删除
    public function mileage_del()
    {
		$param = get_params();
        $res = Db::name('CarMileage')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if ($res) {
			return to_assign();
		}
		else{
			return to_assign(1, '操作失败');
		}
    }  
}
