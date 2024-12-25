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

namespace app\crud\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Crud extends Command
{
    protected function configure()
    {
        $this->setName('auto crud')
            ->addOption('name', 'a', Option::VALUE_OPTIONAL, 'the name', null)
            ->addOption('module', 'm', Option::VALUE_OPTIONAL, 'the module name', null)
            ->addOption('table', 't', Option::VALUE_OPTIONAL, 'the table name', null)
            ->addOption('controller', 'c', Option::VALUE_OPTIONAL, 'the controller name', null)
            ->addOption('types', 'y', Option::VALUE_OPTIONAL, 'the types name', null)
        ->setDescription('auto make crud file');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = $input->getOption('name');
        if (!$name) {
            $output->error("请输入 -na 名称");
            exit;
        }
		
        $module = $input->getOption('module');
        if (!$module) {
            $output->error("请输入 -m 模块名");
            exit;
        }

		$table = $input->getOption('table');
        if (!$table) {
            $output->error("请输入 -t 表名");
            exit;
        }
		
        $controller = $input->getOption('controller');
        if (!$controller) {
            $output->error("请输入 -c 控制器名");
            exit;
        }
		
		$types = $input->getOption('types');
        if (!$types) {
            $types = 1;
        }

		$this->make($name,$module,$table,$controller,$types);
	
        $output->info($name . "crud make success");
    }
	
	public function make($name,$module,$model,$controller,$types)
    {
		!defined('DS') && define('DS', DIRECTORY_SEPARATOR);
		$crud_dir = CMS_ROOT . '\crud\\';
		if (!is_dir($crud_dir  . 'view')) {
            mkdir($crud_dir  . 'view', 0755, true);
        }		
		if (!is_dir($crud_dir  . 'controller')) {
            mkdir($crud_dir . 'controller', 0755, true);
        }		
		if (!is_dir($crud_dir  . 'model')) {
            mkdir($crud_dir  . 'model', 0755, true);
        }		
		if (!is_dir($crud_dir  . 'validate')) {
            mkdir($crud_dir  . 'validate', 0755, true);
        }		
		$Bmodel = ucfirst(camelize($model));
		$Bcontroller = ucfirst(camelize($controller));
		$crud = [
			['name'=>'add','path'=>'view/'. DS  .$controller,'filename'=>'add.html'],
			['name'=>'list','path'=>'view'. DS  .$controller,'filename'=>'datalist.html'],
			['name'=>'view','path'=>'view'. DS  .$controller,'filename'=>'view.html'],
			['name'=>'controller','path'=>'controller','filename'=>$Bcontroller.'.php'],
			['name'=>'model','path'=>'model','filename'=>$Bmodel.'.php'],
			['name'=>'validate','path'=>'validate','filename'=>$Bcontroller.'Validate.php'],
		];
		foreach($crud as $k => $v){	
			$tpl = dirname(__DIR__) . '/tpl/'.$types.'/'.$v['name'].'.tpl';
			
			$tplContent = file_get_contents($tpl);			
			$tplContent = str_replace('<module>', $module, $tplContent);
			$tplContent = str_replace('<controller>', $controller, $tplContent);
			$tplContent = str_replace('<Bcontroller>', $Bcontroller, $tplContent);
			$tplContent = str_replace('<model>', $model, $tplContent);
			$tplContent = str_replace('<Bmodel>', $Bmodel, $tplContent);
			$tplContent = str_replace('<name>', $name, $tplContent);
			if (!is_dir($crud_dir.$v["path"])) {
				mkdir($crud_dir.$v["path"], 0755, true);
			}
			file_put_contents($crud_dir.$v["path"].'\\'.$v["filename"], $tplContent);
		}
    }
}