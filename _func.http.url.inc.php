<?php
/**
 * @package http
 * @subpackage url
 * 网络io相关
 */
/**
 * 获取一个ip地址的掩码字符串
 * 如192.168.0.1 -> 192.168.0.*
 *
 * @param string $ip
 * @return string
 */
function getMaskIp($ip)
{
	if ($pos = strrpos($ip, "."))
	{
		$ip = substr($ip, 0,$pos).".*";
	}
	return $ip;
}


/**
 * 打开指定的URL，并跳过返回的文件头，取得状态码
 *
 * @param array $urlData
 * 可以包含以下key：
 *		proxy_host	代理服务器地址，可以不指定，表示不使用代理
 *		proxy_port	代理服务器端口，缺省为80
 *		hostname	要获取的URL对应的主机名，必须指定
 *		port		要获取的URL对应的端口号，缺省为 80
 *		timeout		缺省为 30秒
 *		method	获取的方法，GET或POST，缺省为 GET
 * @param string $statusLine - 将会存放获取的页面的状态（如“200 OK”）
 * @return boolean
 * true：成功打开的文件句柄，并已经跳过文件头，从页面正文开始
 * false：失败
 */
function openUrl($urlData, &$statusLine)
{
	if ( ! isset($urlData['hostname']) || ! $urlData['hostname'] || ! isset($urlData['uri']) || ! $urlData['uri'])
		return false;

	if ( ! isset($urlData['port']) || ! $urlData['port'] )
		$urlData['port'] = 80;
	if ( ! isset($urlData['timeout']) || ! $urlData['timeout'] )
		$urlData['timeout'] = 30;
	$errno = '';
	$errstr = '';
	if ( isset($urlData['proxy_host']) && $urlData['proxy_host'])
	{
		if ( ! isset($urlData['proxy_port']) )
			$urlData['proxy_port'] = 80;
		$fp = @fsockopen($urlData['proxy_host'], $urlData['proxy_port'], $errno, $errstr, $urlData['timeout']);
		$uri = 'http://' . $urlData['hostname'] . ':' . $urlData['port'] . $urlData['uri'];
		$hostname = $urlData['hostname'];
	}
	else
	{
		$fp = @fsockopen($urlData['hostname'], $urlData['port'], $errno, $errstr, $urlData['timeout']);
		$uri = $urlData['uri'];
		$hostname = $urlData['hostname'];
	}
	if ( ! $fp)
		return false;

	$method = ((isset($urlData['method']) && $urlData['method']) ? strtoupper($urlData['method']) : 'GET');
	if ($method == 'GET')
		fputs($fp, "GET $uri HTTP/1.0\r\nHost: $hostname\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)\r\nConnection: close\r\n\r\n");
	elseif ($method == 'POST')
	{
		$len = strlen($urlData['content']);
		fputs($fp, "POST $uri HTTP/1.0\r\nHost: $hostname\r\nContent-Type: application/x-www-form-urlencode\r\nContent-Encoding: chuncked\r\nContent-Length: $len\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)\r\nConnection: close\r\n\r\n");
		if (isset($urlData['content']))
			fputs($fp, $urlData['content']);
	}

	$statusLine = '';
	$count = 0;
	while ( ! feof($fp) )
	{
		$count ++;
		$data = trim(fgets($fp,4096));
		if ($count == 1)
		{
			ereg("^HTTP/[0-9].[0-9] ([^\r\n]+)", $data, $regs);
			$statusLine = $regs[1];
		}
		if ($data == "")	break;
	}

	return $fp;
}


/**
 * 获取$fp中所有剩下的数据
 * $code 为 -1，则把数据做BASE64解码（base64_decode）后返回； 
 * $code 为 0，直接返回数据； 
 * $code 为 1，则把数据做BASE64编码（base64_encode）后返回
 * 
 * @param filehandler $fp
 * @param int $code
 * @return string
 */
