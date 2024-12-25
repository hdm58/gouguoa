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
use app\api\model\EditLog;
use think\facade\Db;

class Index extends BaseController
{
	//上传文件
	public function upload()
	{
		if (request()->isPost()) {
			$param = get_params();
			$sourse = 'file';
			if(isset($param['sourse'])){
				$sourse = $param['sourse'];
			}
			if($sourse == 'file' || $sourse == 'tinymce'){
				if(request()->file('file')){
					$file = request()->file('file');
				}
				else{
					return to_assign(1, '没有选择上传文件');
				}
			}
			else{
				if (request()->file('editormd-image-file')) {
					$file = request()->file('editormd-image-file');
				} else {
					return to_assign(1, '没有选择上传文件');
				}
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
				'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
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
				$data = [];
				$path = get_config('filesystem.disks.public.url');
				$data['filepath'] = $path . '/' . $filename;
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
				add_log('upload', $data['user_id'], $data,'文件');
				if($sourse == 'editormd'){
					//editormd编辑器上传返回
					return json(['success'=>1,'message'=>'上传成功','url'=>$data['filepath']]);
				}
				else if($sourse == 'tinymce'){
					//tinymce编辑器上传返回
					return json(['success'=>1,'message'=>'上传成功','location'=>$data['filepath']]);
				}
				else{
					//普通上传返回
					return to_assign(0, '上传成功', $res);
				}            
			 }
			 else {
				return to_assign(1, '上传失败，请重试');
			 }
		}
		else{
			return to_assign(1, '非法请求');
		} 
	}
	
