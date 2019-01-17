<?php
 /**
  * 处理缓存相关
  * @package cache
  * 
  */
require_once('_func.util.common.inc.php');
/**
 * Memcache类
 * require Memcache lib
 * http://pecl.php.net/package/memcache
 * 需要定义以下常量
 * <code>
 * define('MEMCACHED', '60.28.197.59');
 * define('MEMCACHED_EXPIRE', '600');
 * define('MEMCACHED_ENABLE', FALSE);
 * </code>
 */
class MemcacheHelper{
	private $host;
	private $port;
	private $memcache;
	private $expire;
	
	const EXPIRE = 600;
	private static $single;
	private static $servers;
	public static $configs;
	
	/**
	 * 构造方法
	 *
	 * @param string $host
	 * @param int $port
	 */
	public function __construct($host, $port=11211){
		$this->host = $host;
		$this->port = $port;
		$this->memcache = new Memcache;
		@$this->memcache->pconnect($host, $port);
	}
	
	/**
	 * Singleton
	 *
	 * @return MemcacheHelper
	 */
	public static function singleton($index=0, $group=0){
		if(self::$configs==null){
			self::config();
		}
		if(is_int($group)){
			$groupNames = array_keys(self::$configs);
			$group = $groupNames[$group];
		}
		
		if(isset(self::$servers) && isset(self::$servers[$group]) 
			&& isset(self::$servers[$group][$index]) && self::$servers[$group][$index]!=NULL ){
			return self::$servers[$group][$index];
		}else{
			self::$servers[$group][$index] = 
				new MemcacheHelper(self::$configs[$group][$index]['host'], self::$configs[$group][$index]['port']);
			return self::$servers[$group][$index];
		}
	}
	
	public static function config(){
		if(self::$configs){
			return self::$configs;
		}
		$groups = array();
		$regex = '|\[([a-z0-9]+)\]((([a-z0-9\.\_]+)(:([0-9]+))),?){1,}|im';
		$configStr = preg_replace("/\s+/","", MEMCACHED);
		preg_match_all($regex, $configStr, $match);
		if(count($match[0])==0){
			$groupNames = array('default');
			$configLine = $configStr;
			$regex = '|([a-z0-9\.\_]+):([0-9]+)|i';
			preg_match_all($regex, $configLine, $match2);
			//var_dump($match2);
			for($i=0; $i<count($match2[0]);$i++){
				$groups[0][] = array('host'=>$match2[1][$i], 'port'=>$match2[2][$i]);
			}
		}else{
			$groupNames = $match[1];
			foreach($match[0] AS $index=>$configLine){
				$regex = '|([a-z0-9\.\_]+):([0-9]+)|i';
				preg_match_all($regex, $configLine, $match2);
				//var_dump($match2);
				for($i=0; $i<count($match2[0]);$i++){
					$groups[$groupNames[$index]][] = array('host'=>$match2[1][$i], 'port'=>$match2[2][$i]);
				}
			}
		}
		self::$configs = $groups;
		return self::$configs;
	}
	
	/**
	 * 返回原始memcache对象
	 *
	 * @return Memcache
	 */
	public function getMemObj(){
		return $this->memcache;
	}
	
	/**
	 * 设置Memcache保存过期时间
	 *
	 * @param int $sec - 秒
	 */
	public function setExpire($sec){
		$this->expire = $sec;
	}
	
	/**
	 * 增加值
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $flag
	 * @param int $expire
	 */
	public function add($key, $value, $expire=self::EXPIRE, $flag=0){
		if(!$expire) {
			$expire = $this->expire;
		}
		
		return $this->memcache->add($key, $value, $flag, $expire);
	}
	/**
	 * 保存值
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $flag
	 * @param int $expire
	 */
	public function set($key, $value, $expire=self::EXPIRE, $flag=0){
		if($expire!==NULL) {
			$expire = $this->expire;
		}
		
		return @$this->memcache->set($key, $value, $flag, $expire);
	}
	
	/**
	 * 替换值
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $flag
	 * @param int $expire
	 */
	public function replace($key, $value, $expire=self::EXPIRE, $flag=0){
		return @$this->memcache->replace($key, $value, $flag, $expire);
	}
	
	/**
	 * 获得$key值
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key){
		return @$this->memcache->get($key);
	}
	
	/**
	 * 删除
	 *
	 * @param string $key
	 */
	public function delete($key){
		return @$this->memcache->delete($key);
	}
	
	/**
	 * 在指定键值的值上增加$value(整数)
	 *
	 * @param string $key
	 * @param int $value
	 * @return int
	 */
	public function increment($key, $value=1){
		return @$this->memcache->increment($key, $value);
	}
	
	/**
	 * 在指定键值的值上减少$value(整数)
	 *
	 * @param string $key
	 * @param int $value
	 * @return int
	 */
	public function decrement($key, $value=1){
		return @$this->memcache->increment($key, $value);
	}
	
