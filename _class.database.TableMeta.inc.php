<?php
class TableMeta{
	
	public $_class;
	public $_table;
	public $_prop_prefix;
	public $_fields = array();
	public $_collections = array();
	public $_sets = array();
	
	public function __construct($class){
		$this->_class = $class;
		$this->parse();
	}
	
	private function parse(){
		$class = new ReflectionClass($this->_class);
		$class_comment = $class->getDocComment();
		$this->parseClass($class_comment);
		
		$props = $class->getProperties();
		foreach($props as $prop){
			$this->parseProp($prop);
		}
	}
	
	private function parseClass($comment){
		$regex = "~@xtable\s(\S+)~";
		$flag = preg_match($regex, $comment, $match);
		if($flag<1){
			trigger_error("missing @xtable comment", E_USER_WARNING );
		}else{
			$this->_table = $match[1];
		}
		$regex = "~@xtable-prop-prefix\s(\S+)~";
		$flag = preg_match($regex, $comment, $match);
		if($flag<1){
			$this->_prop_prefix = "";
		}else{
			$this->_prop_prefix = $match[1]."_";
		}
		
		$regex = "~@xtable-collection\s(.*)~";
		$flag = preg_match_all($regex, $comment, $match);
		if($flag>0){
			for($i=0; $i<$flag; $i++){
				$v = $match[1][$i];
				$entry = array();
				$ll = explode(",", $v);
				foreach($ll as $l){
					list($key, $value) = explode("=", $l);
					$entry[$key] = $value;
				}
				array_push($this->_collections, 
					$entry);
			}
		}		
		
		$regex = "~@xtable-set\s(.*)~";
		$flag = preg_match_all($regex, $comment, $match);
		if($flag>0){
			for($i=0; $i<$flag; $i++){
				$v = $match[1][$i];
				$entry = array();
				$ll = explode(",", $v);
				foreach($ll as $l){
					list($key, $value) = explode("=", $l);
					$entry[$key] = $value;
				}
				array_push($this->_sets, 
					$entry);
			}
		}
	}
	
	/**
	 *
	 * @param ReflectionProperty $prop
	 */
	private function parseProp($prop){
		$comment = $prop->getDocComment();
		$regex = '~@xtable-prop\s(\S+)~';
		$flag = preg_match($regex, $comment, $match);
		if($flag>0){
			$var_name = $prop->getName();
			$prop = $match[1];
			$field = array("var"=>$var_name, "prop"=>$prop);
			
			$regex = '~@xtable-pk~';
			$flag = (preg_match($regex, $comment)==1);
			if($flag>0){
				$field["pk"] = true;
			}
			
			$regex = '~@xtable-fk\s(\S+)~';
			$flag = preg_match($regex, $comment, $match);
			if($flag>0){
				$field["fk"] = $match[1];
			}
			
			array_push($this->_fields, $field);
		}
	}
	
	public function get_table(){
		return array("table"=>$this->_table, "prefix"=>$this->_prop_prefix, "fields"=>$this->_fields);
	}
}