<?php
/**
 * @package http
 * @author marsz@live.com
 */ 
require_once('_func.util.common.inc.php');
require_once('_func.http.url.inc.php');
/**
 * 多线程抓取http页面
 * 需要安装curl
 *
 */
class MultiFetcher{
	/**
	 * 是否开启debug
	 * 
	 * @var boolean
	 */
	public $debug; 		
	/**
	 * 默认的超时时间
	 *
	 * @var int 秒
	 */
	public $timeout; 
	
	public $post = false;
	public $post_fields = "";
	
	public $auto_referer = 1;
	/**
	 * 取回结果是否去掉http header部分
	 *
	 * @var boolean
	 */
	private $withoutHeader = true;
	/**
	 * 待处理队列
	 *
	 * @var string[]
	 */
	private $stack = array();
	
	private $headers = array();
	
	/**
	 * 构造方法
	 *
	 * @param boolean $debug
	 * @param int $timeout
	 * @param boolean $withoutHeader
	 */
	public function __construct($debug=false, $timeout=30, $withoutHeader=true){
		$this->debug = $debug;
		$this->timeout = $timeout;
		$this->withoutHeader = $withoutHeader;
	}
	
	/**
	 * 插入待处理url
	 *
	 * @param string $key
	 * @param string $url
	 * @return MultiFetcher
	 */
	public function push($key, $url){
		$this->stack[] = array('key'=>$key, 'url'=>$url);
		return $this;
	}
	
	public function left(){
		return count($this->stack)>0;
	}
	
	/**
	 * 清楚待处理项
	 * 
	 * @return void
	 */
	public function clean(){
		$this->stack = array();
	}
	
	public function setHeaders($headers){
		$this->headers = $headers;
	}
	
	/**
	 * 抓取内容
	 *
	 * @return mixed[]
	 */
	public function fetch(){
		$url_array = $this->stack;
		
		$res = array();
		$mch = curl_multi_init();

		foreach ($url_array as  $i => $url_item)
		{
			$timeout =  $this->timeout;
			$conn[$i] = curl_init();

			//curl_setopt( $conn[$i], CURLOPT_REFERER,	$g_spider_config[$i]['refer'] );
			curl_setopt( $conn[$i], CURLOPT_URL,			$url_item['url'] );
			curl_setopt( $conn[$i], CURLOPT_HEADER,		1);
			curl_setopt( $conn[$i], CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)' );
			if($this->post){
				curl_setopt($conn[$i], CURLOPT_POST, 1);
				curl_setopt($conn[$i], CURLOPT_POSTFIELDS, $this->post_fields);
			}
			if($this->auto_referer){
				curl_setopt($conn[$i], CURLOPT_REFERER, $url_item[$i]);
			}
			
			$http_header 		= array();
			$http_header[] 	= 'Connection: Keep-Alive';
			$http_header[] 	= 'Pragma: no-cache';
			$http_header[] 	= 'Cache-Control: no-cache';
			$http_header[] 	= 'Accept: *'.'/*';
			$http_header[] 	= "Accept-Language: zh-cn";
			$http_header = array_merge($http_header, $this->headers);
			
			//echo (implode("\r\n",$http_header));

			curl_setopt( $conn[$i], CURLOPT_HTTPHEADER, 	$http_header );
			curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER,1);
			curl_setopt($conn[$i],CURLOPT_TIMEOUT,	$timeout);
			curl_multi_add_handle ($mch,$conn[$i]);


			do{
				$mrc = curl_multi_exec($mch, $active);
			}
			while ($mrc == CURLM_CALL_MULTI_PERFORM);

			while ($active and $mrc == CURLM_OK)
			{
				if (curl_multi_select($mch) != -1)
				{
					do {
						$mrc = curl_multi_exec($mch, $active);
					}
					while ($mrc == CURLM_CALL_MULTI_PERFORM);
				}

			}
		}
		//if ($mrc != CURLM_OK)  return false;

		foreach ($url_array as $i => $url_item)
		{
			$key = empty($url_item['key'])?$i:$url_item['key'];
			if (($err = curl_error($conn[$i])) == '') {
				if($this->withoutHeader){
					$res[$key]['content'] = getHTTPBody(curl_multi_getcontent($conn[$i]));
				}else{
					$res[$key]['content']=curl_multi_getcontent($conn[$i]);
				}
			} else {
				$res[$key]['content'] = '';
			}

			$res[$key]['error'] = $err;
			if($this->debug)
			{
				$res[$key]['info'] = curl_getinfo($conn[$i]);
			}

			curl_multi_remove_handle($mch,$conn[$i]);
			curl_close($conn[$i]);
		}

		curl_multi_close($mch);
		$this->clean();
		return $res;
	}
	
	public static function validResult($r){
		if((asserting($r['info']) 
			&& asserting($r["info"]["http_code"])
			&& ($r["info"]["http_code"]!=200))
			|| trim($r["content"])==''){

			return false;
		}else{
			return true;
		}
	}
}


//require_once('_class.xml.XML.inc.php');
//$xml = new XML();
//$search=new MultiFetcher();
//$search->timeout=10;
//$search->push('img', 'http://bbs.znxf.org/u/200703/934a7abc5f8ee36bd694e498a2fcaf9b.jpg');
//$result = $search->fetch();
//var_dump($result);
//file_put_contents('e:/1.jpg', $result['img']['content']);
//$search->push('tag', 'http://tag.moyu.com/interface/data.tag.php?model=get_top_tags_user_visited&count=10')
//	   ->push('user', 'http://user.moyu.com/interface/data.user.php?model=get_user_infos&uid=1&fld=*');
//
//print_r($result = $search->fetch());
//
//var_dump($xml->parseListByText($result[0]['content']));
//var_dump($xml->parseRowByText($result[1]['content']));
//var_dump(unserialize($result[2]['content']));


?>