<?php
/**
 * @package http
 * @subpackage page
 * 页面、参数和html相关
 */
require_once('_func.util.common.inc.php');
require_once('_func.text.string.inc.php');
/**
 * $_ENV
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function _e($key, $default=NULL, $type=URL_PARAMETER_STRING, $isConvert=true){
	$ret = (isset($_ENV[$key]))?$_ENV[$key]:NULL;
	$ret = ($isConvert && $ret===NULL)?$default:$ret;
	if($isConvert){
		$ret = convertVarType($ret, $type);
	}
	return $ret;
}

/**
 * $_GET
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function _g($key, $default=NULL, $type=URL_PARAMETER_STRING, $isConvert=true){
	$ret = (isset($_GET[$key]))?$_GET[$key]:NULL;
	$ret = ($isConvert && $ret===NULL)?$default:$ret;
	if($isConvert){
		$ret = convertVarType($ret, $type);
	}
	return $ret;
}

/**
 * $_POST
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function _p($key, $default=NULL, $type=URL_PARAMETER_STRING, $isConvert=true){
	$ret = (isset($_POST[$key]))?$_POST[$key]:NULL;
	$ret = ($isConvert && $ret===NULL)?$default:$ret;
	if($isConvert){
		$ret = convertVarType($ret, $type);
	}
	return $ret;
}

/**
 * $_COOKIE
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function _c($key, $default=NULL, $type=URL_PARAMETER_STRING, $isConvert=true){
	$ret = (isset($_COOKIE[$key]))?$_COOKIE[$key]:NULL;
	$ret = ($isConvert && $ret===NULL)?$default:$ret;
	if($isConvert){
		$ret = convertVarType($ret, $type);
	}
	return $ret;
}

/**
 * $_SESSION
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function _s($key, $default=NULL, $type=URL_PARAMETER_STRING, $isConvert=true){
	$ret = (isset($_SESSION[$key]))?$_SESSION[$key]:NULL;
	$ret = ($isConvert && $ret===NULL)?$default:$ret;
	if($isConvert){
		$ret = convertVarType($ret, $type);
	}
	return $ret;
}

/**
 * 获得环境变量快捷方式
 *
 * @param string $key 
 * @param string $default
 * @param string $type - 见本包的常量定义
 * @param string $search 
 * @return mixed
 * 
 * @see _e, _g, _p, _c, _s
 */
function _($key, $default=NULL, $type=URL_PARAMETER_STRING, $search='PG'){
	$regex = '/[egpcs]{1,5}/i';
	preg_match($regex, $search, $match);
	if($match==NULL){
		// throws exception
		return NULL;
	}
	$temp = NULL;
	
	for($i=0; $i<strlen($search); $i++){
		$ret = call_user_func('_'.$search[$i], $key, $default, $type, false);
		if($ret !== NULL){
			break;
		}
	}
	if($ret === NULL){
		$ret = $default;
	}
	if($type==URL_PARAMETER_STRING and is_string($ret) and ini_get('magic_quotes_gpc')==1){
		$ret=stripslashes($ret);
	}
	$ret = convertVarType($ret, $type);
	return $ret;
}

/**
 * 获取高亮显示keyword的文本
 *
 * @param string $text
 * @param string $keyword
 * @param string $color
 * @return string
 */
function highlight($text, $keyword, $class="highlight")
{
	return str_replace($keyword, "<span style='$class'>$keyword</span>", $text);
}

/**
 * 转换文本为HTML可显示格式
 *
 * @param string $str
 * @return string
 */
function htmlcontent($str)
{
//	return nl2br(htmlspecialchars(str_replace("\\n","\n", str_replace("\\r", "", $str))));
	return nl2br(htmlspecialchars(str_replace("\\n","\n", str_replace("\\r", "", preg_replace('`(\s*\n+\s*)`', "\n\n", $str)))));
}

/**
 * 用php生成document.write的js代码
 *
 * @param string $text
 * @return string
 */
function jsPrint($text)
{
	$temp = explode("\n", $text);
	$str = "";
	if ($temp)
	{
		for($i = 0; $i < count($temp); $i++)
		{
			$str .= "document.write(\"".str_replace("\"", "\\\"", $temp[$i])."\");\n";
		}
	}
	return $str;
}

