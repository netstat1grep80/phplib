<?php
include_once("_class.database.TableMeta.inc.php");

/**
 * 库表代理类
 * @version 1.0
 * 
 * 类标识特殊标签@xtable - 标识类对应的数据库表名称
 * 类标识特殊标签@xtable-prop-prefix [prefix] - 字段名前缀，比如为abc，那前缀就是abc_，此项不是必须
 * 类标识特殊标签@xtable-collection name=[name],class=[class],prop=[prop] - 标识1toN结构，name代表集合名称，class是集合对应的类名，prop是class中与本类关联的外键
 * 类表示特殊标签@xtable-set @xtable-set name=[name],table=[table],fk=[fk],class=[class] - 标识NtoN结构，name代表集合名称，table是中间表名，fk是中间表与本表关联的外键字段名，class是集合对应类的名称
 * 
 * 成员变量特殊标签@xtable-prop [prop] - 标识字段名（没有前缀的）
 * 成员变量特殊标签@xtable-pk - 标识字为主键
 * 成员变量特殊标签@xtable-fk [class] - 标识字段为外键，并且对应指定类
 * 
 * 特殊约定：外键名必须是fk_前缀名_字段名
 */
class Table{
	/**
	 * 数据表的描述
	 *
	 * @var array
	 */
	public static $metas=array();
	/**
	 * 代理类的名称
	 * @var string
	 */
	public $_class;
//	public $_table;
//	public $_prop_prefix;
//	public $_fields;
	
	/**
	 * 表描述数据结构
	 *
	 * @var TableMeta
	 */
	public $_meta;
	
	/**
	 * @var IDatabase
	 */
	public $db;
	
	public function __construct($class){
		$this->_class = strtolower($class);
		$this->db = DB::getDB();
		$this->db->uppercase = 0;
		$this->parse();
	}
	
	public function check(){
		return true;
	}
	
	/**
	 * 分析类注释，生成表结构
	 * @return void
	 */
	private function parse(){
		if(!array_key_exists($this->_class,table::$metas)){
			table::$metas[$this->_class] = new TableMeta($this->_class);
		}
		
		$this->_meta = &table::$metas[$this->_class];
	}
	
	/**
	 * 根据field结构返回真实字段名
	 *
	 * @param [] $field
	 * @return string
	 */
	public function get_field_name($field){
		if(array_key_exists("fk", $field)){
			return "fk_" . $this->_meta->_prop_prefix . $field["prop"];
		}else{
			return $this->_meta->_prop_prefix . $field["prop"];
		}
	}
	
	/**
	 * 插入记录
	 *
	 * @return int - 插入记录的主键数值
	 */
	public function insert(){
		if(!$this->check()){
			return 0;
		}
		
		$params = array();
		foreach($this->_meta->_fields as $field){
			if(array_key_exists("pk", $field) || !$this->{$field["var"]}) continue;
			
			$field_name = $this->get_field_name($field);
			$params[$field_name] = $this->{$field["var"]};
		}
		
		if($this->db->insert($this->_meta->_table, $params)){
			$id = $this->db->nextid;
			foreach($this->_meta->_fields as $field){
				if(array_key_exists("pk", $field)){
					$this->{$field["var"]} = $id;
					break;
				}
			}
			return $id;
		}else{
			return 0;
		}
	}
	
	/**
	 * 更新记录
	 * pk字段必须有值，可以部分字段有值部分字段忽略，忽略字段（即值为NULL）不会update到库表中
	 *
	 * @return boolean
	 */
	public function update(){
		if(!$this->check()){
			return 0;
		}
		
		$params = array();
		$wparams = array();
		
		foreach($this->_meta->_fields as $field){
			if(array_key_exists("pk", $field) || !$this->{$field["var"]}) continue;
			
			$field_name = $this->get_field_name($field);
			$params[$field_name] = $this->{$field["var"]};
		}
		
		foreach($this->_meta->_fields as $field){
			if(array_key_exists("pk", $field)){
				$wparams[$this->_meta->_prop_prefix . $field["prop"]] = $this->{$field["var"]};
				$condition = $this->_meta->_prop_prefix . $field["prop"]."=&".$this->_meta->_prop_prefix . $field["prop"];
				break;
			}
		}
	
		if(count($wparams)!=1){
			return 0;
		}else{
			return $this->db->update($this->_meta->_table, $params, $wparams, 
									 $condition);
		}
	}
	
