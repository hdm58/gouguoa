<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 2021~2025 http://www.gouguoa.com All rights reserved.
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
use think\facade\View;
use Mpdf\Mpdf;
use PhpOffice\PhpWord\TemplateProcessor;
use app\adm\model\MeetingRecords;
use app\adm\model\MeetingOrder;
use app\home\model\Leaves;
use app\home\model\Trips;
use app\home\model\Outs;
use app\adm\model\Seal;
use app\user\model\PersonalQuit;
use app\user\model\DepartmentChange;
use app\user\model\Talent;
use app\adm\model\OfficialDocs;
use app\finance\model\Loan;
use app\finance\model\Expense;
use app\finance\model\Invoice;
use app\finance\model\Ticket;
use app\contract\model\Contract;
use app\contract\model\Purchase;

class Export extends BaseController
{	
    public function pdf($types='',$id=0)
    {
		$name='PDF文件';
		$is_header=true;
		$check_record=true;
		$is_page=false;
		$logo = CMS_ROOT.'public/static/home/images/logo.png';
		//请假审批
		if($types=='leaves'){
			$model = new Leaves();
			$detail= $model->getById($id);
			$detail['types_name'] = leaves_types_name($detail['types']);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的请假审批单';
		}
		//出差审批
		if($types=='trips'){
			$model = new Trips();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的出差审批单';
		}
		//外出审批
		if($types=='outs'){
			$model = new Outs();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的外出审批单';
		}
		//加班审批
		if($types=='overtimes'){
			$model = new Outs();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的加班审批单';
		}
		//用印审批
		if($types=='seal'){
			$model = new Seal();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的用章审批单';
		}
		//离职审批
		if($types=='personal_quit'){
			$model = new PersonalQuit();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的离职审批单';
		}
		//人事调动审批单
		if($types=='department_change'){
			$model = new DepartmentChange();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的人事调动审批单';
		}
		//入职审批单
		if($types=='talent'){
			$model = new Talent();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的入职审批单';
		}
		//公文审批
		if($types=='official_docs'){
			$model = new OfficialDocs();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的公文审批单';
		}
		//借支审批
		if($types=='loan'){
			$model = new Loan();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的借支审批单';
		}
		//报销审批
		if($types=='expense'){
			$model = new Expense();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的报销审批单';
		}
		//开票回款
		if($types=='invoice'){
			$model = new Invoice();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的发票开票审批单';
		}
		//无票回款
		if($types=='invoicea'){
			$model = new Invoice();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的无发票回款审批单';
		}
		//收票付款
		if($types=='ticket'){
			$model = new Ticket();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的收票审批单';
		}
		//无发票付款单
		if($types=='ticketa'){
			$model = new Ticket();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的无发票付款审批单';
		}
		//销售合同
		if($types=='contract'){
			$model = new Contract();
			$detail= $model->getById($id);
			$detail['cate_title'] = Db::name('ContractCate')->where(['id' => $detail['cate_id']])->value('title');
			$detail['subject_title'] = Db::name('Enterprise')->where(['id' => $detail['subject_id']])->value('title');
			$detail['department'] = $detail['sign_department'];
			$detail['content_array'] = unserialize($detail['content']);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的销售合同审批单';
		}
		//采购合同
		if($types=='purchase'){
			$model = new Purchase();
			$detail= $model->getById($id);
			$detail['cate_title'] = Db::name('ContractCate')->where(['id' => $detail['cate_id']])->value('title');
			$detail['subject_title'] = Db::name('Enterprise')->where(['id' => $detail['subject_id']])->value('title');
			$detail['department'] = $detail['sign_department'];
			$detail['content_array'] = unserialize($detail['content']);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的采购合同审批单';
		}
		//会议记录
		if($types=='meeting'){
			$model = new MeetingRecords();
			$detail= $model->getById($id);
			$name = date('Ymd',$detail['meeting_date']).$detail['did_name'].'_会议纪要';
			$check_record=false;
		}
		//会议室预定
		if($types=='meeting_order'){
			$model = new MeetingOrder();
			$detail= $model->getById($id);
			$name = to_date($detail['create_time'],'Ymd').$detail['admin_name'].'提交的会议室预定';
		}
		//员工档案
		if($types=='files'){
			$detail = get_admin($id);
			$detail['pname'] = Db::name('Admin')->where('id',$detail['pid'])->value('name');
			$detail['position'] = Db::name('Position')->where('id',$detail['position_id'])->value('title');
			$detail['department'] = Db::name('Department')->where('id',$detail['did'])->value('title');
			$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$id)->column('department_id');
			$department_names = Db::name('Department')->whereIn('id',$department_ids)->column('title');
			$detail['department_names'] = implode(',',$department_names);
			if($detail['file_ids'] !=''){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			$edu = Db::name('AdminProfiles')->where(['types'=>1,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$work = Db::name('AdminProfiles')->where(['types'=>2,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$certificate = Db::name('AdminProfiles')->where(['types'=>3,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$skills = Db::name('AdminProfiles')->where(['types'=>4,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$language = Db::name('AdminProfiles')->where(['types'=>5,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			View::assign('edu', $edu);
			View::assign('work', $work);
			View::assign('skills', $skills);
			View::assign('certificate', $certificate);
			View::assign('language', $language);
			View::assign('detail', $detail);
			$name = $detail['name'].'_档案信息';
			$check_record=false;
			$is_page=true;
		}		
	
		if($check_record){
			if(!empty($detail['check_copy_uids'])){
				$check_copy_names = Db::name('Admin')->where([['id','in',$detail['check_copy_uids']],['status','=',1]])->column('name');
				$detail['check_copy_names'] = implode(',',$check_copy_names);
			}
			$detail['check_status_str'] = check_status_name($detail['check_status']);
			//审批记录
			$check_table = $types;
			if($types=='invoicea'){
				$check_table = 'invoice';
			}
			if($types=='ticketa'){
				$check_table = 'ticket';
			}
			$check_record_array = Db::name('FlowRecord')
							->field('f.*,a.name')
							->alias('f')
							->join('Admin a', 'a.id = f.check_uid', 'left')
							->where([['f.action_id','=',$id],['f.check_table','=',$check_table],['f.delete_time','=',0],['f.step_id','>',0]])->select()->toArray();
			foreach ($check_record_array as $kk => &$vv) {		
				$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
				$vv['status_str'] = '提交';
				if($vv['check_status'] == 1){
					$vv['status_str'] = '审核通过';
				}
				else if($vv['check_status'] == 2){
					$vv['status_str'] = '审核拒绝';
				}
				if($vv['check_status'] == 3){
					$vv['status_str'] = '撤销';
				}
				if($vv['check_status'] == 4){
					$vv['status_str'] = '反确认';
				}
			}
			$detail['check_record_array'] = $check_record_array;
		}
		
		$detail['pdf_admin'] = Db::name('Admin')->where([['id','=',$this->uid]])->value('name');
		$detail['pdf_time'] = date('Y-m-d H:i:s');
		//var_dump($detail);exit;
		View::assign('detail', $detail);
		View::assign('logo', $logo);
        $html = View::fetch($types);
		$time = time();
        //tempDir指定临时文件目录，需要有可写入的权限，否则会报错
        $mpdf = new Mpdf([
			'mode'=>'zh',//或者utf-8
            'format' => 'A4',
			'margin_top'=>23,
			'margin_bottom'=>19,
			//重新定义字体路径
			'fontDir' => [
				CMS_ROOT . "public/static/font/"
			],
			//重新定义默认字体
			'fontdata' => [
				"sun-exta" => [
					'R'  => 'MiSans-Regular.ttf', // regular font
					'B'  => 'MiSans-Bold.ttf', // optional: bold font
					'I'  => 'MiSans-Regular.ttf', // optional: italic font
					'BI' => 'MiSans-Bold.ttf', // optional: bold-italic font 
					'useKashida' => 75,
					'sip-ext' => 'MiSans-Regular.ttf', /* SIP=Plane2 Unicode (extension B) */
				]
			],
            'tempDir' => CMS_ROOT . "public/storage/pdf/"
        ]);
        $mpdf->SetDisplayMode('fullpage');
        //自动分析录入内容字体
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
		//文件名称
		$filename = $name."_".$time.".pdf";
        //文章pdf文件存储路径
		$path = CMS_ROOT . "public" . "/storage/pdf/".$filename;
		$pageNumber = count($mpdf->pages);
		$header= '<table width="100%" style="margin:0;padding:0"><tr> 
<td width="50%" style="text-align:left;font-size:12px; color:#999999;margin:0;padding:0"><img src="'.$logo.'" height="36" /></td> 
<td width="50%" style="text-align:right;font-size:12px; color:#999999;margin:0;padding:0">勾股OA办公系统</td> 
</tr></table><hr style="border-color:#f1f1f1;margin:0">';
		$page= '<p style="text-align:center;font-size:12px; color:#999999">第 {PAGENO}/{nbpg} 页</p>';
		if($is_header){
			$mpdf->SetHTMLHeader($header);
		}
		if($is_page){
			$mpdf->SetHTMLFooter($page);
		}
        //以html为标准分析写入内容
        $mpdf->WriteHTML($html);
		//直接下载文件
		$mpdf->Output('tmp.pdf',true);
		$mpdf->Output($filename,"d");
		exit;
		/*
        //生成磁盘文件
        $mpdf->Output($path);
		if($type==0){
			//下载文件
			if (is_file($path)){
				//return to_assign(0,"文件生成成功");
				$file  =  fopen($path, "rb");
				Header( "Content-type:  application/octet-stream ");
				Header( "Accept-Ranges:  bytes ");
				Header( "Content-Disposition:  attachment;  filename= $filename");
				while (!feof($file)) {
					echo fread($file, 8192);
					ob_flush();
					flush();
				}
				fclose($file);
			} else {
				return to_assign(1,"文件生成失败");
			}
		}
		*/
    }
}
