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
namespace app\install\controller;

use app\install\validate\InstallCheck;
use mysqli;
use think\exception\ValidateException;
use think\facade\View;

class Index
{
    public function __construct()
    {
        // 检测是否安装过
        if (is_installed()) {
			if(Request()->action()!='step4'){
				echo '你已经安装过勾股OA系统！如需重新安装，请删除“config/install.lock”文件';
				die();
			}
        }
    }

    public function index()
    {
		View::assign('TP_VERSION',\think\facade\App::version());
        return view('step1');
    }

    public function step2()
    {
        if (class_exists('pdo')) {
            $data['pdo'] = 1;
        } else {
            $data['pdo'] = 0;
        }

        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = 1;
        } else {
            $data['pdo_mysql'] = 0;
        }

        if (extension_loaded('curl')) {
            $data['curl'] = 1;
        } else {
            $data['curl'] = 0;
        }

        if (ini_get('file_uploads')) {
            $data['upload_size'] = ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = 0;
        }

        if (function_exists('session_start')) {
            $data['session'] = 1;
        } else {
            $data['session'] = 0;
        }
        return view('', ['data' => $data]);
    }

    public function step3()
    {
        return view();
    }

    public function step4()
    {
		\think\facade\Cache::clear();
		$domain = get_system_config('web','domain');
        return view('', ['domain' => $domain]);
    }	
		
    public function install()
    {
        $data = get_params();
        try {
            validate(InstallCheck::class)->check($data);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return to_assign(1, $e->getError());
        }
        $dbName = $data['DB_NAME'];		
		//验证表是否存在		
		try {
			// 连接数据库
			$link = @new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}", $data['DB_USER'], $data['DB_PWD']);
		} catch (\Exception $e) {
			// 这是进行异常捕获,创建数据库
			$error = $e->getMessage();
			return to_assign(1, '数据库链接失败:' . $error);die;
		}

        // 获取错误信息
        $error = $link->connect_error;
        if (!is_null($error)) {
            // 转义防止和alert中的引号冲突
            $error = addslashes($error);
            return to_assign(1, '数据库链接失败:' . $error);die;
        }
        // 设置字符集
        $link->query("SET NAMES 'utf8mb4'");
		//验证表是否存在		
		try {
			// 这里是主体代码
			$isDB=$link->query('SHOW TABLES LIKE '."'".$dbName."'");
			if(!$isDB){
				//创建数据库并选中
				$sql    = "CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4";
				$link->query($sql);
			}
		} catch (\Exception $e) {
			// 这是进行异常捕获,创建数据库并选中
			$error = $e->getMessage();
			$sql    = "CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4";
            $link->query($sql);
		}
		
        $link->select_db($dbName);
        // 导入sql数据并创建表
        $oa_sql = file_get_contents(CMS_ROOT . '/app/install/data/gouguoa.sql');
        $sql_array = preg_split("/;[\r\n]+/", str_replace("oa_", $data['DB_PREFIX'], $oa_sql));
        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {				
				try {
					//var_dump($v);
					$link->query($v);
				} catch (\Exception $e) {
					return to_assign(1, '数据写入失败，请联系官方！');
					break;
				}
            }
        }
        $isTable = $link->query('SHOW TABLES LIKE "'.$data['DB_PREFIX'] . 'admin"');
        if(!$isTable){
            return to_assign(1, '创建数据库表失败，请检查是否有创建权限！');
        }

        //插入管理员信息
        $username = get_params('username');
        $password = get_params('password');
        $nickname = '超级员工';
        $name = '超级员工';
        $thumb = '/static/home/images/icon.png';
        $salt = set_salt(20);
        $password = set_password($password, $salt);
        $now_time = time();

        $create_admin_sql = "INSERT INTO " . $data['DB_PREFIX'] . "admin" .
            "(username,salt,pwd,name,nickname,did,position_id,sex,mobile,email,type,thumb,entry_time,create_time,update_time,auth_did)"
            . "VALUES "
            . "('$username','$salt','$password','$name','$nickname',1,1,1,'13800138000','gouguoa@gougucms.com',1,'$thumb','$now_time','$now_time','$now_time',10)";
        if (!$link->query($create_admin_sql)) {
            return to_assign(1, '创建管理员信息失败');
        }
        $link->close();
        $db_str = "<?php
return [
    // 默认使用的数据库连接配置
    'default'         => 'mysql',
    // 自定义时间查询规则
    'time_query_rule' => [],
    // 自动写入时间戳字段
    // true为自动识别类型 false关闭
    // 字符串则明确指定时间字段类型 支持 int timestamp datetime date
    'auto_timestamp'  => true,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'               =>  'mysql',
            // 服务器地址
            'hostname'           =>  '{$data['DB_HOST']}',
            // 数据库名
            'database'           =>  '{$data['DB_NAME']}',
            // 用户名
            'username'           =>  '{$data['DB_USER']}',
            // 密码
            'password'           =>  '{$data['DB_PWD']}',
            // 端口
            'hostport'           =>  '{$data['DB_PORT']}',
            // 数据库表前缀
            'prefix'             =>  '{$data['DB_PREFIX']}',
            // 数据库连接参数
            'params'          => [],
            // 数据库编码默认采用utf8mb4
            'charset'         => 'utf8mb4',
            // 数据库调试模式
            'debug'           => false,
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'          => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'     => false,
            // 读写分离后 主服务器数量
            'master_num'      => 1,
            // 指定从服务器序号
            'slave_no'        => '',
            // 是否严格检查字段是否存在
            'fields_strict'   => true,
            // 是否需要断线重连
            'break_reconnect' => false,
            // 监听SQL
            'trigger_sql'     => true,
            // 开启字段缓存
            'fields_cache'    => false,
        ],
    ],
];";

        // 创建数据库配置文件
        if (false == file_put_contents(CMS_ROOT . "config/database.php", $db_str)) {
            return to_assign(1, '创建数据库配置文件失败，请检查目录权限');
        }
        if (false == file_put_contents(CMS_ROOT . "config/install.lock", '勾股OA安装鉴定文件，请勿删除！！！！！此次安装时间为：' . date('Y-m-d H:i:s', time()))) {
            return to_assign(1, '创建安装鉴定文件失败，请检查目录权限');
        }
        return to_assign(0,'安装完成');
    }
}
