<?php
/**
 * @package http
 * @subpackage smarty_plugin
 * smarty plugins
 */
require_once('_func.http.page.inc.php');

/*====================================================================
 * plugin handlers
 *===================================================================*/
$smartyexFunctions 		= array();
$smartyexModifiers 		= array();
$smartyexBlocks 		= array();
$smartyexFilters		= array();
	$smartyexFilters['PRE'] 	= array();
	$smartyexFilters['POST'] 	= array();
	$smartyexFilters['OUTPUT'] 	= array();

/**
 * 注册smarty functions
 *
 * @param SmartyEx $smartyex
 * 
 * @return void
 */
function smartyex_register_functions(&$smartyex){
	global $smartyexFunctions;
	for($i=0; $i<count($smartyexFunctions); $i++){
		$smartyex->register_function(
				$smartyexFunctions[$i]['smarty_name'],
				$smartyexFunctions[$i]['func_name']);
	}
}

/**
 * 注册smarty functions
 *
 * @param SmartyEx $smartyex
 * 
 * @return void
 */
function smartyex_register_modifiers(&$smartyex){
	global $smartyexModifiers;
	for($i=0; $i<count($smartyexModifiers); $i++){
		$smartyex->register_modifier(
				$smartyexModifiers[$i]['smarty_name'],
				$smartyexModifiers[$i]['func_name']);
	}
}

/**
 * 注册smarty functions
 *
 * @param SmartyEx $smartyex
 * 
 * @return void
 */
function smartyex_register_blocks(&$smartyex){
	
}

/**
 * 注册smarty functions
 *
 * @param SmartyEx $smartyex
 * 
 * @return void
 */
function smartyex_register_filters(&$smartyex){
	global $smartyexFilters;
	
	foreach($smartyexFilters AS $type => $filters){
		for($i=0; $i<count($filters); $i++){
			switch (strtoupper($type)){
				case 'PRE':
					$smartyex->register_prefilter($filters[$i]);
					break;
				case 'POST':
					$smartyex->register_postfilter($filters[$i]);
					break;
				case 'OUTPUT':
					$smartyex->register_outputfilter($filters[$i]);
					break;
				default:
					break;
			}
		}
	}
}

/*====================================================================
 * modifiers
 * template: mixed smarty_modifier_name (mixed $value, [mixed $param1, ...])
 *===================================================================*/
/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: capitalize
 * Purpose: alias to ucwords
 * Usage: {$var|capitalize}
 * -------------------------------------------------------------
 * </pre>
 * @param string $string
 * 
 * @return string
 */
//function smarty_modifier_capitalize($string) 
//{ 
//    return ucwords($string); 
//}
//$smartyexModifiers[] = array('smarty_name'=>'cap', 		'func_name'=>'smarty_modifier_capitalize'); 

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: concat
 * Purpose: concat strings
 * Usage: {$var|concat:$str1:$str2:...:$strN}
 * -------------------------------------------------------------
 * </pre>
 * @param string $string
 * 
 * @return string
 */
function smarty_modifier_concat($str) 
{ 
	$args = func_get_args();
    return join("", $args); 
}
$smartyexModifiers[] = array('smarty_name'=>'concat', 	'func_name'=>'smarty_modifier_concat');

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: quote_name
 * Purpose: quote string with '[]'
 * Usage: {$var|quote}
 * -------------------------------------------------------------
 * </pre>
 * @param string $string
 * 
 * @return string
 */
function smarty_modifier_quote_name($string) 
{ 
    return '['.$string.']'; 
}
$smartyexModifiers[] = array('smarty_name'=>'quote', 	'func_name'=>'smarty_modifier_quote_name');

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: getUrlMd5
 * Purpose: md5 url with spicified length
 * Usage: {$var|getUrlMd5:10}
 * -------------------------------------------------------------
 * </pre>
 * @param string $url
 * @param int $len
 * 
 * @return string
 */
function smarty_modifier_getUrlMd5($url,$len)
{
	if( substr($url, -1) == '/' )
	{
		$url = substr($url, 0, -1);
	}
	$url = strtolower(urldecode($url));
	$md5 = md5( $url );
	$md5 = substr($md5,0,$len);
	return $md5;
}
$smartyexModifiers[] = array('smarty_name'=>'getUrlMd5', 	'func_name'=>'smarty_modifier_getUrlMd5');

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: getNewFormatTime
 * Purpose: get a customer time format string
 * Usage: {$var|getNewFormatTime}
 * -------------------------------------------------------------
 * </pre>
 * @param int $timestamp
 * 
 * @return string
 */
