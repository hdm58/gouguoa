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
declare (strict_types = 1);
namespace app\analysis\controller;

use app\api\BaseController;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//客户数量
	public function customer_number()
    {
        $param = get_params();
		$types = isset($param['types']) ? $param['types'] : 0;
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['a.belong_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['a.delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['ad.did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['a.sign_uid','=',$param['uid']];
		}	
        $list = Db::name('Customer')
            ->field([
                'a.belong_uid',
                'd.title',
                'ad.name',
                'COUNT(*) as total_num',
            ])
			->alias('a')
			->join('Admin ad','ad.id = a.belong_uid')
			->join('Department d','d.id = ad.did')
            ->group('a.belong_uid')
			->where($where)
            ->order('total_num', 'asc') // 按收款金额降序
            ->select()
            ->toArray();
			
		foreach ($list as &$item) {
			$item['sale_num'] =  Db::name('Order')->where([['sign_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]],['check_status','=',2],['sign_uid','=',$item['belong_uid']]])->count();
			$persent = $item['sale_num']*100/$item['total_num'];
			$item['persent'] = number_format($persent, 2).'%';
		}
        // 提取姓名、金额、数量
        $names = array_column($list, 'name');
        $total = array_column($list, 'total_num');
        $sales = array_column($list, 'sale_num');

        // 构造 yAxis：第一个为空字符串
        $yAxis = array_merge([''], $names);

        // 构造 series.data：第一个值为 null
        $totaltData = array_merge([null], $total);
        $salesData = array_merge([null], $sales);

        // 构建响应
        $result = [
            'title' => [
                'text' => '员工客户量'
            ],
            'legend' => ['员工客户量'],
            'yAxis' => $yAxis,
            'series' => [
                [
                    'name' => '客户数量',
                    'type' => 'bar',
                    'label' => [
                        'show' => true,
                        'position' => 'right',
                        'valueAnimation' => true,
                        'formatter' => '{c} 个'
                    ],
                    'data' => $totaltData
                ],
				[
                    'name' => '客户成交量',
                    'type' => 'bar',
                    'label' => [
                        'show' => true,
                        'position' => 'right',
                        'valueAnimation' => true,
                        'formatter' => '{c} 个'
                    ],
                    'data' => $salesData
                ]
            ]
        ];
		if($types==1){
			$res['data'] = $list;
			return table_assign(0, '', $res);
		}else{		
			return to_assign(0, '', $result);
		}
    }
	
	//客户行业
	public function customer_industry()
    {
        $param = get_params();
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['create_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['delete_time','=',0];
		$where[]=['is_clue','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['belong_uid','=',$param['uid']];
		}
        $data = [];
        $industrys = get_base_data('Industry');
        foreach ($industrys as $industry) {
            $data[] = [
                'name' => $industry['title'],
                'value' => Db::name('Customer')->where([['industry_id','=',$industry['id']]])->where($where)->count()
            ];
        }
        $result = [
            'title' => [
                'text' => '客户所属行业'
            ],
            'series' => [
                'data' => $data
            ]
        ];
		return to_assign(0, '', $result);
    }
	
	//客户来源
	public function customer_source()
    {
        $param = get_params();
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['create_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['delete_time','=',0];
		$where[]=['is_clue','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['belong_uid','=',$param['uid']];
		}
        $data = [];
        $sources = get_base_data('customer_source');
        foreach ($sources as $source) {
            $data[] = [
                'name' => $source['title'],
                'value' => Db::name('Customer')->where([['source_id','=',$source['id']]])->where($where)->count()
            ];
        }
        $result = [
            'title' => [
                'text' => '客户来源'
            ],
            'series' => [
                'data' => $data
            ]
        ];
		return to_assign(0, '', $result);
    }
	
	//客户状态
	public function customer_status()
    {
        $param = get_params();
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['create_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['delete_time','=',0];
		$where[]=['is_clue','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['belong_uid','=',$param['uid']];
		}
        $data = [];
        $statuss = get_base_type_data('basic_customer',1);
        foreach ($statuss as $status) {
            $data[] = [
                'name' => $status['title'],
                'value' => Db::name('Customer')->where([['customer_status','=',$status['id']]])->where($where)->count()
            ];
        }
        $result = [
            'title' => [
                'text' => '客户状态'
            ],
            'series' => [
                'data' => $data
            ]
        ];
		return to_assign(0, '', $result);
    }
	
	//客户类型
	public function customer_grade()
    {
        $param = get_params();
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['create_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['delete_time','=',0];
		$where[]=['is_clue','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['belong_uid','=',$param['uid']];
		}
        $data = [];
        $grades = get_base_data('customer_grade');
        foreach ($grades as $grade) {
            $data[] = [
                'name' => $grade['title'],
                'value' => Db::name('Customer')->where([['grade_id','=',$grade['id']]])->where($where)->count()
            ];
        }
        $result = [
            'title' => [
                'text' => '客户类型'
            ],
            'series' => [
                'data' => $data
            ]
        ];
		return to_assign(0, '', $result);
    }
	
	//跟进记录
	public function customer_trace()
    {
        $param = get_params();
		$year = isset($param['year']) ? $param['year'] : date('Y');
		$where=[];
		$where[]=['delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['admin_id','=',$param['uid']];
		}
		// 初始化月份列表
		$months = [];
		$seriesData = [
			'leads' => [],
			'customers' => [],
			'opportunities' => []
		];
		for ($i = 1; $i <= 12; $i++) {
			$monthStr = sprintf("%02d", $i);
			$months[] = "{$year}-{$monthStr}";
			$month_start=strtotime("{$year}-{$monthStr}-01");
			$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
			// 线索跟进次数
			$leadsCount = Db::name('CustomerTrace')->where([['follow_time','between',[$month_start,$month_end]],['is_clue','=',1]])->where($where)->count();

			// 客户跟进次数
			$customersCount = Db::name('CustomerTrace')->where([['follow_time','between',[$month_start,$month_end]],['is_clue','=',0]])->where($where)->count();

			// 商机跟进次数
			$opportunitiesCount = Db::name('CustomerTrace')->where([['follow_time','between',[$month_start,$month_end]],['is_clue','=',0],['chance_id','>',0]])->where($where)->count();

			$seriesData['leads'][] = (int)$leadsCount;
			$seriesData['customers'][] = (int)$customersCount;
			$seriesData['opportunities'][] = (int)$opportunitiesCount;
		}

		$result = [
			'title' => [
				'text' => '跟进次数汇总'
			],
			'legend' => [
				'线索跟进次数',
				'客户跟进次数',
				'商机跟进次数'
			],
			'xaxis' => $months,
			'series' => [
				[
					'name' => '线索跟进次数',
					'type' => 'line',
					'data' => $seriesData['leads']
				],
				[
					'name' => '客户跟进次数',
					'type' => 'line',
					'data' => $seriesData['customers']
				],
				[
					'name' => '商机跟进次数',
					'type' => 'line',
					'data' => $seriesData['opportunities']
				]
			]
		];
		return to_assign(0, '', $result);
    }
	
	//跟进记录方式
	public function customer_trace_type()
    {
        $param = get_params();
		$year = isset($param['year']) ? $param['year'] : date('Y');
		$year_start=strtotime("{$year}-01-01");
		$year_end=strtotime("{$year}-12-31 23:59:59");
		$where=[];
		$where[]=['follow_time','between',[$year_start,$year_end]];
		$where[]=['delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['admin_id','=',$param['uid']];
		}
        $data = [];
        $types = get_base_type_data('basic_customer',3);
        foreach ($types as $type) {
            $data[] = [
                'name' => $type['title'],
                'value' => Db::name('CustomerTrace')->where([['types','=',$type['id']]])->where($where)->count()
            ];
        }

        $result = [
            'title' => [
                'text' => '跟进方式分析'
            ],
            'series' => [
                'data' => $data
            ]
        ];
		return to_assign(0, '', $result);
    }
	
	//跟进记录阶段
	public function customer_trace_stage()
    {
        $param = get_params();
		$year = isset($param['year']) ? $param['year'] : date('Y');
		$year_start=strtotime("{$year}-01-01");
		$year_end=strtotime("{$year}-12-31 23:59:59");
		$where=[];
		$where[]=['follow_time','between',[$year_start,$year_end]];
		$where[]=['delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['admin_id','=',$param['uid']];
		}
        $data = [];
        $stages = get_base_type_data('basic_customer',4);
        foreach ($stages as $stage) {
            $data[] = [
                'name' => $stage['title'],
                'value' => Db::name('CustomerTrace')->where([['stage','=',$stage['id']]])->where($where)->count()
            ];
        }
        $result = [
            'title' => [
                'text' => '跟进阶段分析'
            ],
            'series' => [
                'data' => $data
            ]
        ];
		return to_assign(0, '', $result);
    }
	
	//合同分析
	public function sale_order()
    {
        $param = get_params();
		$year = isset($param['year']) ? $param['year'] : date('Y');
		$types = isset($param['types']) ? $param['types'] :1;
		$lastyear = $year-1;
		$where=[];
		$where[]=['delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['sign_uid','=',$param['uid']];
		}
		// 初始化月份列表
		$months = [];
		$data2024 = [];
        $data2025 = [];
        $datapersent = [];
		for ($i = 1; $i <= 12; $i++) {
			$monthStr = sprintf("%02d", $i);
			$months[] = "{$year}-{$monthStr}";
			$month_start=strtotime("{$year}-{$monthStr}-01");
			$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
			
			$last_start=strtotime("{$lastyear}-{$monthStr}-01");
			$last_end=strtotime(date('Y-m-t',$last_start).' 23:59:59');
			// 年度某月合同总数
			$thisOrderCount = Db::name('Contract')->where([['sign_time','between',[$month_start,$month_end]]])->where($where)->count();
			// 年度某月合同总额
			$thisOrderAmount = Db::name('Contract')->where([['sign_time','between',[$month_start,$month_end]]])->where($where)->sum('cost');
			
			// 上年度某月合同总数
			$lastOrderCount = Db::name('Contract')->where([['sign_time','between',[$last_start,$last_end]]])->where($where)->count();
			// 上年度某月合同总额
			$lastOrderAmount = Db::name('Contract')->where([['sign_time','between',[$last_start,$last_end]]])->where($where)->sum('cost');
			
			//合同数同比增加率
			$persentCount = '-';
			if($lastOrderCount>0 && $thisOrderCount==0){
				$persentCount = 0;
			}
			if($lastOrderCount==0 && $thisOrderCount>0){
				$persentCount = number_format(($thisOrderCount)*100/1, 2).'%';
			}
			if($lastOrderCount>0 && $thisOrderCount>0){
				$persentCount = number_format(($thisOrderCount-$lastOrderCount)*100/$lastOrderCount, 2).'%';
			}
			
			//合同金额同比增加率
			$persentAmount = '-';
			if($lastOrderAmount>0 && $thisOrderAmount==0){
				$persentAmount = 0;
			}
			if($lastOrderAmount==0 && $thisOrderAmount>0){
				$persentAmount = number_format(($thisOrderAmount)*100/1, 2).'%';
			}
			if($lastOrderAmount>0 && $thisOrderAmount>0){
				$persentAmount = number_format(($thisOrderAmount-$lastOrderAmount)*100/$lastOrderAmount, 2).'%';
			}
			
			if($types==1){
				$data2024[] = $lastOrderAmount;
				$data2025[] = $thisOrderAmount;
				$datapersent[] = $persentAmount;
			}
			if($types==2){
				$data2024[] = $lastOrderCount;
				$data2025[] = $thisOrderCount;
				$datapersent[] = $persentCount;
			}
		}
		
		if($types==1){
			$result = [
				'title' => [
					'text' => '销售金额同比增长分析'
				],
				'legend' => [
					$lastyear.'年销售合同金额',
					$year.'年销售合同金额',
					'同比增长率'
				],
				'xaxis' => $months,
				'yaxis' => [
					[
						'type' => 'value',
						'name' => '销售合同金额',
						'interval' => '10',
						'axisLabel' => [
							'formatter' => '{value} 元'
						]
					],
					[
						'type' => 'value',
						'name' => '增长率',
						'min' => '0',
						'interval' => '20',
						'axisLabel' => [
							'formatter' => '{value} %'
						]
					]
				],
				'series' => [
					[
						'name' => $lastyear.'年销售合同金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2024
					],
					[
						'name' => $year.'年销售合同金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2025
					],
					[
						'name' => '同比增长率',
						'type' => 'line',
						'yAxisIndex' => '1',
						'data' => $datapersent
					]
				]
			];
		}
		else{
			$result = [
				'title' => [
					'text' => '销售合同数量同比增长分析'
				],
				'legend' => [
					$lastyear.'年销售合同数量',
					$year.'年销售合同数量',
					'同比增长率'
				],
				'xaxis' => $months,
				'yaxis' => [
					[
						'type' => 'value',
						'name' => '销售合同数量',
						'interval' => '10',
						'axisLabel' => [
							'formatter' => '{value} 单'
						]
					],
					[
						'type' => 'value',
						'name' => '增长率',
						'min' => '0',
						'interval' => '20',
						'axisLabel' => [
							'formatter' => '{value} %'
						]
					]
				],
				'series' => [
					[
						'name' => $lastyear.'年销售合同数量',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2024
					],
					[
						'name' => $year.'年销售合同数量',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2025
					],
					[
						'name' => '同比增长率',
						'type' => 'line',
						'yAxisIndex' => '1',
						'data' => $datapersent
					]
				]
			];
			
		}
		return to_assign(0, '', $result);
    }

	//合同排行
	public function sale_order_rank()
    {
        $param = get_params();
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['a.sign_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['a.delete_time','=',0];
		$where[]=['a.check_status','=',2];
		if(!empty($param['did'])){
			$where[]=['a.did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['a.sign_uid','=',$param['uid']];
		}	
        $list = Db::name('Contract')
            ->field([
                'a.sign_uid',
                'ad.name',
                'SUM(a.cost) as total_amount',
            ])
			->alias('a')
			->join('Admin ad','ad.id = a.sign_uid')
            ->group('a.sign_uid')
			->where($where)
            ->order('total_amount', 'asc') // 按收款金额降序
            ->select()
            ->toArray();

        // 提取姓名、金额、数量
        $names = array_column($list, 'name');
        $amounts = array_column($list, 'total_amount');

        // 转换金额为整数（如果数据库是分，这里除以100并四舍五入）
        // 示例中数据较小，假设单位已是“元”，且取整
        $amounts = array_map(fn($v) => $v, $amounts);

        // 构造 yAxis：第一个为空字符串
        $yAxis = array_merge([''], $names);

        // 构造 series.data：第一个值为 null
        $amountData = array_merge([null], $amounts);

        // 构建响应
        $result = [
            'title' => [
                'text' => '销售合同排行'
            ],
            'legend' => ['销售合同排行'],
            'yAxis' => $yAxis,
            'series' => [
                [
                    'name' => '合同金额',
                    'type' => 'bar',
                    'label' => [
                        'show' => true,
                        'position' => 'right',
                        'valueAnimation' => true,
                        'formatter' => '{c} 元'
                    ],
                    'data' => $amountData
                ]
            ]
        ];
        return to_assign(0, '', $result);
    }

	//收款排行
	public function sale_income_rank()
    {
        $param = get_params();
		$where=[];
		$diff_time =explode('~', $param['diff_time']);
		$where[] = ['a.enter_time', 'between',[strtotime($diff_time[0]),strtotime($diff_time[1].' 23:59:59')]];
		$where[]=['a.delete_time','=',0];
		$where[]=['a.check_status','=',2];
		if(!empty($param['did'])){
			$where[]=['a.did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['a.admin_id','=',$param['uid']];
		}	
        $list = Db::name('InvoiceIncome')
            ->field([
                'a.admin_id',
                'ad.name',
                'SUM(a.amount) as total_amount',
            ])
			->alias('a')
			->join('Admin ad','ad.id = a.admin_id')
            ->group('a.admin_id')
			->where($where)
            ->order('total_amount', 'asc') // 按收款金额降序
            ->select()
            ->toArray();

        // 提取姓名、金额、数量
        $names = array_column($list, 'name');
        $amounts = array_column($list, 'total_amount');

        // 转换金额为整数（如果数据库是分，这里除以100并四舍五入）
        // 示例中数据较小，假设单位已是“元”，且取整
        $amounts = array_map(fn($v) => $v, $amounts);

        // 构造 yAxis：第一个为空字符串
        $yAxis = array_merge([''], $names);

        // 构造 series.data：第一个值为 null
        $amountData = array_merge([null], $amounts);

        // 构建响应
        $result = [
            'title' => [
                'text' => '收款排行'
            ],
            'legend' => ['收款排行'],
            'yAxis' => $yAxis,
            'series' => [
                [
                    'name' => '收款金额',
                    'type' => 'bar',
                    'label' => [
                        'show' => true,
                        'position' => 'right',
                        'valueAnimation' => true,
                        'formatter' => '{c} 元'
                    ],
                    'data' => $amountData
                ]
            ]
        ];
        return to_assign(0, '', $result);
    }

	//发票分析
	public function finance_invoice()
    {
        $param = get_params();
		$year = isset($param['year']) ? $param['year'] : date('Y');
		$types = isset($param['types']) ? $param['types'] :1;
		$lastyear = $year-1;
		$where=[];
		$where[]=['delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['admin_id','=',$param['uid']];
		}
		// 初始化月份列表
		$months = [];
		$data2024 = [];
        $data2025 = [];
        $datapersent = [];
		for ($i = 1; $i <= 12; $i++) {
			$monthStr = sprintf("%02d", $i);
			$months[] = "{$year}-{$monthStr}";
			$month_start=strtotime("{$year}-{$monthStr}-01");
			$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
			
			$last_start=strtotime("{$lastyear}-{$monthStr}-01");
			$last_end=strtotime(date('Y-m-t',$last_start).' 23:59:59');
			// 本年度某月销项发票总额
			$thisInvoiceAmount = Db::name('Invoice')->where([['open_time','between',[$month_start,$month_end]]])->where($where)->sum('amount');
			// 本年度某月进项发票总额
			$thisTicketAmount = Db::name('Ticket')->where([['open_time','between',[$month_start,$month_end]]])->where($where)->sum('amount');
			
			// 上年度某月销项发票总额
			$lastInvoiceAmount = Db::name('Invoice')->where([['open_time','between',[$last_start,$last_end]]])->where($where)->sum('amount');
			// 上年度某月进项发票总额
			$lastTicketAmount = Db::name('Ticket')->where([['open_time','between',[$last_start,$last_end]]])->where($where)->sum('amount');
			
			//销项发票同比增加率
			$persentInvoice = '-';
			if($lastInvoiceAmount>0 && $thisInvoiceAmount==0){
				$persentInvoice = 0;
			}
			if($lastInvoiceAmount==0 && $thisInvoiceAmount>0){
				$persentInvoice = number_format(($thisInvoiceAmount)*100/1, 2).'%';
			}
			if($lastInvoiceAmount>0 && $thisInvoiceAmount>0){
				$persentInvoice = number_format(($thisInvoiceAmount-$lastInvoiceAmount)*100/$lastInvoiceAmount, 2).'%';
			}
			
			//进项发票总额同比增加率
			$persentTicket = '-';
			if($lastTicketAmount>0 && $thisTicketAmount==0){
				$persentTicket = 0;
			}
			if($lastTicketAmount==0 && $thisTicketAmount>0){
				$persentTicket= number_format(($thisTicketAmount)*100/1, 2).'%';
			}
			if($lastTicketAmount>0 && $thisTicketAmount>0){
				$persentTicket = number_format(($thisTicketAmount-$lastTicketAmount)*100/$lastTicketAmount, 2).'%';
			}
			
			if($types==1){
				$data2024[] = $lastInvoiceAmount;
				$data2025[] = $thisInvoiceAmount;
				$datapersent[] = $persentInvoice;
			}
			if($types==2){
				$data2024[] = $lastTicketAmount;
				$data2025[] = $thisTicketAmount;
				$datapersent[] = $persentTicket;
			}
		}
		
		if($types==1){
			$result = [
				'title' => [
					'text' => '销项发票同比增长分析'
				],
				'legend' => [
					$lastyear.'年销项发票金额',
					$year.'年销项发票金额',
					'同比增长率'
				],
				'xaxis' => $months,
				'yaxis' => [
					[
						'type' => 'value',
						'name' => '销项发票金额',
						'interval' => '10',
						'axisLabel' => [
							'formatter' => '{value} 元'
						]
					],
					[
						'type' => 'value',
						'name' => '增长率',
						'min' => '0',
						'interval' => '20',
						'axisLabel' => [
							'formatter' => '{value} %'
						]
					]
				],
				'series' => [
					[
						'name' => $lastyear.'年销项发票金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2024
					],
					[
						'name' => $year.'年销项发票金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2025
					],
					[
						'name' => '同比增长率',
						'type' => 'line',
						'yAxisIndex' => '1',
						'data' => $datapersent
					]
				]
			];
		}
		else{
			$result = [
				'title' => [
					'text' => '进项发票同比增长分析'
				],
				'legend' => [
					$lastyear.'年进项发票金额',
					$year.'年进项发票金额',
					'同比增长率'
				],
				'xaxis' => $months,
				'yaxis' => [
					[
						'type' => 'value',
						'name' => '进项发票金额',
						'interval' => '10',
						'axisLabel' => [
							'formatter' => '{value} 元'
						]
					],
					[
						'type' => 'value',
						'name' => '增长率',
						'min' => '0',
						'interval' => '20',
						'axisLabel' => [
							'formatter' => '{value} %'
						]
					]
				],
				'series' => [
					[
						'name' => $lastyear.'年进项发票金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2024
					],
					[
						'name' => $year.'年进项发票金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2025
					],
					[
						'name' => '同比增长率',
						'type' => 'line',
						'yAxisIndex' => '1',
						'data' => $datapersent
					]
				]
			];
			
		}
		return to_assign(0, '', $result);
    }


	//收付款分析
	public function finance_payment()
    {
        $param = get_params();
		$year = isset($param['year']) ? $param['year'] : date('Y');
		$types = isset($param['types']) ? $param['types'] :1;
		$lastyear = $year-1;
		$where=[];
		$where[]=['delete_time','=',0];
		if(!empty($param['did'])){
			$where[]=['did','=',$param['did']];
		}
		if(!empty($param['uid'])){
			$where[]=['admin_id','=',$param['uid']];
		}
		// 初始化月份列表
		$months = [];
		$data2024 = [];
        $data2025 = [];
        $datapersent = [];
		for ($i = 1; $i <= 12; $i++) {
			$monthStr = sprintf("%02d", $i);
			$months[] = "{$year}-{$monthStr}";
			$month_start=strtotime("{$year}-{$monthStr}-01");
			$month_end=strtotime(date('Y-m-t',$month_start).' 23:59:59');
			
			$last_start=strtotime("{$lastyear}-{$monthStr}-01");
			$last_end=strtotime(date('Y-m-t',$last_start).' 23:59:59');
			// 本年度某月收款总额
			$thisIncomeAmount = Db::name('InvoiceIncome')->where([['enter_time','between',[$month_start,$month_end]]])->where($where)->sum('amount');
			// 本年度某月付款总额
			$thisPaymentAmount = Db::name('TicketPayment')->where([['pay_time','between',[$month_start,$month_end]]])->where($where)->sum('amount');
			
			// 上年度某月收款总额
			$lastIncomeAmount = Db::name('InvoiceIncome')->where([['enter_time','between',[$last_start,$last_end]]])->where($where)->sum('amount');
			// 上年度某月付款总额
			$lastPaymentAmount = Db::name('TicketPayment')->where([['pay_time','between',[$last_start,$last_end]]])->where($where)->sum('amount');
			
			//收款同比增加率
			$persentIncome = '-';
			if($lastIncomeAmount>0 && $thisIncomeAmount==0){
				$persentIncome = 0;
			}
			if($lastIncomeAmount==0 && $thisIncomeAmount>0){
				$persentIncome = number_format(($thisIncomeAmount)*100/1, 2).'%';
			}
			if($lastIncomeAmount>0 && $thisIncomeAmount>0){
				$persentIncome = number_format(($thisIncomeAmount-$lastIncomeAmount)*100/$lastIncomeAmount, 2).'%';
			}
			
			//付款同比增加率
			$persentPayment = '-';
			if($lastPaymentAmount>0 && $thisPaymentAmount==0){
				$persentPayment = 0;
			}
			if($lastPaymentAmount==0 && $thisPaymentAmount>0){
				$persentPayment= number_format(($thisPaymentAmount)*100/1, 2).'%';
			}
			if($lastPaymentAmount>0 && $thisPaymentAmount>0){
				$persentPayment = number_format(($thisPaymentAmount-$lastPaymentAmount)*100/$lastPaymentAmount, 2).'%';
			}
			
			if($types==1){
				$data2024[] = $lastIncomeAmount;
				$data2025[] = $thisIncomeAmount;
				$datapersent[] = $persentIncome;
			}
			if($types==2){
				$data2024[] = $lastPaymentAmount;
				$data2025[] = $thisPaymentAmount;
				$datapersent[] = $persentPayment;
			}
		}
		
		if($types==1){
			$result = [
				'title' => [
					'text' => '收款同比增长分析'
				],
				'legend' => [
					$lastyear.'年收款金额',
					$year.'年收款金额',
					'同比增长率'
				],
				'xaxis' => $months,
				'yaxis' => [
					[
						'type' => 'value',
						'name' => '收款金额',
						'interval' => '10',
						'axisLabel' => [
							'formatter' => '{value} 元'
						]
					],
					[
						'type' => 'value',
						'name' => '增长率',
						'min' => '0',
						'interval' => '20',
						'axisLabel' => [
							'formatter' => '{value} %'
						]
					]
				],
				'series' => [
					[
						'name' => $lastyear.'年收款金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2024
					],
					[
						'name' => $year.'年收款金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2025
					],
					[
						'name' => '同比增长率',
						'type' => 'line',
						'yAxisIndex' => '1',
						'data' => $datapersent
					]
				]
			];
		}
		else{
			$result = [
				'title' => [
					'text' => '付款同比增长分析'
				],
				'legend' => [
					$lastyear.'年付款金额',
					$year.'年付款金额',
					'同比增长率'
				],
				'xaxis' => $months,
				'yaxis' => [
					[
						'type' => 'value',
						'name' => '付款金额',
						'interval' => '10',
						'axisLabel' => [
							'formatter' => '{value} 元'
						]
					],
					[
						'type' => 'value',
						'name' => '增长率',
						'min' => '0',
						'interval' => '20',
						'axisLabel' => [
							'formatter' => '{value} %'
						]
					]
				],
				'series' => [
					[
						'name' => $lastyear.'年付款金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2024
					],
					[
						'name' => $year.'年付款金额',
						'type' => 'bar',
						'itemStyle' => [
							'normal' => [
								'label' => [
									'show' => true,
									'position' => 'top',
									'textStyle' => [
										'color' => 'black',
										'fontSize' => 14
									]
								]
							]
						],
						'data' => $data2025
					],
					[
						'name' => '同比增长率',
						'type' => 'line',
						'yAxisIndex' => '1',
						'data' => $datapersent
					]
				]
			];
			
		}
		return to_assign(0, '', $result);
    }










}
