<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 勾股OA http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
namespace app\service\model;
use think\model;
use think\facade\Db;
class Problems extends Model
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
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$item['cate'] = Db::name('ProblemsCate')->where('id',$item['cate_id'])->value('title');
				$item['status_name'] = status_name($item['status']);
				$item['priority_name'] = priority_name($item['priority']);
				$item['emergent_name'] = emergent_name($item['emergent']);
				if($item['director_id']>0){
					$item['director_name'] = Db::name('Admin')->where('id',$item['director_id'])->value('name');
				}
				else{
					$item['director_name']='-';
				}
				if($item['customer_id']>0){
					$item['customer'] = Db::name('Customer')->where('id',$item['customer_id'])->value('name');
				}
				else{
					$item['customer']='-';
				}
				if($item['contract_id']>0){
					$item['contract'] = Db::name('Contract')->where('id',$item['contract_id'])->value('name');
				}
				else{
					$item['contract']='-';
				}
				if($item['project_id']>0){
					$item['project'] = Db::name('Project')->where('id',$item['project_id'])->value('name');
				}
				else{
					$item['project']='-';
				}
				if($item['problem_time'] > 0){
					$item['problem_time'] = date('Y-m-d',$item['problem_time']);
				}
				else{
					$item['problem_time'] = '';
				}
				if($item['finish_time'] > 0){
					$item['finish_time'] = to_date($item['finish_time'],'Y-m-d');
				}
				else{
					$item['finish_time'] = '-';
				}
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
			if(!empty($param['director_id'])){
				$msg=[
					'from_uid'=>$param['admin_id'],//发送人
					'to_uids'=>$param['director_id'],//接收人    
					'template_id'=>'problems',//消息模板ID
					'content'=>[ //消息内容
						'title'=>$param['title'],
						'create_time'=>date('Y-m-d H:i:s'),
						'action_id'=>$insertId
					]
				];
				event('SendMessage',$msg);
			}
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
		$info['admin_name'] = Db::name('Admin')->where('id',$info['admin_id'])->value('name');
		$info['request_name'] = Db::name('Admin')->where('id',$info['request_id'])->value('name');
		if($info['director_id']>0){
			$info['director_name'] = Db::name('Admin')->where('id',$info['director_id'])->value('name');
		}
		if($info['customer_id']>0){
			$info['customer'] = Db::name('Customer')->where('id',$info['customer_id'])->value('name');
		}
		else{
			$info['customer']='';
		}
		if($info['contract_id']>0){
			$info['contract'] = Db::name('Contract')->where('id',$info['contract_id'])->value('name');
		}
		else{
			$info['contract']='';
		}
		if($info['project_id']>0){
			$info['project'] = Db::name('Project')->where('id',$info['project_id'])->value('name');
		}
		else{
			$info['project']='';
		}
		if($info['finish_time'] > 0){
			$info['finish_time'] = date('Y-m-d',$info['finish_time']);
		}
		else{
			$info['finish_time'] = '';
		}
		if($info['over_time'] > 0){
			$info['over_time'] = date('Y-m-d',$info['over_time']);
		}
		else{
			$info['over_time'] = '';
		}
		if($info['problem_time'] > 0){
			$info['problem_time'] = date('Y-m-d',$info['problem_time']);
		}
		else{
			$info['problem_time'] = '';
		}
		if($info['file_ids'] !=''){
			$file_array = Db::name('File')->where('id','in',$info['file_ids'])->select()->toArray();
			$info['file_array'] = $file_array;
		}
		else{
			$info['file_array'] = [];
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