/**
 * 显示提示页面并转向
 *
 * @param string $msg － 显示的提示信息
 * @param string $forward - 转向地址
 * @param string $delay - 延迟时间
 * @param string $link - 
 * @param string $header -
 * @param string $msgurl - 提示页面地址
 */
function showMsg($msg, $forward="", $delay="", $link="", $header="", $msgurl="msg.php")
{
	$temp = "$msgurl?msg=".rawurlencode($msg);
	if ($forward!="") $temp .= "&url=".rawurlencode($forward);
	if ($delay != "") $temp .= "&delay=".$delay;
	if ($link != "") $temp .= "&link=".$link;
	if ($header != "") $temp .= "&header=".$header;
	header("Location: $temp");
	die();
}

/**
 * 判断连接是否来自本站
 *
 * @param string $url
 * @return string
 */
function isNativeUrl($url=null)
{
	if (!$url)
	{
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$url=$_SERVER['HTTP_REFERER'];
		}
		else return false;
	}
	$pathinfo = parse_url($url);
	if (($pathinfo) && isset($pathinfo["host"]))
	{
		if  (strtolower(substr($pathinfo["host"], -strlen(HOSTNAME))) == HOSTNAME)
		{
			return true;
		}
	}
	return false;
}

/**
 * 检查页面是否为form POST过来的
 *
 * @return boolean
 */
function isPostPage()
{
	return (count($_POST) > 0) ? true : false;
}

/**
 * 设置域cookie
 * 参数见setcookie()函数
 *
 * @param unknown_type $key
 * @param unknown_type $value
 * @param unknown_type $livetime
 * @param unknown_type $dir
 * @param unknown_type $domain
 */
function setDomainCookie($key, $value, $livetime=2592000, $dir="/", $domain=HOSTNAME)
{
	if(!asserting($domain)){
		$domain = $_SERVER["HTTP_HOST"];
	}
	if (!headers_sent()) setcookie($key, $value, time() + $livetime, $dir, $domain);
}

/**
 * 清除cookie
 *
 * @param string $key
 */
function clearDomainCookie($key, $domain=HOSTNAME)
{
	if(!asserting($domain)){
		$domain = $_SERVER["HTTP_HOST"];
	}
	setcookie($key, false, time(), HOSTNAME);
}

/**
 * 读取cookie
 *
 * @param string $key
 * @param unknown $nullvalue
 * @return unknown
 */
function getCookie($key, $nullvalue=null)
{
	if (isset($_COOKIE[$key]))	return $_COOKIE[$key];
	else return $nullvalue;
}


/**
 * 获取当前页面地址
 *
 * @return string
 */