	/**
	 * array类型的数据转换成类对象
	 *
	 * @param [] $row
	 * @param Table $obj - 如果传入null则返回一个新的对象，如果传入一个对象则对这个对象进行修改
	 * @return Table
	 */
	public function array2obj($row, $obj=NULL){
		if($obj===NULL) $obj = new $this->_class();
		
		foreach ($row as $key=>$value){
			foreach($this->_meta->_fields as $field){
				if( ($this->_meta->_prop_prefix . $field["prop"]) == $key
					or ("fk_". $this->_meta->_prop_prefix . $field["prop"]) == $key){
					$obj->{$field["var"]} = $value;
					continue;
				}
			}
		}
		
		return $obj;
	}
	
	public function to_array(){
		$ret = array();
		foreach($this->_meta->_fields as $field){
			$ret[$field["var"]] = $this->{$field["var"]};
		}
		return $ret;
	}
	
	/**
	 * 根据变量名找到字段信息
	 *
	 * @param string $fld_name
	 * @return []
	 */
	public function find_field($fld_name){
		foreach($this->_meta->_fields as $field){
			if(strtolower($fld_name) == strtolower($field["var"])){
				return $field;
			}
		}
		
		return null;
	}
	
	/**
	 * 载入对象，返回该行数据（数组）
	 * pk必须有一个值
	 *
	 * @param string $fields
	 * @return []
	 */
	public function get($fields="*"){
		$params = array();
		foreach($this->_meta->_fields as $field){
			if(array_key_exists("pk", $field)){
				$params[$this->_meta->_prop_prefix . $field["prop"]] = $this->{$field["var"]};
				$sql = "select ".$fields." from ".$this->_meta->_table." where ".$this->_meta->_prop_prefix . $field["prop"]."=&".$this->_meta->_prop_prefix . $field["prop"];
				break;
			}
		}
		
		$row = $this->db->fetchRow($sql, $params);
		return $row;
	}
	
	/**
	 * 载入对象，返回该行数据对应类的实例
	 *
	 * @param string $fields
	 * @return table
	 */
	public function get_obj($fields="*"){
		$row = $this->get($fields);
		if($row){
			$obj = $this->array2obj($row, $this);
			return $obj;
		}else{
			return null;
		}
	}
	
	/**
	 * 列表
	 *
	 * @param string $fields
	 * @param [] $params
	 * @param string $condition
	 * @param string $order
	 * @return []
	 */
	public function select($fields="*", $params=NULL, $condition="1=1", $order=NULL){
		$sql = "select " . $fields . " from " . $this->_meta->_table . " where " . $condition . ($order===NULL?"":"order by ".$order);
		$rows = $this->db->fetchAll($sql, $params);
		
		return $rows;
	}
	
	/**
	 * 列表对象
	 *
	 * @param string $fields
	 * @param [] $params
	 * @param string $condition
	 * @param string $order
	 * @return []
	 */
	public function select_obj($fields="*", $params=NULL, $condition="1=1", $order=""){
		$rows = $this->select($fields, $params, $condition);
		$ret = array();
		
		foreach($rows as $row){
			array_push($ret, $this->array2obj($row));
		}
		
		return $ret;
	}
	
