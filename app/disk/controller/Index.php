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

namespace app\disk\controller;

use app\base\BaseController;
use app\disk\model\Disk as DiskModel;
use app\disk\validate\IndexValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new DiskModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$pid = isset($param['pid']) ? $param['pid'] : 0;
			$where=[];
			$where[]=['pid','=',$pid];
			$where[]=['admin_id','=',$this->uid];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['ext'])) {
                $where[] = ['file_ext', 'in',$param['ext']];
            }
			if (!empty($param['is_share'])) {
                $where[]=['is_share','=',1];
            }
			if (!empty($param['is_star'])) {
                $where[]=['is_star','=',1];
            }
            $list = $this->model->datalist($where, $param);
			$folder = get_pfolder($param['pid']);
            return table_assign(0, '', $list,$folder);
        }
        else{
            return view();
        }
    }

    /**
    * 分享列表
    */
    public function sharelist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$pid = isset($param['pid']) ? $param['pid'] : 0;
			$where=[];
			if($pid>0){
				$where[]=['pid','=',$pid];
			}
			else{
				$where[]=['is_share','=',1];
			}
			if (!empty($param['ext'])) {
                $where[] = ['file_ext', 'in',$param['ext']];
            }
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($where, $param);
			$folder = get_pfolder($param['pid']);
            return table_assign(0, '', $list,$folder);
        }
        else{
            return view();
        }
    }
    /**
    * 回收站列表
    */
    public function clearlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$pid = isset($param['pid']) ? $param['pid'] : 0;
			$where=[];
			$where[]=['admin_id','=',$this->uid];
			$where[]=['clear_time','=',0];
			if($pid>0){
				$where[]=['pid','=',$pid];
			}
			else{
				$where[]=['delete_time','>',0];
			}
			if (!empty($param['ext'])) {
                $where[] = ['file_ext', 'in',$param['ext']];
            }
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($where, $param);
			$folder = get_pfolder($param['pid']);
            return table_assign(0, '', $list,$folder);
        }
        else{
            return view();
        }
    }
	
    /**
    * 新增上传文件
    */
    public function add_upload()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			try {
				validate(IndexValidate::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['admin_id'] = $this->uid;
			$this->model->add($param);
		}
    }
	
	/**
    * 新增文件夹
    */
    public function add_folder()
    {
		$param = get_params();	
        if (request()->isAjax()) {		
			try {
				validate(IndexValidate::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['types'] = 2;
			$param['admin_id'] = $this->uid;
			$this->model->add($param);	 
        }
    }

	public function add_article()
    {
		$param = get_params();	
        if (request()->isAjax()) {		
            if (!empty($param['id']) && $param['id'] > 0) {
				$param['update_time'] = time();
                $res = Db::name('Article')->strict(false)->field(true)->update($param);
				if ($res !== false) {
					add_log('edit', $param['id'], $param);
					$disk['id'] = $param['disk_id'];
					$disk['name'] = $param['name'];
					$disk['update_time'] = $param['update_time'];
					$this->model->edit($disk);					
				} else {
					 return to_assign(1, "操作失败");
				}
				
            } else {
				$param['admin_id'] = $this->uid;
				$param['create_time'] = time();
                $aid = Db::name('Article')->strict(false)->field(true)->insertGetId($param);
				if ($aid !== false) {
					add_log('add', $aid, $param);
					$param['action_id'] = $aid;
					$param['types'] = 1;
					$param['ext'] = 'article';
					$this->model->add($param);
				} else {
					 return to_assign(1, "操作失败");
				}
            } 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$pid = isset($param['pid']) ? $param['pid'] : 0;
			if ($id>0) {
				$file = $this->model->getById($id);
				$detail = Db::name('Article')->find($file['action_id']);
				if($detail['file_ids'] !=''){
					$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['file_array'] = $file_array;
				}
				$detail['disk_id'] = $id;
				View::assign('detail', $detail);
				return view('edit_article');
			}
			View::assign('pid', $pid);
			return view();
		}
    }
	//查看在线文档
	public function view_article()
    {
		$param = get_params();	
		$id = isset($param['id']) ? $param['id'] : 0;
		if ($id>0) {
			$file = $this->model->getById($id);
			$detail = Db::name('Article')->find($file['action_id']);
			$detail['admin_name'] = Db::name('Admin')->where('id',$detail['admin_id'])->value('name');
			if($detail['file_ids'] !=''){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			View::assign('detail', $detail);
		}
		return view();
    }
	
	/**
    * 新增文件夹
    */
    public function rename()
    {
		$param = get_params();	
        if (request()->isAjax()) {		
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(IndexValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } 
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
		$param = get_params();
        if (request()->isDelete()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$file = Db::name('Disk')->find($val);
				if($file['admin_id'] != $this->uid){
					return to_assign(1, "删除失败,【".$file['name']."】不是你上传的文件");
					break;
				}
				$count = Db::name('Disk')->where(['pid'=>$val,'delete_time'=>0])->count();
				if($count>0){
					return to_assign(1, "删除失败,请先清空【".$file['name']."】里面的文件");
					break;
				}
				$list[] = [
					'id' => $val,
					'delete_time' => time()
				];
			}
			$res = $this->model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    } 
   /**
    * 恢复
    */
    public function back($ids)
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'delete_time' => 0
				];
			}
			$res = $this->model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "恢复成功");
            } else {
                return to_assign(1, "恢复失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }

   /**
    * 清除
    */
    public function clear($ids)
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'clear_time' => time()
				];
			}
			$res = $this->model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "清除成功");
            } else {
                return to_assign(1, "清除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    } 
   /**
    * 移动
    */
    public function move()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
            $pid = $param["pid"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$file = Db::name('Disk')->find($val);
				if($pid == $val){
					return to_assign(1, "移动失败,【".$file['name']."】不能移动到文件夹本身");
					break;
				}
				$list[] = [
					'id' => $val,
					'pid' => $pid,
					'update_time' => time()
				];
			}
			$model = new DiskModel();
			$res = $model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "转移成功");
            } else {
                return to_assign(1, "转移失败");
            }
        }else{
			$pid = isset($param['pid']) ? $param['pid']: 0 ;
			$path = get_pfolder($pid);
			$folder = Db::name('Disk')->where(['pid'=>$pid,'types'=>2,'delete_time'=>0])->order('id desc')->select()->toArray();
			$pfolder = '全部文件';
			if($pid>0){
				$pfolder = Db::name('Disk')->where(['id'=>$pid])->value('name');
			}
			View::assign('pid', $pid);
			View::assign('pfolder', $pfolder);
			View::assign('path', $path);
			View::assign('folder', $folder);
			return view();
		}
	}
	
	/**
    * 分享
    */
    public function share()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'is_share' => 1,
					'update_time' => time()
				];
			}
			$model = new DiskModel();
			$res = $model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "分享成功");
            } else {
                return to_assign(1, "分享失败");
            }
        }
	}
	
	/**
    * 取消分享
    */
    public function unshare()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'is_share' => 0,
					'update_time' => time()
				];
			}
			$model = new DiskModel();
			$res = $model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "取消成功");
            } else {
                return to_assign(1, "取消失败");
            }
        }
	}
	
	/**
    * 标星
    */
    public function star()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'is_star' => 1,
					'update_time' => time()
				];
			}
			$model = new DiskModel();
			$res = $model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "标星成功");
            } else {
                return to_assign(1, "标星失败");
            }
        }
	}
	
	/**
    * 取消标星
    */
    public function unstar()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'is_star' => 0,
					'update_time' => time()
				];
			}
			$model = new DiskModel();
			$res = $model->saveAll($list);
            if ($res!== false) {
                return to_assign(0, "操作成功");
            } else {
                return to_assign(1, "操作失败");
            }
        }
	}
}