function getCurPageUrl()
{
	$currentUrl = "http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?"":":".$_SERVER['SERVER_PORT']);
	if(isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF']!="")
	$currentUrl .= $_SERVER['PHP_SELF'];
	if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']!="")
	$currentUrl .= "?".$_SERVER['QUERY_STRING'];
	return $currentUrl;
}


/**
 * 去掉html里所有的标记
 *
 * @param string $str
 * @return string
 */
function htmlstrip($str){
	return preg_replace('/<[^>]+>/', '', $str);
}

/**
 * 获得html标签的名字
 * 
 * @author tempzzz
 *
 * @param string $str
 * @return string
 */
function htmlGetTagName($str){
	$regex = '/<\/?([\w\d]+)[\s]?[^>]*>/';
	preg_match($regex, $str, $match);
	//var_dump($match);
	if(isset($match) && $match!=NULL && count($match)==2){
		return $match[1];
	}else{
		return NULL;
	}
}
//htmlGetTagName('<h1 color="red">');

/**
 * 修正没闭合的tag
 * @author tempzzz
 *
 * @param string $str
 * @return string
 */
function htmlFixTagPair($str){
	$regex1 = '|<[^/][^>]*>|';
	preg_match_all($regex1, $str, $match1);
	//var_dump($match1);
	if(isset($match1) && $match1!=NULL && count($match1)>0){
		$match1 = & $match1[0];
	}else{
		// 无开始标记, 返回
		return $str;
	}
	$regex2 = '|<[/][^>]*>|';
	preg_match_all($regex2, $str, $match2);
	//var_dump($match2);
	if(isset($match2) && $match2!=NULL && count($match2)>0){
		$match2 = & $match2[0];
	}
	
	$count1 = count($match1);
	$count2 = count($match2);
	$k = $count1 - $count2;
	if($k==0){
		// 开始标记和结束标记数目一样, 返回
		return $str;
	}elseif($k>0){
		// 开始标记比结束标记数目多, 补齐
		for($i=0; $i<$k; $i++){
			$tagName = htmlGetTagName($match1[$i]);
			$str .= '</'.$tagName.'>';
		}
		return $str;
	}elseif($k<0){
		// 开始标记比结束标记数目多, 补齐
		for($i=0; $i>$k; $i--){
			$tagName = htmlGetTagName($match2[$count2-1+$i]);
			$str = '<'.$tagName.'>'.$str;
		}
		return $str;
	}
}

/**
 * 转义符还原
 * @author tempzzz
 *
 * @param string $str
 * @return string
 */
function undoHtmlspecialchars($str){
	if($str!=NULL && strlen($str)>0){
		$str = str_ireplace('&lt;', "<", $str);
		$str = str_ireplace('&gt;', ">", $str);
		$str = str_ireplace('&#039;', "'", $str);
		$str = str_ireplace('&quot;', '"', $str);
		$str = str_ireplace('&amp;', '&', $str);
	}
	return $str;
}

/**
 * 补完截取字符串的不完整tag
 * @author tempzzz
 *
 * @param string $str
 * @param string $substr
 * @return string
 */
function htmltruncate_fixTag($str, $substr){
	$pos = strpos($str, '>', strlen($substr));
	return substr($str, 0, $pos+1);
}

/**
 * 截取字符串
 * <b>不推荐使用</b>
 * @deprecated
 * @ignore  
 * @author tempzzz
 *
 * @param string $str
 * @param int $len
 * @param string $substr
 * @return string
 */
function htmltruncate_truncateRaw($str, $len, $substr=NULL){
	if($substr==NULL){
		$substr = substr($str, 0, $len);
	}
	$regex = '|<[^>]*|';
	while(1){
		preg_match_all($regex, $substr, $match);
		if(isset($match) && $match!=NULL && count($match)>0){
			$match = & $match[0];
			if(count($match)<1) break;
			$last = $match[count($match)-1];
			if($last==substr($substr, -(strlen($last)))){
				// 搜索补完 '>'
				$substr = htmltruncate_fixTag($str, $substr);
			}else{
				// 恰好是标签结尾, do nothing
			}
		}
		
		$nowlen = strlen(htmlstrip($substr));
		if($nowlen == $len){ // 取完, 退出循环
			break;
		}else{
			$substr .= substr($str, strlen($substr), $len-$nowlen);
		}
	}
	return $substr;
}

/**
 * 修正len变量数值
 * <b>不推荐使用</b>
 * @deprecated 
 * @ignore 
 * @author tempzzz
 *
 * @param string $str
 * @param int $len
 * @return int
 */
function htmltruncate_fixlen($str, $len){
	$regex = '/\&[^;]+;/';
	preg_match_all($regex, $str, $match);
	if(isset($match) && $match!=NULL && count($match)>0){
		$match = & $match[0];	
		for($i=0; $i<count($match); $i++){
			$len += strlen($match[$i])-1;
		}
	}
	return $len;
}

/**
 * 取得html字符串的修正长度
 * 1. 去除html标记
 * 2. 转义符记为一个字符
 * 
 * @author tempzzz
 *
 * @param string $str
 * @return int
 */
function htmltruncate_strlen($str,$charset='GB18030'){
	if($charset=='UTF-8'){
		$str = iconv("UTF-8", "GB18030", $str);
	}
	$regex = '/\&[^;]+;/';
	$str = htmlstrip($str);
	return strlen(preg_replace($regex, 'a', $str));
	
}

/**
 * 截取html的前x个字符
 * 安全保护汉字, 补全tag, 计算转义符<br/>
 * <b>推荐使用</b>
 * <code>
 * <?php
 * 	echo htmltruncate('<span id="news_title">&gt;<b>任天堂5&nbsp年前已有意开发游戏手机</b></span>', 24, '...', true);
 * ?>
 * </code>
 * @author tempzzz
 * @see html_substr, htmltruncate_strlen, htmlstrip
 *
 * @param string $str			- 要处理的字符串
 * @param int $len				- 截取长度
 * @param string $etc			- 补充在结尾的字符
 * @param boolean $withTitle    - 是否有title属性
 * 
 * @return string				
 */
function htmltruncate($str, $len, $etc, $withTitle=false,$charset='GB18030'){
	if(htmltruncate_strlen($str, $charset)<=$len){
		return $str;
	}
	$etcLen = htmltruncate_strlen($etc, $charset);
	$substr = html_substr($str, 0, $len,$charset);
	$stripstr = htmlstrip($str);
	
	if($withTitle){
		return ('<span title="'.$stripstr.'">'.$substr.$etc.'</span>');
	}else{
		return $substr.$etc;
	}
}

/**
 * 对html字符串的substr方法, 适用于GB2312编码的HTML代码
 * 
 * 功能:<br/>
 * 1. 安全截取html, 在html标记不匹配时自动匹配不完整标签<br/>
 * 2. 安全截取汉字, 不会出现半个字符的情况<br/>
 * 3. 正确截取html转义符, 如&nbsp; &lt; &amp;等, 这些转义符会作为1个"字节"截取<br/>
 * 4. 支持类似substr的使用方法, $start, $len参数可以为负数<br/>
 * 
 * 不足:
 * 补齐前半html标签的时候不能还原其信息, 比如匹配</span>只能还原前面的<span>属性没有恢复
 * 
 * <b>推荐使用</b>
 * <code>
 * <?php
 * echo html_substr('<span id="news_title">&gt;<b>\任天堂5&nbsp年前已有意开发游戏手机</b></span>',1, -2);
 * ?>
 * </code>
 * @see substr, htmlFixTagPair, htmlstrip
 *
 * @param string $html
 * @param int $start
 * @param int $len
 * @return string
 */
function html_substr($html, $start, $len=NULL, $charset='GB18030'){
	if(strtoupper($charset)=='UTF-8'){
		$html = iconv('UTF-8', 'GB18030', $html);
	}
	$temp = '';
	$tag = '';
	$inTag = false;
	$lenDone = 0;
	$gbcharNum = 0;
	$char = '';
	$begin = false;
	$spCount = 0;
	$spCountIndex = array();
	
	$regex ='/\&[^;]+;/';
	preg_match_all($regex, $html, $specialChars);
	$html = preg_replace($regex, chr(1), $html);
	$htmlLen = strlen(htmlstrip($html));
	
	if($start>=0){
		if($len==NULL){
			$len = $htmlLen - $start;
		}
		
		if($len < 0){
			$len = $htmlLen + $len;
		}
	}elseif($start<0){
		if($len < 0){
			return NULL;
		}
		$len = $htmlLen + $start;
		$start = 0;
	}
	
	for($i = 0, $k=0; $i<strlen($html); $i++){	
		$begin = ($k>=$start);
		if($html[$i]==chr(1)) { $spCount++;};
		if($html[$i] == '<' && $inTag == false){
			$inTag = true;
			$tag = '';
		}
		if($inTag==true){
			$tag .= $html[$i];
		}
		if($html[$i] == '>' && $inTag ==true){
			$inTag = false;
			if($begin){ // not begin, skips
				$temp .= $tag;
			}
			continue;
		}
		
		if($inTag==false){
			if(ord($html[$i]) > '0x80'){
				$gbcharNum+=1;
				if($lenDone +2 <= $len && $i>=$start){
					if($gbcharNum%2==0){
						$char .= $html[$i-1] . $html[$i];
					}else{
						$char .= $html[$i] . $html[$i+1];
						$gbcharNum++;
						$i++;
						$k++;
					}
					$temp .= $char;
					$char = '';
					$lenDone += 2;
				}else{break;} 
			}else{
				if($lenDone < $len && $k>=$start){
					$temp .= $html[$i];
					$lenDone++;
					if($html[$i]==chr(1)) { $spCountIndex[] = $spCount-1;};
				}else{/* ingonre */} 
			}
			$k++;
			
			if($lenDone>=$len){
				break;
			}
		}
	}
	
	$temp = htmlFixTagPair($temp);
	foreach ($spCountIndex AS $index){
		$temp = preg_replace('/'.chr(1).'/', $specialChars[0][$index], $temp, 1);
	}
	if(strtoupper($charset) == 'UTF-8'){
		$temp = iconv('GB18030', 'UTF-8', $temp);
	}
	return  $temp;
}

//echo html_substr('<span id="news_title">&gt;<b>\任天堂5&nbsp年前已有意开发游戏手机</b></span>',1, -2, 'UTF-8');

/**
 * 获得静态化2级页面的路径
 *
 * @param string $uri
 * @param string[] $args
 * @return string
 */
function getListStaticPath($uri, $args){
	$ret = '';
	$file = getFilePathInfo($uri);
	$ret .= $file['path'].$file['name']."/";
	$temp = array();
	if($args!=NULL){
		foreach($args AS $key=>$value){
			$temp[] = $key.','.$value;
		}
	}else{
		$args = array();
	}
	return $ret.join('|', $temp).".html";
}

/**
 * 获得静态化3级页面的路径
 *
 * @param string $uri
 * @param string $key 标识，如"id"
 * @param int $value 标识的值
 * @return string
 */
function getItemStaticPath($uri, $key, $value){
	$ret = '';
	$file = getFilePathInfo($uri);
	$ret .= $file['path'].$file['name']."/";
	$value = fullString($value, 10, '0');
	$ret .= substr($value, 0, 2)."/";
	$ret .= substr($value, 2, 2)."/";
	$ret .= substr($value, 4, 2)."/";
	$ret .= substr($value, 6, 2)."/";
	$ret .= substr($value, 8);
	$ret .= ".html";
	return $ret;
}

/**
 * 静态页生成一般规则下自动生成静态页，此函数为回调函数
 *
 * @param string $buffer
 * @return string
 */
function writeStaticPath($buffer){
	global $static_path;
	global $static_exists;
	global $static_availible;
	
	if(!$static_exists && $static_availible){
		$dir = getFilePathInfo($static_path);
		$dir = $dir['path'];
		mkdirPro($dir);
		
		file_put_contents($static_path, $buffer);
	}
	return $buffer;
}

/**
 * 判断get or post传入参数是否合法
 *
 * @param string[] $vars 被检查的数据
 * @param string[] $checker 检查数据，如果为NULL，则认为$vars有效
 * @return boolean
 */
function isLegalParams($vars, $checker=NULL){
	if(!isset($checker)) return true;
	if($checker == NULL) return true;
	
	foreach($vars as $key=>$value){
		if($value>= $checker[$key][0] && $value<=$checker[$key][1]){
			//
		}else{
			return false;
		}
	}
	return true;
}

/**
 * 生成分页链接的文字
 *
 * @param int $page 当前页
 * @param int $pageCount 总页数
 * @param int $neighborCount 临近值，比如$page=5, $pageCount=7, $neighborCount=3, 则取出连续页面应该是2,3,4,5,6,7
 * @param string $pre 前一页，文字
 * @param string $next 后一页，文字
 * @param string $first 第一页，文字
 * @param string $last 最后一页，文字
 * @param string $seperator 不连续页之间的省略符
 * @param string $interval 大尺度的间隔
 * @param int $limit 最多产生多少项
 * @return string[]
 */
function getPages($page, $pageCount, $neighborCount=3, $seperator='...', $limit=8,
				  $pre='上一页', $next='下一页', $first='第一页', $last='最后页'
				  ){
	$ret = array();
	$start = $page-$neighborCount;
	$start = ($start<1)?1:$start;
	$end = $page + $neighborCount;
	$end = ($end>$pageCount)?$pageCount:$end;
	$interval = (int)($pageCount/(($limit-$neighborCount>0)?($limit-$neighborCount):1));
	$interval = ($interval<=0)?1:$interval;
	
	for($i=1; $i<=$pageCount; $i++){
		if(count($ret)>=$limit && $i>$page+$neighborCount) {
			break;
		}
		if($i>=$start && $i<=$end){
			$value = ($i == $page)?NULL:$i;
			$ret[] = array($i, $value);
			continue;
		}
		
		if($i%$interval==0 ){
			if(count($ret)>0){
				$p = $ret[count($ret)-1][0];
				if($p+1 != $i && asserting($seperator)){
					$ret[] = array($seperator, NULL);
				}
			}
			$ret[] = array($i, $i);
			continue;
		}
	}
	
	$front[] = array($first, ($page!=1)?1:NULL);
	$front[] = array($pre, ($page-1>=1)?($page-1):NULL);
	if($ret[0][0]!=1){
		//$front[] = array($seperator, NULL);
	}
	
	if($ret[count($ret)-1][0]!=$pageCount && asserting($seperator) ){
		$back[] = array($seperator, NULL);
	}
	$back[] = array($next, ($page+1<=$pageCount)?($page+1):NULL);
	$back[] = array($last, ($page!=$pageCount)?$pageCount:NULL);
	
	$ret = array_merge($front, $ret, $back);
	return $ret;
}

//var_dump(getPages(1, 3, 3, '', 10));

/**
 * 获得分页链接字符串
 *
 * @param string[] $pages 从getPages返回的结果
 * @param string $link 链接字符串
 * @param string $linkClass 链接的css class
 * @param string $plainClass 非链接的css class
 * @return string
 */
function getPageText($pages, $link, $linkClass='', $plainClass=''){
	$ret = array();
	foreach($pages AS $page){
		if($page[1]===NULL){
			$ret[] = "<span class='$plainClass'>".$page[0]."</span>";
		}else{
			$l = str_replace('{page}', $page[1], $link);
			$ret[] = "<a href='$l' class='$linkClass'>".$page[0]."</a>";
		}
	}
	return implode(" ", $ret);
}

/**
 * 从数组创建url参数
 *
 * @param string[] $params
 * @return string
 */
function createURLParameters($params){
	$ret = "";
	if($params!=NULL){
		foreach ($params AS $key=>$value){
			$ret .= '&'.$key.'='.urlencode($value);
		}
	}
	if(strlen($ret)>0){
		$ret = substr($ret, 1);
	}
	return $ret;
}

/**
 * 生成分页url
 *
 * @param array $pageParams - 分页参数数组
 * @param string $pageUrl - 页面地址, 如果空则用PHP_SELF代替
 * @param array $otherParams - 其他url参数, 如果为空, 用当前页面的$_GET代替
 * @return string
 */
function createPageUrl($pageParams, $pageUrl=NULL, $otherParams=NULL){
	$ret = '';
	if($otherParams==NULL){
		$getcopy = $_GET;
	}else{
		$getcopy = $otherParams;
	}
	
	foreach($pageParams AS $key=>$value){
		$getcopy[$key] = $value;
	}
	if($pageUrl==NULL){
		$pageUrl = $_SERVER['PHP_SELF'];
	}
	$ret = $pageUrl.'?'.createURLParameters($getcopy);
	return $ret;
}

/**
 * 将html文件转化为正则表达式，作为正则模板的html的匹配部分必须写为如<regex>(.*)</regex>
 *
 * @param string $str 模板字符串
 * @return string
 */
function regexExcape($str){
	$ops = '\'\"^$.[]|()?*+{}:-&';
	$str = addcslashes($str, $ops);
	$regex = '|<regex>.*</regex>|';
	preg_match_all($regex, $str, $match);
	foreach($match[0] AS $m){
		$r = stripcslashes(htmlstrip($m));
		$str = str_replace($m, $r, $str);
	}
	//var_dump($match);
	
	return $str;
}

function getSelect($name, $rows, $defaultValue, $textFieldName='NAME', $idFieldName='ID'){
	$ret = "<select id='$name' name='$name'>";
	foreach ($rows AS $row){
		$selected = ($defaultValue==$row[$idFieldName])?" selected='selected'":"";
		$ret .= "<option value='".$row[$idFieldName]."'$selected>".$row[$textFieldName]."</option>";
	}
	$ret .= "</select>";
	return $ret;
}


function outputDownloadFile($content, $filename){
	$content = isUTF8($content)?iconv('UTF-8', 'GB18030', $content):$content;
	$filename = isUTF8($filename)?iconv('UTF-8', 'GB18030', $filename):$content;
	Header("Content-type: application/octet-stream"); 
	Header("Accept-Ranges: bytes"); 
	Header("Accept-Length: ".strlen($content)); 
	Header("Content-Disposition: attachment; filename=" . $filename);
	
	echo $content;
}

function createSelect($data, $default=NULL, $options=NULL){
	$ret = "<select";
	if(asserting($options)){
		foreach($options AS $key=>$value){
			$ret .= " $key='$value'";
		}
	}
	$ret .= ">";
	foreach ($data as $r){
		list($key, $value) = array_values($r);
		if(!asserting($value)) $value = $key;
		if($default == $value){
			$selected = " selected='selected'";
		}else{
			$selected = '';
		}
		$ret .= "<option value='$value'$selected>$key</option>";
	}
	$ret .= "</select>";
	return $ret;
}

function makeSelect($data, $default=NULL, $options=NULL, $same=false){
	$ret = "<select";
	if(asserting($options)){
		foreach($options AS $key=>$value){
			$ret .= " $key='".htmlspecialchars($value)."'";
		}
	}
	$ret .= ">";
	foreach ($data as $value=>$name){
		if($same) $value = $name;
		
		if(is_array($name)){
			$ret .= "<optgroup label='".htmlspecialchars($name[0])."'>";
			foreach ($name[1] as $subvalue=>$subname){
				$subvalue = ($value<<4) | $subvalue;
				if($default == $subvalue){
					$selected = " selected='selected'";
				}else{
					$selected = '';
				}
				$ret .= "<option value='$subvalue'$selected>$subname</option>";
			}
			$ret .= "</optgroup>";
		}else{
			if($default == $value){
				$selected = " selected='selected'";
			}else{
				$selected = '';
			}
			$ret .= "<option value='$value'$selected>$name</option>";
		}
	}
	$ret .= "</select>";
	return $ret;
}

function makeSelectFromRow($rows, $default=NULL, $options=NULL){
	$ret = "<select";
	if(asserting($options)){
		foreach($options AS $key=>$value){
			$ret .= " $key=\"".htmlspecialchars($value)."\"";
		}
	}
	$ret .= ">";
	foreach ($rows as $row){
		if($default == $row["value"]){
			$selected = " selected=\"selected\"";
		}else{
			$selected = '';
		}
		$ret .= "<option value=\"$row[value]\"$selected>$row[name]</option>";
	}
	$ret .= "</select>";
	return $ret;
}

function makeCheckgroup($values, $selecteds, $options){
	if(!asserting($selecteds, true)) $selecteds = array();
	$ret = array();
	$option_string = '';
	if(asserting($options, true)){
		foreach($options as $key=>$value){
			$option_string.=" $key='$value'";
		}
	}
	$id = uniqid();
	foreach($values as $i=>$value){
		$selected = '';
		foreach($selecteds as $sv){
			if($sv == $i){
				$selected = 'checked';
				break;
			}
		}
		$checkbox = '<input type="checkbox" value="'.$i.'" '.$option_string.' id="'.$id.$i.'" '.$selected.'/> <label for="'.$id.$i.'">'.$value.'</label>';
		$ret[] = $checkbox;
	}
	
	return $ret;
}

function ubb2html($str){
	$str = preg_replace("/\[b\]/i", "<strong>", $str);
	$str = preg_replace("/\[\/b\]/i", "</strong>", $str);
	$str = preg_replace("/\[url=([^\[\]]*)\]/i", "<a href='\$1'>", $str);
	$str = preg_replace("/\[\/url\]/i", "</a>", $str);
	$str = preg_replace("/\[color=([^\[\]]*)\]/i", "<font color='\$1'>", $str);
	$str = preg_replace("/\[\/color\]/i", "</font>", $str);
	$str = preg_replace("/\[img\]([^\[]*)\[\/img\]/i", "<img src='\$1'/>", $str);
	
	return $str;
}

?>
