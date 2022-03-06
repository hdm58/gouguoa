<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

// 这是系统自动生成的middleware定义文件
return [
    //开启session中间件
    //'think\middleware\SessionInit',
    //验证勾股OA是否完成安装
    \app\home\middleware\Install::class,
];
