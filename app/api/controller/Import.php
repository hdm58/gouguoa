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
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;
use app\user\model\Admin;
use app\customer\model\Customer;
use avatars\MDAvatars;
use Overtrue\Pinyin\Pinyin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as Shared;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class Import extends BaseController
{
    //生成头像
    public function to_avatars($char)
    {
        $defaultData = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'S', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            '零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '拾',
            '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        if (isset($char)) {
            $Char = $char;
        } else {
            $Char = $defaultData[mt_rand(0, count($defaultData) - 1)];
        }
        $OutputSize = min(512, empty($_GET['size']) ? 36 : intval($_GET['size']));

        $Avatar = new MDAvatars($Char, 256, 1);
        $avatar_name = '/avatars/avatar_256_' . set_salt(10) . time() . '.png';
        $path = get_config('filesystem.disks.public.url') . $avatar_name;
        $res = $Avatar->Save('.' . $path, 256);
        $Avatar->Free();
        return $path;
    }
	
    //登录名校验
    public function check_name($name,$arr)
    {
        if(in_array($name,$arr)){
			$name = $this->check_name($name.'1',$arr);
		}
		return $name;       
    }
	
	//导入员工
	public function import_admin(){
        // 获取表单上传文件
        $file[]= request()->file('file');
		if($this->uid>1){
			return to_assign(1,'该操作只能是超级管理员有权限操作');
		}
        try {
            // 验证文件大小，名称等是否正确
            validate(['file' => 'filesize:51200|fileExt:xls,xlsx'])->check($file);
			// 日期前綴
			 $dataPath = date('Ym');
			 $md5 = $file[0]->hash('md5');
			 $savename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file[0], function () use ($md5) {
				 return $md5;
			 });
            $fileExtendName = substr(strrchr($savename, '.'), 1);
            // 有Xls和Xlsx格式两种
            if ($fileExtendName == 'xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
            } else {
                $objReader = IOFactory::createReader('Xls');
            }
            $objReader->setReadDataOnly(TRUE);
			$path = get_config('filesystem.disks.public.url');
            // 读取文件，tp6默认上传的文件，在runtime的相应目录下，可根据实际情况自己更改
            $objPHPExcel = $objReader->load('.'.$path . '/' .$savename);
            $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();       // 取得总行数
            $highestColumn = $sheet->getHighestColumn();   // 取得总列数
            Coordinate::columnIndexFromString($highestColumn);
            $lines = $highestRow - 1;
            if ($lines <= 0) {
				return to_assign(1, '数据不能为空');
                exit();
            }
			$sex_array=['未知','男','女'];
			$type_array=['未知','正式','试用','实习'];
			$mobile_array = Db::name('Admin')->where([['status','>=',0]])->column('mobile');
			$email_array = Db::name('Admin')->where([['status','>=',0]])->column('email');
			$username_array = Db::name('Admin')->where([['status','>=',0]])->column('username');
			$department_array = Db::name('Department')->where(['status' => 1])->column('title', 'id');
			$position_array = Db::name('Position')->where(['status' => 1])->column('title', 'id');
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
            for ($j = 3; $j <= $highestRow; $j++) {
				$salt = set_salt(20);
				$reg_pwd  = '123456';
				$name = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
				if(empty($name)){
					continue;
				}
				$char = mb_substr($name, 0, 1, 'utf-8');
				$sex = array_search_plus($sex_array,$objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());
				$department = array_search_plus($department_array,$objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue());
				$position = array_search_plus($position_array,$objPHPExcel->getActiveSheet()->getCell("f" . $j)->getValue());
				$type = array_search_plus($type_array,$objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue());
				$username = Pinyin::name($name,'none')->join('');
				//$username = implode('-', $pinyinname);
				$mobile = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
				$email = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
				$file_check['mobile'] = $mobile;
				$file_check['email'] = $email;
				$validate_mobile = \think\facade\Validate::rule([
					'mobile' => 'require|mobile',
				]);
				$validate_email = \think\facade\Validate::rule([
					'email' => 'email',
				]);
				if (!$validate_mobile->check($file_check)) {
					return to_assign(1, '第'.($j - 2).'行的手机号码的格式错误');
				}
				else{
					if(in_array($mobile,$mobile_array)){
						return to_assign(1, '第'.($j - 2).'行的手机号码已存在或者重复');
					}
					else{
						array_push($mobile_array,$mobile);
					}
				}
				if(!empty($email)){
					if (!$validate_email->check($file_check)) {
						return to_assign(1, '第'.($j - 2).'行的电子邮箱的格式错误');
					}
					else{
						if(in_array($email,$email_array)){
							return to_assign(1, '第'.($j - 2).'行的电子邮箱已存在或者重复');
						}
						else{
							array_push($email_array,$email);
						}
					}
				}
				else{
					$email='';
				}
				if(empty($department)){
					return to_assign(1, '第'.($j - 2).'行的所在部门错误');
				}
				if(empty($position)){
					return to_assign(1, '第'.($j - 2).'行的所属职位错误');
				}
				
				$newusername = $this->check_name($username,$username_array);
				array_push($username_array,$newusername);				
                $data[$j - 3] = [		
                    'name' => $name,
                    'nickname' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'sex' => $sex,
                    'did' => $department,
                    'position_id' => $position,
                    'type' => $type,
					'entry_time' => Shared::excelToTimestamp($objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue(),'Asia/Shanghai'),
					'username' => $newusername,
                    'salt' => $salt,
					'pwd' => set_password($reg_pwd, $salt),
                    'reg_pwd' => $reg_pwd,
                    'thumb' => $this->to_avatars($char)
                ];
            }
           //dd($data);exit;
			$count=0;
			foreach ($data as $a => $aa) {	
				$aid = Admin::strict(false)->field(true)->insertGetId($aa);
				if($aid>0){
					//Db::name('DepartmentAdmin')->insert(['admin_id'=>$aid,'department_id'=>$aa['did'],'create_time' => time()]);
					$count++;
				}
			}
            return to_assign(0, '共成功导入了'.$count.'条员工数据');
        } catch (\think\exception\ValidateException $e) {
			return to_assign(1, $e->getMessage());
        }
    }
	
	//导入客户
	public function import_customer(){
        // 获取表单上传文件
        $file[]= request()->file('file');
		
		$param = get_params();
		$type = 'sea';
		if(isset($param['type'])){
			$type = $param['type'];
		}
        try {
            // 验证文件大小，名称等是否正确
            validate(['file' => 'filesize:51200|fileExt:xls,xlsx'])->check($file);
			// 日期前綴
			 $dataPath = date('Ym');
			 $md5 = $file[0]->hash('md5');
			 $savename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file[0], function () use ($md5) {
				 return $md5;
			 });
            $fileExtendName = substr(strrchr($savename, '.'), 1);
            // 有Xls和Xlsx格式两种
            if ($fileExtendName == 'xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
            } else {
                $objReader = IOFactory::createReader('Xls');
            }
            $objReader->setReadDataOnly(TRUE);
			$path = get_config('filesystem.disks.public.url');
            // 读取文件，tp6默认上传的文件，在runtime的相应目录下，可根据实际情况自己更改
            $objPHPExcel = $objReader->load('.'.$path . '/' .$savename);
            //$objPHPExcel = $objReader->load('./storage/202209/d11544d20b3ca1c1a5f8ce799c3b2433.xlsx');
            $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();       // 取得总行数
            $highestColumn = $sheet->getHighestColumn();   // 取得总列数
            Coordinate::columnIndexFromString($highestColumn);
            $lines = $highestRow - 1;
            if ($lines <= 0) {
				return to_assign(1, '数据不能为空');
                exit();
            }
			$name_array = []; 
			$source_array = Db::name('CustomerSource')->where(['status' => 1])->column('title', 'id');
			$grade_array = Db::name('CustomerGrade')->where(['status' => 1])->column('title', 'id');
			$industry_array = Db::name('Industry')->where(['status' => 1])->column('title', 'id');
					
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
            for ($j = 3; $j <= $highestRow; $j++) {
				$file_check = [];
				$name = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
				if(empty($name)){
					continue;
				}
				$count_name = Db::name('Customer')->where(['name'=>$name,'delete_time'=>0])->count();
				if($count_name>0){
					return to_assign(1, '第'.($j - 2).'行的客户名称已经存在');
				}
				if(in_array($name,$name_array)){
					return to_assign(1, '上传的文件存在相同的客户名称，请删除再操作');
				}
				array_push($name_array,$name);
				$source_id = array_search_plus($source_array,$objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue());
				$grade_id = array_search_plus($grade_array,$objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue());
				$industry_id = array_search_plus($industry_array,$objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());	
				
				$c_name = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue();
				$c_mobile = $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getValue();
				$file_check['c_mobile'] = $c_mobile;
				$tax_num = $objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue();
				$bank = $objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue();
				$bank_sn = $objPHPExcel->getActiveSheet()->getCell("I" . $j)->getValue();
				$file_check['bank_sn'] = $bank_sn;
				$bank_no = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getValue();				
				$cperson_mobile = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getValue();				
				$address = $objPHPExcel->getActiveSheet()->getCell("L" . $j)->getValue();
				$content = $objPHPExcel->getActiveSheet()->getCell("M" . $j)->getValue();
				$market = $objPHPExcel->getActiveSheet()->getCell("N" . $j)->getValue();
				if(empty($c_name)){
					return to_assign(1, '第'.($j - 2).'行的客户联系人姓名没完善');
				}
				if(empty($c_mobile)){
					return to_assign(1, '第'.($j - 2).'行的客户联系人手机号码没完善');
				}
				$validate_mobile = \think\facade\Validate::rule([
					'c_mobile' => 'mobile',
				]);
				if (!$validate_mobile->check($file_check)) {
					return to_assign(1, '第'.($j - 2).'行的客户联系人手机号码格式错误');
				}
				if(empty($source_id)){
					return to_assign(1, '第'.($j - 2).'行的客户来源错误');
				}
				if(empty($grade_id)){
					return to_assign(1, '第'.($j - 2).'行的客户等级错误');
				}
				if(empty($industry_id)){
					return to_assign(1, '第'.($j - 2).'行的所属行业错误');
				}
				if(empty($tax_num)){
					$tax_num='';
				}
				if(empty($bank)){
					$bank='';
				}
				$validate_bank = \think\facade\Validate::rule([
					'bank_sn' => 'number',
				]);
				if(!empty($bank_sn)){
					if (!$validate_bank->check($file_check)) {
						return to_assign(1, '第'.($j - 2).'行的银行卡账号格式错误');
					}
				}
				else{
					$bank_sn='';
				}
				if(empty($bank_no)){
					$bank_no='';
				}
				if(empty($cperson_mobile)){
					$cperson_mobile='';
				}
				if(empty($address)){
					$address='';
				}
				if(empty($content)){
					$content='';
				}
				if(empty($market)){
					$market='';
				}
				$belong_uid = 0;
				$belong_did = 0;
				if($type != 'sea'){
					$belong_uid = $this->uid;
					$belong_did = $this->did;
				}
                $data[$j - 3] = [		
                    'name' => $name,
                    'source_id' => $source_id,
                    'grade_id' => $grade_id,
                    'industry_id' => $industry_id,
                    'tax_num' => $tax_num,
                    'bank' => $bank,
                    'bank_sn' => $bank_sn,
                    'bank_no' => $bank_no,
					'cperson_mobile' => $cperson_mobile,
					'address' => $address,
                    'content' => $content,
					'market' => $market,
                    'admin_id' => $this->uid,
                    'belong_uid' => $belong_uid,
                    'belong_did' => $belong_did,
                    'c_mobile' => $c_mobile,
                    'c_name' => $c_name,
                    'create_time' => time(),
                    'update_time' => time()
                ];
            }
            //dd($data);exit;
            // 批量添加数据
			$count=0;
			foreach ($data as $a => $aa) {	
				$cid = Customer::strict(false)->field(true)->insertGetId($aa);
				if($cid>0){
					$contact = [
						'name' => $aa['c_name'],
						'mobile' => $aa['c_mobile'],
						'sex' => 1,
						'cid' => $cid,
						'is_default' => 1,
						'create_time' => time(),
						'admin_id' => $this->uid
					];
					Db::name('CustomerContact')->strict(false)->field(true)->insert($contact);
					$count++;
				}
			}
            return to_assign(0, '共成功导入了'.$count.'条客户数据');
        } catch (\think\exception\ValidateException $e) {
			return to_assign(1, $e->getMessage());
        }
    }
}
