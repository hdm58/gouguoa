<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
 
// [ 应用入口文件 ]
namespace think;

if (empty(file_exists(__DIR__ . '/../vendor/autoload.php'))) {
    echo '您还未获取PHP依赖包，请输入命令获取：composer install，安装教程点击<a href="https://blog.gougucms.com/home/book/detail/bid/3/id/8.html" target="_blank">这里</a>。';
    exit;
}
require __DIR__ . '/../vendor/autoload.php';

// 定义当前版本号
define('CMS_VERSION','5.7.2');

// 定义手机端当前版本号
define('MB_VERSION','1.0');

// 定义Layui版本号
define('LAYUI_VERSION','2.10.1');

// 定义项目目录
define('CMS_ROOT', __DIR__ . '/../');

// 定义报错模版
define('EEEOR_REPORTING',CMS_ROOT.'/public/tpl/error.html');

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
