<?php
// 这是系统自动生成的middleware定义文件
return [
	//开启session中间件
	//'think\middleware\SessionInit',
	//验证勾股cms是否完成安装
	\app\home\middleware\Install::class,
];