	//执行分块上传的控制器方法
	public function chunkUpload() {
		if ($this->request->isPost()) {
			//执行分块上传流程
			$data = $this->request->post();
			//判断是否是分块上传
			if ($data['type'] === 'chunk') {
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
 
    //附件重命名
    public function file_edit()
    {
        $param = get_params();
		if (Db::name('File')->where('id',$param['id'])->update(['name'=>$param['title']]) !== false) {
			add_log('edit', $param['id'], $param,'文件名称');
			return to_assign(0, "操作成功");
		} else {
			return to_assign(1, "操作失败");
		}
    }

    //获取编辑记录
    public function load_log()
    {
        $param = get_params();
		$log = new EditLog();
		$list = $log->datalist($param);
        return to_assign(0, '', $list);
    }


    //清空缓存
    public function cache_clear()
    {
        \think\facade\Cache::clear();
        return to_assign(0, '系统缓存已清空');
    }

    // 测试邮件发送
    public function email_test()
    {
        $sender = get_params('email');
        //检查是否邮箱格式
        if (!is_email($sender)) {
            return to_assign(1, '测试邮箱码格式有误');
        }
        $email_config = \think\facade\Db::name('config')->where('name', 'email')->find();
        $config = unserialize($email_config['content']);
        $content = $config['template'];
        //所有项目必须填写
        if (empty($config['smtp']) || empty($config['smtp_port']) || empty($config['smtp_user']) || empty($config['smtp_pwd'])) {
            return to_assign(1, '请完善邮件配置信息');
        }

        $send = send_email($sender, '测试邮件', $content);
        if ($send) {
            return to_assign(0, '邮件发送成功');
        } else {
            return to_assign(1, '邮件发送失败');
        }
    }
	
	//获取未读消息
	public function get_msg()
	{
		$msg_map[] = ['to_uid', '=', $this->uid];
		$msg_map[] = ['read_time', '=', 0];
		$msg_map[] = ['delete_time', '=', 0];
		$msg_count = Db::name('Msg')->where($msg_map)->count();
		return to_assign(0, 'ok', $msg_count);
	}

    //获取部门
    public function get_department()
    {
        $department = get_department();
        return to_assign(0, '', $department);
    }

    //获取部门树形节点列表，用于tree前端组件
    public function get_department_tree()
    {
        $department = get_department();
        $list = get_tree($department);
        $data['trees'] = $list;
        return json($data);
    }
	
    //获取下属部门树形节点列表，用于tree前端组件
    public function get_department_tree_sub()
    {
		if($this->uid==1){
			$department = get_department();
		}
		else{
			$dids = get_leader_departments($this->uid);
			$department = Db::name('Department')->order('sort desc,id asc')->where([['status','=',1],['id','in',$dids]])->select()->toArray();
		}
        $list = get_tree($department,$department[0]['pid']);
        $data['trees'] = $list;
        return json($data);
    }
	
    //获取部门树形节点列表，用于X-select前端组件
    public function get_department_select()
    {
		$keyword = get_params('keyword');
		$selected = [];
		if(!empty($keyword)){
			$selected = explode(",",$keyword);
		}		
        $department = get_department();
        $list = get_select_tree($department, 0,0,$selected);
		return to_assign(0, '',$list);
    }

    //获取所有员工，did>0时时获取部门员工,用于picker签单组件
    public function get_employee($did = 0)
    {
		$where=[];
		$whereOr=[];
		if (!empty($did)) {
			$admin_array = Db::name('DepartmentAdmin')->where('department_id',$did)->column('admin_id');
			$map1=[
				['a.id','in',$admin_array],
			];
			$map2=[
				['a.did', '=', $did],
			];
			$whereOr =[$map1,$map2];
		}		
		$where[] = ['a.id', '>', 1];
		$where[] = ['a.status', '=', 1];
		$employee = Db::name('admin')
			->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
			->join('Position p', 'p.id = a.position_id','left')
			->join('Department d', 'a.did = d.id','left')
			->where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->group('a.id')
			->order('a.id desc')
			->select();
        return to_assign(0, '', $employee);
    }
	
   //获取所有下属员工，did>0时时获取部门员工,用于picker签单组件
    public function get_employee_sub($did = 0)
    {
		$where=[];
		$whereOr=[];
		if (!empty($did)) {
			$admin_array = Db::name('DepartmentAdmin')->where('department_id',$did)->column('admin_id');
			$map1=[
				['a.id','in',$admin_array],
			];
			$map2=[
				['a.did', '=', $did],
			];
			$whereOr =[$map1,$map2];
		}
		else{
			if($this->uid>1){
				$dids = get_leader_departments($this->uid);
				$where[] = ['a.did', 'in', $dids];
			}
		}
		$where[] = ['a.id', '>', 1];
		$where[] = ['a.status', '=', 1];
		$employee = Db::name('admin')
			->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
			->join('Position p', 'p.id = a.position_id','left')
			->join('Department d', 'a.did = d.id','left')
			->where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->group('a.id')
			->order('a.id desc')
			->select();
        return to_assign(0, '', $employee);
    }
	
    //获取所有员工
    public function get_personnel()
    {       
		$param = get_params();
		$where[] = ['a.status', '=', 1];
		$where[] = ['a.id', '>', 1];
		if (!empty($param['keywords'])) {
			$where[] = ['a.name', 'like', '%' . $param['keywords'] . '%'];
		}
		if(!empty($param['ids'])){
			//排除某些员工
			$where[] = ['a.id', 'notin', $param['ids']];
		}
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('admin')
            ->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
            ->join('Department d', 'a.did = d.id')
            ->where($where)
			->order('a.id desc')
			->paginate(['list_rows'=> $rows]);
		return table_assign(0, '', $list);
    }	
	
    //获取所有员工，用于X-select前端组件,did>0时时获取部门员工
    public function get_employee_select($did=0)
    {
		$keyword = get_params('keyword');
		$selected = [];
		if(!empty($keyword)){
			$selected = explode(",",$keyword);
		}
		if($did == 0){
			$employee = Db::name('admin')->field('id as value,name')->where(['status' => 1])->select()->toArray();		
		}
		else{
			$employee = get_department_employee($did);
		}
		$list=[];
		foreach($employee as $k => $v){
			$select = '';
			if(in_array($v['id'],$selected)){
				$select = 'selected';
			}
			$list[]=[
				'value'=>$v['id'],
				'name'=>$v['name'],
				'selected'=>$select
			];
		}
        return to_assign(0, '', $list);
    }	


    //获取某部门的负责人
    public function get_department_leader($uid=0,$pid=0)
    {
        $leaders = get_department_leader($uid,$pid);
        return to_assign(0, '', $leaders);
    }

    //获取职位
    public function get_position()
    {
        $position = Db::name('Position')->field('id,title')->where([['status', '=', 1], ['id', '>', 1]])->select();
        return to_assign(0, '', $position);
    }
	
	//获取消息模板
    public function get_template()
    {
		$param = get_params();
		if (!empty($param['keywords'])) {
			$where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['status', '=', 1];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];		
        $list = Db::name('Template')->field('id,title')->where($where)->paginate(['list_rows'=> $rows]);;
		return table_assign(0, '', $list);
    }
	
	//读取报销类型
	function get_expense_cate()
	{
		$cate = get_base_data('ExpenseCate');
		return to_assign(0, '', $cate);
	}

	//读取费用类型
	function get_cost_cate()
	{
		$cate = get_base_data('CostCate');
		return to_assign(0, '', $cate);
	}

	//读取印章类型
	function get_seal_cate()
	{
		$cate = get_base_data('SealCate');
		return to_assign(0, '', $cate);
	}

	//读取车辆类型
	function get_car_cate()
	{
		$cate = get_base_data('CarCate');
		return to_assign(0, '', $cate);
	}

	//读取企业主体
	function get_subject()
	{
		$subject = get_base_data('Subject');
		return to_assign(0, '', $subject);
	}

	//读取行业类型
	function get_industry()
	{
		$industry = get_base_data('Industry');
		return to_assign(0, '', $industry);
	}

	//读取服务类型
	function get_services()
	{
		$services = get_base_data('Services');
		return to_assign(0, '', $services);
	}
	
	//获取工作类型列表
    public function get_work_cate()
    {
        $cate = get_base_data('WorkCate');
        return to_assign(0, '', $cate);
    }
}
