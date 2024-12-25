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

namespace app\home\model;

use think\Model;
use think\facade\Db;

class Message extends Model
{	
    /**
    * 获取发件箱分页列表
    * @param $where
    * @param $param
    */
    public function datalist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item->send_time_str = empty($item->send_time) ? '草稿' : date('Y-m-d H:i:s', $item->send_time);
				if(empty($item->uids)){
					$item->to_name = '-';
				}
				else{
					$to_name = Db::name('Admin')->where([['id','in',$item->uids]])->column('name');
					$item->to_name = implode(',',$to_name);
				}
				if(empty($item->dids)){
					$item->to_department = '-';
				}
				else{
					$to_department = Db::name('Department')->where([['id','in',$item->dids]])->column('title');
					$item->to_department = implode(',',$to_department);
				}
				if(empty($item->pids)){
					$item->to_position = '-';
				}
				else{
					$to_position = Db::name('Position')->where([['id','in',$item->pids]])->column('title');
					$item->to_position = implode(',',$to_position);
				}
				if(empty($item->copy_uids)){
					$item->copy_names = '-';
				}
				else{
					$copy_name = Db::name('Admin')->where([['id','in',$item->copy_uids]])->column('name');
					$item->copy_names = implode(',',$copy_name);
				}
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
    //发件箱消息详情
    public function detail($id)
    {
        $detail = self::find($id);
		if(!empty($detail)){
            if ($detail['types'] == 1) { //人员
                $users = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['uids'])->column('name');
                $detail['unames'] = implode(',',$users);
            } elseif ($detail['types'] == 2) { //部门
                $departments = Db::name('Department')->where('id', 'in', $detail['dids'])->column('title');
                $detail['dnames'] = implode(',',$departments);
            } elseif ($detail['types'] == 3) { //角色
                $positions = Db::name('Position')->where('id', 'in', $detail['pids'])->column('title');
               $detail['pnames'] = implode(',',$positions);
            }
			$copy_users = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['copy_uids'])->column('name');
            $detail['copy_names'] = implode(',',$copy_users);
			//消息附件
			$file_array = Db::name('File')->order('create_time desc')->where([['id','in',$detail['file_ids']]])->select()->toArray();
			$detail['file_array'] = $file_array;
			//引用消息附件
			if($detail['msg_id']>0){
				$from_msg =  Db::name('Msg')->field('content,file_ids,template,action_id')->where(['id' => $detail['msg_id']])->find();
				$detail['from_template'] = $from_msg['template'];
				$detail['from_action_id'] = $from_msg['action_id'];
				$detail['from_content'] = $from_msg['content'];
				$detail['from_file_ids'] = $from_msg['file_ids'];				
				$detail['from_file_array'] = Db::name('File')->order('create_time desc')->where([['id','in',$detail['from_file_ids']]])->select()->toArray();
			}
		}
        return $detail;
    }
	
    //发送消息
    public function send($id)
    {
        //查询要发的消息
        $msg = self::find($id);
        $users = [];
		//查询全部收件人
		if ($msg['types'] == 1) { //人员
			$users = Db::name('Admin')->where('status', 1)->where('id', 'in', $msg['uids'])->column('id');
		} elseif ($msg['types'] == 2) { //部门
			$users = Db::name('Admin')->where('status', 1)->where('did', 'in', $msg['dids'])->column('id');
		} elseif ($msg['types'] == 3) { //角色
			$users = Db::name('Admin')->where('status', 1)->where('position_id', 'in', $msg['pids'])->column('id');
		} elseif ($msg['types'] == 4) { //全部
			$users = Db::name('Admin')->where('status', 1)->column('id');
		}
		if(!empty($msg['copy_uids'])){
			$copy_uids = explode(',',$msg['copy_uids']);
			$users_tem = array_merge($users,$copy_uids);
			$users = array_unique($users_tem);
		}
		//组合要发的消息
		$send_data = [];
		foreach ($users as $key => $value) {
			if ($value == $msg['from_uid']) {
				continue;
			}
			$send_data[] = array(
				'message_id' => $msg['id'],//来源发件箱关联id
				'to_uid' => $value,//接收人
				'msg_id' => $msg['msg_id'],//转发或回复消息关联id
				'title' => $msg['title'],
				'content' => $msg['content'],
				'file_ids' => $msg['file_ids'],
				'from_uid' => $msg['from_uid'],//发送人
				'create_time' => time()
			);
		}
		$res = Db::name('Msg')->strict(false)->field(true)->insertAll($send_data);
		if ($res!==false) {
			//草稿消息变成已发消息
			self::where(['id' => $msg['id']])->update(['is_draft' => '1', 'send_time' => time(), 'update_time' => time()]);
			add_log('send',$msg['id'],[],'消息');
			return true;
		} else {
			return false;
		}
    }
}
