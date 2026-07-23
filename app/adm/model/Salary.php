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

namespace app\adm\model;
use think\model;
use think\facade\Db;
class Salary extends Model
{
	public static $STATUS = ['待发放','已发放'];
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[],$where=[],$whereOr=[])
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'month_time desc' : $param['order'];
        try {
            $list = self::where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$item['month_time'] = to_date($item['month_time'],'Y-m');					
				$item['create_time'] = to_date($item['create_time']);
				if($item['pay_uid']>0){
					$item['pay_time'] = to_date($item['pay_time'],'Y-m-d');
					$item['pay_name'] = Db::name('Admin')->where('id',$item['pay_uid'])->value('name');
				}else{
					$item['pay_time'] = '-';
					$item['pay_name'] = '-';
				}								
				$item['count_a'] = Db::name('SalaryRecords')->where([['salary_id','=',$item['id']],['delete_time','=',0],['status','>',2]])->count();
				$item['count_b'] = Db::name('SalaryRecords')->where([['salary_id','=',$item['id']],['delete_time','=',0],['status','<',3]])->count();
				if(!empty($item['exclude_uids'])){
					$exclude_names = Db::name('Admin')->where('id','in',$item['exclude_uids'])->column('name');
					$item['exclude_names'] = implode(',',$exclude_names);
				}
				else{
					$item['exclude_names'] = '-';
				}
				$item['status_name'] = self::$STATUS[$item['status']];
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
		return to_assign(0,'操作成功',['return_id'=>$insertId]);
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
		return to_assign(0,'操作成功',['return_id'=>$param['id']]);
    }
	
    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		$info['month_time'] = date('Y-m',$info['month_time']);
		$info['status_name'] = self::$STATUS[$info['status']];
		if(!empty($info['exclude_uids'])){
			$exclude_names = Db::name('Admin')->where('id','in',$info['exclude_uids'])->column('name');
			$info['exclude_names'] = implode(',',$exclude_names);
		}
		else{
			$info['exclude_names'] = '';
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
				Db::name('SalaryRecords')->where('salary_id','=',$id)->update(['delete_time'=>time()]);				
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				self::destroy($id);
				Db::name('SalaryRecords')->where('salary_id','=',$id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }
}

