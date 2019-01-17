<?php
/**
 * @package util
 * @subpackage  date
 * 
 * 处理时间相关
 */
/**
 * 得到当前毫秒时间
 *
 * @return float
 */
function microtimeFloat()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * 获取时间差,精确到毫秒
 * 参数格式应从microtime()得到'毫秒 秒'
 *
 * @param string $time1
 * @param string $time2
 * @return unknown
 */
function getMicroTimeDiff($time1,$time2)
{	
	list($usec1,$sec1) = explode(" ",$time1);
	list($usec2,$sec2) = explode(" ",$time2);
	$secDiff = $sec2 - $sec1;
	$usecDiff = $usec2 - $usec1;
	if($secDiff > 0)
		if($usecDiff > 0)
			$diff = $secDiff.".".str_replace('.','',substr($usecDiff,2));
		else
			$diff = ($secDiff-1).".".str_replace('.','',substr((1-$usecDiff),2));
	else
		$diff = "0.".str_replace('.','',substr($usecDiff,2));
	return $diff;
}

/**
 * 把时间格式化为Y-m-d H:i:s形式
 *
 * @param int $timestamp
 * @return string
 */
function getFormatTime($timestamp)
{
	if(date('Y',$timestamp) == '1970')
		return;
	else
		return date('Y-m-d H:i:s',$timestamp);
}

/**
 * http头信息中的时间转化为时间结构数组
 *
 * @param string $gmtDate
 * @return string[]
 */
function gmtdateParse($gmtDate){
//	$months = array(1=>'Jan', 'Feb', 'Mar', 'Apr', 
//					'May', 'Jun', 'Jul', 'Aug', 
//					'Sep', 'Oct', 'Nov', 'Dec');
//	$date_str = substr(substr($gmtDate, strpos($gmtDate, ',')+2), 0, -4);
//	$ret = array();
//	$tmp = explode(' ', $date_str);
//	$ret['date'] = $tmp[0];
//	$ret['month'] = array_search($tmp[1], $months);
//	$ret['year'] = $tmp[2];
//
//	list($ret['hour'], $ret['minute'], $ret['second']) = explode(':', $tmp[3]);	
//	
//	$ret['timestamp'] = mktime($ret['hour'], $ret['minute'], 
//							   $ret['second'], $ret['month'],
//							   $ret['date'], $ret['year']);
//	return $ret;
	$r = date_parse($gmtDate);
	return gmmktime($r['hour'], $r['minute'], $r['second'], $r['month'], $r['day'], $r['year']);
}

function dateParse($date){
	$r = date_parse($date);
	return mktime($r['hour'], $r['minute'], $r['second'], $r['month'], $r['day'], $r['year']);
}

/**
 * 生成http头信息风格的时间
 *
 * @param int $timestamp
 * @return string
 */
function gmtmkDate($timestamp=NULL){
	$timestamp = ($timestamp==NULL)?time():$timestamp;
	$time_diff = mktime(0, 0, 0, 1, 1, 1970);
	$timestamp += $time_diff;
	return date("D, d M Y H:i:s", $timestamp).' GMT';
}

/**
 * 格式化中文时间格式到timestamp
 *
 * @param string $str
 * @return int
 */
function parseTime($str){
	$regex = '/(\d+)年(\d+)月(\d+)日\s+(\d+):(\d+):(\d+)/';
	$flag = preg_match($regex, $str, $match);
	if($flag){
		array_shift($match);
		return mktime($match[3], $match[4], $match[5], $match[1], $match[2], $match[0]);
	}else{
		trigger_error('日期格式不符', E_USER_WARNING);
	}
}
?>