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

namespace app\oa\controller;

use app\base\BaseController;
use app\oa\model\Work as WorkModel;
use app\oa\model\WorkRecord;
use think\facade\Db;
use think\facade\View;

class Work extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new WorkModel();
    }
    //获取接收汇报列表
    public function get_accept($map = [], $param = [])
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$Work = WorkRecord::alias('a')
			->field('a.id,a.from_uid,a.to_uid,a.send_time,a.read_time,w.id as wid,w.types,w.works,w.file_ids,w.start_date,w.end_date')
			->join('Work w','a.work_id = w.id','left')
			->where($map)
			->order('a.send_time desc')
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) {
				$item->send_time = empty($item->send_time) ? '-' : date('Y-m-d H:i:s', $item->send_time);
				$item->from_name = Db::name('Admin')->where(['id' => $item->from_uid])->value('name');
				$item->to_name = Db::name('Admin')->where(['id' => $item->to_uid])->value('name');
				if($item->start_date>0){
					$item->start_date = date('Y-m-d',$item->start_date);
				}
				if($item->end_date>0){
					$item->end_date = date('Y-m-d',$item->end_date);
				}
				$item->files = Db::name('File')->where('id', 'in', $item->file_ids)->count();
			});
		return $Work;
    }
	
    //汇报列表
    public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $map = [];
			//按关键字检索
			if( !empty($param['keywords']) ){
				$map[] = ['works', 'like', '%'.$param['keywords'].'%'];
			}
			if($param['send']==1){
				if (!empty($param['types'])) {
					$map[] = ['types', '=', $param['types']];
				}
				//按时间检索
				if (!empty($param['diff_time'])) {
					$diff_time =explode('~', $param['diff_time']);
					$map[] = ['start_date', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
				}
				$map[] = ['admin_id', '=', $this->uid];         
				$map[] = ['delete_time', '=', 0];
				$list =  $this->model->get_send($param,$map);
			}
			else{
				if (!empty($param['read'])) {
					if($param['read']==1){
						$map[] = ['a.read_time', '=', 0];
					}else{
						$map[] = ['a.read_time', '>', 0];
					}                
				}
				if (!empty($param['types'])) {
					$map[] = ['w.types', '=', $param['types']];
				}
				$map[] = ['a.to_uid', '=', $this->uid];
				$map[] = ['a.delete_time', '=', 0];
				//按时间检索
				if (!empty($param['diff_time'])) {
					$diff_time =explode('~', $param['diff_time']);
					$map[] = ['a.send_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
				}
				$list = $this->get_accept($map, $param);
			}            
            return table_assign(0, '', $list);
        } else {
			$send = empty(get_params('send')) ? 1 : get_params('send');
			return view('datalist_'.$send);
        }
    }

    //新增&编辑
    public function add()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$id = $param['id'] ? $param['id'] : 0;
			if(!empty($param['start_date'])){
				$param['start_date'] = strtotime($param['start_date']);
			}
			if(!empty($param['end_date'])){
				$param['end_date'] = strtotime($param['end_date']);
			}
			if(!empty($param['range_date'])){
				$range_date =explode('~', $param['range_date']);
				$param['start_date'] = strtotime(urldecode($range_date[0]));
				$param['end_date'] = strtotime(urldecode($range_date[1]));
			}
			if($id>0){
				$param['update_time'] = time();
				$res = Db::name('Work')->strict(false)->field(true)->update($param);			
				if ($res !== false) {
					add_log('edit',$id,$param);
					if($param['send']==1){
						$users = explode(',',$param['to_uids']);
						//组合要发的消息
						$send_data = [];
						foreach ($users as $key => $value) {
							if (!$value || ($value == $this->uid)) {
								continue;
							}
							$send_data[] = array(
								'work_id' => $id,//关联id
								'to_uid' => $value,//接收人
								'from_uid' => $this->uid,//发送人
								'send_time' => time()
							);
						}
						Db::name('WorkRecord')->strict(false)->field(true)->insertAll($send_data);
						Db::name('Work')->strict(false)->field('send_time')->update(['id' => $id,'send_time' => time()]);
						add_log('send',$id);
						$msg=[
							'from_uid'=>$this->uid,//发送人
							'to_uids'=>$param['to_uids'],//接收人
							'template_id'=>'work',//消息模板标识
							'content'=>[ //消息内容
								'send_time'=>date('Y-m-d H:i:s'),
								'action_id'=>$id
							]
						];
						event('SendMessage',$msg);
						return to_assign(0, '发送成功');
					}
					return to_assign();				
				}
				else{
					return to_assign(1,'操作失败');	
				}
			}
			else{
				$param['admin_id'] = $this->uid;
				$param['create_time'] = time();
				$wid = Db::name('Work')->strict(false)->field(true)->insertGetId($param);
				if ($wid !== false) {
					add_log('add',$wid,$param);
					if($param['send']==1){
						$users = explode(',',$param['to_uids']);
						//组合要发的内容
						$send_data = [];
						foreach ($users as $key => $value) {
							if (!$value || ($value == $this->uid)) {
								continue;
							}
							$send_data[] = array(
								'work_id' => $wid,//关联id
								'to_uid' => $value,//接收人
								'from_uid' => $this->uid,//发送人
								'send_time' => time()
							);
						}
						Db::name('WorkRecord')->strict(false)->field(true)->insertAll($send_data);
						Db::name('Work')->strict(false)->field('send_time')->update(['id' => $wid,'send_time' => time()]);
						add_log('send',$wid);
						$msg=[
							'from_uid'=>$this->uid,//发送人
							'to_uids'=>$param['to_uids'],//接收人
							'template_id'=>'work',//消息模板ID
							'content'=>[ //消息内容
								'create_time'=>date('Y-m-d H:i:s'),
								'action_id'=>$wid
							]
						];
						event('SendMessage',$msg);
						return to_assign(0, '发送成功');
					}					
					return to_assign();
				}
				else{
					return to_assign(1,'操作失败');	
				}
			}
		}
		else{
			$id = empty(get_params('id')) ? 0 : get_params('id');
			$types = empty(get_params('types')) ? 1 : get_params('types');
			if ($id > 0) {
				$detail = $this->model->detail($id);
				$types = $detail['types'];
				View::assign('detail', $detail);
			}
			View::assign('id', $id);
			View::assign('types', $types);
			return view('add_'.$types);
		}
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = $param['id'];
		$detail = $this->model->detail($id);
		//已读人查询
		$read_user_names = [];
		if($detail['admin_id'] !=$this->uid){
			$record = Db::name('WorkRecord')->where(['work_id' => $detail['id'],'to_uid' => $this->uid])->count();
			if ($record == 0) {
				echo '<div style="text-align:center;color:red;margin-top:20%;">该汇报不存在</div>';exit;
			}
			else{
				Db::name('WorkRecord')->where(['work_id' => $detail['id'],'to_uid' => $this->uid])->update(['read_time' => time()]);
			}
		}
		else{
			$read_user_ids= Db::name('WorkRecord')->where([['work_id','=',$detail['id']],['read_time','>',0]])->column('to_uid');
			$read_user_names = Db::name('Admin')->where('id', 'in', $read_user_ids)->column('name');
		}
		$sender = get_admin($detail['admin_id']);
		$detail['person_name'] = $sender['name'];
		if($detail['send_time']>0){
			$detail['send_time'] = to_date($detail['send_time']);
		}
        //接收人查询
		$user_names = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['to_uids'])->column('name');		
        $detail['users'] = implode(",", $user_names);
		$detail['read_users'] = implode(",", $read_user_names);
		$detail['comment_auth'] = 0;
		$type_user_array = explode(",", $detail['to_uids']);
		if (in_array($this->uid, $type_user_array)) {
			$detail['comment_auth'] = 1;
		}
        View::assign('detail', $detail);
		if(is_mobile()){
			return view('qiye@/index/work_view');
		}
        return view();
    }
	
	
    //删除汇报
    public function del()
    {	
        $param = get_params();
        $id = $param['id'];
		$detail = Db::name('Work')->where(['id' => $id])->find();
		if($detail['admin_id'] == $this->uid){
			$res = Db::name('Work')->where('id',$detail['id'])->update(['delete_time' => time()]);
			if($res!==false){
				Db::name('WorkRecord')->where('work_id',$detail['id'])->update(['delete_time' => time()]);
				add_log('delete', $param['id']);
				return to_assign();
			}
			else{
				return to_assign(1, '操作失败');
			}
		}
		else{
			return to_assign(1, '无权限删除');
		}
	}
}
