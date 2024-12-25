<?php
declare (strict_types = 1);

namespace dateset;

/**
 * 日期时间处理类
 */
class Dateset
{
    const YEAR = 31536000;
    const MONTH = 2592000;
    const WEEK = 604800;
    const DAY = 86400;
    const HOUR = 3600;
    const MINUTE = 60;

	/**
	 * 间隔时间段格式化
	 * @param int $time 时间戳
	 * @param string $format 格式 【d：显示到天 i显示到分钟 s显示到秒】
	 * @return string
	 */
	function time_trans($time, $format = 'd')
	{
		$now = time();
		$diff = $now - $time;
		if ($diff < self::MINUTE) {
			return '1分钟前';
		} else if ($diff < self::HOUR) {
			return floor($diff / self::MINUTE) . '分钟前';
		} else if ($diff < self::DAY) {
			return floor($diff / self::HOUR) . '小时前';
		}
		$yes_start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-1 days'))); //昨天开始时间
		$yes_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 days'))); //昨天结束时间
		$two_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-2 days'))); //2天前结束时间
		$three_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-3 days'))); //3天前结束时间
		$four_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-4 days'))); //4天前结束时间
		$five_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-5 days'))); //5天前结束时间
		$six_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-6 days'))); //6天前结束时间
		$seven_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-7 days'))); //7天前结束时间

		if ($time > $yes_start_time && $time < $yes_end_time) {
			return '昨天';
		}

		if ($time > $yes_start_time && $time < $two_end_time) {
			return '1天前';
		}

		if ($time > $yes_start_time && $time < $three_end_time) {
			return '2天前';
		}

		if ($time > $yes_start_time && $time < $four_end_time) {
			return '3天前';
		}

		if ($time > $yes_start_time && $time < $five_end_time) {
			return '4天前';
		}

		if ($time > $yes_start_time && $time < $six_end_time) {
			return '5天前';
		}

		if ($time > $yes_start_time && $time < $seven_end_time) {
			return '6天前';
		}

		switch ($format) {
			case 'd':
				$show_time = date('Y-m-d', $time);
				break;
			case 'i':
				$show_time = date('Y-m-d H:i', $time);
				break;
			case 's':
				$show_time = date('Y-m-d H:i:s', $time);
				break;
		}
		return $show_time;
	}


    /**
     * 计算两个时间戳之间相差的时间
     *
     * $differ = self::differ(60, 182, 'minutes,seconds'); // array('minutes' => 2, 'seconds' => 2)
     * $differ = self::differ(60, 182, 'minutes'); // 2
     *
     * @param int    $remote timestamp to find the span of
     * @param int    $local  timestamp to use as the baseline
     * @param string $output formatting string
     * @return  string   when only a single output is requested
     * @return  array    associative list of all outputs requested
     * @from https://github.com/kohana/ohanzee-helpers/blob/master/src/Date.php
     */
    public static function differ($remote, $local = null, $output = 'years,months,weeks,days,hours,minutes,seconds')
    {
        // Normalize output
        $output = trim(strtolower((string)$output));
        if (!$output) {
            // Invalid output
            return false;
        }
        // Array with the output formats
        $output = preg_split('/[^a-z]+/', $output);
        // Convert the list of outputs to an associative array
        $output = array_combine($output, array_fill(0, count($output), 0));
        // Make the output values into keys
        extract(array_flip($output), EXTR_SKIP);
        if ($local === null) {
            // Calculate the span from the current time
            $local = time();
        }
        // Calculate timespan (seconds)
        $timespan = abs($remote - $local);
        if (isset($output['years'])) {
            $timespan -= self::YEAR * ($output['years'] = (int)floor($timespan / self::YEAR));
        }
        if (isset($output['months'])) {
            $timespan -= self::MONTH * ($output['months'] = (int)floor($timespan / self::MONTH));
        }
        if (isset($output['weeks'])) {
            $timespan -= self::WEEK * ($output['weeks'] = (int)floor($timespan / self::WEEK));
        }
        if (isset($output['days'])) {
            $timespan -= self::DAY * ($output['days'] = (int)floor($timespan / self::DAY));
        }
        if (isset($output['hours'])) {
            $timespan -= self::HOUR * ($output['hours'] = (int)floor($timespan / self::HOUR));
        }
        if (isset($output['minutes'])) {
            $timespan -= self::MINUTE * ($output['minutes'] = (int)floor($timespan / self::MINUTE));
        }
        // Seconds ago, 1
        if (isset($output['seconds'])) {
            $output['seconds'] = $timespan;
        }
        if (count($output) === 1) {
            // Only a single output was requested, return it
            return array_pop($output);
        }
        // Return array
        return $output;
    }

    /**
     * 获取指定年月拥有的天数
     * @param int $month
     * @param int $year
     * @return false|int|string
     */
    public static function days_in_month($month, $year)
    {
        if (function_exists("cal_days_in_month")) {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        } else {
            return date('t', mktime(0, 0, 0, $month, 1, $year));
        }
    }
	

