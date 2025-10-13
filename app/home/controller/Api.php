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
declare (strict_types = 1);
namespace app\home\controller;

use app\api\BaseController;
use think\facade\Db;

class api extends BaseController
{
    //首页公告
    public function get_note_list()
    {
        $list = Db::name('Note')
            ->field('a.id,a.title,a.create_time,c.title as cate_title')
            ->alias('a')
            ->join('note_cate c', 'a.cate_id = c.id')
            ->where(['a.status' => 1,'a.delete_time' => 0])
            ->order('a.end_time desc,a.sort desc,a.create_time desc')
            ->limit(8)
            ->select()->toArray();
        foreach ($list as $key => $val) {
            $list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
        }
        $res['data'] = $list;
        return table_assign(0, '', $res);
    }
	
	//首页知识列表
    public function get_article_list()
    {
		$prefix = get_config('database.connections.mysql.prefix');//判断是否安装了文章模块
		$exist = Db::query('show tables like "'.$prefix.'article"');
		$res['data'] = [];
		if($exist){
			$list = Db::name('Article')
				->field('a.id,a.title,a.create_time,a.read,c.title as cate_title')
				->alias('a')
				->join('article_cate c', 'a.cate_id = c.id')
				->where(['a.delete_time' => 0])
				->order('a.id desc')
				->limit(8)
				->select()->toArray();
			foreach ($list as $key => $val) {
				$list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
			}
			$res['data'] = $list;			
		}
		return table_assign(0, '', $res);
	}
	
    // 初始历史操作记录数据
    public function initAdminLog(){
        $second_time = time() - 86400;
		$startDate = date('Y-m-d',$second_time);
        $data = [];
        for ($i = 0; $i < 365; $i++) {
            $year = date('Y', strtotime($startDate . ' -' . $i . ' days'));
            $date = date('Y-m-d', strtotime($startDate . ' -' . $i . ' days'));
			$begin_second=strtotime($date. " 00:00:00");
			$end_second=strtotime($date. " 23:59:59");
			$count = Db::name('AdminLogCount')->where('date', $date)->count();
			if($count>0){
				continue;
			}
            $data[] = [
                'year' => $year,
                'date' => $date,
                'num' => Db::name('AdminLog')->whereBetween('create_time', "$begin_second,$end_second")->count(),
                'create_time' => time()
            ];            
            // 分批插入，避免数据量过大
            if (count($data) >= 100) {
                Db::name('AdminLogCount')->insertAll($data);
                $data = [];
            }
        }        
        // 插入剩余数据
        if (!empty($data)) {
             Db::name('AdminLogCount')->insertAll($data);
        }        
        //return to_assign();
    }
    //获取最近访问记录
    public function get_last_data()
    {
        $first_time = time();
        $second_time = $first_time - 86400;
		
        $begin_first = strtotime(date('Y-m-d', $first_time) . " 00:00:00");
        $end_first = strtotime(date('Y-m-d', $first_time) . " 23:59:59");
		
        $begin_second = strtotime(date('Y-m-d', $second_time) . " 00:00:00");
        $end_second = strtotime(date('Y-m-d', $second_time) . " 23:59:59");
		
		//当天
        $data_first = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_first,$end_first")->select();
		//昨天
        $data_second = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_second,$end_second")->select();
        return to_assign(0, '', ['data_first' => hour_document($data_first), 'data_second' => hour_document($data_second)]);
    }
	
    //获取访问记录
    public function get_view_data()
    {
        $first_time = time();
        $second_time = $first_time - 86400;
        $three_time = $first_time - 86400 * 365;
		
		$begin_first = strtotime(date('Y-m-d', $first_time) . " 00:00:00");
        $end_first = strtotime(date('Y-m-d', $first_time) . " 23:59:59");
		
        $begin_time = date('Y-m-d', $three_time);
        $end_time = date('Y-m-d', $second_time);
		
		$last_count = Db::name('AdminLogCount')->where('date', $end_time)->count();
		if($last_count==0){
			//如果不存在生成记录
			$this->initAdminLog();
		}
		//当天
        $today_count = Db::name('AdminLog')->whereBetween('create_time', "$begin_first,$end_first")->count();
		//一年
        $data_three = Db::name('AdminLogCount')->field('date,num')->order('date asc')->whereBetween('date', "$begin_time,$end_time")->column('num', 'date');
		$today = date('Y-m-d');
		$data_three[$today] = $today_count;
        return to_assign(0, '', ['data_three' => $data_three]);
    }
	
	//获取员工活跃数据
    public function get_view_log()
    {		
        $times = strtotime("-30 day");
        $where = [];
        $where[] = ['uid','<>',1];
        $where[] = ['create_time', '>', $times];
        $list = Db::name('AdminLog')->field("id,uid")->where($where)->select();
        $logs = array();
        foreach ($list as $key => $value) {
            $uid = $value['uid'];
            if (empty($logs[$uid])) {
                $logs[$uid]['count'] = 1;
                $logs[$uid]['name'] = Db::name('Admin')->where('id',$uid)->value('name');
            } else {
                $logs[$uid]['count'] += 1;
            }
        }
        $counts = array_column($logs, 'count');
        array_multisort($counts, SORT_DESC, $logs);
        //攫取前10
        $data_logs = array_slice($logs, 0, 10);
        return to_assign(0, '', ['data_logs' => $data_logs]);
    }
	
	public function areaJson($type)
    {
		if($type=='province'){
			$data = Db::name('Area')->where(['level'=>1,'status'=>1])->column('name', 'id');
		}
		if($type=='city'){
			$data = Db::name('Area')->where(['level'=>2,'status'=>1])->column('name', 'id');
		}
		if($type=='district'){
			$data = Db::name('Area')->where(['level'=>3,'status'=>1])->column('name', 'id');
		}
        // 导出为 JSON 格式
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		return $json;
		/*
		输出Json文件
        // 设置响应头
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="oa_area.json"');
        // 输出 JSON 数据
        echo $json;
		*/
    }

}
