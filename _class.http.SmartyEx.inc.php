<?php
/**
 * @package http
 * @author Mars <tempzzz>
 */
if(defined("SMARTY_DIR")){


	require_once(SMARTY_DIR.'/Smarty.class.php');
	require_once('_func.http.smarty_plugin.inc.php');
	require_once('_func.util.common.inc.php');

	/**
 * 一个为了简单使用的smarty扩展<br/>
 * 
 * 在配置文件中配置以下常量<br/><br/>
 * 
 * define('SMARTY_DIR', 'e:/web/libs/');			// smarty根目录<br/>
 * define('SMARTYEX_FILES_HOME', 'e:/web/smarty/'); // 存放smarty模板/编译文件/cache文件的路径<br/>
 * define('SMARTYEX_COMPILE_CHECK', true);			// 是否编译前检查需要重新编译<br/>
 * define('SMARTYEX_DEBUGGING', true);				// 是否打开debug<br/>
 * define('SMARTYEX_CACHING', true);				// 是否允许使用缓存<br/>
 * define('SMARTYEX_CACHING_LIFETIME', 60);			// 缓存默认生存周期<br/>
 * define('SMARTYEX_LEFT_DELIMITER', '<%');			// 自定义的Smarty界定符<br/>
 * define('SMARTYEX_RIGHT_DELIMITER', '%>');<br/><br/>
 * 
 *		使用前先修改etc/setting.inc.php的配置, SMARTY_DIR设置为Smarty的安装绝对路径<br/>
 *		设置SMARTYEX_FILES_HOME常量为smarty 模板/配置及临时文件存放的根目录<br/>
 *		SMARTYEX_FILES_HOME设置好以后, <br/>
 *		模板路径为 SMARTYEX_FILES_HOME/templates<br/>
 *		config路径为 SMARTYEX_FILES_HOME/configs<br/>
 *		编译文件路径为 SMARTYEX_FILES_HOME/compiles<br/>
 *		缓存文件路径为 SMARTYEX_FILES_HOME/caches<br/>
 * 
 * Sample:<br/>
 * setting.inc.php
 * <code>
 * <?php
 *	define('SMARTY_DIR', 'e:/web/smarty_instance/libs/');
 *	define('SMARTYEX_FILES_HOME', 'e:/web/smarty_instance/temp_files/');
 *	define('SMARTYEX_COMPILE_CHECK', true);
 *	define('SMARTYEX_DEBUGGING', true);
 *	define('SMARTYEX_CACHING', true);
 *	define('SMARTYEX_CACHING_LIFETIME', 60);
 *	define('SMARTYEX_LEFT_DELIMITER', '<%');
 *	define('SMARTYEX_RIGHT_DELIMITER', '%>');
 *	?>
 * </code>
 * index.php
 * <code>
 * <?php
 * require_once('etc/setting.inc.php');
 * require_once('inc/_class.http.SmartyEx.inc.php');
 *	
 * $smarty = new SmartyEx('t1.tpl','nothing', 15);  	// 15秒过期
 * $smarty->displayEx();
 *
 * $smarty->init('t2.tpl', 'nothing', 10);				// 10秒过期
 * print($smarty->fetchEx());
 * ?>
 * </code>
 */
	class SmartyEx extends Smarty{
		private $resource;
		private $template;
		private $sign;
		private $identifier;
		private $params;

		public $forTest;

		const FUNC_REGISTER_FUNCTIONS 	= 'smartyex_register_functions';
		const FUNC_REGISTER_MODIFIERS 	= 'smartyex_register_modifiers';
		const FUNC_REGISTER_BLOCKS 		= 'smartyex_register_blocks';
		const FUNC_REGISTER_FILTERS 	= 'smartyex_register_filters';
		const FUNC_REGISTER_RESOURCE 	= 'smartyex_register_resource';

		const CACHE = 0X01;
		const COMPILE = 0X02;
		const BOTH_CACHE_COMPILE = 0x03;

		public function __construct($template=null, $forTest=false, $sign='standalone', $cacheLifeTime=SMARTYEX_CACHING_LIFETIME){
			parent::__construct();

			/*==================================================================
			* initialize smarty's properties
			*==================================================================*/
			$this->template_dir 	= SMARTYEX_FILES_HOME.'templates';
			$this->compile_dir 		= SMARTYEX_FILES_HOME.'compiles';
			$this->config_dir 		= SMARTYEX_FILES_HOME.'configs';
			$this->cache_dir 		= SMARTYEX_FILES_HOME.'caches';
			$this->force_compile	= SMARTYEX_FORCE_COMPILE;
			$this->compile_check 	= SMARTYEX_COMPILE_CHECK;
			$this->debugging 		= SMARTYEX_DEBUGGING;
			$this->caching 			= SMARTYEX_CACHING;
			$this->cache_lifetime 	= SMARTYEX_CACHING_LIFETIME;
			//$this->error_reporting	= "E_ALL & ~E_ERROR";

			$this->left_delimiter 	= SMARTYEX_LEFT_DELIMITER;
			$this->right_delimiter 	= SMARTYEX_RIGHT_DELIMITER;

			$this->init($template, $sign, $cacheLifeTime);

			$this->forTest = $forTest;

			/*==================================================================
			* initialize smarty's plugins
			*==================================================================*/
			$this->initPluginFunc();
		}

		public function __destruct(){
			$this->clean(self::BOTH_CACHE_COMPILE );
		}

		/**
	 * 初始化插件方法
	 * 
	 * @return void
	 */
		private function initPluginFunc(){
			call_user_func(self::FUNC_REGISTER_FUNCTIONS, $this);
			call_user_func(self::FUNC_REGISTER_MODIFIERS, $this);
			call_user_func(self::FUNC_REGISTER_BLOCKS , $this);
			call_user_func(self::FUNC_REGISTER_FILTERS , $this);
			call_user_func(self::FUNC_REGISTER_RESOURCE , $this);
		}

		/**
	 * 重置smarty状态
	 *
	 * @param string $template
	 * @param string $sign
	 * @param int $cacheLifeTime
	 * 
	 * @return void
	 */
		public function init($template, $sign='standalone', $cacheLifeTime=SMARTYEX_CACHING_LIFETIME){
			$this->cache_lifetime = $cacheLifeTime;
			$this->template = $template;
			$this->sign = $sign;
			$this->cleanAll();
		}

		/**
	 * 变量赋值
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return SmartyEx
	 */
		public function assignEx($key, $value){
			$this->pushParam($key, $value);
			return $this;
		}

		/**
	 * 压入变量
	 *
	 * @param string $key
	 * @param string $value
	 * 
	 * @return SmartyEx
	 */
		public function pushParam($key, $value){
			$this->params[$key] = &$value;
			return $this;
		}

		public function pushParams($params){
			foreach($params AS $key=>$value){
				$this->pushParam($key, $value);
			}
			return $this;
		}

		/**
	 * 设置params变量
	 *
	 * @param mixed[] $params
	 * 
	 * @return void
	 */
		public function setParams(& $params){
			$this->params = & $params;
		}

		/**
	 * 获得params变量
	 *
	 * @return mixed[]
	 */
		public function & getParams(){
			return $this->params;
		}

		/**
	 * 批量插入变量
	 *
	 * @param mixed[][] $params
	 */
		public function batchAssign($params=NULL){
			if(!isset($params)){
				$params = & $this->params;
			}

			foreach($params AS $key=>&$value){
				$this->assign_by_ref($key, $value);
			}
		}

		/**
	 * Smarty::display的代替方法
	 * 
	 * @return void
	 */
		public function displayEx(){
			$this->batchAssign();
			parent::display($this->template, $this->createId());
		}

		/**
	 * Smarty::fetch的代替方法
	 * 
	 * @return string
	 */
		public function fetchEx(){
			$this->batchAssign();
			return parent::fetch($this->template, $this->createId());
		}

		/**
	 * 清除该模板的缓存或编译文件
	 *
	 * @param int $type
	 * @return void
	 */
		public function clean($type = self::BOTH_CACHE_COMPILE){
			$id = $this->createId();
			if(self::CACHE & $type){
				parent::clear_cache($this->template, $id);
			}

			if(self::COMPILE  & $type){
				parent::clear_compiled_tpl($this->template, $id);
			}
		}

		/**
	 * 清除所有变量
	 * 
	 * @return void
	 */
		public function cleanAll(){
			parent::clear_all_assign();
			$this->params = array();
		}

		public function reset(){
			$this->cleanAll();
		}

		public function evaluate($params){
			$this->setParams($params);
			$ret = $this->fetchEx();
			$this->reset();
			return $ret;
		}

		/**
	 * 生成cacheid
	 *
	 * @return string
	 */
		public function createId(){
			return md5($this->template.'_'.$this->sign);
		}

		/**
	 * 判断是否被template是否被cache
	 * @return boolean
	 */
		public function isCached(){
			return parent::is_cached($this->template, $this->createId());
		}

		/**
	 * 获得cache文件路径
	 *
	 * @return string
	 */
		public function getCacheFile(){
			if($this->caching){
				$filename = $this->_get_auto_filename($this->cache_dir, $this->template,
				$this->_get_auto_id($this->createId())
				);
				return $filename;
			}else{
				return NULL;
			}
		}

		/**
	 * 获得cache文件最后更新时间
	 *
	 * @return int timestamp
	 */
		public function getCacheFileModifiedTime(){
			if($this->caching){
				return @filemtime($this->getCacheFile());
			}else{
				return 0;
			}
		}

		/**
	 * 智能显示
	 *
	 * @return void
	 */
		public function displaySmart(){
			$file = $this->getCacheFile();
			$content = $this->fetchEx();
			//$now = gmtmkDate(1);
			$now = @filemtime($file);
			if(!$now){
				$now = time();
			}
			//$servertime = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))?gmtdateParse($_SERVER['HTTP_IF_MODIFIED_SINCE']):0;
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
				$servertime = gmtdateParse($_SERVER['HTTP_IF_MODIFIED_SINCE']);
				$servertime = $servertime['timestamp'];
			}else{
				$servertime = 0;
			}

			$etag = md5($file + '_' + $now);
			header("ETag: ". $etag);

			if($now > $servertime){
				header("Last-Modified:".gmtmkDate($now));
				if(isset($_SERVER["HTTP_IF_NONE_MATCH"]) &&
				$_SERVER["HTTP_IF_NONE_MATCH"] == $etag){
					header("HTTP/1.1 304 Not Modified");
				}else{
					echo $content;
				}
			}else{
				header("HTTP/1.1 304 Not Modified");
			}
		}
	}

}
?>