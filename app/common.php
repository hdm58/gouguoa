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

// 应用公共文件,内置主要的数据处理方法
use think\facade\Config;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Db;

/***************************************************系统相关*****************************************************/
//设置缓存
function set_cache($key='', $value='', $date = 86400)
{
    Cache::set($key, $value, $date);
}

//读取缓存
function get_cache($key)
{
    return Cache::get($key);
}

//清空缓存
function clear_cache($key)
{
    Cache::clear($key);
}

//读取文件配置
function get_config($key)
{
    return Config::get($key);
}

//读取系统配置
function get_system_config($name='', $key = '')
{
    $config = [];
    if (get_cache('system_config' . $name)) {
        $config = get_cache('system_config' . $name);
    } else {
        $conf = Db::name('config')->where('name', $name)->find();
        if (isset($conf['content'])) {
            $config = unserialize($conf['content']);
        }
        set_cache('system_config' . $name, $config);
    }
    if ($key == '') {
        return $config;
    } else {
        if (isset($config[$key])) {
            return $config[$key];
        }
		else{
			return '';
		}
    }
}

//设置系统配置
function set_system_config($name='', $key='', $value='')
{
    $config = [];
	$conf = Db::name('config')->where('name', $name)->find();
	if ($conf['content']) {
		$config = unserialize($conf['content']);
	}
	$config[$key] = $value;
	set_cache('system_config' . $name, $config);
	$content = serialize($config);
	Db::name('config')->where('name', $name)->update(['content'=>$content]);
}


//判断系统是否已安装
function is_installed()
{
    static $isInstalled;
    if (empty($isInstalled)) {
        $isInstalled = file_exists(CMS_ROOT . 'config/install.lock');
    }
    return $isInstalled;
}

//判断系统是否存在模板
function isTemplate($url='')
{
    static $isTemplate;
    if (empty($isTemplate)) {
        $isTemplate = file_exists(CMS_ROOT . 'app/'.$url);
    }
    return $isTemplate;
}

//判断模块是否禁用
function isModule($name)
{
	$map = [];
	$map[] = ['name', '=', $name];
	$map[] = ['status', '=', 1];
    $count = Db::name('AdminModule')->where($map)->count();
    return $count;
}

//获取某模块的某数据配置
function valueAuth($name,$conf)
{
	$map = [];
	$map[] = ['name', '=', $name];
    $val = Db::name('DataAuth')->where($map)->value($conf);
    return $val;
}

//是否是某模块的数据权限,>1即有权限,$uid,要鉴别的用户，$name模块名称，$conf权限类型(字段),
function isAuth($uid,$name,$conf)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', $name];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',$conf)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//判断是否是部门负责人,
function isLeader($uid = 0,$did='')
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['status','=',1];
	if(!empty($did)){
		$map[] = ['id','in',$did];
	}
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',leader_ids)")];
	$count = Db::name('Department')->where($map)->count();
	return $count;
}

//获取url参数,$key为空时返回所有参数数组
function get_params($key = "")
{
    return Request::instance()->param($key);
}

/**
 * 菜单节点权限判断
 * @rule String
 * @uid Int
 * @return bool
 */
