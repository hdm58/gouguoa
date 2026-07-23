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
class TicketPayment extends Model
{	
	public function datalist($param,$where,$whereOr=[])
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
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
				$item['department'] = Db::name('Department')->where('id',$item['did'])->value('title');
				$item['pay_time'] = date('Y-m-d H:i',$item['pay_time']);
				if($item['ticket_id']>0){
					$item['ticket'] = Db::name('Ticket')->where('id',$item['ticket_id'])->value('code');
				}
				else{
					$item['ticket']='-';
				}
				if($item['supplier_id']>0){
					$item['supplier'] = Db::name('Supplier')->where('id',$item['supplier_id'])->value('title');
				}
				else{
					$item['supplier']='-';
				}
				if($item['purchase_id']>0){
					$item['purchase'] = Db::name('Purchase')->where('id',$item['purchase_id'])->value('name');
				}
				else{
					$item['purchase']='-';
				}
				if($item['project_id']>0){
					$item['project'] = Db::name('Project')->where('id',$item['project_id'])->value('name');
				}
				else{
					$item['project']='-';
				}
				$item['subject'] = Db::name('Enterprise')->where('id',$item['enterprise_id'])->value('title');
				$item['account'] = Db::name('Account')->where('id',$item['account_id'])->value('title');
				$item['fundscate'] = Db::name('FundsCate')->where('id',$item['fundscate_id'])->value('title');
				$item['paytype'] = Db::name('PayType')->where('id',$item['paytype_id'])->value('title');
				
				$item['check_status_str'] = check_status_name($item['check_status']);
				$item['check_user'] = '-';
				if($item['check_status']==1 && !empty($item['check_uids'])){
					$check_user = Db::name('Admin')->where('id','in',$item['check_uids'])->column('name');
					$item['check_user'] = implode(',',$check_user);
				}
				$item['create_time'] = to_date($item['create_time']);
				$item['confirm_time'] = to_date($item['confirm_time']);
				$item['confirm_admin'] = '-';
				if($item['confirm_uid']>0){
					$item['confirm_admin'] = Db::name('Admin')->where('id',$item['confirm_uid'])->value('name');
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
		$info['pay_time'] = to_date($info['pay_time'],'Y-m-d H:i');
		$file_array = Db::name('File')->where('id','in',$info['file_ids'])->select();
		$info['file_array'] = $file_array;
		
		$info['ticket'] = Db::name('Ticket')->where('id',$info['ticket_id'])->value('code');
		$info['supplier'] = Db::name('Supplier')->where('id',$info['supplier_id'])->value('title');
		$info['purchase'] = Db::name('Purchase')->where('id',$info['purchase_id'])->value('name');
		$info['project'] = Db::name('Project')->where('id',$info['project_id'])->value('name');
		$info['subject'] = Db::name('Enterprise')->where('id',$info['enterprise_id'])->value('title');
		$info['account'] = Db::name('Account')->where('id',$info['account_id'])->value('title');
		$info['fundscate'] = Db::name('FundsCate')->where('id',$info['fundscate_id'])->value('title');
		$info['paytype'] = Db::name('PayType')->where('id',$info['paytype_id'])->value('title');
		$info['confirm_admin'] = '-';
		if($info['confirm_uid']>0){
			$info['confirm_admin'] = Db::name('Admin')->where('id',$info['confirm_uid'])->value('name');
		}
		$info['confirm_time'] = to_date($info['confirm_time']);
		$info['check_status_str'] = check_status_name($info['check_status']);
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