	/**
	 * 将秒数转换为时间 (小时、分、秒）
	 * @param
	 */
	function getTimeBySec($time,$second=true)
	{
		if (is_numeric($time)) {
			$value = array(
				"hours" => 0,
				"minutes" => 0, "seconds" => 0,
			);
			$t='';
			if ($time >= 3600) {
				$value["hours"] = floor($time / 3600);
				$time = ($time % 3600);
				$t .= $value["hours"] . "小时";
			}
			if ($time >= 60) {
				$value["minutes"] = floor($time / 60);
				$time = ($time % 60);
				$t .= $value["minutes"] . "分钟";
			}
			if ($time > 0 && $time < 60 && $second==true) {
				$value["seconds"] = floor($time);
				$t .= $value["seconds"] . "秒";
			}
			return $t;
		} else {
			return (bool)FALSE;
		}
	}

	/**
	 * 将秒数转换为时间 (年、天、小时、分、秒）
	 * @param
	 */
	function getDateBySec($time,$second=false)
	{
		if (is_numeric($time)) {
			$value = array(
				"years" => 0, "days" => 0, "hours" => 0,
				"minutes" => 0, "seconds" => 0,
			);
			$t='';
			if ($time >= 31556926) {
				$value["years"] = floor($time / 31556926);
				$time = ($time % 31556926);
				$t .= $value["years"] . "年";
			}
			if ($time >= 86400) {
				$value["days"] = floor($time / 86400);
				$time = ($time % 86400);
				$t .= $value["days"] . "天";
			}
			if ($time >= 3600) {
				$value["hours"] = floor($time / 3600);
				$time = ($time % 3600);
				$t .= $value["hours"] . "小时";
			}
			if ($time >= 60) {
				$value["minutes"] = floor($time / 60);
				$time = ($time % 60);
				$t .= $value["minutes"] . "分钟";
			}
			if ($time < 60 && $second==true) {
				$value["seconds"] = floor($time);
				$t .= $value["seconds"] . "秒";
			}
			return $t;
		} else {
			return (bool)FALSE;
		}
	}

