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

namespace app\project\controller;

use app\base\BaseController;
use app\project\model\ProjectDocument;
use app\project\validate\DocumentCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Document extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new ProjectDocument();
    }
	
    public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();			
			$where = array();
			$whereOr  = array();
			if (!empty($param['keywords'])) {
				$where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
			}
			if (!empty($param['project_id'])) {
				$where[] = ['project_id', '=', $param['project_id']];
			} else {
				$project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
				$whereOr[] = ['admin_id', '=', $this->uid];
				$whereOr[] = ['project_id', 'in', $project_ids];
			}
			$where[] = ['delete_time', '=', 0];
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                $detail = $this->model->detail($param['id']);
                try {
                    validate(DocumentCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = ProjectDocument::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(DocumentCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $sid = ProjectDocument::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
                }
                return to_assign();
            }
        } else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$project_id = isset($param['project_id']) ? $param['project_id'] : 0;
			if($id>0){
				View::assign('detail', $this->model->detail($param['id']));
			}
			if($project_id>0){
				$project_name =  Db::name('Project')->where(['id' => $project_id])->value('name');
				View::assign('project_name', $project_name);
			}
            View::assign('project_id', $project_id);
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = $this->model->detail($id);
        if (empty($detail)) {
			if (empty($detail)) {
				echo '<div style="text-align:center;color:red;margin-top:20%;">该文档不存在</div>';exit;
			}
        } else {
            $project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
            if (in_array($detail['project_id'], $project_ids) || ($this->uid = $detail['admin_id'])) {
                View::assign('detail', $detail);
                if(is_mobile()){
					return view('qiye@/project/document_view');
				}
                return view();
            }
            else{
				echo '<div style="text-align:center;color:red;margin-top:20%;">您没权限查看该文档</div>';exit;
            }
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('ProjectDocument')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid) {
                return to_assign(1, "你不是该文档的创建人，无权限删除");
            }
            if (Db::name('ProjectDocument')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
}
