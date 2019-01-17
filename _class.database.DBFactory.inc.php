<?php
/**
 * @package database
 * @author Mars <tempzzz>
 */
require_once('_interface.IDatabase.inc.php');
require_once('_class.database.Mysql.inc.php');
require_once('_class.database.Pdo.inc.php');
require_once('_class.database.Oracle.inc.php');
require_once('_class.database.Sqlrelay.inc.php');
require_once('_class.database.Sqlrelay4Mysql.inc.php');
//require_once('_class.database.Mssql.inc.php');
/**
 * DB工厂类
 * 
 */
class DBFactory{
	const TYPE_DB_MYSQL = 'MYSQL';
    const TYPE_DB_PDO = 'PDO';
	const TYPE_DB_MSSQL = 'MSSQL';
	const TYPE_DB_ORACLE = 'ORACLE';
	const TYPE_DB_SQLRELAY = 'SQLRELAY';
	const TYPE_DB_SQLRELAY_MYSQL = 'SQLRELAY4MYSQL';
	/**
	 * 唯一数据库实例
	 *
	 * @var IDatabase
	 */
	private static $singleton;
	/**
	 * db handler
	 *
	 * @var IDatabase
	 */
	private $db;

	public function __construct(){

	}

	public function __destruct(){

	}
	/**
	 * 获得唯一实例
	 *
	 * @param string $dbType
	 * @param string $host
	 * @param int $port
	 * @param string $dbName
	 * @param string $username
	 * @param string $password
	 * @param int $retryTimes
	 * @param int $tries
	 * @param boolean $debug
	 * 
	 * @return IDatabase
	 */
	public static function getInstance($dbType, $host=null, $port=null, $dbName=null, $username=null,
								   $password=null, $charset=null, $retryTimes=null, $tries=null, $debug=null){
		if(is_object($dbType) || is_array($dbType)){
			return call_user_func_array(array(self, "getInstance"), $dbType);
		}
		if(self::$singleton==NULL){
			self::$singleton = self::createInstance($dbType, $host, $port, $dbName, $username,
								   $password, $charset, $retryTimes, $tries, $debug);
		}else{
			// do nothing
		}
		return self::$singleton;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $dbType
	 * @param string $host
	 * @param int $port
	 * @param string $dbName
	 * @param string $username
	 * @param string $password
	 * @param int $retryTimes
	 * @param int $tries
	 * @param boolean $debug
	 * @return IDatabase
	 */
	public static function createInstance($dbType, $host, $port, $dbName, $username,
								   $password, $charset, $retryTimes, $tries, $debug){
		$db = NULL;
		switch ($dbType){
			case self::TYPE_DB_MYSQL :
				$db = new MySql($host, $port, $username, $password, $dbName, $charset);
				break;
            case self::TYPE_DB_PDO :
                $db = new ZZZ_Pdo($host, $port, $username, $password, $dbName, $charset);
                break;
			case self::TYPE_DB_ORACLE  :
				$db = new Oracle($username, $password, $dbName);
				break;
			case self::TYPE_DB_SQLRELAY  :
				$db = new Sqlrelay($host, $port, $username, $password, $retryTimes, $tries, $debug);
				break;
			case self::TYPE_DB_SQLRELAY_MYSQL  :
				$db = new Sqlrelay4Mysql($host, $port, $username, $password, $retryTimes, $tries, $debug);
				break;
			case self::TYPE_DB_MSSQL :
				$db = NULL; /* not implemented yet */
				break;
			default:
				$db = NULL;
				break;
		}
		return $db;
	}
}

?>