<?php
require_once("_func.text.string.inc.php");
/**
 * @package base
 *
 */

abstract class AbstractService {
	
	/**
	 * 类名
	 *
	 * @var AbstractObject
	 */
	public  $class_name;
	public  $table_name;
	/**
	 * 构造方法
	 * @param int $id 对象ID
	 */
	public function __construct($class_name){
		$this->class_name=$class_name;
		$this->setTableName($class_name);
	}
	/**
	 * 获取类名称
	 *
	 * @return String
	 */
	public function getClassName(){
		return $this->class_name;
	}
	
	/**
	 * 获取Table名称
	 *
	 * @return String
	 */
	public function getTableName(){
		return $this->table_name;
	}
	
	/**
	 * 设置Table名称
	 *
	 * @return String
	 */
	public function setTableName($table){
		$this->table_name=$table;
	}
	
	/**
	 * 通过ID获取对象...
	 *
	 * @param int $id
	 * @return AbstractObject
	 */
	public function get($id){
		$obj=new $this->class_name();
		$obj->setId($id);
		$obj->load();
		if(!$obj->isValid()) return false;
		return $obj;
	}
	
	/**
	 * 获取数据列表
	 *
	 * @param where子句 $where_clause
	 * @param 参数列表 $params
	 * @param 字段列表 $fields
	 * @return string[]
	 */
	public function getList($where_clause=" ",$params=null,$fields="*"){
		$sql="select $fields from `".$this->getTableName().
				"`  ".$where_clause;
		$result=array();
		if($temp=$this->getDB()->fetchAll($sql,$params)){
			foreach($temp as $row){
				/*
				if($fields=="*"){
					$obj=new $this->class_name;
					$obj->setFields($row);
					$result[]=$obj;
				}else{*/
					$result[]=$row;
					//$this->getDB()->affectCount();
				//}
			}
		}
		return $result;
	}
	
	/**
	 * 获取记录集大小
	 *
	 * @param string $where_clause
	 * @param string[] $params
	 * @return int
	 */
	public function getRecordCount($where_clause=" ",$params=null){
		$sql="select count(*) rcount from `".$this->getTableName().
				"`  ".$where_clause;
		$row = $this->getDB()->fetchRow($sql, $params);
		$count = $row['RCOUNT'];
		return $count;
	}
	
	/**
	 * 获取翻页信息...
	 *
	 * @param string $where_clause
	 * @param string[] $params
	 * @param int $page
	 * @param int $pageSize
	 * @return array
	 */
	public function getPagination($where_clause=" ",$params=null,$page=1, $pageSize=20){
		$count=$this->getRecordCount($where_clause,$params);
		$pageCount = (int)floor($count/$pageSize);
		$pageCount += ($count%$pageSize==0)?0:1;
		$page = ($page<1)?1:$page;
		$page = ($page>$pageCount)?$pageCount:$page;

		return array("COUNT"=>$count,
				"PAGECOUNT"=>$pageCount,
				"PAGE"=>$page,
				"PAGESIZE"=>$pageSize);
	}
	
	/**
	 * 获取信息,列表数据,记录集大小,翻页信息
	 *
	 * @param string $where_clause
	 * @param string[] $params
	 * @param string $fields
	 * @param int $page
	 * @param int $pageSize
	 * @return array
	 */
	public function getRows($where_clause=" ",$params=null,$fields="*",$page=1, $pageSize=20){
		$where_clause_page=$where_clause.' limit '.(($page-1)*$pageSize).','.($pageSize);
		$list=$this->getList($where_clause_page,$params,$fields);
		$pagination=$this->getPagination($where_clause,$params);
		return array("LIST"=>$list,
					"PAGINATION"=>$pagination);
	}
	
	/**
	 * 获取全部信息,列表数据,记录集大小,翻页信息
	 *
	 * @param string $where_clause
	 * @param string[] $params
	 * @param string $fields
	 * @return array
	 */
	public function getAll($where_clause=" ",$params=null,$fields="*"){
		$list=$this->getList($where_clause,$params,$fields);
		return $list;
	}
	
	/*                        未定义方法                       */
	/**
	 * 初始化实例
	 */
	//public abstract static function init();
	/**
	 * 取得实例
	 *
	 * @return AbstractService
	 */
	//public abstract static function getInstance();
	/**
	 * get db instance
	 * 
	 * @return IDatabase
	 */
	public abstract function getDB();
	
}
?>