function smarty_modifier_getNewFormatTime($timestamp)
{
	$mktime =  mktime();
	$realStampDiff = $mktime - $timestamp;
	if(date('Y',$timestamp) == '1970')
		return;
	else if(date('H',$timestamp) == 0 && date('i',$timestamp) == 0 && date('s',$timestamp) == 0)
		$pdate = date('Y-m-d',$timestamp);
	else if($realStampDiff > 60 * 60 * 24)
	{
		$pdate = date('Y-m-d H:i:s',$timestamp);
	}		
	else if($realStampDiff > 60 * 60)
	{
		$pdate = sprintf("%d小时前", $realStampDiff/(60*60));
	}
	else if ($realStampDiff > 5 * 60) 
	{
		$pdate = sprintf("%d分钟前", $realStampDiff/60);
	}
	else
	{
		 $pdate="5分钟内";
	}
	return $pdate;
}
$smartyexModifiers[] = array('smarty_name'=>'getNewFormatTime', 	'func_name'=>'smarty_modifier_getNewFormatTime');

function smarty_modifier_formatTime($timestamp, $format){
	return date($format, $timestamp);
}
$smartyexModifiers[] = array('smarty_name'=>'formatTime', 	'func_name'=>'smarty_modifier_formatTime');


/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: htmlFilter
 * Purpose: strip html tags in string
 * Usage: {$var|htmlFilter}
 * -------------------------------------------------------------
 * </pre>
 * @param string $str
 * 
 * @return string
 */
function smarty_modifier_htmlFilter($str)
{
	return preg_replace('|<.*?>|s','',$str);
}
$smartyexModifiers[] = array('smarty_name'=>'htmlFilter', 	'func_name'=>'smarty_modifier_htmlFilter');

function smarty_truncate($str, $length, $etc){
	$sub = html_substr($str, 0, $length, 'utf-8');
	if($sub!=$str){
		$sub .= $etc;
	}
	return $sub;
}
$smartyexModifiers[] = array('smarty_name'=>'truncate', 	'func_name'=>'smarty_truncate');

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: secureSubstr
 * Purpose: safe substr , without breaking html tag
 * Usage: {$var|secureSubstr:$len}
 * -------------------------------------------------------------
 * </pre>
 * @param string $str
 * @param int $len
 *  
 * @return string
 */
function smarty_modifier_secureSubstr($str,$len, $etc=true)
{	
	if($etc){
		$str = htmltruncate($str, $len, '...');
	}else{
		$str = htmltruncate($str, $len, '');
	}
	/*
	$str = str_replace('&amp;','&',htmlspecialchars($str));
	$str = preg_replace('#&lt;(font.*?|/font)&gt;#is','<\\1>',$str);	
	$str = preg_replace('|<font color=#FF0000>(.*?)</font>|is','<span class="red">\\1</span>',$str);	
	$str = short_substr_smarty($str,$len, $etc);
	*/
	return $str;
}
$smartyexModifiers[] = array('smarty_name'=>'secureSubstr', 	'func_name'=>'smarty_modifier_secureSubstr');

function smarty_modifier_arrayLength($arr){
	return count($arr);
}
$smartyexModifiers[] = array('smarty_name'=>'arrayLength', 	'func_name'=>'smarty_modifier_arrayLength');

function short_substr_smarty($str , $len, $subfix=true)
{
	$strlen  = strlen(preg_replace('|<.*?>|is','',$str));
	if($strlen >$len && $subfix) 
		{$end = '...';}
	return getSubstr_smarty($str , 0 , $len).$end;
}
function getSubstr_smarty($str,$start,$len)
{
//支持中文字符串截取，而且考虑到html标签的完整性，html标签不计为字符串长度 -- by zhanghuanling
	$s = '';
	$lenDone = 0;
	$in = false;
	for ($i=0; $i<strlen($str); $i++) {
		if($str[$i] == '<' && $in === false)
		{
			$s.= $str[$i];
			$in = true;
			continue;
		}
		else if($str[$i] == '>' && $in === true)
		{
			$s.= $str[$i];
			$in = false;
			continue;
		}
		if($in === true)
		{
			$s.= $str[$i];
			continue;
		}
		if (ord($str[$i]) > '0x80') {
			if ($lenDone + 2 <= $len) $s.= $str[$i].$str[$i+1];
			$lenDone+= 2;
			$i++;
		} else {
			if ($lenDone < $len) $s.= $str[$i];
			$lenDone++;
		}
		if($lenDone >= $len) 
			break;
	}
	$s = tagFull_smarty($s); //tagFull函数默认补全span标签
	if(strrpos($s,'&'))
	{
		$sub = substr($s,strrpos($s,'&'));
		$subLen = strlen($sub);
		if($sub == substr('&amp;',0,$subLen) || $sub == substr('&quot;',0,$subLen) || $sub == substr('&#039;',0,$subLen) || $sub == substr('&lt;',0,$subLen) || $sub == substr('&gt;',0,$subLen))
			$s = substr($s,0,strrpos($s,'&'));
	}		
	return $s;
}
function tagFull_smarty($str,$type='span')
{
//为解决字符串截取时可能出现的html标签只保存一半时用
	preg_match_all("|<$type.*?>|is",$str,$match);
	$fontStartNum = count($match[0]);
	preg_match_all("|</$type>|is",$str,$match);
	$fontEndNum = count($match[0]);	
	$needNum = $fontStartNum-$fontEndNum;
	for($i=1; $i<=$needNum; $i++)
	{
		$str.= "</$type>";
	}
	return $str;
}

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: urldecode
 * Purpose: alias to urldecode
 * Usage: {$var|urldecode}
 * -------------------------------------------------------------
 * </pre>
 * @param string $str
 *  
 * @return string
 */
