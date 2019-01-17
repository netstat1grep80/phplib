<?php

require_once '_func.text.string.inc.php';
require_once '_class.cache.MemcacheEx.inc.php';

class MemcacheGroup{
	var $servers = array();
	public static $instance;
	public static $changed = false;
	public static $config_path;
	const SUB = 65536;
	
	public static $get_times = 0;
	public static $get_success_times = 0;
	public static $set_times = 0;
	
	/**
	 *
	 * @param [] $configs
	 * @return MemcacheGroup
	 */
	public static function getInstance($config_path){
		self::$config_path = $config_path;
		if(empty(self::$instance)){
			include $config_path;
			$class = __CLASS__;
			self::$instance = new $class($servers);
		}
		return self::$instance;
	}
	
	public static function getInstance2($config){
		if(empty(self::$instance)){
			$class = __CLASS__;
			self::$instance = new $class($config);
		}
		return self::$instance;
	}
	
	public function __construct($configs){
		$hash_update = false;
		foreach($configs as $config){
			if(!isset($config['hash'])){
				$config['hash'] = hashCode(md5($config['host'].":".$config['port'])) % self::SUB ;
				$hash_update = true;
			}
			array_push($this->servers, $config);
		}
		usort($this->servers, array($this, 'sort'));
		
		if($hash_update){
//			$this->updateConfig();
		}
	}
	
	private function sort($a, $b){
		if($a['hash'] == $b['hash']) return 0;
		return ($a['hash'] < $b['hash']) ? -1 : 1;
	}
	
	public function __destruct(){
		foreach($this->servers as $server){
			if(isset($server['cache']) and !empty($server['cache'])){
				@$server['cache']->close();
			}
		}
		unset($this->servers);
	}
	
	/**
	 *
	 * @param string $key
	 * @return MemcacheEx
	 */
	public function find($key){
		if (count($this->servers)==0) return null;
		self::$changed = false;
		$hash = hashCode(md5($key))%self::SUB ;
				
		$index = -1;
		$first = -1;
		foreach($this->servers as $i => $server){		
			if($first==-1){
				$first = $i;
			}
			if($hash<$server['hash']){
				$index = $i;
				break;
			}
		}
		if($index==-1){
			$index = $first;
		}
		
		$server = &$this->servers[$index];
		if(!isset($server['cache'])){
			$server['cache'] = new MemcacheEx();
			$this->initMemcache($server['cache'], $index);
		}
		
		return $server['cache'];
	}
	
	/**
	 *
	 * @param MemcacheEx $memc
	 */
	private function initMemcache($memc, $index){
		$count = 0;
		for($i=0; $i<count($this->servers); $i++){
			$si = ($i+$index)%count($this->servers);
			$memc->addServer($this->servers[$si]['host'], $this->servers[$si]['port']);
			$count ++;
		}
	}
	
	private function updateConfig(){
		$configs = $this->servers;
		foreach($configs as &$config){
			unset($config['cache']);
		}
		ob_start(array($this, 'writeConfig'));
?>
$servers = <?= var_export($configs)?>;
<?php
		ob_end_flush();
	}
	
	private function writeConfig($str){
		file_put_contents(self::$config_path, "<?php\n".$str);
	}
	
	public function add($key, $value, $flag=0, $expire=0){
		return $this->find($key)->add($key, $value, $flag, $expire);
	}
	
	public function set($key, $value, $flag=0, $expire=0){
		self::$set_times ++;
		return $this->find($key)->set($key, $value, $flag, $expire);
	}
	
	public function get($key, $flag = null){
		self::$get_times++;
		$ret= $this->find($key)->get($key);
		if($ret){
//			var_dump(array($key=>$ret));
			self::$get_success_times++;
			return $ret;
		}
	}
	
	public function replace($key, $value, $flag=0, $expire=0){
		return $this->find($key)->replace($key, $value, $flag, $expire);
	}
	
	public function delete ($key ,$timeout=0 ){
		return $this->find($key)->delete($key, $timeout);
	}
	
	public function increment ($key, $increment=1){
		return $this->find($key)->increment($key, $increment);
	}
	
	public function decrement($key, $increment=1){
		return $this->find($key)->decrement($key, $increment);
	}
	
	public function flush(){
		// do nothing , too dangerous
	}

}

//$servers = array(
//	array('host'=>'128.0.0.1', 'port'=>'11211'),
//	array('host'=>'192.168.0.109', 'port'=>'11211'),
//	array('host'=>'www.test.com', 'port'=>'11211')
//);
//
//$memc = MemcacheGroup::getInstance($servers)->find('1');
//$memc->set('1', '123');
//echo $memc->get('1');