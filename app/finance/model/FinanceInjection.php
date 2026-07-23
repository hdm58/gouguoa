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

namespace app\finance\model;
use think\Model;
use think\facade\Db;
class FinanceInjection extends Model
{	
	public function datalist($param,$where)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$handler_users = Db::name('Admin')->where('id','in',$item['handler_ids'])->column('name');
				$item['handler_users'] = implode(',',$handler_users);
				$item['enter_time'] = date('Y-m-d H:i',$item['enter_time']);
				$item['enterprise'] = Db::name('Enterprise')->where('id',$item['enterprise_id'])->value('title');
				$item['account'] = Db::name('Account')->where('id',$item['account_id'])->value('title');
				$item['create_time'] = to_date($item['create_time']);
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
		$info['income_time'] = to_date($info['income_time'],'Y-m-d');
		$info['subject'] = Db::name('Enterprise')->where('id',$info['enterprise_id'])->value('title');
		$info['account'] = Db::name('Account')->where('id',$info['account_id'])->value('title');
		$handler_users = Db::name('Admin')->where('id','in',$info['handler_ids'])->column('name');
		$info['handler_users'] = implode(',',$handler_users);
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
				$detail = self::find($id);
				self::where('id', $id)->update(['delete_time'=>time()]);
				$has_back = Db::name('IncomeRefund')->where([['income_id','=',$detail['income_id']],['delete_time','=',0]])->sum('amount');
				$income_amount = Db::name('Income')->where([['id','=',$detail['income_id']]])->value('amount');
				$back_status = 2;
				if($has_back*1000 == 0){
					$back_status = 0;
				}
				if($has_back*1000>0 && $has_back*1000 < $income_amount*1000){
					$back_status = 1;
				}
				Db::name('Income')->where('id',$detail['income_id'])->update(['back_status'=>$back_status,'back_amount'=>$has_back]);
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