	/**
	 * 分页列表
	 *
	 * @param int $page
	 * @param int $pageSize
	 * @param string $fields
	 * @param [] $params
	 * @param string $condition
	 * @param string $order
	 * @return []
	 */
	public function page($page, $pageSize, $fields="*", $params=NULL, $condition="1=1", $order=""){
		$sql = "select " . $fields . " from " . $this->_meta->_table . " where " . $condition . ($order===NULL?"":" order by ".$order);
		$ret = $this->db->selectPage($sql, $params, $page, $pageSize);
		
		return $ret;
	}
	
	/**
	 * 分页对象列表
	 *
	 * @param int $page
	 * @param int $pageSize
	 * @param string $fields
	 * @param [] $params
	 * @param string $condition
	 * @param string $order
	 * @return []
	 */
	public function page_obj($page, $pageSize, $fields="*", $params=NULL, $condition="1=1", $order=""){
		$ret = $this->page($page, $pageSize, $fields, $params, $condition, $order);
		$rows = & $ret["ROWS"];
		foreach($rows AS &$row){
			$row = $this->array2obj($row);
		}
		
		return $ret;
	}
	
	/**
	 * 返回指定外键的对应类名
	 *
	 * @param string $fk_name
	 * @return string
	 */
	public function find_fk_class($fk_name){
		foreach($this->_meta->_fields as $field){
			if( ($field["var"] == $fk_name) && array_key_exists("fk", $field)){
				$class = $field["fk"];
				break;
			}
		}
		
		if(!$class || !class_exists($class)){
			trigger_error("fk $class not found", E_USER_WARNING );
			return null;
		}else{
			return $class;
		}
	}
	
	/**
	 * 获得外键对应的数据（对象）
	 *
	 * @param string $fk_name
	 * @return table
	 */
	public function get_fk($fk_name){
		$class = $this->find_fk_class($fk_name);
		$obj = new $class();
		
		$pk_id = $this->{$fk_name};
		
		foreach($obj->_meta->_fields as $field){
			if(array_key_exists("pk", $field)){
				$obj->{$field["prop"]} = $pk_id;
				break;
			}
		}
		
		return $obj->get();
	}
	
	/**
	 * 获得外键对应的数据
	 *
	 * @param string $fk_name
	 * @return []
	 */
	public function get_fk_obj($fk_name){
		$class = $this->find_fk_class($fk_name);
		$obj = new $class();
		
		$row = $this->get_fk($fk_name);
		if($obj){
			return $obj->get_obj($row);
		}else{
			return null;
		}
	}
	
	/**
	 * 获得1toN的数据结构描述
	 *
	 * @param string $col_name
	 * @return []
	 */
	public function find_collection($col_name){
		foreach($this->_meta->_collections as $collection){
			if(strtolower($collection["name"]) == strtolower($col_name) ){
				return $collection;
			}
		}
		
		return null;
	}
	
	/**
	 * 返回类中定义的表格的主键结构
	 *
	 * @return []
	 */
	public function find_pk(){
		foreach($this->_meta->_fields as $field){
			if(array_key_exists("pk", $field)){
				return $field;
			}
		}
		
		return NULL;
	}
	/**
	 * 
	 *
	 * @param string $col_name
	 * @return []
	 */
	public function __prepare_collection_query($col_name){
		$collection = $this->find_collection($col_name);
		if(!$collection) return null;
		if(!class_exists($collection["class"])) {
			trigger_error("collection $set[class] not found", E_USER_WARNING );
			return null;
		}
		/**
		 * @var Table
		 */
		$obj = new $collection["class"]();
		if(array_key_exists($collection["prop"], $obj)){
			$field = $obj->find_field($collection["prop"]);
			$field_name = $obj->get_field_name($field);
			$condition = $field_name."=&".$field_name;
			$params = array();
			
			$pk = $this->find_pk();
			$params[$field_name] = $this->{$pk["var"]};
			return array($obj, "*", $params, $condition);
		}
		
		return null;
	}
	
