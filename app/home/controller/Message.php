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

namespace app\home\controller;

use app\api\BaseController;
use app\home\model\Message as MessageList;
use app\home\model\Msg as MsgList;
use think\facade\Db;
use think\facade\View;

class Message extends BaseController
{	
	/**
     * 构造函数
     */
	protected $model;
	protected $model2;
	public function __construct() {
        parent::__construct(); // 调用父类构造函数
        $this->model = new MessageList();
        $this->model2 = new MsgList();
    }
    //收件箱
    public function inbox()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
			$where[] = ['to_uid', '=', $this->uid];
			$where[] = ['delete_time', '=', 0];
            if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['read'])) {
				if($param['read']==1){
					$where[] = ['read_time', '=', 0];
				}else{
					$where[] = ['read_time', '>', 0];
				}                
            }
			if (!empty($param['types'])) {
				if($param['types']==1){
					$where[] = ['from_uid', '=', 0];
				}else{
					$where[] = ['from_uid', '>', 0];
				}                
            }
            //按发送时间检索
			if (!empty($param['range_time'])) {
				$range_time =explode('~', $param['range_time']);
				$where[] = ['send_time', 'between',[strtotime(urldecode($range_time[0])),strtotime(urldecode($range_time[1]))]];
			}			
            $list = $this->model2->datalist($where,$param);
            return table_assign(0, '', $list);
        } else {
			$a = MessageList::where([['from_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$b = MsgList::where([['to_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$count = [
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 1],['delete_time', '=', 0]])->count(),
				'inbox'   => MsgList::where([['to_uid', '=', $this->uid],['delete_time', '=', 0]])->count(),
				'star'   => MsgList::where([['to_uid', '=', $this->uid],['is_star','=',1],['delete_time', '=', 0]])->count(),
				'draft'   => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['delete_time', '=', 0]])->count(),
				'rubbish' => $a+$b
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }
	
    //星标信息
    public function star()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
			$where[] = ['to_uid', '=', $this->uid];
			$where[] = ['delete_time', '=', 0];
			$where[] = ['is_star', '=', 1];
            if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['read'])) {
				if($param['read']==1){
					$where[] = ['read_time', '=', 0];
				}else{
					$where[] = ['read_time', '>', 0];
				}                
            }
			if (!empty($param['types'])) {
				if($param['types']==1){
					$where[] = ['from_uid', '=', 0];
				}else{
					$where[] = ['from_uid', '>', 0];
				}                
            }
            //按发送时间检索
			if (!empty($param['range_time'])) {
				$range_time =explode('~', $param['range_time']);
				$where[] = ['send_time', 'between',[strtotime(urldecode($range_time[0])),strtotime(urldecode($range_time[1]))]];
			}			
            $list = $this->model2->datalist($where,$param);
            return table_assign(0, '', $list);
        } else {
			$a = MessageList::where([['from_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$b = MsgList::where([['to_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$count = [
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 1],['delete_time', '=', 0]])->count(),
				'inbox'   => MsgList::where([['to_uid', '=', $this->uid],['delete_time', '=', 0]])->count(),
				'star'   => MsgList::where([['to_uid', '=', $this->uid],['is_star','=',1],['delete_time', '=', 0]])->count(),
				'draft'   => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['delete_time', '=', 0]])->count(),
				'rubbish' => $a+$b
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }
	
    //发件箱
    public function sendbox()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
            if (!empty($param['keywords'])) {
                $map[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['from_uid', '=', $this->uid];
            $where[] = ['is_draft', '=', 1];
            $where[] = ['delete_time', '=', 0];
            //按发送时间检索
			if (!empty($param['range_time'])) {
				$range_time =explode('~', $param['range_time']);
				$where[] = ['send_time', 'between',[strtotime(urldecode($range_time[0])),strtotime(urldecode($range_time[1]))]];
			}			
            $list = $this->model->datalist($where,$param);
            return table_assign(0, '', $list);
        } else {
			$a = MessageList::where([['from_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$b = MsgList::where([['to_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$count = [
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 1],['delete_time', '=', 0]])->count(),
				'inbox'   => MsgList::where([['to_uid', '=', $this->uid],['delete_time', '=', 0]])->count(),
				'star'   => MsgList::where([['to_uid', '=', $this->uid],['is_star','=',1],['delete_time', '=', 0]])->count(),
				'draft'   => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['delete_time', '=', 0]])->count(),
				'rubbish' => $a+$b
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }

    //草稿箱
    public function draft()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
            if (!empty($param['keywords'])) {
                $map[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['from_uid', '=', $this->uid];
            $where[] = ['is_draft', '=', 2];
            $where[] = ['delete_time', '=', 0];
            //按发送时间检索
			if (!empty($param['range_time'])) {
				$range_time =explode('~', $param['range_time']);
				$where[] = ['send_time', 'between',[strtotime(urldecode($range_time[0])),strtotime(urldecode($range_time[1]))]];
			}			
            $list = $this->model->datalist($where,$param);
            return table_assign(0, '', $list);
        } else {
			$a = MessageList::where([['from_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$b = MsgList::where([['to_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$count = [
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 1],['delete_time', '=', 0]])->count(),
				'inbox'   => MsgList::where([['to_uid', '=', $this->uid],['delete_time', '=', 0]])->count(),
				'star'   => MsgList::where([['to_uid', '=', $this->uid],['is_star','=',1],['delete_time', '=', 0]])->count(),
				'draft'   => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['delete_time', '=', 0]])->count(),
				'rubbish' => $a+$b
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }

    //垃圾箱
    public function rubbish()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$uid = $this->uid;
			$where = "delete_time > 0";
			$where.= " AND clear_time = 0";
            if (!empty($param['keywords'])) {
				$where.= " AND title like '%" . $param['keywords'] . "%'";
            }
			$sqlParts = [];
			$sqlCounts = [];
			$tables=['message','msg'];
			$prefix = get_config('database.connections.mysql.prefix');
			foreach ($tables as $table) {
				$tableName = $prefix.$table;
				if($table=='message'){
					$wherea= $where.' AND from_uid = '.$uid;
					// 假设每个表都有相同的列结构，这里只是简单示例
					$sqlPart = "SELECT id,title,from_uid,send_time,'{$table}' as table_name FROM {$tableName} WHERE {$wherea}";
					$sqlCount = "SELECT COUNT(*) AS count FROM {$tableName} WHERE {$wherea}";
				}
				else{
					$whereb= $where.' AND to_uid = '.$uid;
					$sqlPart = "SELECT id,title,from_uid,create_time as send_time,'{$table}' as table_name FROM {$tableName} WHERE {$whereb}";
					$sqlCount = "SELECT COUNT(*) AS count FROM {$tableName} WHERE {$whereb}";
				}			
				
				// 查询数据库中是否存在该数据表
				$is_table = Db::query("SHOW TABLES LIKE '{$tableName}'");
				// 判断查询结果
				if (!empty($is_table)) {
					$sqlParts[] = $sqlPart;
					$sqlCounts[] = $sqlCount;
				}
			}
			// 使用implode将各个部分用UNION ALL连接起来
			$unionSql = implode(" UNION ALL ", $sqlParts);
			
			$totalCount = 0;
			foreach ($sqlCounts as $sql) {
				$count = Db::query($sql)[0]['count']; // 假设每个查询都返回了一个包含'count'键的数组
				$totalCount += $count;
			}
			// 添加排序和分页逻辑
			// 假设每页显示10条记录，当前页码为$page（需要预先定义或获取）
			$pageSize = $param['limit'];
			$page = 1; // 示例页码
			$offset = ($page - 1) * $pageSize;

			// 注意：不同的数据库分页语法可能有所不同，这里以MySQL为例
			$finalSql = $unionSql . " ORDER BY send_time DESC LIMIT {$offset}, {$pageSize}";

			// 执行查询
			$result = Db::query($finalSql);
			// 处理结果
			foreach ($result as &$row) {
				// 处理每一行数据
				$row['types'] = 0;
				$row['sourse'] = '发件箱';
				if($row['send_time'] == 0){
					$row['sourse'] = '草稿箱';
					$row['types'] = 1;
					$row['send_time'] = '-';
				}
				else{
					if($row['table_name'] == 'msg'){
						$row['sourse'] = '收件箱';
						$row['types'] = 2;
					}
					$row['send_time'] = date('Y-m-d H:i:s',$row['send_time']);
				}
				if(!empty($row['from_uid'])){
					$row['from_name'] = Db::name('Admin')->where('id','=',$row['from_uid'])->value('name');
				}
				else{
					$row['from_name']='-';
				}
			}
			$list=array(
				'data'=>$result,
				'total'=>$totalCount
			);
            return table_assign(0, '', $list);
        } else {
			$a = MessageList::where([['from_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$b = MsgList::where([['to_uid', '=', $this->uid],['delete_time', '>', 0],['clear_time', '=', 0]])->count();
			$count = [
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 1],['delete_time', '=', 0]])->count(),
				'inbox'   => MsgList::where([['to_uid', '=', $this->uid],['delete_time', '=', 0]])->count(),
				'star'   => MsgList::where([['to_uid', '=', $this->uid],['is_star','=',1],['delete_time', '=', 0]])->count(),
				'draft'   => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['delete_time', '=', 0]])->count(),
				'rubbish' => $a+$b
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
			return view();
        }
    }

    //新增&编辑信息
    public function add()
    {
        $param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$msg_id = 0;
        if ($id > 0) {
			$detail = $this->model->detail($id);
            if (empty($detail)) {
				throw new \think\exception\HttpException(404, '找不到记录');
            }
			if ($detail['from_uid'] != $this->uid) {
				throw new \think\exception\HttpException(406, '无权限编辑');
			}
			if($detail['msg_id']>0){
				 $msg_id = $detail['msg_id'];
			}
            View::assign('detail', $detail);
        }
        View::assign('id', $id);
        View::assign('msg_id', $msg_id);
        return view();
    }
	
    //查看发件箱、草稿箱消息
    public function view($id)
    {
		$detail = $this->model->detail($id);
		if (empty($detail)) {
			throw new \think\exception\HttpException(404, '找不到记录');
		}
		if ($detail['from_uid'] != $this->uid) {
			throw new \think\exception\HttpException(406, '无权限查看');
		}
		//已读回执
		if($detail['send_time']>0){	
			$read_user_ids= msgList::where([['message_id','=',$id],['read_time','>',0]])->column('to_uid');
			$read_users = Db::name('Admin')->where('status', 1)->where('id', 'in', $read_user_ids)->column('name');
			$detail['read_user_names'] = implode(',',$read_users);
		}
		View::assign('detail', $detail);
        return view();
    }
	
	//删除发件、草稿
    public function del()
    {
        $param = get_params();
        $ids = empty($param['ids']) ? 0 : $param['ids'];
        $idArray = explode(',', $ids);
        foreach ($idArray as $key => $val) {
			MessageList::update(['id' => $val,'delete_time' => time()]);
			add_log('delete', $val,[],'消息');
        }
        return to_assign(0, '操作成功');
    }

    //保存消息
    public function save()
    {
        $param = get_params();
        $id = empty($param['id']) ? 0 : $param['id'];
        //接受人类型判断
        if ($param['types'] == 1 && empty($param['uids'])) {
			return to_assign(1, '收件人员不能为空');
        }
		if ($param['types'] == 2 && empty($param['dids'])) {
			return to_assign(1, '收件部门不能为空'); 
        }
		if ($param['types'] == 3 && empty($param['pids'])) {
           return to_assign(1, '收件岗位不能为空');
        }
		if ($param['types'] == 4) {
			$param['uids'] = '';
			$param['dids'] = '';
			$param['pids'] = '';
			$param['copy_uids'] = '';
        }
		$res = false;
        if ($id > 0) {
            //编辑信息的情况
            $param['update_time'] = time();
            $res = MessageList::strict(false)->field(true)->update($param);
        } else {
            //新增信息的情况
            $param['create_time'] = time();
			$param['update_time'] = time();
            $param['from_uid'] = $this->uid;
            $res = MessageList::strict(false)->field(true)->insertGetId($param);
        }
        if ($res !== false) {
            if ($id > 0) {
                $mid = $id;
            } else {
                $mid = $res;
            }
            add_log('save',$mid,$param,'消息');
			if($param['is_draft'] == 1){
				$res = $this->model->send($mid);
				if ($res!==false) {
					return to_assign(0, '发送成功');
				} else {
					return to_assign(1, '发送失败');
				}
			}
			else{
				return to_assign(0, '保存成功', $mid);
			}
        } else {
            return to_assign(1, '操作失败');
        }
    }
	
    //发送消息
    public function send_message($id)
    {
        //查询要发的消息
        $msg = MessageList::where(['id' => $id])->find();
        if (!empty($msg)) {
            $res = $this->model->send($id);
            if ($res!==false) {
                return to_assign(0, '发送成功');
            } else {
                return to_assign(1, '发送失败');
            }
        } else {
            return to_assign(1, '发送失败，找不到要发送的内容');
        }
    }
	

    //查看收件箱消息
    public function read($id )
    {
        $detail = $this->model2->detail($id);
        if (empty($detail)) {
            throw new \think\exception\HttpException(406, '找不到记录');
        }
        if ($detail['to_uid'] != $this->uid) {
            throw new \think\exception\HttpException(406, '找不到记录');
        }
        MsgList::where(['id' => $id])->update(['read_time' => time()]);
		if($detail['message_id']>0){
			 View::assign('message', $this->model->detail($detail['message_id']));
		}
		$detail['from_uname'] = Db::name('Admin')->where('id', $detail['from_uid'])->value('name');
        View::assign('detail', $detail);
        return view();
    }
	
	
    //回复信息
    public function reply()
    {
        $param = get_params();
		$msg_id = isset($param['msg_id']) ? $param['msg_id'] : 0;
		$detail = $this->model2->detail($msg_id);
		if (empty($detail)) {
			throw new \think\exception\HttpException(404, '找不到记录');
		}
		if ($detail['to_uid'] != $this->uid) {
			throw new \think\exception\HttpException(406, '无权限回复');
		}
        $detail['from_name'] =  Db::name('Admin')->where('id', $detail['from_uid'])->value('name');
		View::assign('detail', $detail);
        return view();
    }
	
    //转发信息
    public function resend()
    {
        $param = get_params();
		$msg_id = isset($param['msg_id']) ? $param['msg_id'] : 0;
		$detail = $this->model2->detail($msg_id);
		if (empty($detail)) {
			throw new \think\exception\HttpException(404, '找不到记录');
		}
		if ($detail['to_uid'] != $this->uid) {
			throw new \think\exception\HttpException(406, '无权限回复');
		}
        $detail['from_name'] =  Db::name('Admin')->where('id', $detail['from_uid'])->value('name');
		View::assign('detail', $detail);
        return view();
    }

    //状态修改
    public function check()
    {
        $param = get_params();
        $type = empty($param['type']) ? 0 : $param['type'];
        $ids = empty($param['ids']) ? 0 : $param['ids'];
        $idArray = explode(',', $ids);
        foreach ($idArray as $key => $val) {
            if ($type==1) { //设置信息为已读
				MsgList::update(['id' => $val,'read_time' => time()]);
				add_log('view', $val,[],'消息');
            }
            else if ($type==2) {  //信息进入垃圾箱
				MsgList::update(['id' => $val,'delete_time' => time()]);
				add_log('delete', $val,[],'消息');
            }
            else if ($type==3) {  //信息从垃圾箱恢复
				MsgList::update(['id' => $val,'delete_time' => 0]);
				add_log('recovery', $val,[],'消息');
            }
            else if ($type==4) {  //信息彻底删除
				MsgList::update(['id' => $val,'clear_time' => time()]);
				add_log('clear', $val,[],'消息');
            }
			else if ($type==5) {  //星标信息
				MsgList::update(['id' => $val,'is_star' => 1]);
				add_log('star', $val,[],'消息');
            }
			else if ($type==6) {  //取消星标信息
				MsgList::update(['id' => $val,'is_star' => 0]);
				add_log('unstar', $val,[],'消息');
            }
        }
        return to_assign(0, '操作成功');
    }
	
    //还原消息
    public function recovery()
    {
        $param = get_params();
        $table = empty($param['table']) ? '' : $param['table'];
        $ids = empty($param['ids']) ? 0 : $param['ids'];
        $idArray = explode(',', $ids);
        foreach ($idArray as $key => $val) {
			Db::name($table)->update(['id' => $val,'delete_time' => 0]);
			add_log('recovery', $val,[],'消息');
        }
        return to_assign(0, '操作成功');
    }
	//清除消息
    public function clear()
    {
        $param = get_params();
        $table = empty($param['table']) ? '' : $param['table'];
        $ids = empty($param['ids']) ? 0 : $param['ids'];
        $idArray = explode(',', $ids);
        foreach ($idArray as $key => $val) {
			Db::name($table)->update(['id' => $val,'clear_time' => time()]);
			add_log('clear', $val,[],'消息');
        }
        return to_assign(0, '操作成功');
    }

}
