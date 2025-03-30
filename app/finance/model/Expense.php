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
namespace app\finance\model;
use think\model;
use think\facade\Db;
class Expense extends Model
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
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item->check_status_str = check_status_name($item->check_status);
				$item->income_month = empty($item->income_month) ? '-' : date('Y-m', $item->income_month);
				$item->expense_time = empty($item->expense_time) ? '-' : date('Y-m-d', $item->expense_time);
				$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
				$item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
				$item->create_time = to_date($item->create_time);
				$item['check_user'] = '-';
				if($item['check_status']==1 && !empty($item['check_uids'])){
					$check_user = Db::name('Admin')->where('id','in',$item['check_uids'])->column('name');
					$item['check_user'] = implode(',',$check_user);
				}
				if($item->pay_admin_id>0){
					$item->pay_name = Db::name('Admin')->where(['id' => $item->pay_admin_id])->value('name');
					$item->pay_time = date('Y-m-d H:i', $item->pay_time);
				}
				else{
					$item->pay_name='-';
					$item->pay_time='-';
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
			//相报销选项多个数组;
			$amountData = isset($param['amount']) ? $param['amount'] : '';
			$remarksData = isset($param['remarks']) ? $param['remarks'] : '';
			$cateData = isset($param['cate_id']) ? $param['cate_id'] : '';
			$idData = isset($param['expense_id']) ? $param['expense_id'] : 0;
			if ($amountData) {
				foreach ($amountData as $key => $value) {
					if (!$value) {
						continue;
					}    
					$data = [];
					$data['id'] = $idData[$key];
					$data['exid'] = $insertId;
					$data['admin_id'] = $param['admin_id'];
					$data['amount'] = $amountData[$key];
					$data['cate_id'] = $cateData[$key];
					$data['remarks'] = $remarksData[$key];
					if ($data['id'] > 0) {
						$data['update_time'] = time();
						$resa = Db::name('ExpenseInterfix')->strict(false)->field(true)->update($data);
					} else {
						$data['create_time'] = time();
						$eid = Db::name('ExpenseInterfix')->strict(false)->field(true)->insertGetId($data);
					}
				}
			}
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
			//相报销选项多个数组;
			$amountData = isset($param['amount']) ? $param['amount'] : '';
			$remarksData = isset($param['remarks']) ? $param['remarks'] : '';
			$cateData = isset($param['cate_id']) ? $param['cate_id'] : '';
			$idData = isset($param['expense_id']) ? $param['expense_id'] : 0;
			if ($amountData) {
				foreach ($amountData as $key => $value) {
					if (!$value) {
						continue;
					}    
					$data = [];
					$data['id'] = $idData[$key];
					$data['exid'] = $param['id'];
					$data['admin_id'] = $param['admin_id'];
					$data['amount'] = $amountData[$key];
					$data['cate_id'] = $cateData[$key];
					$data['remarks'] = $remarksData[$key];
					if ($data['id'] > 0) {
						$data['update_time'] = time();
						$resa = Db::name('ExpenseInterfix')->strict(false)->field(true)->update($data);
					} else {
						$data['create_time'] = time();
						$eid = Db::name('ExpenseInterfix')->strict(false)->field(true)->insertGetId($data);
					}
				}
			}
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
		$info['income_month'] = empty($info['income_month']) ? '-' : date('Y-m', $info['income_month']);
		$info['expense_time'] = empty($info['expense_time']) ? '-' : date('Y-m-d', $info['expense_time']);
		$info['admin_name'] = Db::name('Admin')->where(['id' => $info['admin_id']])->value('name');
		$info['department'] = Db::name('Department')->where(['id' => $info['did']])->value('title');
		if ($info['pay_time'] > 0) {
			$info['pay_time'] = date('Y-m-d H:i:s', $info['pay_time']);
			$info['pay_admin'] = Db::name('Admin')->where(['id' => $info['pay_admin_id']])->value('name');
		}
		else{
			$info['pay_time'] = '-';
		}
		if ($info['project_id'] > 0) {
			$info['ptname'] = Db::name('Project')->where(['id' => $info['project_id']])->value('name');
		}
		else{
			$info['ptname'] = '';
		}
		if ($info['subject_id'] > 0) {
			$info['subject_name'] = Db::name('Enterprise')->where(['id' => $info['subject_id']])->value('title');
		}
		$info['list'] = Db::name('ExpenseInterfix')
			->field('a.*,c.title as cate_title')
			->alias('a')
			->join('ExpenseCate c', 'a.cate_id = c.id','LEFT')
			->where(['a.exid' => $info['id']])
			->select();
		$file_array = Db::name('File')->where('id','in',$info['file_ids'])->select();
		$info['file_array'] = $file_array;	
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

