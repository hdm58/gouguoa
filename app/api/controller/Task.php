<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\exception\ValidateException;
use think\facade\Db;

class Task
{
 	//定时发送到期证书提醒
    public function email_excel()
    {
		$today = date('Y-m-d');
		$start_time = strtotime($today);
		$end_time = $start_time+(3600*24*90);
		$list = Db::name('Cert')->where([['end_time','between',[$start_time,$end_time]],['delete_time','=',0]])->select()->toArray();
		//var_dump($list);exit();
		//$send = send_email($sender, $title, '<pre style="font-family: inherit;">'. $content .'</pre>');
		$content = '早上好，这是'.date('Y-m-d').'项目列表和任务列表，请查看附件。';
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$email_config = Db::name('config')->where('name', 'email')->find();
		$config = unserialize($email_config['content']);
        //所有项目必须填写
        if (empty($config['smtp']) || empty($config['smtp_port']) || empty($config['smtp_user']) || empty($config['smtp_pwd'])) {
            return to_assign(1, '请完善邮件配置信息');
        }
		
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
		
		$filePath = app()->getRootPath() . 'public/勾股OA产品模板.xlsx';
		if (file_exists($filePath)) {
			$mail->addAttachment($filePath, '测试文件.xlsx');
		} else {
			throw new Exception('附件文件不存在: ' . $filePath);
		}
		
		$status = $mail->send();
		if ($status) {
			return to_assign(0, '执行成功');
		} else {
			//  echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息
			//  die;
			return to_assign(1, '执行失败');
		}	
    }
	
}
