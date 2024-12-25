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
class Car extends Model
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
				$item->buy_time = date('Y-m-d', $item->buy_time);
				$item->insure_time = date('Y-m-d', $item->insure_time);
				$item->insure_time_note = count_days($item->insure_time);
				$item->review_time = date('Y-m-d', $item->review_time);
				$item->review_time_note = count_days($item->review_time);
				if($item->driver>0){
					$item->driver_name = Db::name('Admin')->where('id','=',$item->driver)->value('name');
				}
				else{
					$item->driver_name='-';
				}
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
			$param['mileage_now'] = $param['mileage'];
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
	
	//维修(保养)记录
    public function repairlist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = Db::name('CarRepair')
				->field('cr.*,c.title as car,c.name,u.name as handled_name')
				->alias('cr')
				->join('Car c', 'c.id = cr.car_id', 'left')
				->join('Admin u', 'u.id = cr.handled', 'left')
				->where($where)
				->order($order)
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key){
					$item['repair_time'] = date('Y-m-d',$item['repair_time']);
					$item['create_time'] = date('Y-m-d H:i',$item['create_time']);
					return $item;
				});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
	//费用记录
    public function feelist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = Db::name('CarFee')
				->field('cf.*,c.title as car,c.name,u.name as handled_name,ba.title as types_str')
				->alias('cf')
				->join('Car c', 'c.id = cf.car_id', 'left')
				->join('basicAdm ba', 'ba.id = cf.types', 'left')
				->join('Admin u', 'u.id = cf.handled', 'left')
				->where($where)
				->order($order)
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key){
					$item['fee_time'] = date('Y-m-d',$item['fee_time']);
					$item['create_time'] = date('Y-m-d H:i',$item['create_time']);
					return $item;
				});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
	//里程记录
    public function mileagelist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
			$list = Db::name('CarMileage')
				->order('mileage_time desc')
				->where($where)
				->order($order)
				->paginate($rows, false)->each(function($item, $key){
					$item['mileage_time'] = date('Y-m', $item['mileage_time']);
					$item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
					$item['admin_name'] =  Db::name('Admin')->where('id',$item['admin_id'])->value('name');
					return $item;
				});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
}

