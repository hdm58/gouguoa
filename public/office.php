<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
 
// [ 应用入口文件 ]
namespace think;
use think\facade\Db;
require __DIR__ . '/../vendor/autoload.php';
// 定义项目目录
define('CMS_ROOT', __DIR__ . '/../');
// 执行HTTP应用并响应
$http = (new App())->http;
$response = $http->run();
if (($body_stream = file_get_contents("php://input"))===FALSE){
	echo "Bad Request";
}
//echo dirname(CMS_ROOT)."/storage/202312/i4b1z_e724c0b9ea54214fc0eaa13192f92b93.docx";
//exit;
$data = json_decode($body_stream, TRUE);
if ($data["status"] == 2){
	$downloadUri = $data["url"];
	$key = $data["key"];
	//$key = substr($key,10,strlen($key) - 1);
	//$file_path = str_replace("T", "/", $key);
	$id = explode('T', $key)[1];
	$file_path = Db::name('File')->where('id',$id)->value('filepath');
	$path_for_save =  dirname(CMS_ROOT).$file_path;
	if (($new_data = file_get_contents($downloadUri))===FALSE){
		echo "Bad Response";
	} else {
		Db::name('File')->where('id',$id)->inc('audit_time')->update();
		file_put_contents($path_for_save, $new_data, LOCK_EX);
	}
}
echo "{\"error\":0}";
exit;
