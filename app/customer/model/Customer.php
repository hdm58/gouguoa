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
namespace app\customer\model;
use think\model;
use think\facade\Db;
use app\api\model\EditLog;
class Customer extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[],$where=[],$whereOr=[])
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
			->orderRaw($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item->create_time = to_date($item->create_time);
				if($item->belong_uid>0){
					$item->belong_name = Db::name('Admin')->where('id',$item->belong_uid)->value('name');
					$item->belong_department = Db::name('Department')->where(['id' => $item->belong_did])->value('title');
				}
				else{
					$item->belong_name='-';
					$item->belong_department='-';
				}
				if($item->industry_id>0){
					$item->industry = Db::name('Industry')->where(['id' => $item->industry_id])->value('title');
				}
				else{
					$item->industry='-';
				}
				if($item->grade_id>0){
					$item->grade = Db::name('CustomerGrade')->where(['id' => $item->grade_id])->value('title');
				}
				else{
					$item->grade='-';
				}
				if($item->source_id>0){
					$item->source = Db::name('CustomerSource')->where(['id' => $item->source_id])->value('title');
				}
				else{
					$item->source='-';
				}
				if($item->customer_status>0){
					$item->customer_status_name = Db::name('BasicCustomer')->where(['id' => $item->customer_status])->value('title');
				}
				else{
					$item->customer_status_name='-';
				}
				if($item->intent_status>0){
					$item->intent_status_name = Db::name('BasicCustomer')->where(['id' => $item->intent_status])->value('title');
				}
				else{
					$item->intent_status_name='-';
				}
				if($item->follow_time==0){
					$item->follow_time='-';
				}
				else{
					$item->follow_time = date('Y-m-d',$item->follow_time);
				}
				if($item->next_time==0){
					$item->next_time='-';
				}
				else{
					$item->next_time = date('Y-m-d',$item->next_time);
				}
				$contact = Db::name('CustomerContact')->where(['is_default'=>1,'cid' => $item->id])->find();
				if(!empty($contact)){
					$item->contact_name = $contact['name'];
					$item->contact_mobile = $contact['mobile'];
					$item->contact_email = $contact['email'];
				}
				else{
					$item->contact_name = '';
					$item->contact_mobile = '';
					$item->contact_email = '';
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
			$param['update_time'] = time();
			$insertId = self::strict(false)->field(true)->insertGetId($param);
			$contact = [
				'name' => $param['c_name'],
				'mobile' => $param['c_mobile'],
				'sex' => $param['c_sex'],
				'qq' => $param['c_qq'],
				'wechat' => $param['c_wechat'],
				'email' => $param['c_email'],
				'cid' => $insertId,
				'is_default' => 1,
				'create_time' => time(),
				'admin_id' => $param['admin_id']
			];
			Db::name('CustomerContact')->strict(false)->field(true)->insert($contact);
			add_log('add', $insertId, $param);
			$log=new EditLog();
			$log->add('Customer',$insertId);
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
			$old = self::find($param['id']);
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);
			add_log('edit', $param['id'], $param);
			$log=new EditLog();
			$log->edit('Customer',$param['id'],$param,$old);
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
		$info['belong_department'] = Db::name('Department')->where(['id' => $info['belong_did']])->value('title');
		$info['belong_name'] = Db::name('Admin')->where(['id' => $info['belong_uid']])->value('name');
		if(!empty($info['share_ids'])){
			$share_names = Db::name('Admin')->where([['id','in',$info['share_ids']]])->column('name');
			$info['share_names'] = implode(',',$share_names);
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
				self::where('id', $id)->update(['discard_time'=>time()]);
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

