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

namespace app\adm\model;
use think\model;
use think\facade\Db;
class Property extends Model
{
	//资产来源
	public static $property_source = ['','采购','赠与','自产','其他'];
	
	//资产状态
	public static $property_status = ['闲置','在用','维修','报废','丢失'];
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'p.id desc' : $param['order'];
        try {			
            $list = self::where($where)
				->field('p.*,pc.title as cate,pb.title as brand,pu.title as unit,u.name as create_name')
				->alias('p')
				->join('PropertyCate pc', 'pc.id = p.cate_id', 'left')
				->join('PropertyBrand pb', 'pb.id = p.brand_id', 'left')
				->join('PropertyUnit pu', 'pu.id = p.unit_id', 'left')
				->join('Admin u', 'u.id = p.admin_id', 'left')
				->order($order)
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key){
					$item->update_time_str = '-';
					$item->update_name = '-';
					if(!empty($item->update_time)){
						$item->update_time_str = $item->update_time;
						$item->update_name = Db::name('Admin')->where('id',$item->update_id)->value('name');
					}
					$item->status_str = self::$property_status[$item->status];
					$item->source_str = self::$property_source[$item->source];
					$item->create_time = to_date($item->create_time);
					$item->update_time_str = to_date($item->update_time);
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
		$info['status_str'] = self::$property_status[$info['status']];
		$info['source_str'] = self::$property_source[$info['source']];
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
