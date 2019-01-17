<?php
/**
 * @package base
 * @author Becoder <becoder@gmail.com>
 */
/**
 * DB异常
 */
class ObjectException extends Exception {
	private $class_name;
	public function __construct($class_name, $message, $code=0){
		parent::__construct($message, $code);
		$this->class_name = $class_name;
	}
	
	/**
	 * toString
	 * @uses test
	 * @return string
	 */
	public function __toString(){
		return __CLASS__."[{$this->class_name}:{$this->code}]: {$this->message}";
	}
}
?>