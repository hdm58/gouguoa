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

namespace app\install\validate;

use think\Validate;

class InstallCheck extends Validate
{
    protected $rule = [
        'DB_TYPE' => 'require|eq:mysql',
        'DB_HOST' => 'require',
        'DB_PORT' => 'require',
        'DB_USER' => 'require',
        'DB_PWD' => 'require',
        'DB_NAME' => 'require',
        'DB_PREFIX' => 'require',
        'username' => 'require',
        'password' => 'require|confirm',
    ];

    protected $message = [
        'DB_TYPE.require' => '数据库类型不能为空',
        'DB_TYPE.eq' => '数据库类型固定为mysql',
        'DB_HOST.require' => '数据库地址不能为空',
        'DB_PORT.require' => '数据库端口不能为空',
        'DB_USER.require' => '数据库用户名不能为空',
        'DB_PWD.require' => '数据库密码不能为空',
        'DB_NAME.require' => '数据库名字不能为空',
        'DB_PREFIX.require' => '表前缀不能为空',
        'username.require' => '管理员账户不能为空',
        'password.require' => '密码不能为空',
        'password.confirm' => '两次密码不一致',
    ];

    protected $scene = [

    ];
}
