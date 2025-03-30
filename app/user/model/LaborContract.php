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
namespace app\user\model;
use think\model;
use think\facade\Db;
class LaborContract extends Model
{
	//合同类别
	public static $laborcontract_cate = array(
		array('id' => 1, 'title' => '劳动合同'),
		array('id' => 2, 'title' => '劳务合同'),
		array('id' => 3, 'title' => '实习协议'),
		array('id' => 4, 'title' => '保密协议')
	);

	//合同类型
	public static $laborcontract_types = ['','新签合同','续签合同','变更合同'];

	//合同属性
	public static $laborcontract_properties = ['','初级职称','中级职称','高级职称'];
	
	//合同状态
	public static $laborcontract_status = ['','正常','已到期','已解除'];

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
				$item->cate_str = self::$laborcontract_cate[$item->cate-1]['title'];
				$item->types_str = self::$laborcontract_types[$item->types];
				$item->properties_str = self::$laborcontract_properties[$item->properties];
				$item->status_str = self::$laborcontract_status[$item->status];
				$item->enterprise = Db::name('Enterprise')->where('id',$item->enterprise_id)->value('title');
				$item->user_name = Db::name('Admin')->where('id',$item->uid)->value('name');
				$item->admin_name = Db::name('Admin')->where('id',$item->admin_id)->value('name');
				$item->sign_time = date('Y-m-d',$item->sign_time);
				$item->start_time = date('Y-m-d',$item->start_time);
				$item->end_time = date('Y-m-d',$item->end_time);
				$item->diff_time = $item->start_time.' 至 '.$item->end_time;
				$item->renewal = Db::name('LaborContract')->where(['renewal_pid'=>$item->id,'delete_time'=>0])->count();
				$item->change = Db::name('LaborContract')->where(['change_pid'=>$item->id,'delete_time'=>0])->count();
				$item->create_time = to_date($item->create_time,'Y-m-d H:i:s');
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }

    /**
    * 添加数据
    * @param $param
    */
    public function add($param)
    {
		$insertId = 0;
        try {
			$param['create_time'] = time();
			$insertId = self::strict(false)->field(true)->insertGetId($param);
			add_log('add', $insertId, $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['aid'=>$insertId]);
    }

    /**
    * 编辑信息
    * @param $param
    */
    public function edit($param)
    {
        try {
            $param['update_time'] = time();
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);
			add_log('edit', $param['id'], $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign();
    }
	

    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		$info['cate_str'] = self::$laborcontract_cate[$info['cate']-1]['title'];
		$info['types_str'] = self::$laborcontract_types[$info['types']];
		$info['properties_str'] = self::$laborcontract_properties[$info['properties']];
		$info['status_str'] = self::$laborcontract_status[$info['status']];
		$info['enterprise'] = Db::name('Enterprise')->where('id',$info['enterprise_id'])->value('title');
		$info['user_name'] = Db::name('Admin')->where('id',$info['uid'])->value('name');
		$info['sign_time'] = date('Y-m-d',$info['sign_time']);
		$info['start_time'] = date('Y-m-d',$info['start_time']);
		$info['end_time'] = date('Y-m-d',$info['end_time']);
		if($info['trial_end_time']>0){
			$info['trial_end_time'] = date('Y-m-d',$info['trial_end_time']);
		}
		return $info;
    }

    /**
    * 删除信息
    * @param $id
    * @param $type
    * @return array
    */
    public function delById($id,$type=0)
    {
		if($type==0){
			//逻辑删除
			try {
				$param['delete_time'] = time();
				self::where('id', $id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				self::destroy($id);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }
}

