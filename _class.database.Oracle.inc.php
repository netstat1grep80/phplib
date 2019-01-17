<?php
/**
 * @package database
 * @author Mars <tempzzz>
 */
require_once('_interface.IDatabase.inc.php');
require_once('_class.database.DBException.inc.php');
require_once('_class.database.AbstractDB.inc.php');
require_once('_func.text.string.inc.php');

/**
 * Oracle Handler
 */
class Oracle extends AbstractDB  {
	private $con;
	private $stmt;
	private $cur;

	private $host;
	private $port;
	private $username;
	private $password;
	private $dbName;
	
	const TYPE = 'Oracle';
	
	/**
	 * 构造方法
	 * 
	 * @param string $username - 用户名
	 * @param string $password - 密码
	 * @param string $dbName - 数据库名
	 */
	public function __construct($username, $password, $dbName){
		$this->username = $username;
		$this->password = $password;
		$this->dbName = $dbName;

		$this->connect();
	}
	
	/**
	 * 析构方法
	 *
	 */
	public function __destruct(){
		$this->freeResult();
		$this->close();
	}

	/**
	 * 连接数据库
	 * 
	 * @return resource_id
	 */
	public function connect(){
		$this->con = oci_pconnect(
			$this->username, 
			$this->password, 
			$this->dbName
		);
		if(!$this->con){
			throw new DBException(self::TYPE, $this->getErrorMsg(), $this->getErrorNo());
		}

		return $this->con;
	}
	
	public function getConnection(){
		return $this->con;
	}

	/**
	 * 关闭数据库
	 *
	 * @return boolean
	 */
	public function close(){
		$this->commit();
		return oci_close($this->con);
	}

	/**
	 * 释放查询结果
	 *
	 * @return boolean
	 */
	public function freeResult(){
		if($this->stmt){
			return @oci_free_cursor($this->stmt);
		}else{
			return true;
		}
	}

	/**
	 * 绑定参数
	 * 
	 * @access private
	 *
	 * @param string $key
	 * @param string $value
	 * 
	 * @return string
	 */
	private function bindParam($key, &$value){
		if($key=="CURSOR") //$params中存在名为CURSOR的键值，说明是要获取游标
		{
			$this->cur = oci_new_cursor($this->conn);//游标
			oci_bind_by_name($this->stmt, $value, $this->cur, -1, OCI_B_CURSOR);
		}
		else{
			oci_bind_by_name($this->stmt, $key, $value); //不能直接使用oci_bind_by_name($this->stid, $key, $value);
		}
	}

	/**
	 * 绑定参数
	 * 
	 * @access private
	 *
	 * @param string[] $params
	 * 
	 * @return string
	 */
	private function bindParams($params){
		if($params==NULL){
			return ;
		}
		foreach($params AS $key=>&$value){
			$sql = $this->bindParam($key, $value);
		}
		return $sql;
	}

	/**
	 * 执行sql
	 *
	 * @param string $sql
	 * @param string[] $params
	 * @param boolean $transaction
	 * 
	 * @return resource_id
	 * 
	 * @throws DBException
	 */
	public function query($sql, $params=NULL, $transaction=true){
		unset($this->stmt);
		$sql = formatSQL(removeBlank($sql));
		$this->stmt = oci_parse($this->con, $sql);
		if(!$this->stmt){
			throw new DBException(self::TYPE ,$this->getErrorMsg(), $this->getErrorNo());
		}
		$this->bindParams($params);
		
		$this->beginTransaction($transaction);
		$flag = oci_execute($this->stmt);
		if(isset($this->cur))
		{
			oci_execute($this->cur);

			$rows = array();
			while ($row = @oci_fetch_array($this->cur))
			{
				$rows[] = $row;
			}
			oci_free_statement($this->cur);//注意释放游标资源！
			unset($this->cur);
			if (count($rows) == 0) $rows = null;
			return $rows; //直接返回游标记录集
		}		
		
		if(!$flag){
			$this->rollback($transaction);
			throw new DBException(self::TYPE ,$this->getErrorMsg(), $this->getErrorNo());
		}else{
			$this->commit($transaction);
		}
		
		return $flag;
	}
	
	/**
	 * 业务开始
	 *
	 * @param boolean $transaction
	 */
	public function beginTransaction($transaction = true){
		// not implemented
	}
	
	/**
	 * 业务提交
	 *
	 * @param boolean $transaction
	 */
	public function commit($transaction = true){
		if($transaction){
			oci_commit($this->con);
		}
	}
	
	/**
	 * 业务回滚
	 *
	 * @param boolean $transaction
	 */
	public function rollback($transaction = true){
		if($transaction){
			oci_rollback($this->con);
		}
	}