function getAllData(&$fp, $code=0)
{
	if ( ! $fp )
		return '';
	$data = "";
	while ( ! feof($fp) )
	{
		$data .= fgets($fp, 4096);
	}
	fclose($fp);
	if ($code == 0)
		return $data;
	elseif ($code < 0)	// 做BASE64解码
		return base64_decode($data);
	else		// 做BASE64编码
		return chunk_split(base64_encode($data));
}

/**
 * 传入未处理的http内容，只返回body
 *
 * @param string $rawHTTP
 * @return string
 */
function getHTTPBody($rawHTTP){
	$pos = strpos($rawHTTP, "\r\n\r\n");
	return substr($rawHTTP, $pos+4);
}

function make_url($params){
	$pairs = array();
	foreach($params as $key=>$value){
		if(is_array($value)){
			foreach($value as $v){
				$pairs[] = $key."[]=".urlencode($v);
			}
		}else{
			$pairs[] = $key."=".urlencode($value);
		}
	}
	return implode("&", $pairs);	
}

function http_request($url, $params=array(), $method='post', $timeout=20){
	$options = array(
		CURLOPT_RETURNTRANSFER => true,         // return web page
		CURLOPT_HEADER         => false,        // don't return headers
		CURLOPT_FOLLOWLOCATION => true,         // follow redirects
		CURLOPT_ENCODING       => "",           // handle all encodings
		CURLOPT_USERAGENT      => "",     // who am i
		CURLOPT_AUTOREFERER    => true,         // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 5,          // timeout on connect
		CURLOPT_TIMEOUT        => $timeout,          // timeout on response
		CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
		CURLOPT_POST           => $method=='post',            // i am sending post data
		CURLOPT_POSTFIELDS     => $params,    // this are my post vars
		CURLOPT_VERBOSE        => 1                //
	);

	$ch      = curl_init($url);
	curl_setopt_array($ch,$options);
	$content = curl_exec($ch);
	$err     = curl_errno($ch);
	$errmsg  = curl_error($ch) ;
	$header  = curl_getinfo($ch);
	curl_close($ch);
	
	return $content;
}

function http_headers($url){
	$options = array(
		CURLOPT_RETURNTRANSFER => true,         // return web page
		CURLOPT_HEADER         => true,        // don't return headers
		CURLOPT_FOLLOWLOCATION => false,         // follow redirects
		CURLOPT_ENCODING       => "",           // handle all encodings
		CURLOPT_USERAGENT      => "",     	// who am i
		CURLOPT_AUTOREFERER    => true,         // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
		CURLOPT_TIMEOUT        => 20,          // timeout on response
		CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
		CURLOPT_POST           => 0,            // i am sending post data
		CURLOPT_VERBOSE        => 1                //
	);

	$ch      = curl_init($url);
	curl_setopt_array($ch,$options);
	$content = curl_exec($ch);
	$pos = strpos($content, "\r\n\r\n");
	$content = substr($content, 0, $pos);
	
	return explode("\n",$content);
}

function make_md5_request_data($params, $key){
	$ret = array();
	$ret["data"] = serialize($params);
	$ret['checksum'] = md5($ret["data"].$key);
	return base64_encode(serialize($ret));
}

function restore_md5_request_data($string, $key){
	$ret = unserialize(base64_decode($string));
	if(!$ret){
		return false;
	}else{
		if($ret['checksum'] == md5($ret["data"].$key)){
			return unserialize($ret['data']);
		}else{
			return false;
		}
	}
}

function check_md5_url($params, $key, $expire=600){
	$checksum = $params['checksum'];
	unset($params['checksum']);	
	$pstr = make_url($params);
	if(abs($params['time']-$_SERVER['REQUEST_TIME'])<=$expire){
		return ($checksum == md5($pstr.$key));
	}else{
		return 'e_checksum_expired';
	}
}

function add_url_params($url, $param){
	$parse = parse_url($url);
//	var_dump($parse);
	$ret = $parse['scheme']."://".$parse['host'].$parse['path'];
	if($parse['query']){
		$ret .= "?".$parse['query']."&".$param;
	}else{
		$ret .= "?".$param;
	}
	return $ret;
}

?>