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
class SalaryRecords extends Model
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
		$order = empty($param['order']) ? 'uid asc' : $param['order'];
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
				$user = Db::name('Admin')->where('id',$item['uid'])->find();
				$item['name'] = $user['name'];
				$item['position'] = Db::name('Position')->where('id',$user['position_id'])->value('title');
				$item['department'] = Db::name('Department')->where('id',$user['did'])->value('title');
				$item['month_time'] = date('Y-m',$item['month_time']);
				$item['status_name'] = self::$STATUS[$item['status']];
				$item['pay_time'] = to_date($item['pay_time'],'Y-m-d');
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
			$this->update_salary($param['salary_id']);
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
			$this->update_salary($param['salary_id']);
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
		$user = Db::name('Admin')->where('id',$info['uid'])->find();
		$info['name'] = $user['name'];
		$info['position'] = Db::name('Position')->where('id',$user['position_id'])->value('title');
		$info['department'] = Db::name('Department')->where('id',$user['did'])->value('title');
		$info['status_name'] = self::$STATUS[$info['status']];
		$total = $info['latencies']+$info['departures'];
		$info['absenteeism_days_a'] = 0;
		if($total>=5){
			$info['absenteeism_days_a'] = 1;
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
		$detail = self::where('id', $id)->find();
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
		$this->update_salary($detail['salary_id']);
		return to_assign();
    }
	
	public function update_salary($salary_id)
    {
		$salary = DB::name('SalaryRecords')->where(['salary_id'=>$salary_id,'delete_time'=>0])->sum('total_payment');
		$social = DB::name('SalaryRecords')->where(['salary_id'=>$salary_id,'delete_time'=>0])->sum('deduct_social');
		$gongjijin = DB::name('SalaryRecords')->where(['salary_id'=>$salary_id,'delete_time'=>0])->sum('deduct_gongjijin');
		$tax = DB::name('SalaryRecords')->where(['salary_id'=>$salary_id,'delete_time'=>0])->sum('deduct_tax');
		
		$amount = ($salary*10000+$social*10000+$gongjijin*10000+$tax*10000)/10000;
        DB::name('Salary')->where('id', $salary_id)->strict(false)->field(true)->update(['amount'=>$amount,'salary'=>$salary,'social'=>$social,'gongjijin'=>$gongjijin,'tax'=>$tax]);
    }
}

