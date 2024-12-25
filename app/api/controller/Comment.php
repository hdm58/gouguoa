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
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;
use app\api\model\Comment as CommentModel;
class Comment extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CommentModel();
    }
	
	//获取评论列表
    public function datalist()
    {
		$param = get_params();
		$where=[];
		if (!empty($param['module'])) {
			$where[] = ['module', '=', $param['module']];
		}
		if (!empty($param['topic_id'])) {
			$where['topic_id'] = $param['topic_id'];
		}			
		$where[] = ['delete_time', '=', 0];
		$param['admin_id'] = $this->uid;
		$list = $this->model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
    //添加修改评论内容
    public function add()
    {
		$param = get_params();	
		if (!empty($param['id']) && $param['id'] > 0) {
			$param['update_time'] = time();
			unset($param['pid']);
			unset($param['padmin_id']);
            $res = CommentModel::where(['admin_id' => $this->uid,'id'=>$param['id']])->strict(false)->field(true)->update($param);
			if ($res!==false) {
				add_log('edit', $param['id'], $param,'评论');
				return to_assign();
			}
        } else {
            $param['create_time'] = time();
            $param['admin_id'] = $this->uid;
            $insertId = CommentModel::strict(false)->field(true)->insertGetId($param);
			if ($insertId) {
				add_log('add', $insertId, $param,'评论');
				return to_assign();
			}			
		}
    }
	
	//设为已读评论内容
    public function view()
    {
		if (request()->isPost()) {
			$id = get_params("id");
			$res = Db::name('CommentRead')->strict(false)->field(true)->insertGetId(['comment_id'=>$id,'admin_id'=>$this->uid,'create_time'=>time()]);
			if ($res!==false) {
				add_log('view', $id,[],'评论');
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
		}else{
			return to_assign(1, "错误的请求");
		}
    }
	
	//删除评论内容
    public function del()
    {
		if (request()->isDelete()) {
			$id = get_params("id");
			$res = CommentModel::where('id',$id)->strict(false)->field(true)->update(['delete_time'=>time()]);
			if ($res) {
				add_log('delete', $id,[],'评论');
				return to_assign(0, "删除成功");
			} else {
				return to_assign(1, "删除失败");
			}
		}else{
			return to_assign(1, "错误的请求");
		}
    }
}