function check_auth($rule, $uid)
{
    $auth_list = Cache::get('RulesSrc' . $uid);
    if (!in_array($rule, $auth_list)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 返回json数据，用于接口
 * @param    integer    $code
 * @param    string     $msg
 * @param    array      $data
 * @param    string     $url
 * @param    integer    $httpCode
 * @param    array      $header
 * @param    array      $options
 * @return   json
 */
function to_assign($code = 0, $msg = "操作成功", $data = [], $action = '', $url = '', $httpCode = 200, $header = [], $options = [])
{
    $res = ['code' => $code];
    $res['msg'] = $msg;
    $res['action'] = $action;
    $res['url'] = $url;
    if (is_object($data)) {
        $data = $data->toArray();
    }
    $res['data'] = $data;
    $response = \think\Response::create($res, "json", $httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

/**
 * 适配layui table数据列表的返回数据方法，用于接口
 * @param    integer    $code
 * @param    string     $msg
 * @param    array      $data
 * @param    integer    $httpCode
 * @param    array      $header
 * @param    array      $options
 * @return   json
 */
function table_assign($code = 0, $msg = '请求成功', $data = [],$totalRow = [], $httpCode = 200, $header = [], $options = [])
{
    $res['code'] = $code;
    $res['msg'] = $msg;
    $res['totalRow'] = $totalRow;
    if (is_object($data)) {
        $data = $data->toArray();
    }
    if (!empty($data['total'])) {
        $res['count'] = $data['total'];
    } else {
        $res['count'] = 0;
    }
    $res['data'] = $data['data'];
    $response = \think\Response::create($res, "json", $httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

//写入操作日主
function add_log($type, $param_id = 0, $param = [] ,$subject='')
{
	try {
		// 可能会抛出异常的代码
		$title = '操作';
		$session_admin = get_config('app.session_admin');
		$uid = \think\facade\Session::get($session_admin);
		$type_action = get_config('log.type_action');
		if($type_action[$type]){
			$title = $type_action[$type];
		}
		$data = [
			'uid' => $uid,
			'type' => $type,
			'action' => $title,
			'param_id' => $param_id,
			'param' => json_encode($param),
			'module' => strtolower(app('http')->getName()),
			'controller' => strtolower(app('request')->controller()),
			'function' => strtolower(app('request')->action()),
			'ip' => app('request')->ip(),
			'create_time' => time(),
			'subject' => 'OA系统'
		];
		if($subject!=''){
			$data['subject'] =$subject;
		}
		else{
			$rule = $data['module'] . '/' . $data['controller'] . '/' . $data['function'];
			$rule_menu = Db::name('AdminRule')->where(array('src' => $rule))->find();
			if($rule_menu){
				$data['subject'] = $rule_menu['name'];
			}
		}
		Db::name('AdminLog')->strict(false)->field(true)->insert($data);
	} catch (\Exception $e) {
	// 处理异常，记录日志或者其他逻辑
	// 但不要抛出异常，以免中断主程序流程
	}
}

//消息链接信息转换
function get_message_link($template_id,$action_id){
	$content='';
	$template = Db::name('Template')->where('id',$template_id)->find();
	$link = $template['msg_link'];
	if(!empty($link)){
		$content = str_replace('{action_id}', $action_id, $link);
	}
	return '<a class="side-a" data-href="'.$content.'">查看详情</a>';
}

function get_message_mobile($template_id,$action_id){
	$content='';
	$template = Db::name('Template')->where('id',$template_id)->find();
	$link = $template['msg_link'];
	if(!empty($link)){
		$content = str_replace('{action_id}', $action_id, $link);
	}
	return '<a class="side-a" href="'.$content.'">查看详情</a>';
}

/**
 * 邮件发送
 * @param $to    接收人
 * @param string $subject 邮件标题
 * @param string $content 邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function send_email($to, $subject = '', $content = '')
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $email_config = Db::name('config')->where('name', 'email')->find();
    $config = unserialize($email_config['content']);

    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->isSMTP();
    $mail->SMTPDebug = 0;

    //调试输出格式
    //$mail->Debugoutput = 'html';
    //smtp服务器
    $mail->Host = $config['smtp'];
    //端口 - likely to be 25, 465 or 587
    $mail->Port = $config['smtp_port'];
    if ($mail->Port == '465') {
        $mail->SMTPSecure = 'ssl'; // 使用安全协议
    }
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //发送邮箱
    $mail->Username = $config['smtp_user'];
    //密码
    $mail->Password = $config['smtp_pwd'];
    //Set who the message is to be sent from
    $mail->setFrom($config['email'], $config['from']);
    //回复地址
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //接收邮件方
    if (is_array($to)) {
        foreach ($to as $v) {
            $mail->addAddress($v);
        }
    } else {
        $mail->addAddress($to);
    }

    $mail->isHTML(true); // send as HTML
    //标题
    $mail->Subject = $subject;
    //HTML内容转换
    $mail->msgHTML($content);
    $status = $mail->send();
    if ($status) {
        return true;
    } else {
        //  echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息
        //  die;
        return false;
    }
}

/***************************************************员工相关*****************************************************/
//获取指定用户的信息
function get_admin($uid)
{
    $admin = Db::name('Admin')
    ->alias('a')
    ->field('a.*,d.title as department,p.title as position')
    ->leftJoin ('Department d ','d.id= a.did')
    ->leftJoin ('Position p ','p.id= a.position_id')
    ->where(['a.id' => $uid])
    ->find();
    return $admin;
}

//获取指定部门所有的员工信息(包含主部门、次要部门，如果son=1，则包含当前子部门)
function get_department_employee($did=0,$son=0)
{
	$admin_array = [];
	$department_array = [];
	$department_array[] = $did;
	if($son==1){
		$department_array = get_department_son($did);
		$admin_array = Db::name('DepartmentAdmin')->whereIn('department_id',$department_array)->column('admin_id');
	}
	else{
		$admin_array = Db::name('DepartmentAdmin')->where('department_id',$did)->column('admin_id');
	}
	
	$map1=[
		['id','in',$admin_array],
	];
	$map2=[
		['did','in',$department_array],
	];
	$where=[['id','>',1],['status','=',1]];
	$whereOr =[$map1,$map2];
	$admin = Db::name('Admin')
		->where(function ($query) use($whereOr) {
			if (!empty($whereOr)){
				$query->whereOr($whereOr);
			}
		})
		->where($where)->select()->toArray();
    return $admin;
}

/***************************************************部门相关*****************************************************/
//读取部门列表
function get_department()
{
    $department = Db::name('Department')->order('sort desc,id asc')->where(['status' => 1])->select()->toArray();
    return $department;
}

//读取部门名称
function get_department_name($dids)
{
	$departments = Db::name('Department')->where([['id','in',$dids],['status','=',1]])->column('title');
	if(!empty($departments)){
		return implode(',',$departments);
	}else{
		return '';
	}
}

//获取某部门的子部门id，$is_self是否包含自己
//输出部门数组，如:['1,2,3']
function get_department_son($did = 0, $is_self = 1)
{
    $department = get_department();
    $department_list = get_data_node($department, $did);
    $department_array = array_column($department_list, 'id');
    if ($is_self == 1) {
        //包括自己部门在内
        $department_array[] = $did;
    }
    return $department_array;
}

//读取某员工所在主部门的负责人（pid=1，上一级部门负责人）
//输出负责人数组，如:1,2,3
function get_department_leader($uid=0,$pid=0)
{
	$did = get_admin($uid)['did'];
	if($pid==1){
		$did = Db::name('Department')->where('id',$did)->value('pid');
	}
	$leader_ids = Db::name('Department')->where('id',$did)->value('leader_ids');
    return $leader_ids;
}

//获取某员工所在部门的顶级部门（如果默认顶级部门当做是分公司的，即是获取某员工所属分公司）
function get_department_top($did=0,$uid=0)
{
	if($uid>0){
		$did = get_admin($uid)['did'];
	}
	$top_id = $did;
	if($did>0){
		$pid = Db::name('Department')->where('id',$did)->value('pid');
		if($pid>0){
			$top_id = get_department_top($pid,0);
		}
	}
    return $top_id;
}


//获取某负责人所负责的部门的数据集(ids)
function get_leader_departments($uid = 0)
{
	$dids = Db::name('Admin')->where('id',$uid)->value('son_dids');
	if(empty($dids)){
		return [];
	}
	else{
		return explode(',',$dids);
	}
}

//获取某员工所能看的部门数据(dids)
function get_role_departments($uid = 0)
{
	$dids = Db::name('Admin')->where('id',$uid)->value('auth_dids');
	if(empty($dids)){
		return [];
	}
	else{
		return explode(',',$dids);
	}
}

/***************************************************审批相关*****************************************************/

//获取全部审批状态
function get_check_status()
{
	$check_status_array = ['待提交审批','审批中','审批通过','审批不通过','已撤回'];
	return $check_status_array;
}

//根据审批状态读取审批状态名称
function check_status_name($check_status=0)
{
	$check_status_array = get_check_status();
	return $check_status_array[$check_status];
}

//根据流程类型读取某部门某模块的审核流程
function get_cate_department_flows($cate=1,$department=0)
{
	$map1 = [];
	$map2 = [];
	$map1[] = ['status', '=', 1];
	$map1[] = ['flow_cate', '=', $cate];
	$map1[] = ['department_ids', '=', ''];

	$map2[] = ['status', '=', 1];
	$map2[] = ['flow_cate', '=', $cate];
	$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$department}',department_ids)")];

    $list = Db::name('Flow')->field('id,name,check_type')->whereOr([$map1,$map2])->order('id desc')->select()->toArray();
    return $list;
}

//根据流程所属模块读取某部门某模块的审核流程
function get_type_department_flows($type=6,$department=0)
{
	$map1 = [];
	$map2 = [];
	$map1[] = ['status', '=', 1];
	$map1[] = ['type', '=', $type];
	$map1[] = ['department_ids', '=', ''];

	$map2[] = ['status', '=', 1];
	$map2[] = ['type', '=', $type];
	$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$department}',department_ids)")];

    $list = Db::name('Flow')->field('id,name,check_type')->whereOr([$map1,$map2])->order('id desc')->select()->toArray();
    return $list;
}

/**
 * 初始化审批流程数据
 * @param  $flow_id 审批流程id
 * @param  $check_admin_ids 传入当前审批人ids，用于设置当前审批步骤的审批人
 * @param  $uid 传入当前登录用户id，用于查找其所在部门的负责人或者上一部门负责人
 * @return
 */
function set_flow($flow_id,$check_admin_ids,$uid)
{
    $flow_detail = Db::name('Flow')->where('id',$flow_id)->find();
    $check_type = $flow_detail['check_type'];
    $flow = unserialize($flow_detail['flow_list']);
    if ($check_type == 1) {
        if($flow[0]['flow_type'] == 1){
            //部门负责人
            $leader = get_department_leader($uid);
            if($leader == 0){
                return to_assign(1,'审批流程设置有问题：当前部门负责人还未设置，请联系HR或者管理员');
            }
            else{
                $check_admin_ids = $leader;
            }                        
        }
        else if($flow[0]['flow_type'] == 2){
            //上级部门负责人
            $leader = get_department_leader($uid,1);
            if($leader == 0){
                return to_assign(1,'审批流程设置有问题：上级部门负责人还未设置，请联系HR或者管理员');
            }
            else{
                $check_admin_ids = $leader;
            }
        }
        else{
            $check_admin_ids = $flow[0]['flow_uids'];
        }
    }
    else if ($check_type == 3) {
        $check_admin_ids = $flow[0]['flow_uids'];
    }
    $flow_data = array(
        'check_type' => $check_type,
        'flow' => $flow,
        'check_admin_ids' => $check_admin_ids
    );
    return $flow_data;
}

/**
 * 获取审批流程数据
 * @param  $uid 当前登录用户
 * @param  $flows 当前步骤内容
 * @return
 */
function get_flow($uid,$flows)
{
    $check_user = '';
    $check_user_ids = [];
    if($flows['flow_type']==1){
        $check_user = '部门负责人-';                
        $check_user_ids[]=get_department_leader($uid);
    }
    else if($flows['flow_type']==2){
        $check_user = '上级部门负责人-';
        $check_user_ids[]=get_department_leader($uid,1);
    }
    else{
        $check_user_ids = explode(',',$flows['flow_uids']);        
    }
    $check_user_array = Db::name('Admin')->where('id','in',$check_user_ids)->column('name');
    $res = array(
        'check_user' => $check_user.implode(',',$check_user_array),
        'check_user_ids' => $check_user_ids
    );
    return $res;
}

/**
 * 获取审批记录数据
 * @param  $check_table 关联内容表
 * @param  $action_id 关联内容记录id
 * @return
 */
function get_check_record($check_table,$action_id)
{
	$check_record = Db::name('FlowRecord')
					->field('f.*,a.name')
					->alias('f')->join('Admin a', 'a.id = f.check_uid', 'left')
					->where([['f.check_table','=',$check_table],['f.action_id','=',$action_id],['f.delete_time','=',0]])
					->select()->toArray();				
	foreach ($check_record as $kk => &$vv) {		
		$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
		$vv['status_str'] = '提交申请';
		if($vv['check_status'] == 1){
			$vv['status_str'] = '审核通过';
		}
		else if($vv['check_status'] == 2){
			$vv['status_str'] = '审核拒绝';
		}
		if($vv['check_status'] == 3){
			$vv['status_str'] = '撤销申请';
		}
		if($vv['check_status'] == 4){
			$vv['status_str'] = '反确认';
		}
	}
	return $check_record;
}

/***************************************************常规数据获取*****************************************************/
//读取基础数据
function get_base_data($table)
{
    $data = Db::name($table)->where(['status' => 1])->select()->toArray();
    return $data;
}

//读取模块基础数据
function get_base_type_data($table,$type)
{
    $data = Db::name($table)->where(['status' => 1,'types' => $type])->select()->toArray();
    return $data;
}

//读取所属地区
function get_region_name($id){
    $region = Db::name('city')->where(['id'=>$id])->find();
	if(empty($region)){
		return '';
	}
	else{
		return $region['name'];
	}
}

//读取分类子分类ids,返回id数组
function get_cate_son($table='',$id=0,$is_self = 1)
{
    $cate = Db::name($table)->order('id desc')->select()->toArray();
    $cate_list = get_data_node($cate, $id);
    $ids_array = array_column($cate_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $ids_array[] = $id;
    }
    return $ids_array;
}

/**
 * 根据附件表的id返回url地址
 * @param  [type] $id [description]
 */
function get_file($id)
{
    if ($id) {
        $geturl = Db::name("file")->where(['id' => $id])->find();
        if ($geturl['status'] == 1) {
            //审核通过
            //获取签名的URL
            $url = $geturl['filepath'];
            return $url;
        } elseif ($geturl['status'] == 0) {
            //待审核
            return '/static/home/images/none_pic.jpg';
        } else {
            //不通过
            return '/static/home/images/none_pic.jpg';
        }
    }
    return false;
}

/***************************************************工具函数相关*****************************************************/

//生成一个不会重复的字符串
function make_token()
{
    $str = md5(uniqid(md5(microtime(true)), true));
    $str = sha1($str); //加密
    return $str;
}

//随机字符串，默认长度10
function set_salt($num = 10)
{
    $str = 'qwertyuiopasdfghjklzxcvbnm1234567890';
    $salt = substr(str_shuffle($str), 10, $num);
    return $salt;
}
//密码加密
function set_password($pwd, $salt)
{
    return md5(md5($pwd . $salt) . $salt);
}

/**
 * 生成时间编号
 * $prefix前缀
 */
function get_codeno($prefix=1){
    $no = $prefix . date('YmdHis') . rand(1,9);
    return $no;
}

/**
 * 去除空格
 * @param string $str 字符串
 * @return string     字符串
 */
function trim_space($str=''){
	$str = mb_ereg_replace('^(　| )+', '', $str);
	$str = mb_ereg_replace('(　| )+$', '', $str);
	return mb_ereg_replace('　　', "\n　　", $str);
}

/**
 * 隐藏电话号码中间4位和邮箱
 */
function hidetel($phone)
{
	//隐藏邮箱
	if (strpos($phone, '@')) {
		$email_array = explode("@", $phone);
		$prevfix = (strlen($email_array[0]) < 4) ? "" : substr($phone, 0, 3); //邮箱前缀
		$count = 0;
		$str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $phone, -1, $count);
		$rs = $prevfix . $str;
		return $rs;
	} else {
		//隐藏联系方式中间4位
		$Istelephone = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
		if ($Istelephone) {
			return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
		} else {
			return preg_replace('/(1[0-9]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
		}
	}
}

/**
 * 间隔时间段格式化
 * @param int $time 时间戳
 * @param string $format 格式 【d：显示到天 i显示到分钟 s显示到秒】
 * @return string
 */
function time_trans($time, $format = 'd')
{
	$now = time();
	if (!is_numeric($time)) {
		$time = strtotime($time);
	}
	$diff = $now - $time;
	if ($diff < 60) {
		return '1分钟前';
	} else if ($diff < 3600) {
		return floor($diff / 60) . '分钟前';
	} else if ($diff < 86400) {
		return floor($diff / 3600) . '小时前';
	}
	$yes_start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-1 days'))); //昨天开始时间
	$yes_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 days'))); //昨天结束时间
	$two_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-2 days'))); //2天前结束时间
	$three_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-3 days'))); //3天前结束时间
	$four_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-4 days'))); //4天前结束时间
	$five_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-5 days'))); //5天前结束时间
	$six_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-6 days'))); //6天前结束时间
	$seven_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-7 days'))); //7天前结束时间

	if ($time > $yes_start_time && $time < $yes_end_time) {
		return '昨天';
	}

	if ($time > $yes_start_time && $time < $two_end_time) {
		return '1天前';
	}

	if ($time > $yes_start_time && $time < $three_end_time) {
		return '2天前';
	}

	if ($time > $yes_start_time && $time < $four_end_time) {
		return '3天前';
	}

	if ($time > $yes_start_time && $time < $five_end_time) {
		return '4天前';
	}

	if ($time > $yes_start_time && $time < $six_end_time) {
		return '5天前';
	}

	if ($time > $yes_start_time && $time < $seven_end_time) {
		return '6天前';
	}

	switch ($format) {
		case 'd':
			$show_time = date('Y-m-d', $time);
			break;
		case 'i':
			$show_time = date('Y-m-d H:i', $time);
			break;
		case 's':
			$show_time = date('Y-m-d H:i:s', $time);
			break;
	}
	return $show_time;
}

/**
 * 时间戳格式化
 * @param int    $time
 * @param string $format 默认'Y-m-d H:i:s'
 * @return string 完整的时间显示
 */
function to_date($time = 0, $format = 'Y-m-d H:i:s')
{
	if(empty($time)){
		return '';
	}
	else{
		if (is_numeric($time)) {
			return date($format, intval($time));
		}
		else{
			return $time;
		}
	}
}

/**
 * 计算按相差天数
 */
function count_days($a=0, $b = 0)
{
	if ($b == 0) {
		$b = date("Y-m-d");
	}
	$date_1 = $a;
	$date_2 = $b;
	$d1 = strtotime($date_1);
	$d2 = strtotime($date_2);
	$days = round(($d2 - $d1) / 3600 / 24);
	if ($days > 0) {
		return $days;
	} else {
		return 0;
	}
}

/**
 * @Method: 文件大小格式化
 * @param[type] $file_size [文件大小]
 */
function to_size($file_size){
	$file_size = $file_size-1;
	if ($file_size >= 1099511627776){
		$show_filesize = number_format(($file_size / 1099511627776),2) . " TB";
	}
	elseif ($file_size >= 1073741824) {
		$show_filesize = number_format(($file_size / 1073741824),2) . " GB";
	}
	elseif ($file_size >= 1048576) {
		$show_filesize = number_format(($file_size / 1048576),2) . " MB";
	}
	elseif ($file_size >= 1024) {
		$show_filesize = number_format(($file_size / 1024),2) . " KB";
	}
	elseif ($file_size > 0) {
		$show_filesize = $file_size . " b";
	}
	elseif ($file_size == 0 || $file_size == -1) {
		$show_filesize = "0 b";
	}
	return $show_filesize;
}

//格式化附件展示
function file_card($file,$view=''){
	if(empty($file['file_id'])){
		$file['file_id'] = $file['id'];
	}
	$image=['jpg','jpeg','png','gif'];
	$office=['doc','docx','xls','xlsx','ppt','pptx'];
	$type_icon = 'icon-xiangmuguanli';
	$type = 0;//0下载+重命名+删除，1下载+查看+重命名+删除，2下载+查看+编辑+重命名+删除
	$ext = 'zip';
	$view_btn = '<a class="blue" href="'.$file['filepath'].'" download="'.$file['name'].'" target="_blank" title="下载"><i class="iconfont icon-xiazai"></i></a>';
	
	if($file['fileext'] == 'pdf'){
		$type_icon = 'icon-kejian';
		$ext = 'pdf';
		$type = 1;
	}
	if(in_array($file['fileext'], $image)){
		$type_icon = 'icon-sucaiguanli';
		$ext = 'image';
		$type = 1;
	}
	if(in_array($file['fileext'], $office)){
		$type_icon = 'icon-shenbao';
		$ext = 'office';
		$type = 2;
	}	
	
	if(empty($view)){
		$view_btn = '<span class="file-ctrl blue" data-ctrl="edit" data-type="'.$type.'" data-fileid="'.$file['file_id'].'" data-ext="'.$ext.'" data-filename="'.$file['name'].'" data-href="'.$file['filepath'].'" data-id="'.$file['id'].'" data-uid="'.$file['admin_id'].'" title="附件操作"><i class="iconfont icon-gengduo1"></i></span><span class="name-edit green" style="display:none;" data-id="'.$file['id'].'" data-fileid="'.$file['file_id'].'" id="fileEdit'.$file['file_id'].'" data-name="'.$file['name'].'" data-fileext="'.$file['fileext'].'" title="重命名"></span><span class="file-delete red" style="display:none;" data-uid="'.$file['admin_id'].'" data-id="'.$file['id'].'" data-fileid="'.$file['file_id'].'" id="fileDel'.$file['file_id'].'" title="删除"></span>';
	}
	else{
		$view_btn = '<span class="file-ctrl blue" data-ctrl="view" data-type="'.$type.'" data-fileid="'.$file['file_id'].'" data-ext="'.$ext.'" data-filename="'.$file['name'].'" data-href="'.$file['filepath'].'" title="附件操作"><i class="iconfont icon-gengduo1"></i></span>';
	}
	
	$file_del='';
	if(!empty($file['delete_time'])){
		$file_del = 'file-hasdelete';
	}
	$item = '<div class="file-card '.$file_del.'" id="fileItem'.$file['file_id'].'">
		<i class="file-icon iconfont '.$type_icon.'"></i>
		<div class="file-info">
			<div class="file-title" title="'.$file['name'].'">'.$file['name'].'</div>
			<div class="file-ops">'.to_size($file['filesize']).'，'.date('Y-m-d H:i',$file['create_time']).'</div>
		</div>
		<div class="file-tool">'.$view_btn.'</div>
	</div>';
	return $item;
}

//格式化附件展示
function file_item($file,$view=''){
	$image=['jpg','jpeg','png','gif'];
    $fileshow='<div class="mbui-file-icon"><i class="iconfont icon-weizhigeshi"></i></div>';
	if($file['fileext'] == 'pdf'){

	}
	if(in_array($file['fileext'], $image)){
		$fileshow='<div class="mbui-file-icon file-img"><img src="'.$file['filepath'].'" alt="'.$file['name'].'"></div>';
	}
	$file_del='';
	if(empty($view)){
		$file_del = '<div class="mbui-file-del"><i class="iconfont icon-cuowukongxin"></i></div>';
	}
	$filesize = to_size($file['filesize']);
	$filedate = date('Y-m-d H:i',$file['create_time']);
	$item = '<div class="mbui-file-div"data-id="'.$file['id'].'">
				'.$fileshow.'
				<div class="mbui-file-info">
					<div class="mbui-file-name line-limit-1">'.$file['name'].'</div>
					<div class="mbui-file-size">'.$filesize.'，'.$filedate.'</div>
				</div>
				'.$file_del.'
			</div>';
	return $item;
}

/**
 * fullcalendar日历控件方法1
 */
function parseDateTime($string, $timeZone=null) {
  $date = new DateTime(
	$string,
	$timeZone ? $timeZone : new DateTimeZone('UTC')
  );
  if ($timeZone) {
	$date->setTimezone($timeZone);
  }
  return $date;
}

/**
 * fullcalendar日历控件方法2
 */
function stripTime($datetime) {
  return new DateTime($datetime->format('Y-m-d'));
}
/**
 * 截取文章摘要
 *  @return bool
 */
function get_desc_content($content, $count)
{
    $content = preg_replace("@<script(.*?)</script>@is", "", $content);
    $content = preg_replace("@<iframe(.*?)</iframe>@is", "", $content);
    $content = preg_replace("@<style(.*?)</style>@is", "", $content);
    $content = preg_replace("@<(.*?)>@is", "", $content);
    $content = str_replace(PHP_EOL, '', $content);
    $space = array(" ", "　", "  ", " ", " ");
    $go_away = array("", "", "", "", "");
    $content = str_replace($space, $go_away, $content);
    $res = mb_substr($content, 0, $count, 'UTF-8');
    if (mb_strlen($content, 'UTF-8') > $count) {
        $res = $res . "...";
    }
    return $res;
}

/**
 * 二维数组排序
 * @param $array 要进行排序的select结果集
 * @param $field  排序的字段
 * @param $order 排序方式1降序2升序
 */
function sort_array($array = [], $field='', $order = 1)
{
    $count = count($select);
    if ($order == 1) {
        for ($i = 0; $i < $count; $i++) {
            $k = $i;
            for ($j = $i; $j < $count; $j++) {
                if ($select[$k][$field] < $select[$j][$field]) {
                    $k = $j;
                }
            }
            $temp = $select[$i];
            $select[$i] = $select[$k];
            $select[$k] = $temp;
        }
        return $select;
    } else {
        for ($i = 0; $i < $count; $i++) {
            $k = $i;
            for ($j = $i; $j < $count; $j++) {
                if ($select[$k][$field] > $select[$j][$field]) {
                    $k = $j;
                }
            }
            $temp = $select[$i];
            $select[$i] = $select[$k];
            $select[$k] = $temp;
        }
        return $select;
    }
}

//查找数组索引,支持一维数组，二维数组查找
function array_search_plus($array, $searchFor) {
    foreach($array as $key => $value) {
		if(is_array($value)){
			foreach($value as $key1 => $value1) {
				if($value1 == $searchFor) {
					return array("index" => $key, "key" => $key1);
				}
			}
		}
		else{
			if($value == $searchFor) {
				return $key;
			}
		}
    } 
    return false;
}

//根据数据库查询出来二维数组获取某个字段拼接字符串
function split_array_field($array = [], $field = '',$separator=',')
{
	$str='';
    if (is_array($array)) {
		if($field){
			$ary = array();
			foreach ($array as $value) {
				$ary[] = $value[$field];
			}
		}
		else{
			$ary = $array;
		}
        $str = implode($separator, $ary);
    }
	return $str;
}

//数组转换字符串
function array_to_string($array=[],$separator=',')
{
    if (!is_array($array)) {
        $data_arr[] = $array;
    } else {
        $data_arr = $array;
    }
    $data_arr = array_filter($data_arr); //数组去空
    $data_arr = array_unique($data_arr); //数组去重
    $data_arr = array_merge($data_arr);
    $string = $data_arr ? ',' . implode($separator, $data_arr) . ',' : '';
    return $string ?: '';
}

//字符串转换数组
function string_to_array($string='',$separator=',')
{
    if (is_array($string)) {
        $data_arr = array_unique(array_filter($string));
    } else {
        $data_arr = $string ? array_unique(array_filter(explode($separator, $string))) : [];
    }
    $data_arr = $data_arr ? array_merge($data_arr) : [];
    return $data_arr ?: [];
}

/**
 * 格式化字节大小
 * @param number $size      字节数
 * @param string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 截取字符串
 * @param $start 开始截取位置
 * @param $length 截取长度
 * @return
 */
function msubstr($str='', $start = 0, $length=1, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if (utf8_strlen($str) < $length) $suffix = false;
    return $suffix ? $slice . '...' : $slice;
}

//计算字符串长度
function utf8_strlen($string = null)
{
    preg_match_all("/./us", $string, $match);
    return count($match[0]);
}

/**
 * 截取文字长度
 * @return string
 */
function sub_str($str,$len=20){
    $strlen=strlen($str)/3;#在编码utf8下计算字符串的长度，并把它交给变量$strlen
    if($strlen<$len){
        return $str;
    }else{
        return mb_substr($str,0,$len,"utf-8")."...";
    }
}

/*
* 下划线转驼峰
* 思路:
* step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
* step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
*/
function camelize($uncamelized_words,$separator='_')
{
	$uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
	return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
}

/**
* 驼峰命名转下划线命名
* 思路:
* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
*/
function uncamelize($camelCaps,$separator='_')
{
	return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

/**
 *数据处理成树形格式1
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'list', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[$data[$pk]] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][$data[$pk]] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 *数据处理成树形格式2
 * @return array
 */
function create_tree_list($pid, $arr, $group, &$tree = [])
{
    foreach ($arr as $key => $vo) {
        if ($key == 0) {
            $vo['spread'] = true;
        }
        if (!empty($group) and in_array($vo['id'], $group)) {
            $vo['checked'] = true;
        } else {
            $vo['checked'] = false;
        }
        if ($vo['pid'] == $pid) {
            $child = create_tree_list($vo['id'], $arr, $group);
            if ($child) {
                $vo['children'] = $child;
                $vo['len'] = count($child);
            }
			else{
				$vo['len'] = 0;
			}
            $tree[] = $vo;
        }
    }
    return $tree;
}


//递归排序，用于分类选择
function set_recursion($result, $pid = 0, $level=-1)
{
    /*记录排序后的类别数组*/
    static $list = array();
    static $space = ['','├─','§§├─','§§§§├─','§§§§§§├─'];
	$level++;
    foreach ($result as $k => $v) {
        if ($v['pid'] == $pid) {
            if ($pid != 0) {
                $v['title'] = $space[$level] . $v['title'];
                $v['level'] = $level+1;
            }
            /*将该类别的数据放入list中*/
            $list[] = $v;
            set_recursion($result, $v['id'],$level);
        }
    }
    return $list;
}


//递归返回树形菜单数据
function get_tree($data, $pid = 0, $level = 1, $open = 1, $first = 0) {
    $tree = array();
    foreach ($data as $item) {
		$item['checkArr']=array('type'=>0, 'isChecked'=>0);
		$item['spread']=false;
		if($level<=$open){
			$item['spread']=true;
		}
		$item['level']=$level;
        if ($item['pid'] == $pid) {
            $children = get_tree($data, $item['id'],$level+1,$open);
            if ($children) {
                $item['children'] = $children;
            }
            $tree[] = $item;
        }
    }
	if($first==1&&!empty($tree)){
		$tree[0]['spread']=true;
	}
    return $tree;
}

//递归返回树形菜单数据
function get_select_tree($data, $pid ,$deep=0, $selected=[])
{
	$tree = [];		
	foreach($data as $k => $v)
	{	
		$vv=[];
		$vv['name']=$v['title'];	
		$vv['value']=$v['id'];
		$vv['selected']='';
		if(in_array($v['id'],$selected)){
			$vv['selected'] = 'selected';
		}
		if($v['pid'] == $pid){ 
		//父亲找到儿子
		$deep++;
		$vv['children'] = get_select_tree($data, $v['id'],$deep,$selected);
		$tree[] = $vv;
	   }
	}
	return array_values($tree);
}

/**
 * 根据id递归返回子数据
 * @param  $data 数据
 * @param  $pid 父节点id
 */
function get_data_node($data=[],$pid=0){
	$dep = [];		
	foreach($data as $k => $v){			
		if($v['pid'] == $pid){
			$node=get_data_node($data, $v['id']);
			array_push($dep,$v);
			if(!empty($node)){					
				$dep=array_merge($dep,$node);
			}
		}   	
	}
	return array_values($dep);
}


function generateTree($flatArray, $parentId = 0) {
  $tree = [];
  foreach ($flatArray as $item) {
    if ($item['pid'] === $parentId) {
      $node = $item;
	  $node['children'] = generateTree($flatArray, $item['id']);
      $tree[] = $node;
    }
  }
  return $tree;
}

//访问按小时归档统计
function hour_document($arrData)
{
    $documents = array();
    $hour = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23];
    foreach ($hour as $val) {
        $documents[$val] = 0;
    }
    foreach ($arrData as $index => $value) {
        $archivesTime = intval(date("H", $value['create_time']));
        $documents[$archivesTime] += 1;
    }
    return $documents;
}

//访问按日期归档统计
function date_document($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $archivesTime = date("Y-m-d", $value['create_time']);
        if (empty($documents[$archivesTime])) {
            $documents[$archivesTime] = 1;
        } else {
            $documents[$archivesTime] += 1;
        }
    }
    return $documents;
}

/**
 * 人民币转大写
 * @param
 */
function cny($amount){
	$capitalNumbers = [
        '零',
        '壹',
        '贰',
        '叁',
        '肆',
        '伍',
        '陆',
        '柒',
        '捌',
        '玖',
    ];

    $integerUnits = ['', '拾', '佰', '仟',];
    $placeUnits = ['', '万', '亿', '兆',];
    $decimalUnits = ['角', '分', '厘', '毫',];
    $result = [];
    $arr = explode('.', (string)$amount);
    $integer = trim($arr[0] ?? '', '-');
    $decimal = $arr[1] ?? '';
    if (!((int)$decimal)) {
        $decimal = '';
    }
    // 转换整数部分,从个位开始
    $integerNumbers = $integer ? array_reverse(str_split($integer)) : [];
    $last = null;
    foreach (array_chunk($integerNumbers, 4) as $chunkKey => $chunk) {
        if (!((int)implode('', $chunk))) {
            // 全是 0 则直接跳过
            continue;
        }
        array_unshift($result, $placeUnits[$chunkKey]);
        foreach ($chunk as $key => $number) {
            // 去除重复 零，以及第一位的 零，类似：1002、110
            if (!$number && (!$last || $key === 0)) {
                $last = $number;
                continue;
            }
            $last = $number;
            // 类似 1022，中间的 0 是不需要 佰 的
            if ($number) {
                array_unshift($result, $integerUnits[$key]);
            }
            array_unshift($result, $capitalNumbers[$number]);
        }
    }
    if (!$result) {
        $result[] = $capitalNumbers[0];
    }
    $result[] = '圆';
    if (!$decimal) {
        $result[] = '整';
    }
    // 转换小数位
    $decimalNumbers = $decimal ? str_split($decimal) : [];
    foreach ($decimalNumbers as $key => $number) {
        $result[] = $capitalNumbers[$number];
        $result[] = $decimalUnits[$key];
    }
    if (strpos((string)$amount, '-') === 0) {
        array_unshift($result, '负');
    }
    return implode('', $result);
}


/**
 * 金额展示规则,超过1万时以万为单位，低于1万时以千为单位，低于1千时以元为单位
 * @param string $money 金额
 * @return string
 */
function format_money($money)
{
    $data = '0元';
    if (($money / 10000) > 1) {
        $data = is_int($money / 10000) ? ($money / 10000) . '万' : round(($money / 10000), 2) . '万';
    } elseif (($money / 1000) > 1) {
        $data = is_int($money / 1000) ? ($money / 1000) . '千' : round(($money / 1000), 2) . '千';
    } else {
        $data = $money . '元';
    }
    return $data;
}

/***************************************************校验相关*****************************************************/
/**
 * 判断是否是手机浏览器
 *  @return bool
 */
function is_mobile()
{ 
    if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }elseif (strpos($_SERVER['HTTP_USER_AGENT'] , 'wxwork') !== false ) {
		return true;
	}else {
		return false;
	}
}

/**
 * 判断是否是微信浏览器
 *  @return bool
 */
function is_wechat(){
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
		return true;
	} else {
		return false;
	}
}

/**
 * 判断是否是企业微信浏览器
 *  @return bool
 */
function is_wxwork()
{ 
	if (strpos($_SERVER['HTTP_USER_AGENT'] , 'wxwork') !== false ) {
		return true;
	} else {
		return false;
	}
}

/**
 * 验证输入的邮件地址是否合法
 * @param $user_email 邮箱
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
        if (preg_match($chars, $user_email)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


/**
 * 获取客户浏览器类型
 */
function get_browser_name()
{
    $Browser = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE/i', $Browser)) {
        $Browser = 'MSIE';
    } elseif (preg_match('/Firefox/i', $Browser)) {
        $Browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $Browser)) {
        $Browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $Browser)) {
        $Browser = 'Safari';
    } elseif (preg_match('/Opera/i', $Browser)) {
        $Browser = 'Opera';
    } else {
        $Browser = 'Other';
    }
    return $Browser;
}

