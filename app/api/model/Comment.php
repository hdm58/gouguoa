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
namespace app\api\model;
use think\facade\Db;
use think\Model;

class Comment extends Model
{
    //列表
    function datalist($param=[],$where=[]) {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) use($param){
				$item['create_times'] = time_trans($item['create_time']);
				if($item['update_time']>0){
					$item['update_times'] = '，最后编辑时间：'.time_trans($item['update_time']);
				}
				else{
					$item['update_times'] = '';
				}
				$item['thumb'] = Db::name('Admin')->where(['id' => $item['admin_id']])->value('thumb');
				$item['name'] = Db::name('Admin')->where(['id' => $item['admin_id']])->value('name');
				$to_names = Db::name('Admin')->where([['id', 'in', $item['to_uids']]])->column('name');
				if (empty($to_names)) {
                    $item['to_names'] = '-';
                } else {
                    $item['to_names'] = implode(',', $to_names);
                }
			    $item['read'] = 0;
				if($item['admin_id'] == $param['admin_id']){
					$item['read'] = 2;
				}
				else{
					$count = Db::name('CommentRead')->where(['comment_id' => $item['id'],'admin_id' => $param['admin_id']])->count();
					if($count>0){
						$item['read'] = 1;
					}
				}
				if($item['pid']>0){
					$pcomment = Db::name('Comment')->where('id','=',$item['pid'])->find();
					$padmin_id =$pcomment['admin_id'];
					$item['padmin'] =Db::name('Admin')->where('id','=',$padmin_id)->value('name');
					$item['ptimes'] =time_trans($pcomment['create_time']);
					$item['pcontent'] = $pcomment['content'];
				}
				return $item;
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
}
