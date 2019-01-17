<?php
/**
 * @package text
 * @subpackage string
 * 字符串处理相关
 */
require_once('_func.util.common.inc.php');


/**
 * @desc 将一个字符串部分字符用*替代隐藏
 * @param string    $string   待转换的字符串
 * @param int       $bengin   起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int       $len      需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int       $type     转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string    $glue     分割符
 * @return string   处理后的字符串
$str0 = "abcd一二三四五六七八九十";
$str2 = "hengqin2008@qq.com";
$str3 = "hengqin2008@yahoo.com.cn";
$str4 = "0123456789";
echo "原始字符串：$str0";
echo "<br/>从左边第5个字符向右隐藏4个字符：" . hideStr($str0, 4, 4);
echo "<br/>===========================================================";
echo "<br/>原始字符串：$str0";
echo "<br/>从右边第5个字符向左隐藏4个字符：" . hideStr($str0, 4, 4, 1);
echo "<br/>===========================================================";
echo "<br/>原始字符串：$str2";
echo "<br/>隐藏指定字符@（或其他字符）前面的从右边第1个字符向左隐藏4个字符：" . hideStr($str2, 0, 4, 2);
echo "<br/>===========================================================";
echo "<br/>原始字符串：$str3";
echo "<br/>隐藏指定字符@（或其他字符）后面的从左边第1个字符向右隐藏4个字符：" . hideStr($str3, 0, 4, 3);
echo "<br/>===========================================================";
echo "<br/>原始字符串：$str4";
echo "<br/>隐藏首保留3个字符，右保留2个字符：" . hideStr($str4, 3, 2, 4);
 */


function hideStr($string, $bengin=0, $len = 4, $type = 0, $glue = "@") {
    if (empty($string))
        return false;
    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    if ($type == 0) {
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", $array);
    }else if ($type == 1) {
        $array = array_reverse($array);
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", array_reverse($array));
    }else if ($type == 2) {
        $array = explode($glue, $string);
        $array[0] = hideStr($array[0], $bengin, $len, 1);
        $string = implode($glue, $array);
    } else if ($type == 3) {
        $array = explode($glue, $string);
        $array[1] = hideStr($array[1], $bengin, $len, 0);
        $string = implode($glue, $array);
    } else if ($type == 4) {
        $left = $bengin;
        $right = $len;
        $tem = array();
        for ($i = 0; $i < ($length - $right); $i++) {
            if (isset($array[$i]))
                $tem[] = $i >= $left ? "*" : $array[$i];
        }
        $array = array_chunk(array_reverse($array), $right);
        $array = array_reverse($array[0]);
        for ($i = 0; $i < $right; $i++) {
            $tem[] = $array[$i];
        }
        $string = implode("", $tem);
    }
    return $string;
}

/**
 * @desc 验证身份证格式
 * @param $id
 * @return bool
 */