function smarty_modifier_urldecode($string)
{
    return urldecode($string);
}
$smartyexModifiers[] = array('smarty_name'=>'urldecode', 	'func_name'=>'smarty_modifier_urldecode');

/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: modifier
 * Name: urlencode
 * Purpose: alias to urlencode
 * Usage: {$var|urlencode}
 * -------------------------------------------------------------
 * </pre>
 * @param string $str
 *  
 * @return string
 */
function smarty_modifier_urlencode($string)
{
    return urlencode($string);
}
$smartyexModifiers[] = array('smarty_name'=>'urlencode', 	'func_name'=>'smarty_modifier_urlencode');

/*====================================================================
 * functions
 * template: void smarty_function_name (array $params, object &$smarty)
 *===================================================================*/
/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: function
 * Name: add
 * Purpose: sample code demonstrat how smarty function works
 * Usage: {add i=10 k=20}
 * -------------------------------------------------------------
 * </pre>
 * @param string $str
 *  
 * @return string
 */
function smarty_function_add($params){
	return $params['i']+$params['k'];
}
$smartyexFunctions[] = array('smarty_name'=>'add', 	'func_name'=>'smarty_function_add');

/*====================================================================
 * blocks
 * template:void smarty_block_name (array $params, mixed $content, object &$smarty, boolean &$repeat)
 *===================================================================*/


/*====================================================================
 * Prefilters/Postfilters
 * smarty_prefilter_name (string $source, object &$smarty)
 *===================================================================*/
/**
 * Smarty plugin
 * <pre>
 * -------------------------------------------------------------
 * Type: postfilter
 * Name: add_timestamp_comment
 * Purpose: add a timestamp comment to smarty output
 * Usage: N/A
 * -------------------------------------------------------------
 * </pre>
 * @param string $str
 * @param Smarty $smarty
 *  
 * @return string
 */
 function smarty_postfilter_add_timestamp_comment($tpl_source, &$smarty){
 	return "<?php echo \"<!-- generated at ".(date("Y-m-d H:i:s"))." -->\n\"; ?>\n".$tpl_source;
 }
 //$smartyexFilters['POST'][] = 'smarty_postfilter_add_timestamp_comment';
 
 /*====================================================================
 * Inserts
 * string smarty_insert_name (array $params, object &$smarty)
 *===================================================================*/

 
 class SMARTYDB{
 	/**
	 * singleton db instance
	 * 
	 * @var IDatabase
	 */
 	public static $db;

 	/**
	 * get db instance
	 * 
	 * @return IDatabase
	 */
 	public static function getDB(){
 		if(!asserting(self::$db)){
 			self::$db = DBFactory::createInstance(SMARTY_DB_TYPE,
 			SMARTY_DB_HOST, SMARTY_DB_PORT, SMARTY_DB_NAME, SMARTY_DB_USER, SMARTY_DB_PASSWORD,
 			SMARTY_DB_CHARSET, 0, 0, 0);
 		}
 		return self::$db;
 	}
 }
 
 /**
  * 注册resource处理程序
  *
  * @param SmartyEx $smartyEx
  */
 function smartyex_register_resource(&$smartyEx){
 	if(!defined('SMARTY_DB_HOST')) return;
 	$smartyEx->register_resource("db", array("db_get_template",
 	"db_get_timestamp",
 	"db_get_secure",
 	"db_get_trusted"));

 }

 function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
 {
 	// do database call here to fetch your template,
 	// populating $tpl_source
 	$sql = SMARTYDB::getDB();
 	$row = $sql->fetchRow("select content, test_data
 from smarty_template
 where name='$tpl_name'");
 	if ($row) {
 		$tpl_source = $row['CONTENT'];
 		
 		if(isset($smarty_obj->forTest) && $smarty_obj->forTest && asserting($row['TEST_DATA'])){
 			$func = create_function('', 'return '.$row['TEST_DATA'].';');
 			$data = $func();
 			foreach($data AS $key=>$value){
 				//echo "$key=$value";
 				$smarty_obj->assign ($key, $value);
 			}
 		}
 		return true;
 	} else {
 		return false;
 	}
 }

 function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
 {
 	// do database call here to populate $tpl_timestamp.
 	$sql = SMARTYDB::getDB();
 	$row = $sql->fetchRow("select modify_time
 from smarty_template
 where name='$tpl_name'");
 	if ($row) {
 		$tpl_timestamp = $row['MODIFY_TIME'];
 		return true;
 	} else {
 		return false;
 	}
 }

 function db_get_secure($tpl_name, &$smarty_obj)
 {
 	// assume all templates are secure
 	return true;
 }

 function db_get_trusted($tpl_name, &$smarty_obj)
 {
 	// not used for templates
 }

?>