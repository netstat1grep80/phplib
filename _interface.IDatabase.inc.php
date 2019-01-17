<?php
/**
 * @package database
 * @author Mars <tempzzz>
 *
 */
/**
 * DB Handler统一接口
 *   
 */
interface IDatabase{
	/**
	 * 连接数据库
	 * 
	 * @return resource_id
	 */
	public function connect();
	/**
	 * 获得连接资源
	 * @return resource
	 */
	public function getConnection();
	/**
	 * 关闭数据库
	 *
	 * @return boolean
	 */
	public function close();
	/**
		 * 释放查询结果
		 *
		 * @return boolean
		 */	
	public function freeResult();

	/**
	 * 影响行数
	 *
	 * @return int
	 */
	public function affectCount();
	/**
	 * 结果集记录数
	 *
	 * @return int
	 */
	public function numrows();
	/**
	 * 执行sql
	 *
	 * @param string $sql
	 * @param string[] $params
	 * @return resource_id
	 */
	public function query($sql, $params=NULL, $transaction=false);
	/**
	 * 业务开始
	 *
	 * @param boolean $transaction
	 */
	public function beginTransaction($transaction = true);
	/**
	 * 业务提交
	 *
	 * @param boolean $transaction
	 */
	public function commit($transaction = true);
	/**
	 * 业务回滚
	 *
	 * @param boolean $transaction
	 */
	public function rollback($transaction = true);
	/**
	 * 查询page相关参数
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @param string[] $params
	 * @return string[]
	 */
	public function queryPageVar($sql, $page, $pageSize, $params=NULL);
	/**
	 * 跳转到指定行
	 *
	 * @param int $pos - 从0开始
	 * @return boolean
	 */
	public function seek($pos);

	/**
	 * 返回单行结果
	 *
	 * @return string[]
	 */
	public function next();
	/**
	 * 返回单行结果
	 *
	 * @param string $sql
	 * @param string[] $params
	 * @return string[]
	 */
	public function fetchRow($sql, $params=NULL);
	public function fetchField($sql, $fieldName, $params=NULL);
	/**
	 * 返回查询的所有结果
	 *
	 * @param string $sql
	 * @param string[] $params
	 * @return string[][]
	 */
	public function fetchAll($sql, $params=NULL);
	public function getNextID();
	public function getInsertID();
	/**
	 * 生成分页SQL
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @return string
	 */
	public function createPageSQL($sql, $page, $pageSize);

	/**
	 * 得到栏位信息
	 *
	 * @param string $sql
	 * @param string[] $params
	 * 
	 * @return object[]
	 */
	public function getColumns($sql=NULL, $params=NULL);

	/**
	 * 获得错误代码
	 *
	 * @return string
	 */
	public function getErrorNo();
	/**
	 * 获得错误信息
	 *
	 * @return string
	 */
	public function getErrorMsg();

	/**
	 * escape the string 
	 * 
	 * @return string
	 */
	public static function escapeString($str);
	
	/**
	 * insert a row
	 *
	 * @param string $table
	 * @param [] $params
	 * @return boolean
	 */
	public function insert($table, $params);
	/**
	 * insert a row
	 *
	 * @param string $table
	 * @param [] $params
	 * @return boolean
	 */
	public function save($table, $params);
	/**
	 * update a row
	 *
	 * @param string $table
	 * @param int $id
	 * @param [] $params
	 * @param [] $condition_params
	 * @param string $condition
	 * 
	 * @return boolean
	 */	
	public function update($table, $params, $condition_params=array(), $condition='id=&id');
	/**
	 * update a row with a single field
	 *
	 * @param string $table
	 * @param int $id
	 * @param string $fieldName
	 * @param string $value
	 * @return boolean
	 */
	public function updateSingle($table, $id, $fieldName, $value);
	
	/**
	 * select rows for list page
	 *
	 * @param string $sql
	 * @param [] $params
	 * @param int $page
	 * @param int $pageSize
	 */
	public function selectPage($sql, $params, $page, $pageSize);
	
	public function remove($table, $id);
	
	public function forgeRemove($table, $id);
}
?>