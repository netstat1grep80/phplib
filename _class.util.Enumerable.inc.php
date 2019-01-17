<?php
require_once('_func.util.common.inc.php');

class Enumerable{ 
	private $array;
	function __construct($A){	
		$this->array = $A;
	}
	
	/**
	 * Enter description here...
	 * 
	 * @param function $funcName
	 * 
	 * @example itr(array(1, 2, 3, 4))->each(create_function('$v', 'echo $v;'));
	 */
	function each($funcName){
		$params = func_get_args();
		array_shift($params);
		foreach($this->array AS $key=>$value){
			$parameters = array();
			$parameters = array_merge(array($value), $params);
			call_user_func_array($funcName, $parameters);
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param function $funcName
	 * @return mixed[]
	 * 
	 * @example var_dump(itr(array(1, 2, 3, 4))->map(create_function('$v', 'return $v*2;')));
	 */
	function map($funcName){
		$params = func_get_args();
		array_shift($params);
		
		$ret = array();
		foreach($this->array AS $key=>$value){
			$parameters = array();
			$parameters = array_merge(array($value), $params);
			$ret[$key] = call_user_func_array($funcName, $parameters);
		}
		return $ret;
	}
	
	/**
	 * 如果数组任何值使用指定函数返回true
	 *
	 * @param function $func
	 * @return boolean
	 * 
	 * @example var_dump(itr(array(1, 2, 3, 4))->any(create_function('$v', 'return $v%2==0;')));
	 */
	function any($funcName=NULL){
		$params = func_get_args();
		array_shift($params);
		
		if($funcName==NULL){
			$funcName = create_function('$v', 'return asserting($v);');
		}
		
		foreach ($this->array AS $value){
			$parameters = array();
			$parameters = array_merge(array($value), $params);
			
			if(call_user_func_array($funcName, $parameters)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 找出所有符合条件的数组值
	 *
	 * @param function $funcName
	 * @return mixed
	 * 
	 * @example var_dump(itr(array(1, 2, 3, 4))->findAll(create_function('$v', 'return $v%2==0;')));
	 */
	function findAll($funcName){
		$params = func_get_args();
		array_shift($params);
		
		$ret = array();
		foreach($this->array AS $key=>$value){
			$parameters = array();
			$parameters = array_merge(array($value), $params);
			$result = call_user_func_array($funcName, $parameters);
			if(asserting($result)){
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	/**
	 * 判断数组是否包含指定值
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	function included($value){
		return $this->any(create_function('$v1, $v2', 'return $v1 == $v2;'), $value);
	}
	
	/**
	 * 匹配符合正则表达式的数组值，返回
	 *
	 * @param string $pattern
	 * @return mixed
	 * 
	 * @example var_dump(itr(array("3"=>1.1, 2.2, 3, 4))->match("/^(\d+)?\.\d+$/"));
	 */
	function match($pattern){
		$ret = array();
		
		$ret = preg_grep($pattern, $this->array);
		return $ret;
	}
	
	function toArray(){
		return $this->array;
	}
}
/**
 * Enter description here...
 *
 * @param mixed[] $array
 * @return Enumerable
 */
function itr($array){
	return new Enumerable($array);
}

//itr(array(1, 2, 3, 4))->each(create_function('$v, $t', 'echo $v*$t;'), 2);
?>