	/**
	 * 获得集合列表
	 *
	 * @param string $col_name
	 * @param strng $order
	 * @return []
	 */
	public function get_collection($col_name, $order=NULL){
		$ret = $this->__prepare_collection_query($col_name);
		if($ret){
			list($obj, $fields, $params, $condition) = $ret;
			return $obj->select($fields, $params, $condition, $order);
		}
		
		return null;
	}
	
	/**
	 * 获得集合列表分页
	 *
	 * @param string $col_name
	 * @param int $page
	 * @param int $pagesize
	 * @param string $order
	 * @return []
	 */
	public function get_collection_page($col_name, $page, $pagesize, $order=NULL){
		$ret = $this->__prepare_collection_query($col_name);
		if($ret){
			list($obj, $fields, $params, $condition) = $ret;
			return $obj->page($page, $pagesize, "*", $params, $condition, $order);
		}
		
		return null;
	}
	
	public function get_collection_obj($col_name, $order=NULL){
		$ret = $this->__prepare_collection_query($col_name);
		if($ret){
			list($obj, $fields, $params, $condition) = $ret;
			return $obj->select_obj($fields, $params, $condition, $order);
		}
		
		return null;
	}
	
	public function get_collection_page_obj($col_name, $page, $pagesize, $order=NULL){
		$ret = $this->__prepare_collection_query($col_name);
		if($ret){
			list($obj, $fields, $params, $condition) = $ret;
			return $obj->page_obj($page, $pagesize, "*", $params, $condition, $order);
		}
		
		return null;
	}
	
	/**
	 * 返回NtoN结构
	 *
	 * @param string $set_name
	 * @return []
	 */
	public function find_set($set_name){
		foreach($this->_meta->_sets as $set){
			if(strtolower($set["name"]) == strtolower($set_name) ){
				return $set;
			}
		}
		
		return null;
	}
	
	public function find_set_by_table($set_table){
		foreach($this->_meta->_sets as $set){
			if(strtolower($set["table"]) == strtolower($set_table) ){
				return $set;
			}
		}
		
		return null;
	}
	
	/**
	 * 
	 *
	 * @param string $set_name
	 * @param string $order
	 * @return []
	 */
	public function __prepare_set_query($set_name, $order=NULL){
		$set = $this->find_set($set_name);
		if(!$set) return null;
		if(!class_exists($set["class"])){
			trigger_error("set $set[class] not found", E_USER_WARNING );
			return null;
		}
		$obj = new $set["class"]();
		
		$left_pk = $this->find_pk();
		$left_field_name = $this->get_field_name($left_pk);
		$params[$left_field_name] = $this->{$left_pk["var"]};
		
		$right_set = $obj->find_set_by_table($set["table"]);
		//var_dump($right_set);
		$right_pk = $obj->find_pk();
		$right_field_name = $obj->get_field_name($right_pk);
		
		$sql = "select ".$obj->_meta->_table.".* from ".$set["table"]
			."\n left join ".$this->_meta->_table." on ". $set["fk"] ."=". $left_field_name
			."\n left join ".$obj->_meta->_table." on ". $right_set["fk"] ."=". $right_field_name
			."\n where ".$set["fk"]."=&".$left_field_name;
		if($order){
			$sql .= "\n order by ".$order;
		}
		
		return array($obj, $sql, $params, $set, $left_pk, $left_field_name, $right_set, $right_pk, $right_field_name);
	}
	
	/**
	 * 获得n2n列表
	 *
	 * @param string $set_name
	 * @param string $order
	 * @return []
	 */
	public function get_set($set_name, $order=NULL){
		$ret = $this->__prepare_set_query($set_name, $order);
		if($ret){
			list($obj, $sql, $params) = $ret;
			$rows = $this->db->fetchAll($sql, $params);
			return ($rows);
		}
		
		return null;
	}
	
