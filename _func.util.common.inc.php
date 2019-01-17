<?php
/**
 * @package util
 * @subpackage common
 * 基础方法
 */
/**
 * 断言变量
 *
 * @param mixed $var
 * @param boolean $isarray
 * @return boolean
 */
function asserting($var, $isarray=false){
	$flag = isset($var)&&$var!=NULL&&!empty($var);
	if($isarray){
		$flag = $flag&&(gettype($var)=='array');
	}
	return $flag;
}

/**
 * 字符串转化为散列整数
 *
 * @param string $key
 * @return int
 */
function hashCode($key){
	$hashCode = 0;
	for ($i = 0, $len = strlen($key); $i < $len; $i++) {
		$hashCode = (int)(($hashCode*33)+ord($key[$i])) & 0x7fffffff;
	}
	return $hashCode;
}


/**
 * 数组排序函数
 * 说明：
 * 比如后台接口返回一个排好序的数组，但是用该数组的返回值调用其余接口时，
 * 返回的数组却不能按照原先的顺序排序，此函数用来将返回的数组按照原先的数组顺序排序
 * e.g.
 * $a = array(array('FLD_ID' => 4), array('FLD_ID' => 2), 
 *      	  array('FLD_ID' => 1), array('FLD_ID' => 7));
 * $b = array(array('ID' => 1, 'NAME' => 'name1'), array('ID' => 2, 'NAME' => 'name2'),
 *            array('ID' => 4, 'NAME' => 'name4'), array('ID' => 7, 'NAME' => 'name7'));
 * $b = array_key_bind($b, $a, 'ID', 'FLD_ID');
 * var_dump($b);
 *
 * @param array $arr	需要排序的数组
 * @param array $m		原先的数组
 * @param string $key1	需要排序的数组下标
 * @param string $key2	原先数组的下标
 * @param string $key3	排序后的顺序（内部用，可以不传）
 * @return array
 */
function array_key_bind(&$arr, $m, $key1, $key2, $key3 = 'RID') {
	foreach ($m as $key => $value) {
		$m[$key] = $value[$key2];
	}
	array_walk($arr, create_function('&$value, $key, $m', "\$value['$key3'] = \$m[\$value['$key1']];"), array_flip($m));
	return array_key_sort($arr, $key3);
}

//该函数为array_key_bind专用
function array_key_sort($arr, $l, $f = 'strnatcasecmp') {
	usort($arr, create_function('$a, $b', "return $f(\$a['$l'], \$b['$l']);"));
	return $arr;
}


define('URL_PARAMETER_INT', 		'int');
define('URL_PARAMETER_INTEGER', 	'integer');
define('URL_PARAMETER_FLOAT', 		'float');
define('URL_PARAMETER_STRING', 		'string');
define('URL_PARAMETER_BOOLEAN', 	'boolean');
define('URL_PARAMETER_ARRAY', 		'array');

define('URL_PARAMETER_BOOLEAN_YES', 	'YES');
define('URL_PARAMETER_BOOLEAN_NO', 		'NO');
define('URL_PARAMETER_BOOLEAN_TRUE', 	'TRUE');
define('URL_PARAMETER_BOOLEAN_FALSE', 	'FALSE');

/**
 * 转换变量类型
 *
 * @param mixed $var
 * @param string $type - 见常量定义
 * @return mixed
 */
function convertVarType($var, $type){
	if(gettype($var)==URL_PARAMETER_ARRAY && $type!=URL_PARAMETER_ARRAY){
		foreach($var AS &$value){
			switch ($type){
				case URL_PARAMETER_BOOLEAN:
				if($value==1 || strtoupper($value)==URL_PARAMETER_BOOLEAN_YES || strtoupper($value)==URL_PARAMETER_BOOLEAN_TRUE){
					$value = true;
				}else{
					$value = false;
				}
				break;
				case URL_PARAMETER_FLOAT:
				$value = floatval($value);
				break;
				case URL_PARAMETER_INT:
				case URL_PARAMETER_INTEGER:
				$value = intval($value);
				break;
				case URL_PARAMETER_STRING:
				$value .= '';
				if(!isUTF8($value)){
					$value = iconv('GB18030', 'UTF-8', $value);
				}
				if(ini_get('magic_quotes_gpc')){
					$value = stripslashes($value);
				}
				break;
				default:
				break;
			}
		}
		return $var;
	}
	
	switch ($type){
		case URL_PARAMETER_ARRAY:
			if(gettype($var) != 'array') $ret=array();
			break;
		case URL_PARAMETER_BOOLEAN:
			if($var==1 || strtoupper($var)==URL_PARAMETER_BOOLEAN_YES || strtoupper($var)==URL_PARAMETER_BOOLEAN_TRUE){
				$var = true;
			}else{
				$var = false;
			}
			break;
		case URL_PARAMETER_FLOAT:
			$var = floatval($var);
			break;
		case URL_PARAMETER_INT:
		case URL_PARAMETER_INTEGER:
			$var = intval($var);
			break;
		case URL_PARAMETER_STRING:
			$var .= '';
			if(!isUTF8($var)){
				$var = iconv('GB18030', 'UTF-8', $var);
			}
			break;
		default:
			break;
	}
	
	return $var;
}
/**
 * 对一个数组里的每个元素都使用同一函数运算，并返回（将改变原始数组）
 *
 * @param array $stack 
 * @param mixed $func
 * @param string type
 * @param array $otherparams
 * @return array
 */
