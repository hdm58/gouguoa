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
use app\adm\model\Property as PropertyModel;
use app\adm\validate\PropertyCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Property extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PropertyModel();
    }
	
	public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['p.title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (isset($param['status']) && $param['status']!='') {
				$where[] = ['p.status', '=', $param['status']];
            }
			else{
               $where[] = ['p.status','>=',0];
			}
			if (!empty($param['cate_id'])) {
				$cate_id_array = get_cate_son('PropertyCate',$param['cate_id']);
                $where[] = ['p.cate_id', 'in', $cate_id_array];
            }
			if (!empty($param['brand_id'])) {
                $where[] = ['p.brand_id', '=', $param['brand_id']];
            }
			$list = $this->model->datalist($where, $param);
            return table_assign(0, '', $list);
        } else {
			View::assign('status', $this->model::$property_status);
            return view();
        }
    }
    //新建编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['buy_time'])) {
                $param['buy_time'] = strtotime($param['buy_time']);
            }
			if (isset($param['quality_time'])) {
                $param['quality_time'] = strtotime($param['quality_time']);
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(PropertyCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
				$param['update_id'] = $this->uid;
                $this->model->edit($param);
            } else {
                try {
                    validate(PropertyCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $this->model->add($param);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
			View::assign('status', $this->model::$property_status);
			View::assign('source', $this->model::$property_source);
            if($id>0){
                $detail = $this->model->getById($id);
				if(!empty($detail['user_ids'])){
					$users =  Db::name('Admin')->where('id','in',$detail['user_ids'])->column('name');
					$detail['users_name'] = implode(',',$users);
				}
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
                View::assign('detail', $detail);
				return view('edit');
            }
            return view();
        }
    }
	
    //查看
    public function view()
    {
        $param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$detail = $this->model->getById($id);
		if($detail['cate_id']>0){
			$detail['cate'] = Db::name('PropertyCate')->where('id',$detail['cate_id'])->value('title');
		}
		if($detail['unit_id']>0){
			$detail['unit'] = Db::name('PropertyUnit')->where('id',$detail['unit_id'])->value('title');
		}
		if($detail['brand_id']>0){
			$detail['brand'] = Db::name('PropertyBrand')->where('id',$detail['brand_id'])->value('title');
		}
		if(!empty($detail['user_ids'])){
			$users =  Db::name('Admin')->where('id','in',$detail['user_ids'])->column('name');
			$detail['users_name'] = implode(',',$users);
		}
		if(!empty($detail['user_dids'])){
			$titles =  Db::name('Department')->where('id','in',$detail['user_dids'])->column('title');
			$detail['dids_title'] = implode(',',$titles);
		}
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		$repair = Db::name('PropertyRepair')
				->field('pr.*,u.name as director_name')
				->alias('pr')
				->join('Admin u', 'u.id = pr.director_id', 'left')
				->where(['pr.delete_time'=>0,'pr.property_id'=>$detail['id']])
				->select()->toArray();
		$detail['repair'] = $repair;
		View::assign('detail', $detail);
		return view();
    }
	
    //设置
    public function check()
    {
		$param = get_params();
        $res = Db::name('Property')->strict(false)->field('id,status')->update($param);
		if ($res) {
			add_log('set', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }   
	
	
	//维修记录列表
    public function repair_list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['p.title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['pr.repair_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
            }
			$where[] = ['pr.delete_time','=',0];
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = Db::name('PropertyRepair')
				->field('pr.*,pc.title as cate,pb.title as brand,p.title,p.model,u.name as director_name')
				->alias('pr')
				->join('Property p', 'p.id = pr.property_id', 'left')
				->join('PropertyCate pc', 'pc.id = p.cate_id', 'left')
				->join('PropertyBrand pb', 'pb.id = p.brand_id', 'left')
				->join('Admin u', 'u.id = pr.director_id', 'left')
				->where($where)
				->order('id desc')
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key){
					$item['repair_time'] = date('Y-m-d',$item['repair_time']);
					$item['create_time'] = date('Y-m-d H:i',$item['create_time']);
					return $item;
				});
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
				$res = Db::name('PropertyRepair')->strict(false)->field(true)->update($param);
				if($res){
					add_log('edit', $param['id'], $param);
					return to_assign();
				}
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('PropertyRepair')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
            if ($id > 0) {
                $detail = Db::name('PropertyRepair')->where(['id' => $id])->find();
                $detail['director_name'] = Db::name('Admin')->where('id',$detail['director_id'])->value('name');
                $detail['property'] = Db::name('Property')->where('id',$detail['property_id'])->value('title');
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
                View::assign('detail', $detail);
				return view('repair_edit');
            }
			if($pid>0){
				View::assign('property', $this->model->getById($pid));
			}
            View::assign('pid', $pid);
            View::assign('id', $id);
            return view();
        }
    }
	
    //维修记录查看
    public function repair_view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
		$detail = Db::name('PropertyRepair')->where(['id' => $id])->find();
		$detail['director_name'] = Db::name('Admin')->where('id',$detail['director_id'])->value('name');
		$detail['property'] = Db::name('Property')->where('id',$detail['property_id'])->value('title');
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
        $res = Db::name('PropertyRepair')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if ($res) {
			add_log('delete', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }  
}