	/**
	 * 获得n2n分页列表
	 *
	 * @param string $set_name
	 * @param int $page
	 * @param int $pagesize
	 * @param string $order
	 * @return []
	 */
	public function get_set_page($set_name, $page, $pagesize, $order=NULL){
		$ret = $this->__prepare_set_query($set_name, $order);
		if($ret){
			list($obj, $sql, $params) = $ret;
			$ret = $this->db->selectPage($sql, $params, $page, $pagesize);
			return ($ret);
		}
		
		return null;
	}
	
	public function get_set_obj($set_name, $order=NULL){
		$ret = $this->__prepare_set_query($set_name, $order);
		if($ret){
			list($obj, $sql, $params) = $ret;
			$rows = $this->db->fetchAll($sql, $params);
			if($rows){
				foreach($rows as &$row){
					$row = $obj->array2obj($row);
				}
			}
			return ($rows);
		}
		
		return null;
	}
	
	public function get_set_page_obj($set_name, $page, $pagesize, $order=NULL){
		$ret = $this->__prepare_set_query($set_name, $order);
		if($ret){
			list($obj, $sql, $params) = $ret;
			$ret = $this->db->selectPage($sql, $params, $page, $pagesize);
			if($ret){
				foreach($ret["ROWS"] as &$row){
					$row = $obj->array2obj($row);
				}
			}
			return ($ret);
		}
		
		return null;
	}
	
	/**
	 * 绑定对象间的n2n关系
	 *
	 * @param string $set_name
	 * @param [] $objs - 可以是一个object而不是数组，函数会自动转换
	 * @return boolean
	 */
	public function bind_set($set_name, $objs){
		if(!is_array($objs)) $objs = array($objs);
		$ret = $this->__prepare_set_query($set_name);
		if($ret){
			list($_, $_, $_, $left_set, $left_pk, $left_field_name, $right_set, $right_pk, $right_field_name) = $ret;
			$sql = "insert into ".$left_set["table"]
				. " (".$left_set["fk"].", ".$right_set["fk"].")"
				. " values(&fk1, &fk2)";
			echo $sql."\n";
			foreach($objs as $obj){
				$params = array("fk1"=>$this->{$left_pk["var"]}, "fk2"=>$obj->{$right_pk["var"]});
				try{
					@$this->db->query($sql, $params);
				}catch(Exception $e){
					//var_dump($e);
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 解除对象间的n2n关系
	 *
	 * @param string $set_name
	 * @param [] $objs - 可以是一个object而不是数组，函数会自动转换
	 * @return boolean
	 */
	public function unbind_set($set_name, $objs){
		if(!is_array($objs)) $objs = array($objs);
		$ret = $this->__prepare_set_query($set_name);
		if($ret){
			list($_, $_, $_, $left_set, $left_pk, $left_field_name, $right_set, $right_pk, $right_field_name) = $ret;
			$sql = "delete from ".$left_set["table"]
				. " where ".$left_set["fk"]."=&fk1 and ".$right_set["fk"]."=&fk2";
			echo $sql."\n";
			foreach($objs as $obj){
				$params = array("fk1"=>$this->{$left_pk["var"]}, "fk2"=>$obj->{$right_pk["var"]});
				try{
					@$this->db->query($sql, $params);
				}catch(Exception $e){
					//var_dump($e);
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 智能选择是插入或更新
	 *
	 */
	public function save_or_update(){
		$pk = $this->find_pk();
		if($this->{$pk["var"]}===NULL){
			$this->insert();
		}else{
			$this->update();
		}
	}
	
	/**
	 * 删除
	 *
	 * @return boolean
	 */
	public function remove(){
		$pk = $this->find_pk();
		if($this->{$pk["var"]}!==NULL){
			$field_name = $this->get_field_name($pk);
			$sql = "delete from ".$this->_meta->_table ." where ".$field_name."=&value";
			$params = array("value"=>$this->{$pk["var"]});
			return $this->db->query($sql, $params);
		}
		
		return false;
	}
	
	public function count(){
		$row = $this->db->fetchRow("select count(*) as c from ".$this->_meta->_table);
		return $row["c"];
	}
}