function &iterator(&$stack, $func, $type=NULL, $preparams=array(), $postparams=array()){
	foreach ($stack AS &$value){
		if($type===NULL || gettype($value)==$type){
			$value = call_user_func_array($func, array_merge($preparams, array($value), $postparams));
		}
		unset($value);
	}
	return $stack;
}

function debug_output($msg){
	if( defined("IS_DEBUG") && IS_DEBUG===true)echo("\n<!--[DEBUG]    ".$msg."     -->\n");
}

function tryThese(){
	$args = func_get_args();
	foreach($args AS $func){
		try{
			$ret = $func();
			if($ret){
				return $ret;
			}
		}
		catch(Exception $e){
			
		}
	}
}

function abs_equals($v1, $v2){
	if($v1 == NULL){
		return $v1 === $v2;
	}
	
	if(gettype($v1)!=gettype($v2)){
		return false;
	}
	
	if(strtoupper(gettype($v1)=='OBJECT') && strtoupper(gettype($v2)=='OBJECT')){
		if(get_class($v1)!=get_class($v2)){
			return false;
		}
		
		if(method_exists($v1, 'equals')){
			return $v1->equals($v2);
		}
	}
	
	return $v1 === $v2;
}

function print_json($v){
	print(json_encode($v));
}

/**
 * 多维数组一维化
 *
 * @param mixed[] $array
 * @return mixed[]
 */
function array_flatten($array){
	$ret = array();
	foreach($array AS $key=>$value){
		if(is_array($value)){
			$ret = array_merge($ret, array_flatten($value));
		}else{
			$ret[] = $value;
		}
	}
	return $ret;
}

/**
 * 随机数
 *
 * @param int $min
 * @param int $max
 * @return mixed
 */
if(!function_exists('random')){
function random($min=NULL, $max=NULL){
	list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);
    srand($seed);
	$randval = rand($min, $max);
	return $randval;
}
}

function callback_eacclerator($buffer){
	return "";
}

function useEacclerator(){
	ob_start("callback_eacclerator");
	phpinfo(INFO_GENERAL);
	$buffer = ob_get_flush();
	//
	@ob_end_clean();
	$pos = strpos($buffer, "eAccelerator");
	return $pos;
}

function array_indexof($haystack, $needle){
	foreach($haystack AS $key => $value){
		if($value == $needle){
			return $key;
		}
	}
}

function array_find($haystack, $search, $func){
	foreach($haystack AS $key => $value){
		if($func($value, $search)){
			return $value;
		}
	}
	return false;
}

function trim_params(&$params, $function=null, $remove_null=false){
	if(!$function) $function = "trim";
	foreach($params as $key=>&$param){
		if($param===null){
			if($remove_null) unset($params[$key]);
		}else{
			$param = $function($param);
		}
	}

}

function filter_params($params){
	$keys = func_get_args();
	$keys = array_slice($keys, 1);
	$ret = array();
	for($i=0; $i<count($keys); $i++){
		if(array_key_exists($keys[$i], $params)){
			if(!is_array($params[$keys[$i]])){
				$ret[$keys[$i]] = ini_get('magic_quotes_gpc')==1?stripslashes($params[$keys[$i]]):$params[$keys[$i]];
			}else{
				$ret[$keys[$i]] = $params[$keys[$i]];
			}
		}else{
			$ret[$keys[$i]] = NULL;
		}
	}
	return $ret;
}

function cookie_remove($name, $path=null, $domain=null){
	if(isset($_COOKIE[$name]))
		setcookie($name, null, time()-1, $path, $domain);
}

function ip2hex($ip){
	$ips = explode(".", $ip);
	$n = 0;
	foreach($ips as $i=>$v){
		$n += $v * pow(256, 3-$i);
	}
	return sprintf("%08x", $n);
}

function hex2ip($hex){
	$ips = array();
	for($i=0; $i<4; $i++){
		$ips[$i] = hexdec(substr($hex, $i * 2, 2));
	}
	return implode(".", $ips);
	
}

function date2timestamp($str){
	$date = date_parse($str);
	return mktime($date["hour"], $date["minute"], $date["second"], $date["month"], $date["day"], $date["year"]);
}
//function add($i, $k){
//	return $i+$k;
//}
//
//$a=array(1,2,3);
//iterator($a, 'add', array(100));
//var_dump($a);

function safe_print($s){
	print(addslashes($s));
}

function check_int($s){
	return is_numeric($s) && is_int($s+0);
}

function array_to_object($arr, $className){
	$obj = new $className;
	foreach($arr as $key=>$value){
		$obj->{$key} = $value;
	}
	
	return $obj;
}
?>