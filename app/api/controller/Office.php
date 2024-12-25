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
use Firebase\JWT\JWT;


class Office extends BaseController
{	
    public function view($id=0,$mode='edit')
    {
		$file = Db::name('File')->where('id',$id)->find();
		if(empty($file)){
			return view('../../base/view/common/filetemplate');
		}
		$path = $file['filepath'];
		$title = $file['name'];		
		$extension = pathinfo($path, PATHINFO_EXTENSION);
		$filename = pathinfo($path, PATHINFO_FILENAME);
		$office_config = get_system_config('other');
		//$directory = substr($path, 0, 16);
		//$key = set_salt(10).str_replace("/", "T", $directory).$filename.'.'.$extension;
		$key = "key".$file['audit_time']."T".$id;
		$domain = $_SERVER['HTTP_HOST'];
		$url = "http://".$domain.$path;
		$callbackUrl = "http://".$domain."/office.php";
		$admin = Db::name('Admin')->where('id',$this->uid)->find();		
		$config = [
			"document" => [
				"url" => $url,
				"key" => $key,
				"permissions" => [
					"chat"=> true,
					"comment"=> true,
					"copy"=> true,
					"deleteCommentAuthorOnly"=> false,
					"download"=> true,
					"edit"=> true,
					"editCommentAuthorOnly"=> false,
					"fillForms"=> true,
					"modifyContentControl"=> true,
					"modifyFilter"=> true,
					"print"=>true,
					"protect"=> true,
					"review"=> true
				]
			],
			"editorConfig"=>[
				"mode" => $mode,//view,edit
				"forcesave"=>true,
				"lang"=>"zh-CN",
				"createUrl" => '',
				"customization"=>[
					"autosave"=>true,//是否自动保存
					"comments"=>false,
					"help"=>false
				],
				"user" => [ 
					"id" => $admin['id'],
					"name" => $admin['name']
				],
				"callbackUrl"=>$callbackUrl
			]
		];
		$token = JWT::encode($config, $office_config['token'], 'HS256'); //输出Token  默认'HS256'
		return View('',['token'=>$token,'key'=>$key,'office'=>$office_config,'mode'=>$mode,'domain'=>$domain,'url'=>$url,'title'=>$title,'callbackUrl'=>$callbackUrl,'admin'=>$admin]);    
    }
	
    public function officeapps($id=0,$mode='edit')
    {
        $file = Db::name('File')->where('id',$id)->find();
		if(empty($file)){
			return view('../../base/view/common/filetemplate');
		}
		$path = $file['filepath'];
		$domain = $_SERVER['HTTP_HOST'];
		$url = "//".$domain.$path;
        return View('',['url'=>$url]); 
    }
}
