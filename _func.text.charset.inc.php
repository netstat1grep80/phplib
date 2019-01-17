<?php
/**
 * @package text
 * @subpackage charset
 * 中文GB/Unicode/UTF-8转换相关
 * 在iconv不可用情况下的替代方法
 */
require_once('_const.text.charset.inc.php');
function gb2utf8($text) {
	global $charset;
	//提取文本中的成分，汉字为一个元素，连续的非汉字为一个元素
	preg_match_all("/(?:[\x80-\xff].)|[\x01-\x7f]+/",$text,$tmp);
	$tmp = $tmp[0];
	//分离出汉字
	$ar = array_intersect($tmp, array_keys($charset));
	//替换汉字编码
	foreach($ar as $k=>$v){
		$c = $charset[$v];
		for($i=0; $i<strlen($c); $i++){
			echo hexdec($c[$i]);
		}
		$tmp[$k] = $charset[$v];
	}
	//返回换码后的串
	return join('',$tmp);
}

/**
 * utf-8到gb2312
 *
 * @param string $text
 * @return string
 */
function utf82gb($text) {
	global $charset;
	$p = "/[xf0-xf7][x80-xbf]{3}|[xe0-xef][x80-xbf]{2}|[xc2-xdf][x80-xbf]|[x01-x7f]+/";
	preg_match_all($p,$text,$r);
	$utf8 = array_flip($charset);
	foreach($r[0] as $k=>$v)
	if(isset($utf8[$v]))
	$r[0][$k] = $utf8[$v];
	return join('',$r[0]);
}

/**
 * gb2312 -> unicode
 *
 * @param string $text
 * @return string
 */
function gb2unicode($text) {
	global $charset;
	//提取文本中的成分，汉字为一个元素，连续的非汉字为一个元素
	preg_match_all("/(?:[\x80-\xff].)|[\x01-\x7f]+/",$text,$tmp);
	$tmp = $tmp[0];
	//分离出汉字
	$ar = array_intersect($tmp, array_keys($charset));
	//替换汉字编码
	foreach($ar as $k=>$v)
	$tmp[$k] = utf82unicode_char($charset[$v]);
	//返回换码后的串
	return join('',$tmp);
}

/**
 * gb2312 -> unicode
 *
 * @param string $text
 * @return string
 */
function gb2unicode2($text)
{
	preg_match_all("/[\x80-\xff]?./",$text,$ar);
	$tempstr = "";
	foreach($ar[0] as $v)
	$tempstr .= utf82unicode_char(iconv("GB2312","UTF-8",$v));
	return $tempstr;
}

/**
 * utf8 char -> unicode char
 *
 * @param string(1) $c
 * @return string(1)
 */
function utf82unicode_char($c) {
	switch(strlen($c)) {
		case 1:
		$n = ord($c);
		case 2:
		$n = (ord($c[0]) & 0x3f) << 6;
		$n += ord($c[1]) & 0x3f;
		break;
		case 3:
		$n = (ord($c[0]) & 0x1f) << 12;
		$n += (ord($c[1]) & 0x3f) << 6;
		$n += ord($c[2]) & 0x3f;
		break;
		case 4:
		$n = (ord($c[0]) & 0x0f) << 18;
		$n += (ord($c[1]) & 0x3f) << 12;
		$n += (ord($c[2]) & 0x3f) << 6;
		$n += ord($c[3]) & 0x3f;
		break;
	}
	return "&#".$n.";";
}

/**
 * unicode -> gb2312
 *
 * @param string $str
 * @return string
 */
function unicode2gb($str) {
	$str = rawurldecode($str);
	preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
	$ar = $r[0];
	print_r($ar);
	foreach($ar as $k=>$v) {
		if(substr($v,0,2) == "%u")
		$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,-4)));
		elseif(substr($v,0,3) == "&#x")
		$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,3,-1)));
		elseif(substr($v,0,2) == "&#") {
			echo substr($v,2,-1)."<br>";
			$ar[$k] = iconv("UCS-2","GB2312",pack("n",substr($v,2,-1)));
		}
	}
	return join("",$ar);
}
?>