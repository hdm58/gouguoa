<?php
namespace app\listener;
use think\facade\Db;
use think\facade\Cache;
class SendMessage
{
	/**
	 * 发送系统模板消息
	 * @param  $user_id 接收人
	 * @param  $template_id 消息模板
	 * @param  $data 操作内容
	 * @return
	 */
	public function handle($msg=[])
	{
		try {
			// 可能会抛出异常的代码
			if(is_numeric($msg['template_id'])){
				$template = Db::name('Template')->where('id',$msg['template_id'])->find();
			}
			else{
				$template = Db::name('Template')->where('name',$msg['template_id'])->find();
			}
			if(empty($template)){
				return true;
			}
			if(empty($msg['template_field'])){
				$msg['template_field'] = 0;
			}
			$title_field = 'msg_title_'.$msg['template_field'];
			$content_field = 'msg_content_'.$msg['template_field'];
			$title = $template[$title_field];
			$content = $template[$content_field];
			$msg_link = $template['msg_link'];
			$data = $msg['content'];
			$data['from_user'] = Db::name('Admin')->where('id',$msg['from_uid'])->value('name');
			$data['date'] = date('Y-m-d');
			if(!isset($data['action_id'])){
				$data['action_id'] = 0;
			}
			foreach ($data as $key => $val) {
				$title = str_replace('{' . $key . '}', (string)$val, $title);
				$content = str_replace('{' . $key . '}', (string)$val, $content);
			}
			$wxmsg_link ='';
			if(!empty($msg_link)){
				$wxmsg_link = str_replace('{action_id}', $data['action_id'], $msg_link);
			}
			if (!is_array($msg['to_uids'])) {
				$users = explode(",", strval($msg['to_uids']));
			} else {
				$users = $msg['to_uids'];
			}
			$users = array_unique(array_filter($users));
			//组合要发的消息
			$send_data = [];
			foreach ($users as $key => $value) {
				$send_data[] = array(
					'to_uid' => $value,//接收人
					'action_id' => $data['action_id'],
					'title' => $title,
					'content' => $content,
					'template' => $template['id'],
					'create_time' => time()
				);
			}
			$res = Db::name('Msg')->strict(false)->field(true)->insertAll($send_data);
			$this->qiyeMessage($users,$title,$content,$wxmsg_link);
			$this->dingdingMessage($users,$title,$content,$wxmsg_link);
		} catch (\Exception $e) {
			// 处理异常，记录日志或者其他逻辑
			// 但不要抛出异常，以免中断主程序流程
		}
	}
	
	
	//获取access_token
	function get_access_token($corpid,$corpsecret)
	{
	    $cacheKey = 'workchat_access_token';
        $token = Cache::get($cacheKey);
        if ($token) return $token;
        
		$url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corpid&corpsecret=$corpsecret";
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		$output=curl_exec($ch);

		curl_close($ch);
		$jsoninfo = json_decode($output,true);
		if (isset($jsoninfo["access_token"])) {
            // 官方有效期7200秒，这里缓存7000秒防止临界点失效
            Cache::set($cacheKey, $jsoninfo["access_token"], 7000);
            return $jsoninfo["access_token"];
        }
		return null;
	}

	function qiyeMessage($users,$title,$content,$msg_link)
	{	
		$workchat = get_config('workchat');
		if($workchat['workweixin']==false){
			return false;
		}
		$userid = Db::name('Admin')->where([['id','in',$users],['userid','<>','']])->column('userid');
		if (empty($userid)) {
		   return false;
		}
		$users = implode('|' ,$userid);
		$corpid=$workchat['corpid'];
		$corpsecret=$workchat['corpsecret'];
		$agentid=$workchat['agentid'];
		$host=$workchat['host'];
		//获取access_token
		$accesstoken=$this->get_access_token($corpid,$corpsecret);
		$url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$accesstoken";
		$time_str = date('Y年m月d日 H:i:s',time());
		$data = array(
			'touser' => $users,
			'agentid' => $agentid,
			'msgtype' => 'textcard',
			"textcard" => array(
				"title" => $title,
				"description" => "<div class=\"gray\">".$time_str."</div><div class=\"normal\">".$content."</div><div class=\"highlight\">点击【详情】可查看详细内容。</div>",
				"url" => $host.$msg_link,
				"btntxt"=>"详情"
			)
		);
		$res=curl_post($url,json_encode($data));
		$ret= json_decode($res,true);
		$errcode = $ret['errcode'];
		if ($errcode == 0) {
			return true;
		} else {
			return false;
			//echo '消息发送失败，错误码：' . $errcode;
		}
	}
	
	function dingdingMessage($users,$title,$content,$msg_link)
	{	
		$dingtalk = get_config('dingtalk');
		if($dingtalk['dingtalk']==false){
			return false;
		}
		$userid = Db::name('Admin')->where([['id','in',$users],['dingtalk_userid','<>','']])->column('dingtalk_userid');
		if (empty($userid)) {
		   return false;
		}
		$users = implode(',' ,$userid);
		$appKey = $dingtalk['appKey'];
		$appSecret = $dingtalk['appSecret'];
		$agentId = $dingtalk['agentId'];
		$host = $dingtalk['host'];
		//获取access_token
		$accessToken = $this->getAccessToken($appKey,$appSecret);
        if (!$accessToken) {
            return false;
        }
        $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token={$accessToken}";
		
		$msg = [
            'msgtype' => 'action_card',
            'action_card' => [
                'title' => $title,
                'markdown' => "### ".$content,
                'single_title' => '查看详情',
                'single_url' => $host.$msg_link
            ]
        ];
		
        $postData = [
            'agent_id'    => $agentId,
            'userid_list' => $users,
            'msg'         => $msg
        ];
        $res = $this->httpPostJson($url, $postData);
        $errcode = $res['errcode'];
		if ($errcode == 0) {
			return true;
		} else {
			return false;
			//echo '消息发送失败，错误码：' . $errcode;
		}
	}
	
	private function getAccessToken($appKey,$appSecret)
    {
        $cacheKey = 'dingtalk_access_token';
        $token = Cache::get($cacheKey);
        if ($token) return $token;

        $url = "https://oapi.dingtalk.com/gettoken?appkey={$appKey}&appsecret={$appSecret}";
        $res = json_decode(file_get_contents($url), true);

        if (isset($res['access_token'])) {
            // 官方有效期7200秒，这里缓存7000秒防止临界点失效
            Cache::set($cacheKey, $res['access_token'], 7000);
            return $res['access_token'];
        }
        return null;
    }

    /**
     * 封装 HTTP POST JSON 请求
     */
    private function httpPostJson($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: ['errcode' => -1, 'errmsg' => '请求钉钉接口失败'];
    }
}