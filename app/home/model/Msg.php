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

class Msg extends Model
{	
    /**
    * 获取分页列表
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
				$item->from_name = '系统';
				$item->thumb = '/static/home/images/icon.png';
				$item['create_time'] = to_date($item['create_time'],'Y-m-d H:i:s');	
				if($item->from_uid>0){
					$from_name = Db::name('Admin')->where('id',$item->from_uid)->find();
					$item->from_name = $from_name['name'];
					$item->thumb = $from_name['thumb'];
				}
				if(!empty($item->uids)){
					$to_name = Db::name('Admin')->where(['id','in',$item->uids])->column('name');
					$item->to_name = implode(',',$to_name);
				}
				else{
					$item->to_name = '-';
				}
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
    //消息详情
    public function detail($id)
    {
        $detail = self::find($id);
		if(!empty($detail)){
			//消息附件
			$file_array = Db::name('File')->order('create_time desc')->where([['id','in',$detail['file_ids']]])->select()->toArray();
			$detail['file_array'] = $file_array;
			//引用消息附件
			if($detail['msg_id']>0){
				$from_msg =  Db::name('Msg')->find($detail['msg_id']);
				if(!empty($from_msg)){
					$detail['from_template'] = $from_msg['template'];
					$detail['from_action_id'] = $from_msg['action_id'];
					$detail['from_content'] = $from_msg['content'];
					$detail['from_file_ids'] = $from_msg['file_ids'];				
					$from_file_array = Db::name('File')->order('create_time desc')->where([['id','in',$detail['from_file_ids']]])->select()->toArray();
					$detail['from_file_array'] = $from_file_array;
				}
			}
			$detail['create_time'] = to_date($detail['create_time'],'Y-m-d H:i:s');	
		}
        return $detail;
    }
}
