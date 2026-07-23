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

namespace app\disk\controller;

use app\base\BaseController;
use app\disk\model\Disk as DiskModel;
use app\disk\validate\DiskValidate;
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
    * 个人空间
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$pid = isset($param['pid']) ? $param['pid'] : 0;
			$where=[];
			$whereOr=[];
			$where[]=['admin_id','=',$this->uid];
			$where[]=['delete_time','=',0];
			$where[]=['group_id','=',0];
			$where[]=['pid','=',$pid];
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['ext'])) {
				if($param['ext'] == 'other'){
					$where[] = ['file_ext', 'notin','jpg,jpeg,png,gif,mpg,mp4,mpeg,avi,wmv,mov,flv,m4v,mp3,wav,wma,flac,midi,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,article,folder,zip,rar,7z,gz,tar'];
				}
				else{
					$where[] = ['file_ext', 'in',$param['ext']];
				}
            }
            $list = $this->model->datalist($param,$where);
			$folder = get_pfolder($pid);
            return table_assign(0, '', $list,$folder);
        }
        else{
            return view();
        }
    }

	/**
    * 标星文件
    */
    public function starlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['admin_id','=',$this->uid];
			$where[]=['delete_time','=',0];
			$where[]=['group_id','=',0];
			$where[]=['types','<',2];
			$where[]=['is_star','=',1];
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }            
			if (!empty($param['ext'])) {
				if($param['ext'] == 'other'){
					$where[] = ['file_ext', 'notin','jpg,jpeg,png,gif,mpg,mp4,mpeg,avi,wmv,mov,flv,m4v,mp3,wav,wma,flac,midi,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,article,folder,zip,rar,7z,gz,tar'];
				}
				else{
					$where[] = ['file_ext', 'in',$param['ext']];
				}
            }
            $list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
	/**
    * 我分享的文件
    */
    public function mysharelist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$where[]=['admin_id','=',$this->uid];
			$where[]=['delete_time','=',0];
			$where[]=['group_id','=',0];
			$where[]=['types','<',2];
			$where[]=['share_types','>',0];
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }            
			if (!empty($param['ext'])) {
				if($param['ext'] == 'other'){
					$where[] = ['file_ext', 'notin','jpg,jpeg,png,gif,mpg,mp4,mpeg,avi,wmv,mov,flv,m4v,mp3,wav,wma,flac,midi,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,article,folder,zip,rar,7z,gz,tar'];
				}
				else{
					$where[] = ['file_ext', 'in',$param['ext']];
				}
            }
            $list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
	/**
    * 别人分享的文件
    */
    public function tosharelist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$where[]=['admin_id','<>',$this->uid];
			$where[]=['delete_time','=',0];
			$where[]=['group_id','=',0];
			$where[]=['types','<',2];
			$where[]=['share_types','>',0];			
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }            
			if (!empty($param['ext'])) {
				if($param['ext'] == 'other'){
					$where[] = ['file_ext', 'notin','jpg,jpeg,png,gif,mpg,mp4,mpeg,avi,wmv,mov,flv,m4v,mp3,wav,wma,flac,midi,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,article,folder,zip,rar,7z,gz,tar'];
				}
				else{
					$where[] = ['file_ext', 'in',$param['ext']];
				}
            }
			
			$uid=$this->uid;
			$did=$this->did;
			$pid=$this->pid;
			
			$map1=[
				['share_types','=',1]
			];
			$map2=[
				['share_types','=',2],
				['', 'exp', Db::raw("FIND_IN_SET('{$did}',share_dids)")]
			];
			$map3=[
				['share_types','=',3],
				['', 'exp', Db::raw("FIND_IN_SET('{$pid}',share_pids)")]
			];
			$map4=[
				['share_types','=',4],
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_uids)")]
			];
			$whereOr =[$map1,$map2,$map3,$map4];
            $list = $this->model->datalist($param,$where,$whereOr);
			return table_assign(0, '', $list);
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
			$group_id = isset($param['group_id']) ? $param['group_id'] : 0;
			$where=[];
			$where[]=['delete_time','=',0];
			$where[]=['group_id','=',$group_id];
			$where[]=['pid','=',$pid];
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }            
			if (!empty($param['ext'])) {
				if($param['ext'] == 'other'){
					$where[] = ['file_ext', 'notin','jpg,jpeg,png,gif,mpg,mp4,mpeg,avi,wmv,mov,flv,m4v,mp3,wav,wma,flac,midi,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,article,folder,zip,rar,7z,gz,tar'];
				}
				else{
					$where[] = ['file_ext', 'in',$param['ext']];
				}
            }
            $list = $this->model->datalist($param,$where);
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
			$where[]=['delete_time','>',0];
			if($pid>0){
				$where[]=['pid','=',$pid];
			}
			if (!empty($param['ext'])) {
				if($param['ext'] == 'other'){
					$where[] = ['file_ext', 'notin','jpg,jpeg,png,gif,mpg,mp4,mpeg,avi,wmv,mov,flv,m4v,mp3,wav,wma,flac,midi,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,article,folder,zip,rar,7z,gz,tar'];
				}
				else{
					$where[] = ['file_ext', 'in',$param['ext']];
				}
            }
            if (!empty($param['keywords'])) {
                $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
            }
			//dd($where);exit;
            $list = $this->model->datalist($param,$where);
			$folder = get_pfolder($param['pid']);
            return table_assign(0, '', $list,$folder);
        }
        else{
            return view();
        }
    }
	
    //添加&编辑
    public function add_group()
    {
        if (request()->isAjax()) {
			$param = get_params();
			if($param['title'] == '全部' || $param['title']=='未共享空间'){
				return to_assign(1, '该共享空间名称已经存在');
			}
            if (!empty($param['id']) && $param['id'] > 0) {				
				$uid=$this->uid;
				$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
				if($uid==1 || $detail['admin_id'] == $uid){
					$count = Db::name('DiskGroup')->where([['id','<>',$param['id']],['delete_time','=',0],['title','=',$param['title']]])->count();
					if ($count > 0) {
						return to_assign(1, '该共享空间名称已经被其他员工占用');
					}
					$param['update_time'] = time();
					$res = Db::name('DiskGroup')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
					if($res!=false){
						add_log('edit', $param['id'], $param);
						return to_assign(0,'编辑成功',$param['id']);
					}else{
						return to_assign(1,'操作失败');
					}
				}else{
					return to_assign(1,'只要超级管理员和创建人才有权限操作');
				}
            } else {
				$param['admin_id'] = $this->uid;
				$param['create_time'] = time();
                $count = Db::name('DiskGroup')->where([['delete_time','=',0],['title','=',$param['title']]])->count();
                if ($count > 0) {
                    return to_assign(1, '该共享空间名称已经被其他员工占用');
                }
                $gid = Db::name('DiskGroup')->strict(false)->field(true)->insertGetId($param);
				if($gid!=false){
					add_log('add', $gid, $param);
					return to_assign(0,'添加成功',$gid);
				}else{
					return to_assign(1,'操作失败');
				}                
            }
        }
    }

    //删除
    public function del_group()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
			$uid=$this->uid;
			$detail = Db::name('DiskGroup')->where('id',$id)->find();
			if($uid==1 || $detail['admin_id'] == $uid){
				$count = Db::name('Disk')->where(["group_id" => $id,'delete_time'=>0])->count();
				if ($count > 0) {
					return to_assign(1, "该共享空间还存在文件，请去除文件或者转移文件后再删除");
				}
				if (Db::name('DiskGroup')->delete($id) !== false) {
					add_log('delete', $id, []);
					return to_assign(0, "删除成功");
				} else {
					return to_assign(1, "删除失败");
				}
			}else{
				return to_assign(1,'只要超级管理员和创建人才有权限操作');
			}
        } else {
            return to_assign(1, "错误的请求");
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
				validate(DiskValidate::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
			$this->model->add($param);
		}
		else{
			return view();
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
				validate(DiskValidate::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['types'] = 2;
			$param['admin_id'] = $this->uid;
			$param['did'] = $this->did;
			$param['file_ext'] = 'folder';
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
					$disk['file_ext'] = 'article';
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
					$param['file_ext'] = 'article';
					$param['did'] = $this->did;
					$this->model->add($param);
				} else {
					 return to_assign(1, "操作失败");
				}
            } 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$pid = isset($param['pid']) ? $param['pid'] : 0;
			$group_id = isset($param['group_id']) ? $param['group_id'] : 0;
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
			View::assign('group_id', $group_id);
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
			$uid=$this->uid;
			$detail = Db::name('Disk')->where('id',$param['id'])->find();
			if($uid==1 || $detail['admin_id'] == $uid){
				try {
					validate(DiskValidate::class)->scene('edit')->check($param);
				} catch (ValidateException $e) {
					// 验证失败 输出错误信息
					return to_assign(1, $e->getError());
				}
				$this->model->edit($param);
			}else{
				return to_assign(1,'只要超级管理员和创建人才有权限操作');
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
				if($file['admin_id'] != $this->uid && $this->uid>1){
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
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    } 
   /**
    * 恢复
    */
    public function back()
    {
		$param = get_params();
        if (request()->isAjax()) {
            $ids = $param["ids"];
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				$detail = Db::name('Disk')->where(['id'=>$val])->find();
				$has=Db::name('Disk')->where(['pid'=>$detail['pid'],'name'=>$detail['name'],'group_id'=>$detail['group_id'],'admin_id'=>$detail['admin_id'],'delete_time'=>0])->find();
				if(!empty($has)){
					if($detail['type']==0){
						$newname = pathinfo($detail['name'], PATHINFO_FILENAME).'_'.date('Ymd').'_'.date('His').'还原.'.$detail['fileext'];
					}
					else{
						$newname = $detail['name'].'_'.date('Ymd').'_'.date('His').'还原';
					}
				}
				else{
					$newname = $detail['name'];
				}
				$list[] = [
					'id' => $val,
					'newname' => $newname,
					'delete_time' => 0
				];
			}
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }

   /**
    * 清除
    */
    public function clear()
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
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    } 
	
	//查找父文件的所有父ids
	public function get_pidsa($id)
    {
		$pid = Db::name('Disk')->where('id',$id)->value('pid');
		if($pid==0){
			return [];
		}
		else{
			$pids=$this->get_pids($pid);
			$pids[] = $pid;
			return $pids;			
		}
		return [];
	}
	//while方法
	public function get_pids($categoryId)
	{
		$parentIds = [];
		while ($categoryId > 0) {
			$category = Db::name('Disk')->where('id',$categoryId)->find();
			if ($category && $category['pid'] > 0) {
				$parentIds[] = $category['id'];
				$categoryId = $category['pid'];
			} else {
				break;
			}
		}
		return $parentIds;
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
			$pids = $this->get_pids($pid);
			$idArray = explode(',', strval($ids));
			$list = [];
			foreach ($idArray as $key => $val) {
				if(in_array($val,$pids) || $val==$pid){
					$file = Db::name('Disk')->find($val);
					return to_assign(1, "移动失败,【".$file['name']."】不能移动到文件夹本身或其子目录");
					break;
				}
				$list[] = [
					'id' => $val,
					'pid' => $pid,
					'update_time' => time()
				];
			}
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "转移失败");
			}
        }else{
			$pid = isset($param['pid']) ? $param['pid']: 0 ;
			$group_id = isset($param['group_id']) ? $param['group_id']: 0 ;
			$path = get_pfolder($pid);
			$folder = Db::name('Disk')->where(['pid'=>$pid,'group_id'=>$group_id,'types'=>2,'delete_time'=>0])->order('id desc')->select()->toArray();
			$pfolder = '全部文件';
			if($pid>0){
				$pfolder = Db::name('Disk')->where(['id'=>$pid])->value('name');
			}
			View::assign('pid', $pid);
			View::assign('pfolder', $pfolder);
			View::assign('path', $path);
			View::assign('folder', $folder);
			View::assign('group_id', $group_id);
			return view();
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
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
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
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
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
			if(!empty($param['share_time'])){
				$param['share_time'] = strtotime($param['share_time'].' 23:59:59');
			}
			else{
				$param['share_time'] = 0;
			}
			$list = [];
			foreach ($idArray as $key => $val) {
				$list[] = [
					'id' => $val,
					'share_types' => $param["share_types"],
					'share_dids' => $param["share_dids"],
					'share_pids' => $param["share_pids"],
					'share_uids' => $param["share_uids"],
					'share_time' => $param['share_time'],
				];
			}
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
        }else{
			$id = isset($param['id']) ? $param['id']: 0 ;
			$detail = Db::name('Disk')->where(['id'=>$id])->find();
			View::assign('detail', $detail);
			return view();
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
					'share_types' => 0,
					'share_dids' => '',
					'share_pids' => '',
					'share_uids' => '',
					'share_time' => 0,
				];
			}
			if(!empty($list)){
				$model = new DiskModel();
				foreach ($list as $item) {
					$model->update($item);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
        }
	}
}