	/**
	 * 影响行数
	 *
	 * @return int
	 */
	public function affectCount(){
		// not implemented
		return 0;
	}
	
	/**
	 * 结果集记录数
	 *
	 * @return int
	 */
	public function numrows(){
		if($this->stmt){
			return oci_num_rows($this->stmt);
		}
		return NULL;
	}

	/**
	 * 查询page相关参数
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @param string[] $params
	 * 
	 * @return string[]
	 */
	public function queryPageVar($sql, $page, $pageSize, $params=NULL){
		$regx = "|select\s+(.*)\s+from\s+(.*)|Ui";
		$sql = preg_replace($regx,
		'
select count(*) as rcount from \\2
			',
		$sql);
		$row = $this->fetchRow($sql, $params);
		$count = $row['RCOUNT'];
		$pageCount = (int)floor($count/$pageSize);
		$pageCount += ($count%$pageSize==0)?0:1;
		$page = ($page<1)?1:$page;
		$page = ($page>$pageCount)?$pageCount:$page;

		return array("COUNT"=>$count,
		"PAGECOUNT"=>$pageCount,
		"PAGE"=>$page,
		"PAGESIZE"=>$pageSize);
	}
	
	public static function getPageSql($sql,$page, $pageSize){
		
	}

	/**
	 * 跳转到指定行
	 *
	 * @param int $pos - 从0开始
	 * 
	 * @return boolean
	 */
	public function seek($pos){
		// not implemented
		return false;
	}

	/**
	 * 返回单行结果
	 *
	 * @return string[]
	 */
	public function next(){
		$row = oci_fetch_assoc($this->stmt);
		if(!$row) return NULL;
//		$ret =  array();
//		foreach($row AS $key=>$value){
//			$ret[strtoupper($key)] = $value;
//		}
		return $row;
	}

	/**
	 * 返回单行结果
	 *
	 * @param string $sql
	 * @param string[] $params
	 * 
	 * @return string[]
	 */
	public function fetchRow($sql, $params=NULL){
		$this->query($sql, $params);
		return $this->next();
	}

	/**
	 * 返回查询的所有结果
	 *
	 * @param string $sql
	 * @param string[] $params
	 * 
	 * @return string[][]
	 */
	public function fetchAll($sql, $params=NULL){
		$this->query($sql, $params);
		$ret = array();
		while($row = $this->next()){
			$ret[]=$row;
		}
		return $ret;
	}

	public function getNextID(){
		$tmpstid = $this->query("select $seqname.nextval as nextid from dual");
		if (!$tmpstid)
		{
			return false;
		}
		if($row = sqlrcur_getRowAssoc($this->cur, 0))
		{
			return $row['NEXTID'];
		}
	}
	
	public function getInsertID(){
		return $this->getNextID();
	}
	/**
	 * 生成分页SQL
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @return string
	 */
	public function createPageSQL($sql, $page, $pageSize){
		$sql .= '';
		return $sql;
	}

	/**
	 * 得到栏位信息
	 *
	 * @param string $sql
	 * @param string[] $params
	 * 
	 * @return object[]
	 */
	public function getColumns($sql=NULL, $params=NULL){
		if($sql!=NULL){
			$this->query($sql, $params);
		}
		$ret = array();

		for($i=1; $i<=oci_num_fields($this->stmt); $i++){
			$obj = new stdClass();
			$obj->name = oci_field_name($this->stmt, $i);
			$obj->type = oci_field_type($this->stmt, $i);
			$obj->size = oci_field_size($this->stmt, $i);
			$obj->isnull = oci_field_is_null($this->stmt, $i);
			$ret[] = $obj;
		}
		return $ret;
	}

	/**
	 * 获得错误代码
	 *
	 * @return string
	 */
	public function getErrorNo(){
		$err = oci_error($this->stmt);
		return $err["code"];
	}

	/**
	 * 获得错误信息
	 *
	 * @return string
	 */
	public function getErrorMsg(){
		$err = oci_error($this->stmt);
		return $err['message'];
	}

	/**
	 * escape the string 
	 * 
	 * @return string
	 */
	public static function escapeString($str){
		if(gettype($str)!='string') return $str;
		return str_replace("'", "''", $str);
	}
}
/*try{
	$db = new Oracle('zhouxiaoyang', 'zhouxiaoyang', 'MOYU');
	//$db->query("set names 'gbk'");
	$sql = 'select * from tab_tag';
	var_dump($db->fetchAll($sql));
	var_dump($db->getColumns());
}catch(DBException $e){
	echo $e;
}*/
?>