	/**
	 * 取得键值=$key的值并返回， 如果$key不存在，则执行函数$function并返回其结果
	 *
	 * @param string $key
	 * @param string $function
	 * @param string[] $args
	 * @param boolean $update - 是否把$function返回的值写入缓存
	 * @param int $expire - 秒
	 * @return mixed
	 */
	public function getCallback($key, $function, $args, $update=true, $expire=self::EXPIRE){
		if($value=$this->get($key)){
			return $value; 
		}else{
			$value = call_user_func_array($function, $args);
			if($update){
				$this->set($key, $value, $expire,0);
			}
		}
		return $value;
	} 
	
	/**
	 * 得到$key的值并删除memcache里的记录
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getAndDelete($key){
		$value = $this->get($key);
		$this->delete($key);
		return $value;
	}
	
	/**
	 * 清除所有
	 *
	 */
	public function flush(){
		$this->memcache->flush();
	}
	
	/**
	 * 生成key
	 *
	 * @param string $prefix
	 * @param sring $funcName
	 * @param mixed[] $args
	 * @return string
	 */
	public static function makeKey($prefix, $funcName, $args){
		return $prefix.'_'.$funcName.'_'.implode('_', $args);
	}
}

/**
 * 回调class的cache方法，如果从memcache中能取得值则直接返回，如果取不到则执行指定class方法
 *
 * @param string 	$clazz 			类名
 * @param string 	$method 		方法名，必须是该class的静态方法(static method)
 * @param mixed[] 	$args 			回调方法的参数数组
 * @param mixed 	$groupName		memcache的分组名字(需要和配置相符)或分组序号(从0开始)
 * @param int 		$expire 		过期时间(秒)
 * @param boolean 	$enableCache 	是否使用cache $enableCache 如果为false，则每次都运行回调方法
 * @return mixed					
 */
function cacheClassMethod($clazz, $method, $args, $groupName=0, $expire=MEMCACHED_EXPIRE, $enableCache=MEMCACHED_ENABLE){
	MemcacheHelper::config();
	$key = ''; $mod = 0;
	if(is_int($groupName)){
		$groupNames = array_keys(MemcacheHelper::$configs);
		$groupName = $groupNames[$groupName];
	}
	$mod = count(MemcacheHelper::$configs[$groupName]);
	if($enableCache==true){
		$key = MemcacheHelper::makeKey($clazz, $method, $args);
	}
	if($mod!=0 && $enableCache){
		$memc = MemcacheHelper::singleton(hashCode($key)%$mod, $groupName);
		return $memc->getCallback($key, array($clazz, 'callback_'.$method), $args, true, $expire);
	}else{
		return call_user_func_array(array($clazz, 'callback_'.$method), $args);
	}
}

/**
 * 回调一般的cache方法，如果从memcache中能取得值则直接返回，如果取不到则执行指定方法
 *
 * @param string 	$namespace		名字空间，用于标识不同的命名
 * @param string 	$method 		方法名
 * @param mixed[] 	$args 			回调方法的参数数组
 * @param mixed 	$groupName		memcache的分组名字(需要和配置相符)或分组序号(从0开始)
 * @param int 		$expire 		过期时间(秒)
 * @param boolean 	$enableCache 	是否使用cache $enableCache 如果为false，则每次都运行回调方法
 * @return mixed					
 */
function cacheNormalMethod($namespace, $method, $args, $groupName=0, $expire=MEMCACHED_EXPIRE, $enableCache=MEMCACHED_ENABLE){
	MemcacheHelper::config();
	$key = ''; $mod = 0;
	if(is_int($groupName)){
		$groupNames = array_keys(MemcacheHelper::$configs);
		$groupName = $groupNames[$groupName];
	}
	$mod = count(MemcacheHelper::$configs[$groupName]);
	if($enableCache==true){
		$key = MemcacheHelper::makeKey($namespace, $method, $args);
	}
	if($mod!=0 && $enableCache){
		$memc = MemcacheHelper::singleton(hashCode($key)%$mod, $groupName);
		return $memc->getCallback($key, 'callback_'.$method, $args, true, $expire);
	}else{
		return call_user_func_array('callback_'.$method, $args);
	}
}

	
//
//	define('MEMCACHED', 'localhost');
//	define('MEMCACHED_EXPIRE', 100);
//	define('MEMCACHED_ENABLE', TRUE);
//	
//	$memc = new MemcacheHelper(MEMCACHED);
//	$memc->set("key1", "xxxx");
//	echo $memc->get("key1");
//	class Simple{
//		function a($var1, $var2){
//			echo cacheClassMethod(__CLASS__, __FUNCTION__, array($var1, $var2), 1000, false);
//		}
//		
//		static function callback_a($var1, $var2){
//			return $var1+$var2;
//		}
//	}
//	$simple = new Simple();
//	$simple->a(89,218);
	
	
	/*	class Object{
		public $name;
		public $value;
		
		public function __construct($name, $value){
			$this->name = $name;
			$this->value = $value;
		}
	}*/
/*	
	$mem = new MemcacheHelper('60.28.197.59');
	//$mem->set("obj1", new Object('mars', 29));
	var_dump($mem->get('obj1'));
	
	function add($i, $k){
		return $i+$k;
	}

	var_dump($mem->getCallback('func1Value', 'add', array(100, 400)));
*/

?>