function validation_idcard( $id )
{
    $id = strtoupper($id);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = array();
    if(!preg_match($regx, $id))
    {
        return FALSE;
    }
    if(15==strlen($id)) //检查15位
    {
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
        if(!strtotime($dtm_birth))
        {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    else      //检查18位
    {
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
        if(!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return FALSE;
        }
        else
        {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ( $i = 0; $i < 17; $i++ )
            {
                $b = (int) $id{$i};
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id,17, 1))
            {
                return FALSE;
            } //phpfensi.com
            else
            {
                return TRUE;
            }
        }
    }

}


/**
 * 全角->半角
 *
 * @param string $keytmp
 * @return string
 */
function convert_quan2ban($keytmp){
	$Queue = array('０' => '10', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9', 
                  'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 
                  'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T', 
                  'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 
                  'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 
                  'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 
                  'ｙ' => 'y',	'ｚ' => 'z','，'=>',');
	for($i=0;$i<strlen($keytmp);$i++){
		if (ord($keytmp[$i]) > 128){
			$i++;	
			$tmp = $i-1;
			$str = $keytmp[$tmp].$keytmp[$i];
			if($Queue[$str]){
				if($Queue[$str]!=10)
					$t.=$Queue[$str];
				else
					$t.=0;
			}else
				$t .= $str;					
		}else
			$t .= $keytmp[$i];	
	}
	return $t;
}

/**
 * 分析关键字 <br/>
 * keywords格式: 用全角/半角","或"\s"或"、"分割，引号内部的数据不会分割 <br/>
 * sample : var_dump(parseKeywords('a	b, "c de、f"、我'));
 * 
 * @param string $keywords
 * @return array
 */
function parseKeywords($keywords)
{
	//将单词字符串分解为单词数组
	if (!$keywords) return null;
	$keywords = strval($keywords);
	$keywords = str_replace("，", ",", $keywords);
	$keywords = str_replace("、", ",", $keywords);
	$delimiters = array(",", '\s');
	$delimiterstring =implode($delimiters);
	$loop = true;
	//将第一个引号对内部包括分隔符保留
	while ($loop)
	{
		$loop = false;
		for ($i=0; $i<count($delimiters); $i++)
		{
			while (preg_match('/^[^"]*"[^"]*'.$delimiters[$i].'[^"]*"/', $keywords))
			{
				$loop = true;
				//保护分隔符
				$keywords = preg_replace('/^([^"]*)"([^"]*)'.$delimiters[$i].'([^"]*)"/', '$1"$2_@_#_'.$i.'_%_$3"', $keywords);
			}
		}
		//去掉引号对
		$keywords = preg_replace('/^([^"]*)"([^"'.$delimiterstring.']*)"/', '$1$2', $keywords);
	}
	//按分隔符生成数组
	$keyword = preg_split("/[$delimiterstring]+/", $keywords);

	//还原引号内部分隔符
	for ($i=0; $i<count($keyword); $i++)
	{
		for ($j=0; $j<count($delimiters); $j++)
		{
			//还原分隔符
			$keyword[$i] = str_replace("_@_#_{$j}_%_", $delimiters[$j], $keyword[$i]);
		}
	}
	if ((count($keyword) == 1) && ($keyword[0] == "")) $keyword = null;
	return $keyword;
}

/**
 * 删除引号
 *
 * @param string $word
 * @return string
 */
function stripQuotes($word)
{
	//$word前后的引号去掉
	return stripslashes(preg_replace('/^[\'"](.*)[\'"]$/', '$1', $word));
}

/**
 * 连接数组
 *
 * @param array $arr
 * @param string $delim - 连接key和value的符号？
 * @param string $keyquote - key的引号？
 * @param string $valuequote - value的引号？
 * @return array(1)
 */
function joinArray($arr, $delim="=", $keyquote="", $valuequote="'")
{
	//生成array([$key]=>$value, ... ) 为 array("$key=$value", ...), 可用于生成sql语句
	if (($arr) && (is_array($arr)) && (count($arr) > 0))
	{
		$temp = array();
		foreach($arr as $key=>$value)
		{
			$temp[] = "$keyquote$key$keyquote$delim$valuequote$value$valuequote";
		}
		return $temp;
	}
	else
	return array($arr);
}

/**
 * 返回 $data 做过BASE64编码之后的数据
 *
 * @param string $data
 * @return string
 */
function dataEncodeBase64($data)
{
	return chunk_split(base64_encode($data));
}

/**
 * 格式化数字, 1234567->1,234,567
 *
 * @param int $total
 * @return string
 */
function getFormatNum($total)
{
	$total = "".intval($total);
	for($i=strlen($total)-1,$j=1; $i>=0; $i--,$j++)
	{		
		$formatTotal = $total[$i].$formatTotal;
		if($j%3 == 0 && $j != strlen($total))
		{
			$formatTotal = ','.$formatTotal;
		}
	}
	return $formatTotal;
}

/**
 * 格式化文件尺寸
 *
 * @param int $size
 * @return unknown
 */
function getFormatSize($size)
{
	if($size > 1024 * 1024)
	{
		return round($size/(1024*1024),1)."M";
	}
	else if($size > 1024)
	{
		return round($size/1024,1)."K";
	}
	else
	{
		return $size."B";
	}
}

/**
 * 判断字符是否是gb2312编码
 *
 * @param strng $str
 * @return boolean
 */
function isGB2312($str){
	preg_match_all("/(?:[\x80-\xff]{2})|[\x01-\x7f]+/",$str,$match);
	$str2 = implode('', $match[0]);
	//echo $str2;
	return $str==$str2;
}

function isUTF8($str){
	return utf8_probability($str)>0;
}

/**
 * 判断字符串为utf-8的可能性
 *
 * @param string $rawtextstr
 * @return int - 如果返回值大于0，则代表字符串为utf-8的可能性百分比
 */
function utf8_probability($rawtextstr) {
	$score = 0;
	$i = 0;
	$rawtextlen = 0;
	$goodbytes = 0;
	$asciibytes = 0;
	$rawtextarray = preg_split("//",$rawtextstr,-1, PREG_SPLIT_NO_EMPTY); //转换成char数组，如果是php5，则可使用str_split
	$rawtext = array();
	for($i=0;$i<count($rawtextarray); $i++)
		$rawtext[] = ord($rawtextarray[$i]); //ord(char)
	// Maybe also use UTF8 Byte Order Mark(BOM): EF BB BF
	//BOM，某些utf8文件流的首3个字节，可以表示这个文件的编码方式

	// Check to see if characters fit into acceptable ranges
	$rawtextlen = strlen($rawtextstr);
	for ($i = 0; $i < $rawtextlen; $i++) {
		if ($rawtext[$i] < 0x80) { // One byte
			$asciibytes++; // Ignore ASCII, can throw off count
		} else if (0xC0 <= $rawtext[$i] && $rawtext[$i] <= 0xDF && // Two bytes
		$i+1 < $rawtextlen && 0x80 <= $rawtext[$i+1] && $rawtext[$i+1] <= 0xBF) {
			$goodbytes += 2; $i++;
		} else if (0xE0 <= $rawtext[$i] && $rawtext[$i] <= 0xEF && // Three bytes
		$i+2 < $rawtextlen && 0x80 <= $rawtext[$i+1] && $rawtext[$i+1] <= 0xBF &&
		0x80 <= $rawtext[$i+2] && $rawtext[$i+2] <= 0xBF) {
			$goodbytes += 3; $i+=2;
		}
	}
	//ascii is sub of utf8
	if ($asciibytes == $rawtextlen) { return 100; }
	$score = (int)(100 * ($goodbytes/($rawtextlen-$asciibytes)));
	// If not above 98, reduce to zero to prevent coincidental matches
	if ($score > 98) {
		return $score;
	} else if ($score > 95 && $goodbytes > 30) {
		// Allows for some (few) bad formed sequences
		return $score;
	} else {
		return 0;
	}
}

/**
 * 补完字符串<br/>
 * 如 fullString("123", 5, 'x') = "xx123"
 *
 * @param string $string
 * @param int $len
 * @param string $char 补完字符
 * @return string
 */
function fullString($string, $len, $char=' '){
	if($len - strlen($string)>0){
		$string = str_repeat($char, $len-strlen($string)).$string;
	}
	return $string;
}

/**
 * 去除空格/tab
 *
 * @param string $str
 * @return string
 */
function removeBlank($str){
	$str = preg_replace("/[ \t]+/"," ", $str);
	return $str;
}


/**
 * 去除空格
 *
 * @param string $str
 * @return string
 */
function removeBlank2($str){
	$str = preg_replace("/[\s]+/"," ", $str);
	return $str;
}

function trimnl($str){
	$str = preg_replace("/[\n]/"," ", $str);
	return $str;
}

/**
 * 去掉所有空格
 *
 * @param string $str
 * @return string
 */
function trimAll($str){
	$str = preg_replace('/[\s]+/', '', $str);
	return $str;
}

/**
 * format sql
 *
 * @param string $str
 * @return string
 */
function formatSQL($str){
	$str = preg_replace('/\(/', '( ', $str);
	$str = preg_replace('/\)/', ' )', $str);
	$str = preg_replace('/,/', ' , ', $str);
	return $str;
}

/**
 * 空格转&nbsp;
 *
 * @param string $str
 * @return string
 */
function space2nbsp($str){
	$reg = '/ /';
	return preg_replace($reg, '&nbsp;', $str);
}

/**
 * tab转&nbsp;
 *
 * @param string $str
 * @param int $num - 一个tab字符=?个空格
 * @return string
 */
function tab2nbsp($str, $num=4){
	$reg = '/\t/';
	return preg_replace($reg, str_repeat('&nbsp;', $num), $str);
}

/**
 * 空格/tab转&nbsp;
 *
 * @param string $str
 * @return string
 */
function blank2nbsp($str){
	return space2nbsp(tab2nbsp($str));
}

/**
 * br转回车
 *
 * @param string $str
 * @return string
 */
function br2nl($str){
	$reg = '/<br[\/]?>/i';
	return preg_replace($reg, "\n", $str);
}

/**
 * 根据标志字符串位置截取子串
 *
 * @param string $hack 要截取的字符串
 * @param string $startstr 起始字符串
 * @param string $endstr 结束字符串
 * @param boolean $fromstartpos 是否从起始字符串后开始搜索
 * @return string
 */
function substrByString($hack, $startstr, $endstr, $fromstartpos=true){
	if((strpos($hack, $startstr)===false) || (strpos($hack, $endstr) === false)){
		return "";
	}
	$start = strpos($hack, $startstr) + strlen($startstr);
	$end = strpos($hack, $endstr, ($fromstartpos)?$start:null);
	return substr($hack, $start, $end-$start);
}


function convertnl($txt){
	return str_replace("\r\n", "\n", $txt);
}

function convertspace($txt){
	return preg_replace('|\s+|', '\s+', $txt);
}

function randomString($length){
	$str = 'abcdefghijklmnopqrstuvwxyz1234567890';
	$ret = '';
	for($i=0; $i<$length; $i++){
		$rand = rand(0, strlen($str)-1);
		$ret .= $str[$rand];
	}
	return $ret;
}

function addLineNo($text){
	$lines = split("\n", $text);
	$i=0;
	foreach ($lines AS &$line){
		$line = "<dl style='margin:0px; clear:both; line-height:18px; width:90%'><dt style='float:left;margin:1px;padding:0px;display:block;width:48px;background-color:#EEE'>".($i+1)."</dt><dt style='float:left;margin:1px;padding:0px;display:block;width:90%'><pre style='display:inline;margin:0px;word-wrap:break-word;white-space:-moz-pre-wrap;'>".htmlspecialchars($line)."</pre></dt></dl>";
		$i++;
	}
	return implode("\n", $lines);
}

?>