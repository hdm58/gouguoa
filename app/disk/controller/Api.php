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

use app\api\BaseController;
use think\facade\Db;
use think\Image;
use think\facade\View;

class Api extends BaseController
{
	//目录
    public function folder()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			$path = $param['path'];
			$pid = $param['pid'];
			$group_id = $param['group_id'];
			//用 '/' 分割路径，得到数组
			$dirArray = explode('/', $path);
			//去除第一个空元素（因为路径以 '/' 开头）
			if ($dirArray[0] === '') {
				array_shift($dirArray);
			}
			//print_r($dirArray);exit;
			foreach ($dirArray as $key => $value) {
				$has=Db::name('Disk')->where(['pid'=>$pid,'name'=>$value,'group_id'=>$group_id,'admin_id'=>$this->uid,'types'=>2,'delete_time'=>0])->find();
				if(!empty($has)){
					$pid = $has['id'];
				}else{
					$pid = $this->add_folder($pid,$group_id,$value);
				}
			}
			return to_assign(0,'操作成功',['return_id'=>$pid]);
        }
    }
	
	//新增目录
	public function add_folder($pid,$group_id,$name)
    {
		$data = [
			'types'=>2,
			'pid'=>$pid,
			'group_id' => $group_id,
			'name' => $name,
			'admin_id'=> $this->uid,
			'did' => $this->did,
			'file_ext' => 'folder',
			'create_time' => time()
		];
		$insertId = Db::name('Disk')->strict(false)->field(true)->insertGetId($data);
		return $insertId;	 
    }
	
	//拖拽上传文件
	public function drop_upload()
	{
		if (request()->isPost()) {
			$param = get_params();
			$pid = $param['pid'];
			$group_id = $param['group_id'];
			if(request()->file('file')){
				$file = request()->file('file');
			}
			else{
				return to_assign(1, '没有选择上传文件');
			}
			// 获取上传文件的hash散列值
			$sha1 = $file->hash('sha1');
			$md5 = $file->hash('md5');
			$rule = [
				'image' => 'jpg,png,jpeg,gif',
				'doc' => 'txt,doc,docx,ppt,pptx,xls,xlsx,pdf',
				'file' => 'zip,gz,7z,rar,tar',
				'video' => 'mpg,mp4,mpeg,avi,wmv,mov,flv,m4v',
				'audio' => 'mp3,wav,wma,flac,midi',
			];
			$fileExt = $rule['image'] . ',' . $rule['doc'] . ',' . $rule['file'] . ',' . $rule['video'] . ',' . $rule['audio'];
			//1M=1024*1024=1048576字节
			$file_size = get_system_config('system','upload_max_filesize');
			if(!isset($file_size)){
				$file_size=50;
			}
			$fileSize = $file_size * 1024 * 1024;
			if (isset($param['type']) && $param['type']) {
				$fileExt = $rule[$param['type']];
			}
			if (isset($param['size']) && $param['size']) {
				$fileSize = $param['size'];
			}
			$validate = \think\facade\Validate::rule([
				//'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
				'image' => 'require|fileSize:' . $fileSize,
			]);
			$file_check['image'] = $file;
			if (!$validate->check($file_check)) {
				return to_assign(1, $validate->getError());
			}
			// 日期前綴
			$dataPath = date('Ym');
			$use = 'thumb';
			$filename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file, function () use ($md5) {
				return set_salt(5).'_'.$md5;
			});
			if ($filename) {
				//写入到附件表
				$imagePath = get_config('filesystem.disks.public.url'). '/' . $filename;
				$thumbPath = '';
				if(in_array($file->extension(),['jpg','png','jpeg','gif'])){
					// 生成等比缩略图
					$image = Image::open(request()->file('file'));
					$thumbPath = dirname($imagePath) . '/thumb_' . basename($imagePath);
					// 生成等比缩略图保存到指定位置，这里设置最大宽度为360px, 高度自适应
					//$image->thumb(360,360,Image::THUMB_CENTER)->save('./'.$thumbPath);
					$image->thumb(256,256)->save('./'.$thumbPath);
				}
				$data = [];
				$data['filepath'] = $imagePath;
				$data['thumbpath'] = $thumbPath;
				
				$data['name'] = $file->getOriginalName();
				$data['mimetype'] = $file->getOriginalMime();
				$data['fileext'] = $file->extension();
				$data['filesize'] = $file->getSize();
				$data['filename'] = $filename;
				$data['sha1'] = $sha1;
				$data['md5'] = $md5;
				$data['module'] = \think\facade\App::initialize()->http->getName();
				$data['action'] = app('request')->action();
				$data['uploadip'] = app('request')->ip();
				$data['create_time'] = time();
				$data['user_id'] = $this->uid;
				if ($data['module'] = 'admin') {
					//通过后台上传的文件直接审核通过
					$data['status'] = 1;
					$data['admin_id'] = $data['user_id'];
					$data['audit_time'] = time();
				}
				$data['use'] = request()->has('use') ? request()->param('use') : $use; //附件用处
				$res['id'] = Db::name('file')->insertGetId($data);
				$res['filepath'] = $data['filepath'];
				$res['name'] = $data['name'];
				$res['uid'] = $this->uid;
				$res['filename'] = $data['filename'];
				$res['filesize'] = $data['filesize'];
				$res['fileext'] = $data['fileext'];
				if(!empty($param['filePath'])){
					$dirPath = dirname($param['filePath']);
					if($dirPath!='.'){
						$dirArray = explode('/', $dirPath);
						//去除第一个空元素（因为路径以 '/' 开头）
						if ($dirArray[0] === '') {
							array_shift($dirArray);
						}
						//print_r($dirArray);exit;
						foreach ($dirArray as $key => $value) {
							$has=Db::name('Disk')->where(['pid'=>$pid,'name'=>$value,'group_id'=>$group_id,'admin_id'=>$this->uid,'types'=>2,'delete_time'=>0])->find();
							if(!empty($has)){
								$pid = $has['id'];
							}else{
								$pid = $this->add_folder($pid,$group_id,$value);
							}
						}
					}
				}
				$has=Db::name('Disk')->where(['pid'=>$pid,'name'=>$res['name'],'group_id'=>$group_id,'admin_id'=>$this->uid,'types'=>0,'delete_time'=>0])->find();
				if(!empty($has)){
					$newname = pathinfo($res['name'], PATHINFO_FILENAME).'_'.date('Ymd').'_'.date('His').'.'.$res['fileext'];					
				}
				else{
					$newname = $res['name'];
				}
				$diskData = [
					'types'=>0,
					'pid'=>$pid,
					'group_id' => $group_id,
					'action_id' => $res['id'],
					'name' => $newname,
					'file_ext' => $res['fileext'],
					'file_size' => $res['filesize'],
					'admin_id'=> $this->uid,
					'did' => $this->did,
					'create_time' => time()
				];
				$insertId = Db::name('Disk')->strict(false)->field(true)->insertGetId($diskData);
				add_log('upload', $data['user_id'], $data,'文件');
				return to_assign(0, '上传成功', $res);           
			 }
			 else {
				return to_assign(1, '上传失败，请重试');
			 }
		}
		else{
			return to_assign(1, '非法请求');
		} 
	}
	
	//执行分片上传
	public function chunkUpload() {
		if (request()->isPost()) {
			//执行分块上传流程
			$data = request()->post();
				$file = request()->file('file');				
				$rule = [
					'image' => 'jpg,png,jpeg,gif',
					'doc' => 'txt,doc,docx,ppt,pptx,xls,xlsx,pdf',
					'file' => 'zip,gz,7z,rar,tar',
					'video' => 'mpg,mp4,mpeg,avi,wmv,mov,flv,m4v',
					'audio' => 'mp3,wav,wma,flac,midi',
				];
				$fileExt = $rule['image'] . ',' . $rule['doc'] . ',' . $rule['file'] . ',' . $rule['video'] . ',' . $rule['audio'];
				//1M=1024*1024=1048576字节
				$file_size = get_system_config('system','upload_max_filesize');
				if(!isset($file_size)){
					$file_size=50;
				}
				$fileSize = $file_size * 1024 * 1024;
				$validate = \think\facade\Validate::rule([
					'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
				]);
				$file_check['image'] = $file;
				if (!$validate->check($file_check)) {
					return to_assign(1, $validate->getError());
				}
				
				//获取对应的上传配置
				$fs = \think\facade\Filesystem::disk('public');
				$ext = $file->extension();
				$chunkPath = $data['file_id'].'/'.$file->md5().($ext ? '.'.$ext : '');
				//存储分片文件到指定路径
				$savename = $fs->putFileAs( 'chunk', $file,$chunkPath);
				if (!$savename) {
					return json([
						'code' => 1,
						'msg' => '上传失败',
						'data' => [],
					]);
				}
				if (!$data['is_end']) {
					$filepath = '';
				} else {
					//合并块文件
					$fileUrl = '';
					$chunkSaveDir = \think\facade\Filesystem::getDiskConfig('public');
					$smallChunkDir = $chunkSaveDir['root'].'/chunk/'.$data['file_id'];
					//获取已存储的属于源文件的所有分块文件 进行合并
					if ($handle = opendir($smallChunkDir)) {
						$chunkList = [];
						$modifyTime = [];
						while (false !== ($file = readdir($handle))) {
							if ($file != "." && $file != "..") {
								$temp['path'] = $smallChunkDir.'/'.$file;
								$temp['modify'] = filemtime($smallChunkDir.'/'.$file);
								$chunkList[] = $temp;
								$modifyTime[] = $temp['modify'];
							}
						}
						//对分块文件进行排序
						array_multisort($modifyTime,SORT_ASC,$chunkList);
						$saveDir = \think\facade\Filesystem::getDiskConfig('public');
						$saveName = md5($data['file_id'].$data['file_name']).'.'.$data['file_extension'];
						$newPath = $saveDir['root'].'/'.date('Ym').'/'.$saveName;
						if (!file_exists($saveDir['root'].'/'.date('Ym'))) {
							mkdir($saveDir['root'].'/'.date('Ym'),0777,true);
						}
						$newFileHandle = fopen($newPath,'a+b');
						foreach ($chunkList as $item) {
							fwrite($newFileHandle,file_get_contents($item['path']));
							unlink($item['path']);
						}
						rmdir($smallChunkDir);
						//将合并后的文件存储到指定路径
						$fileUrl = $saveDir['url'].'/'.date('Ym').'/'.$saveName;
						fclose($newFileHandle);
						closedir($handle);
					} else {
						return json([
							'code' => 1,
							'msg' => '目录：'.$chunkSaveDir['root'].'/chunk/'.$data['file_id'].'不存在',
							'data' => [],
						]);
					}
					$filepath = $fileUrl;
				}
				$res=[];
				//合并流程结束
				if ($filepath!='') {
					$fileinfo = [];
					$fileinfo['filepath'] = $filepath;
					$fileinfo['name'] = $data['file_name'];
					$fileinfo['fileext'] = $data['file_extension'];
					$fileinfo['filesize'] = $data['file_size'];
					$fileinfo['filename'] = date('Ym').'/'.$saveName;
					$fileinfo['sha1'] = $data['file_id'];
					$fileinfo['md5'] = $data['file_id'];
					$fileinfo['module'] = \think\facade\App::initialize()->http->getName();
					$fileinfo['action'] = app('request')->action();
					$fileinfo['uploadip'] = app('request')->ip();
					$fileinfo['create_time'] = time();
					$fileinfo['user_id'] = get_login_admin('id') ? get_login_admin('id') : 0;
					if ($fileinfo['module'] = 'admin') {
						//通过后台上传的文件直接审核通过
						$fileinfo['status'] = 1;
						$fileinfo['admin_id'] = $fileinfo['user_id'];
						$fileinfo['audit_time'] = time();
					}
					$fileinfo['use'] = 'big';
					$res['id'] = Db::name('file')->insertGetId($fileinfo);
					$res['filepath'] = $fileinfo['filepath'];
					$res['name'] = $fileinfo['name'];
					$res['filename'] = $fileinfo['filename'];
					$res['filesize'] = $fileinfo['filesize'];
					$res['fileext'] = $fileinfo['fileext'];
					add_log('upload', $fileinfo['user_id'], $fileinfo);
				} 
				return to_assign(0, '上传成功', $res);
		}
		else{
			return to_assign(1, '非法请求', $res);
		}
	}

	//取消上传，删除临时文件
	public function clearChunk() {
		if ($this->request->isPost()) {
			$param = get_params();
			$saveDir = \think\facade\Filesystem::getDiskConfig('public');
			$smallChunkDir = $saveDir['root'].'/chunk/'.$param['file_id'];
			if(!is_dir($smallChunkDir)){
				return to_assign(0, '上传的临时文件已删除');
			}
			//获取已存储的属于源文件的所有分块文件
			if ($handle = opendir($smallChunkDir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$temp['path'] = $smallChunkDir.'/'.$file;
						unlink($temp['path']);
					}
				}
				rmdir($smallChunkDir);
				closedir($handle);
				return to_assign(0, '已取消上传');
			}
		}
	}
	

    //获取共享空间
    public function get_group()
    {
		$where=[];
		$whereOr=[];
		$where[] = ['delete_time','=',0];
		$uid=$this->uid;
		if($uid>1){
			$map1=[
				['admin_id','=',$uid],
			];
			$map2=[
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',director_uids)")],
			];
			$map3=[
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',group_uids)")],
			];
			$whereOr =[$map1,$map2,$map3];
		}
		$list = Db::name('DiskGroup')
			->where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->select()->toArray();
		return to_assign(0, '',$list);
	}
	
    /**
    * 空间成员列表
    */
    public function memberlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$group_uids = Db::name('DiskGroup')->where('id',$param['id'])->value('group_uids');
			$list['data']=[];
			if(!empty($group_uids)){
				$list['data'] = Db::name('Admin')
					->field('a.*,p.title as position, d.title as department')
					->alias('a')
					->join('Position p','p.id = a.position_id')
					->join('Department d','d.id = a.did')
					->where([['a.id','in',$group_uids],['a.status','=',1]])
					->select()->toArray();
			}
			table_assign(0,'', $list);
        }
        else{
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			View::assign('detail', $detail);
			return view();
        }
    }
	
    /**
    * 设置空间成员
    */
    public function memberset()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid=$this->uid;
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			$array = array_map('trim', explode(',', $detail['director_uids']));
			if($uid==1 || $detail['admin_id'] == $uid || in_array($uid, $array)){
				$res = Db::name('DiskGroup')->where('id',$param['id'])->update(['group_uids'=>$param['group_uids']]);
				if($res!==false){
					return to_assign(0,'操作成功',['return_id'=>$param['id']]);
				}
				else{
					return to_assign(1,'操作失败');
				}
			}else{
				return to_assign(1,'只要超级管理员、创建人、空间管理人员才有权限操作');
			}
        }
        else{
			// 禁止访问
			throw new \think\exception\HttpException(403, '禁止访问');
        }
    }
	
    /**
    * 空间管理员列表
    */
    public function adminlist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$director_uids = Db::name('DiskGroup')->where('id',$param['id'])->value('director_uids');
			$list['data']=[];
			if(!empty($director_uids)){
				$list['data'] = Db::name('Admin')
					->field('a.*,p.title as position, d.title as department')
					->alias('a')
					->join('Position p','p.id = a.position_id')
					->join('Department d','d.id = a.did')
					->where([['a.id','in',$director_uids],['a.status','=',1]])
					->select()->toArray();
			}
			table_assign(0,'', $list);
        }
        else{
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			View::assign('detail', $detail);
			return view();
        }
    }
	
    /**
    * 设置空间管理员
    */
    public function adminset()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid=$this->uid;
			$detail = Db::name('DiskGroup')->where('id',$param['id'])->find();
			if($uid==1 || $detail['admin_id'] == $uid){
				$res = Db::name('DiskGroup')->where('id',$param['id'])->update(['director_uids'=>$param['director_uids']]);
				if($res!==false){
					return to_assign(0,'操作成功',['return_id'=>$param['id']]);
				}
				else{
					return to_assign(1,'操作失败');
				}
			}else{
				return to_assign(1,'只要超级管理员和创建人才有权限操作');
			}
        }
        else{
			// 禁止访问
			throw new \think\exception\HttpException(403, '禁止访问');
        }
    }
}