	/*
	 *根据年月计算有几天
	 */
	function getmonthByYM($param)
	{
		$month = $param['month'] ? $param['month'] : date('m', time());
		$year = $param['year'] ? $param['year'] : date('Y', time());
		if (in_array($month, array('1', '3', '5', '7', '8', '01', '03', '05', '07', '08', '10', '12'))) {
			$days = '31';
		} elseif ($month == 2) {
			if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0)) {
				//判断是否是闰年  
				$days = '29';
			} else {
				$days = '28';
			}
		} else {
			$days = '30';
		}
		return $days;
	}

	/**
	 * 根据时间戳计算当月天数
	 * @param
	 */
	function getmonthdays($time)
	{
		$month = date('m', $time);
		$year = date('Y', $time);
		if (in_array($month, array('1', '3', '5', '7', '8', '01', '03', '05', '07', '08', '10', '12'))) {
			$days = '31';
		} elseif ($month == 2) {
			if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0)) {
				//判断是否是闰年  
				$days = '29';
			} else {
				$days = '28';
			}
		} else {
			$days = '30';
		}
		return $days;
	}

	/**
	 * 生成从开始时间到结束时间的日期数组
	 * @param type，默认时间戳格式
	 * @param type = 1 时，date格式
	 * @param type = 2 时，获取每日开始、结束时间
	 */
	function dateList($start, $end, $type = 0)
	{
		if (!is_numeric($start) || !is_numeric($end) || ($end <= $start)) return '';
		$i = 0;
		//从开始日期到结束日期的每日时间戳数组
		$d = array();
		if ($type == 1) {
			while ($start <= $end) {
				$d[$i] = date('Y-m-d', $start);
				$start = $start + 86400;
				$i++;
			}
		} else {
			while ($start <= $end) {
				$d[$i] = $start;
				$start = $start + 86400;
				$i++;
			}
		}
		if ($type == 2) {
			$list = array();
			foreach ($d as $k => $v) {
				$list[$k] = $this->getDateRange($v);
			}
			return $list;
		} else {
			return $d;
		}
	}

	/**
	 * 获取指定日期开始时间与结束时间
	 */
	function getDateRange($timestamp)
	{
		$ret = array();
		$ret['sdate'] = strtotime(date('Y-m-d', $timestamp));
		$ret['edate'] = strtotime(date('Y-m-d', $timestamp)) + 86400;
		return $ret;
	}

	/**
	 * 生成从开始月份到结束月份的月份数组
	 * @param int $start 开始时间戳
	 * @param int $end 结束时间戳
	 */
	function monthList($start, $end)
	{
		if (!is_numeric($start) || !is_numeric($end) || ($end <= $start)) return '';
		$start = date('Y-m', $start);
		$end = date('Y-m', $end);
		//转为时间戳
		$start = strtotime($start . '-01');
		$end = strtotime($end . '-01');
		$i = 0;
		$d = array();
		while ($start <= $end) {
			//这里累加每个月的的总秒数 计算公式：上一月1号的时间戳秒数减去当前月的时间戳秒数
			$d[$i] = $start;
			$start += strtotime('+1 month', $start) - $start;
			$i++;
		}
		return $d;
	}

	/**
	 * 等于（时间段）数据处理
	 *
	 * @param $type
	 * @return array
	 * @since 2021-06-11
	 * @author fanqi
	 */
	function advancedDate($type)
	{
		// 本年度
		if ($type == 'year') {
			$arrTime = DataTime::year();
			$start_time = date('Y-m-d 00:00:00', $arrTime[0]);
			$end_time = date('Y-m-d 23:59:59', $arrTime[1]);
		}

		// 上一年度
		if ($type == 'lastYear') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-1 year'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 year'));
		}

		// 下一年度
		if ($type == 'nextYear') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 year'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+1 year'));
		}

		// 上半年
		if ($type == 'firstHalfYear') {
			$start_time = date('Y-01-01 00:00:00');
			$end_time = date('Y-06-30 23:59:59');
		}

		// 下半年
		if ($type == 'nextHalfYear') {
			$start_time = date('Y-07-01 00:00:00');
			$end_time = date('Y-12-31 23:59:59');
		}

		// 本季度
		if ($type == 'quarter') {
			$season = ceil((date('n')) / 3);
			$start_time = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
			$end_time = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
		}

		// 上一季度
		if ($type == 'lastQuarter') {
			$season = ceil((date('n')) / 3) - 1;
			$start_time = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
			$end_time = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
		}

		// 下一季度
		if ($type == 'nextQuarter') {
			$season = ceil((date('n')) / 3);
			$start_time = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 + 1, 1, date('Y')));
			$end_time = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3 + 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
		}

		// 本月
		if ($type == 'month') {
			$start_time = date('Y-m-01 00:00:00');
			$end_time = date('Y-m-31 23:59:59');
		}

		// 上月
		if ($type == 'lastMonth') {
			$start_time = date('Y-m-01 00:00:00', strtotime(date('Y-m-d') . '-1 month'));
			$end_time = date('Y-m-31 23:59:59', strtotime(date('Y-m-d') . '-1 month'));
		}

		// 下月
		if ($type == 'nextMonth') {
			$start_time = date('Y-m-01 00:00:00', strtotime(date('Y-m-d') . '+1 month'));
			$end_time = date('Y-m-31 23:59:59', strtotime(date('Y-m-d') . '+1 month'));
		}

		// 本周
		if ($type == 'week') {
			$start_time = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y')));
			$end_time = date('Y-m-d 23:59:59', mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y')));
		}

		// 上周
		if ($type == 'lastWeek') {
			$date = date("Y-m-d");
			$w = date("w", strtotime($date));
			$d = $w ? $w - 1 : 6;
			$start = date("Y-m-d", strtotime($date . " - " . $d . " days"));
			$start_time = date('Y-m-d', strtotime($start . " - 7 days"));
			$end_time = date('Y-m-d', strtotime($start . " - 1 days"));
		}

		// 下周
		if ($type == 'nextWeek') {
			$date = date("Y-m-d");
			$w = date("w", strtotime($date));
			$d = $w ? $w - 1 : 6;
			$start = date("Y-m-d", strtotime($date . " - " . $d . " days"));
			$start_time = date('Y-m-d', strtotime($start . " + 7 days"));
			$end_time = date('Y-m-d', strtotime($start . " + 13 days"));
		}

		// 今天
		if ($type == 'today') {
			$start_time = date('Y-m-d 00:00:00');
			$end_time = date('Y-m-d 23:59:59');
		}

		// 昨天
		if ($type == 'yesterday') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 day'));
		}

		// 明天
		if ($type == 'tomorrow') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+1 day'));
		}

		// 过去3天
		if ($type == 'previous3day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-3 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 day'));
		}

		// 过去5天
		if ($type == 'previous5day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-5 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 day'));
		}

		// 过去7天
		if ($type == 'previous7day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-7 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 day'));
		}
		// 过去10天
		if ($type == 'previous10day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-10 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 day'));
		}
		// 过去30天
		if ($type == 'previous30day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '-30 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '-1 day'));
		}
		// 未来3天
		if ($type == 'future3day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+3 day'));
		}
		// 未来5天
		if ($type == 'future5day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+5 day'));
		}
		// 未来7天
		if ($type == 'future7day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+7 day'));
		}
		// 未来10天
		if ($type == 'future10day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+10 day'));
		}
		// 未来30天
		if ($type == 'future30day') {
			$start_time = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . '+1 day'));
			$end_time = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+30 day'));
		}
		return [$start_time,$end_time];
	}

	/**
	 * 根据时间戳获取星期几
	 * @param $time 要转换的时间戳
	 */
	function getTimeWeek($time, $i = 0)
	{
		$weekarray = array("日", "一", "二", "三", "四", "五", "六");
		$oneD = 24 * 60 * 60;
		return "星期" . $weekarray[date("w", $time + $oneD * $i)];
	}
}