/**
 * 获取客户端系统
 */
function get_os_name()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/win/i', $agent)) {
        if (preg_match('/nt 6.1/i', $agent)) {
            $OS = 'Windows 7';
        } else if (preg_match('/nt 6.2/i', $agent)) {
            $OS = 'Windows 8';
        } else if (preg_match('/nt 10.0/i', $agent)) {
            $OS = 'Windows 10';
        } else {
            $OS = 'Windows';
        }
    } elseif (preg_match('/mac/i', $agent)) {
        $OS = 'MAC';
    } elseif (preg_match('/linux/i', $agent)) {
        $OS = 'Linux';
    } elseif (preg_match('/unix/i', $agent)) {
        $OS = 'Unix';
    } elseif (preg_match('/bsd/i', $agent)) {
        $OS = 'BSD';
    } else {
        $OS = 'Other';
    }
    return $OS;
}

/**
 * curl 模拟GET请求
 * @author lee
 ***/
function curl_get($url)
{
    //初始化
    $ch = curl_init();
    //设置抓取的url
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // https请求 不验证hosts
	curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);//添加这个获取请求头信息
    //执行命令
    $output = curl_exec($ch);
	$meta = curl_getinfo($ch,CURLINFO_HEADER_OUT);
	$accept = substr($meta,0,strpos($meta, 'Accept:'));
	$host = substr($accept,strpos($accept, 'Host:')+5);
    curl_close($ch); //释放curl句柄
    return $output;
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 */
function curl_post($url = '', $post = array())
{
	//$post['host'] = $_SERVER['HTTP_HOST'];
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $res = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $res; // 返回数据，json格式
}