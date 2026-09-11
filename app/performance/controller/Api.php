<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
declare (strict_types = 1);
namespace app\performance\controller;

use app\api\BaseController;
use app\performance\model\PerformanceRecords;
use app\performance\model\PerformanceTemplates;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//获取模板列表
	public function get_templates()
    {
        $param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['status', '=', 1];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = PerformanceTemplates::field('id,title,score')->order('id asc')->where($where)->paginate(['list_rows'=> $rows]);
        table_assign(0, '', $list);
    }
	
   	//获取绩效记录列表
	public function get_records()
    {
        $param = get_params();
		$where = array();
		$where[] = ['performance_id', '=', $param['performance_id']];
		$where[] = ['delete_time', '=', 0];
		$model = new PerformanceRecords();
		$list = $model->datalist($param,$where);
        return table_assign(0, '', $list);
    }
	
    //绩效单发布
    public function performance_send()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$count = Db::name('PerformanceRecords')->where('performance_id',$param['id'])->count();
			if($count==0){
				return to_assign(1, "该绩效单没有绩效考核人员，不能发布");
			}			
			$count_a = Db::name('PerformanceRecords')->where([['performance_id','=',$param['id']],['kpi_uid|action_uid|event_uid|check_uid','=',0]])->count();
			if($count_a>0){
				return to_assign(1, "该绩效单有KPI收集人/行为态度评价人/加减分项考核人/行政审核人未完善，不能发布");
			}
			$res = Db::name('Performance')->where('id',$param['id'])->update(['status'=>1]);
			if($res!==false){
				Db::name('PerformanceRecords')->where('performance_id',$param['id'])->update(['status'=>1]);
				$records = Db::name('PerformanceRecords')->where([['performance_id','=',$param['id']],['status','=',1]])->select();
				foreach ($records as $key => $value) {
					//发送消息通知
					$msg=[
						'from_uid'=>0,//发送人
						'to_uids'=>$value['kpi_uid'].','.$value['action_uid'].','.$value['event_uid'],//接收人
						'template_id'=>'performance_send',//消息模板标识
						'content'=>[ //消息内容
							'create_time'=>date('Y-m-d H:i:s'),
							'action_id'=>$value['id'],
							'title' => '绩效待完善',
							'status' => '待完善'
						]
					];
					event('SendMessage',$msg);
				}
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
		}
    }
	
    //绩效审核通过
    public function performance_check()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$res = Db::name('PerformanceRecords')->where('id',$param['id'])->update(['status'=>4,'check_time'=>time()]);
			if($res!==false){
				//发送消息通知
				$detail = Db::name('PerformanceRecords')->where('id',$param['id'])->find();
				$msg=[
					'from_uid'=>0,//发送人
					'to_uids'=>$detail['kpi_uid'].','.$detail['action_uid'].','.$detail['event_uid'].','.$detail['uid'],//接收人
					'template_id'=>'performance_ok',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$detail['id'],
						'title' => '绩效审核通过',
						'status' => '审核通过'
					]
				];
				event('SendMessage',$msg);
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
		}
    }
	 //绩效审核拒绝
    public function performance_refue()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$res = Db::name('PerformanceRecords')->where('id',$param['id'])->update(['status'=>3,'check_remark'=>$param['check_remark'],'check_time'=>time(),'kpi_check_time'=>0,'kpi_check_time'=>0,'action_check_time'=>0,'event_check_time'=>0]);
			if($res!==false){
				$detail = Db::name('PerformanceRecords')->where('id',$param['id'])->find();
				$msg=[
					'from_uid'=>0,//发送人
					'to_uids'=>$detail['kpi_uid'].','.$detail['action_uid'].','.$detail['event_uid'],//接收人
					'template_id'=>'performance_check',//消息模板标识
					'content'=>[ //消息内容
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$detail['id'],
						'title' => '绩效审核被驳回',
						'status' => '被驳回'
					]
				];
				event('SendMessage',$msg);
				return to_assign();
			}
			else{
				return to_assign(1, "操作失败");
			}
		}
    }

}
