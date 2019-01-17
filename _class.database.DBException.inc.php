<?php
/**
 * @package database
 * @author Mars <tempzzz>
 */
/**
 * DB异常
 */
class DBException extends Exception {
	private $dbType;
	public function __construct($dbType, $message, $code=0){
		parent::__construct($message, $code);
		$this->dbType = $dbType;
	}
	
	/**
	 * toString
	 * @uses test
	 * @return string
	 */
	public function __toString(){
		return __CLASS__."[{$this->dbType}:{$this->code}]: {$this->message}";
	}

	const CODE_CONNECTION_FAILED = 0x0001;
	const MSG_CONNECTION_FAILED = '无法连接数据库。';
}

//throw new DBException("1", "2", "3");

?>