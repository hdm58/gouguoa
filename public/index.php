<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */
 
// [ 应用入口文件 ]
namespace think;

if (empty(file_exists(__DIR__ . '/../vendor/autoload.php'))) {
    echo '您还未安装PHP依赖包，请输入命令安装：composer install，安装教程点击<a href="https://blog.gougucms.com/home/book/detail/bid/3/id/8.html" target="_blank">这里</a>。';
    exit;
}
require __DIR__ . '/../vendor/autoload.php';

// 定义当前版本号
define('CMS_VERSION','2.0.9');

// 定义ThinkPHP版本号
define('TP_VERSION','6.0.9');

// 定义Layui版本号
define('LAYUI_VERSION','2.6.8');

// 定义项目目录
define('CMS_ROOT', __DIR__ . '/../');

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
