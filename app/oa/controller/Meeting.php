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

namespace app\oa\controller;

use app\base\BaseController;
use app\adm\model\MeetingRecords as MeetingRecordsModel;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Meeting extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new MeetingRecordsModel();
    }
	
	//会议纪要
    public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where=[];
			$whereOr = [];
			$uid = $this->uid;
            if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['anchor_id'])) {
                $where[] = ['anchor_id', '=', $param['anchor_id']];
            }
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
                $where[] = ['meeting_date', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1].' 23:59:59'))]];
            }
            $where[] = ['delete_time', '=', 0];
			$whereOr[] = ['recorder_id|anchor_id','=',$uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',join_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',sign_uids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_uids)")];
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //查看会议纪要
    public function view($id)
    {
		View::assign('detail', $this->model->getById($id));
		if(is_mobile()){
			return view('qiye@/index/meeting_view');
		}
        return view();
    }
}
