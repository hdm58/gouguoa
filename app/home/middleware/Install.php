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

namespace app\home\middleware;
use systematic\Systematic;
use think\facade\Db;
use think\facade\Session;

class Install
{
	protected $module = '';
	protected $controller = '';
	protected $action = '';
    public function handle($request, \Closure $next)
    {
        if (!is_installed()) {
            return $request->isAjax() ? to_assign(1, '请先完成系统安装引导') : redirect((string) url('/install/index'));
        }
		$module = strtolower(app('http')->getName());
		$this->module = $module;
		$param = $request->param();
		$logtype = isset($param['logtype'])?$param['logtype']:'';
		$module_son = isset($param['module_son'])?$param['module_son']:'';
		$module_old = [];	
		if($module_son!='' && ($logtype != 'add' || $logtype != 'delete')){
			$module_old = Db::name($module_son)->where('id',$param['id'])->find();
		}
        //return $next($request);
        //获取响应,获取响应之前的代码为前置中间件，
		//do songthing
		//----------以上部分为前置中间件--------------
		
        $response=$next($request);
		
        //----------以下部分为后置中间件--------------
        //获取响应之后的为后置中间件的执行内容
		
		$this->controller = strtolower($request->controller());
		$this->action = strtolower($request->action());
		//获取返回的HTTPCode
		//$code = $response->getCode();
		$responseData = $response->getData();
		if(isset($responseData['code']) && $responseData['code'] == 0){
			if(isset($responseData['datas'])){
				$logData = $responseData['datas'];
			}
			else{
				$logData = $responseData['data'];
			}
			$log_conf = get_config('log');
			$type_action = $log_conf['type_action'];
			if(isset($logData['logtype']) && isset($logData['id']) && isset($type_action[$logData['logtype']])){
				$logData['type_title'] = $type_action[$logData['logtype']];
				$moduleLogData = [];
				if($module_son!='' && isset($log_conf[$this->module])){
					$log_conf_module = $log_conf[$this->module];
					$module_field_key = $log_conf_module[$module_son];
					$module_new = Db::name($module_son)->where('id',$logData['id'])->find();
					$param_id = $module_new['id'];
					$param_son_id = 0;
					if($module_son!=$this->module){
						$param_id = $module_new[$this->module.'_id'];
						$param_son_id = $module_new['id'];						
					}
					else{
						$module_son = '';
					}
					if(isset($module_field_key)){
						$moduleLogData = [
							'param_id'=>$param_id,
							'module_son'=>$module_son,
							'param_son_id'=>$param_son_id,
							'old'=>$module_old,
							'new'=>$module_new,
							'key'=>$module_field_key
						];
					}
				}			
				$this->addLog($logData,$moduleLogData);
			}
		}

        //这里回调本身返回response对象
        return $response;
    }
	/*
	public function end(\think\Response $response){
		
			dump($response->getData());
			exit;			
	
		//dump($response->header());
    }
	*/
	
	protected function addLog($logData = [],$moduleLogData=[])
	{		
		$session_admin = get_config('app.session_admin');
        $uid = Session::get($session_admin);
		$data = [
			'uid' => $uid,
			'type' => $logData['logtype'],
			'action' => $logData['type_title'],
			'param_id' => $logData['id'],
			'param' => json_encode($logData),
			'module' => $this->module,
			'controller' => $this->controller,
			'function' => $this->action,
			'ip' => app('request')->ip(),
			'create_time' => time(),
			'subject' => '系统'
		];
		if(isset($logData['subject']) && $logData['subject'] != ''){
			$data['subject'] = $logData['subject'];
		}
		else{
			$rule = $data['module'] . '/' . $data['controller'] . '/' . $data['function'];
			$rule_menu = Db::name('AdminRule')->where(array('src' => $rule))->find();
			if($rule_menu){
				$data['subject'] = $rule_menu['name'];
			}
		}
		Db::name('AdminLog')->strict(false)->field(true)->insert($data);
		if(!empty($moduleLogData)){
			$GOUGU = new Systematic();
			$GOUGU->moduleLog($uid,$logData['logtype'],$this->module,$moduleLogData);
		}
	}
}
