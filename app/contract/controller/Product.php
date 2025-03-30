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

namespace app\contract\controller;

use app\base\BaseController;
use app\contract\model\Product as ProductModel;
use app\contract\validate\ProductValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Product extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ProductModel();
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
			if (!empty($param['cate_id'])) {
				$cate_id_array = get_cate_son('ProductCate',$param['cate_id']);
                $where[] = ['p.cate_id', 'in', $cate_id_array];
            }
			$where[] = ['p.delete_time', '=', 0];
			$list = $this->model->datalist($where, $param);
            return table_assign(0, '', $list);
        } else {
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
                    validate(ProductValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
				$param['update_id'] = $this->uid;
                $this->model->edit($param);
            } else {
                try {
                    validate(ProductValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['contractin_id'] = $this->uid;
                $this->model->add($param);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if($id>0){
                $detail = $this->model->getById($id);
				if($detail['file_ids'] !=''){
					$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['file_array'] = $file_array;
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
			$detail['cate'] = Db::name('ProductCate')->where('id',$detail['cate_id'])->value('title');
		}
		if($detail['file_ids'] !=''){
			$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['file_array'] = $file_array;
		}
		View::assign('detail', $detail);
		return view();
    }
	
    //设置
    public function set()
    {
		$param = get_params();
        $res = $this->model->strict(false)->field('id,status')->update($param);
		if ($res) {
			add_log('set', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
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
