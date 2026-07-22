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

namespace app\home\controller;

use app\base\BaseController;
use app\adm\model\Regulation as RegulationModel;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Regulation extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new RegulationModel();
    }
	
	public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['dids'])) {
				$did = $param['dids'];
				$where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$did}',use_dids)")];
            }
			if (!empty($param['cate_id'])) {
				$cate_id_array = get_cate_son('RegulationCate',$param['cate_id']);
                $where[] = ['cate_id', 'in', $cate_id_array];
            }
			$where[] = ['delete_time', '=', 0];
			$list = $this->model->datalist($where, $param);
            return table_assign(0, '', $list);
        } else {
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
			$detail['cate'] = Db::name('RegulationCate')->where('id',$detail['cate_id'])->value('title');
		}
		if($detail['file_ids'] !=''){
			$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['file_array'] = $file_array;
		}
		View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/index/regulation_view');
		}
		return view();
    }
}
