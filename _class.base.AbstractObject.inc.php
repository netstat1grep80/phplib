<?php

require_once("_func.text.string.inc.php");
require_once("_class.base.ObjectException.inc.php");
/**
 * @package base
 *
 */

abstract class AbstractObject {
	
	public  $id;
	public  $table_name;
	/**存放持久化数据**/
	public  $fields;
	/**存放未持久化数据**/
	public  $new_rows=array();
	/**
	 * 对象状态
	 *
	 * @var string
	 */
	private  $_state;
	
	/**
	 * 构造方法
	 * @param int or string[] $id_fields 对象ID 或者数据源
	 */
	public function __construct($id_fields=null){
		if(!asserting($id_fields)){
			/**
			 * 无效对象或未赋值对象
			 */
			$this->set_State("new");
			return;
		}
		if(asserting($id_fields,true)){
			/**
			 * 如果为数组则认为是数据源
			 */
			$this->setFields($id_fields);
			return;
		}
		$this->setId($id_fields);
	}
	
	/**
	 * just magic method
	 *
	 * @param unknown_type $fname
	 * @param unknown_type $plist
	 **/
	public function __call($fname, $plist){
		/*If function is a getter!*/
		if(strpos($fname,"get")===0){
			$field_name=substr($fname, 3);
			if(asserting($this->fields,true)&&array_key_exists(strtoupper($field_name),$this->fields)){
				return $this->getFieldValue($field_name);
			}else{
				return false;
			}
		}
		
		/*If function is a setter!*/
		if(strpos($fname,"set")===0){
			/**
			 * 只读对象 禁止对其他属性进行setter操作
			 */
			if($this->get_State()=="readonly") {
				throw new ObjectException(__CLASS__, "只读对象,禁止对其他属性进行setter操作", "200036");
				return false;
			}
			$field_name=substr($fname, 3);
			$this->setFieldValue(strtoupper($field_name),$plist[0]);
		}
	}
	
	/**
	 * 设置ID
	 */
	public function setId($id){
		$this->setFieldValue("id",$id);
		/* 
		*  当你通过setter设置id或者通过obj=new obj(3) 方式来生成对象时,
		*  你只能进行load操作来判断对象是否存在于数据库中
		*  此时  你无法通过setter  -  save方法来创建一个新的对象
		*/
		$this->set_State("readonly");
	}
	
	/**
	 * 获得ID
	 * @return id
	 */
	public function getId(){
		return $this->getFieldValue("id");
	}
	
	/**
	 * 设置数据源
	 *
	 * @param string[] $fields
	 */
	public function setFields($fields){
		if(asserting($fields,true)){
			$this->fields=$fields;
			$this->persist();
		}
	}
	
	/**
	 * 设置更新数据源
	 *
	 * @param string[] $new_rows
	 */
	public function setNewRows($new_rows){
		/**
			 * 只读对象 禁止对new_rows进行set操作
			 */
		if($this->get_State()=="readonly") {
			throw new ObjectException(__CLASS__, "只读对象,禁止对new_rows进行set操作", "200036");
			return false;
		}
		if(asserting($new_rows,true)){
			$this->new_rows=$new_rows;
		}
	}
	/**
	 * 取得对象状态
	 *
	 * @return string
	 */
	public function get_State(){
		return $this->state;
	}
	
	/**
	 * 设置对象状态
	 *
	 * @param string $_state
	 */
	private function set_State($_state){
		$this->state=$_state;
	}
	
	/**
	 * 返回当前对象所对应的数据库记录中字段取值
	 *
	 * @param string $field_name
	 * @return unknown
	 */
	public final function getFieldValue($field_name){
		/**先返回未持久化数据**/
		if(asserting($this->new_rows,true)){
			if(array_key_exists($field_name,$this->new_rows)){
				return $this->new_rows[$field_name];
			}
		}
		if(asserting($this->fields,true)){
			if(array_key_exists(strtoupper($field_name),$this->fields)){
				return $this->fields[strtoupper($field_name)];
			}else if(array_key_exists($field_name,$this->fields)){
				return $this->fields[$field_name];
			}
		}
	}
	
	/**
	 * 设置当前对象所对应的数据库记录中字段值
	 *
	 * @param string $field_name
	 * @param unknown_type $value
	 * @return none
	 */
	 
	public final function setFieldValue($field_name,$value){
		if(array_key_exists(strtoupper($field_name),$this->new_rows)){
			/**如果已持久化数据与赋值数据相等  则取消操作**/
			if($this->fields[strtoupper($field_name)]==$value) return;
		}
		$this->new_rows[$field_name]=$value;
		/**
		 * 无效对象或未赋值对象进行set操作后,
		 * 状态更改为新对象
		 */
		if(asserting($value)&&"invalid"==$this->get_State()) $this->set_State("new");
	}
	
	/**
	 * 从数据库中读取数据
	 */
	
	public function load(){		
		$sql = "select * from ".$this->getTableName()." where id=&id";
		if($this->fields=$this->getDB()->fetchRow($sql, $this->new_rows)){
			/**
			 * 已经持久化后对象
			 */
			$this->persist();
		}else{
			/**
			 * 无效对象或未赋值对象
			 */
			$this->set_State("invalid");
		}
		return $this->isValid();
	}
	
	/**
		 * 持久化新对象
		 *
		 * @return boolean
		 */	
	public function save(){
		if($this->get_State()!="new") return false;
		
		$this->setFieldValue("create_time",time());
		if($this->getDB()->insert($this->getTableName(),$this->new_rows)){
			$this->fields=$this->new_rows;
			$this->fields["id"]=$this->getDB()->getInsertID();
			$this->persist();
			return true;
		}
		return false;
	}
	
	/**
		 * 持久化已有对象
		 *
		 * @return boolean
		 */	
	public function update(){
		if(!asserting($this->getId())){
			/*id字段未定义*/
			return false;
		}
		
		/**  未持久化对象无法进行update 操作*/
		if($this->get_State()!="persistent") return false;
		
		$cparams = array(
			'id'=>$this->getId()
		);
		if($this->getDB()->update($this->getTableName(), $this->new_rows,$cparams)){
			$this->persist();
			return true;
		}
		return false;
	}
	
	/**
	 * 设置对象状态为持久化对象
	 *
	 */
	public function persist(){ 
		$this->set_State("persistent");
		$this->new_rows=array();
	}
	/**
		 * 删除现有对象
		 *
		 * @return boolean
		 */	
	public function delete(){
		if(!asserting($this->getId())){
			/*id字段未定义*/
			return false;
		}
		return $this->getDB()->remove($this->getTableName(),$this->getId());
	}
	
	public function isValid(){
		return $this->get_State()!="new"&&$this->get_State()!="invalid";
	}
	
	public function equals($obj){
		//echo "equals method!";
		return ($this->getId()===$obj->getId())&&($this->getTableName()===$obj->getTableName())&&($this->fields===$obj->fields);
	}

	/*                        未定义方法                       */
	
	/**
	 * get db instance
	 * 
	 * @return IDatabase
	 */
	public abstract function getDB();
	
	/**
	 * 获取Object mapping Table名称
	 *
	 * @return String
	 */
	public abstract function getTableName();
}
?>