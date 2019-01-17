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
 * Mysql Handler
 * @package database
 * @author Mars <tempzzz>
 */
class Mysql extends AbstractDB {
	private $con;
	private $cur;
	private $username;
	private $password;
	private $dbName;
	private $charset;
	
	public $affected;
	public $nextid;
	
	const TYPE = 'MySQL';
	
	/**
	 * 构造方法
	 *
	 * @param string $host - 数据库主机
	 * @param int $port - 数据库端口
	 * @param string $username - 用户名
	 * @param string $password - 密码
	 * @param string $dbName - 数据库名
	 */
	public function __construct($host, $port, $username, $password, $dbName, $charset){
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->dbName = $dbName;
		$this->charset = $charset;

		$this->connect();
		$this->selectDB();
		
		@$this->query('set names '.$this->charset);
		$this->query('SET AUTOCOMMIT=0');
	}
	
	/**
	 * 析构方法
	 *
	 */
	public function __destruct(){
		//$this->freeResult();
		$this->close();
	}

	/**
	 * 连接数据库
	 * 
	 * @return resource_id
	 */
	public function connect(){
		$this->con = mysql_connect(
			$this->host.':'.$this->port,
			$this->username,
			$this->password,
			true
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
	 * 选择数据库
	 *
	 * @param string $dbName
	 * @return boolean
	 * 
	 * @throws DBException
	 */
	public function selectDB($dbName=NULL){
		$dbName = ($dbName==NULL)?$this->dbName:$dbName;
		$flag = mysql_select_db($dbName, $this->con);
		if(!$flag){
			throw new DBException(self::TYPE , $this->getErrorMsg(), $this->getErrorNo());
		}
		return $flag;
	}

	/**
	 * 关闭数据库
	 *
	 * @return boolean
	 */
	public function close(){
		return mysql_close($this->con);
	}

	/**
	 * 释放查询结果
	 *
	 * @return boolean
	 */
	public function freeResult(){
		if($this->cur){
			return @mysql_freeresult($this->cur);
		}else{
			return true;
		}
	}

	/**
	 * 绑定参数
	 * 
	 * @access private
	 *
	 * @param string $sql
	 * @param string $key
	 * @param string $value
	 * 
	 * @return string
	 */
	private function bindParam($sql, $key, $value){
		$sql .= ' ';
		$value = $this->escapeString($value);
		$sql = preg_replace('/&'.$key.'\s+/', $value.' ', $sql);
		return $sql;
	}

	/**
	 * 绑定参数
	 * 
	 * @access private
	 *
	 * @param string $sql
	 * @param string[] $params
	 * 
	 * @return string
	 */
	private function bindParams($sql, $params){
		if($params == NULL) return $sql;
		$regex = '|\&[a-z\_][a-z\_\d]*|i';
		$blocks = preg_split($regex, $sql);
		preg_match_all($regex, $sql, $match);
		$exp = array();
		for($i=0; $i<count($match[0]); $i++){
			$bind = false;
			foreach($params AS $key => $value){
				$v = '&'.$key;
				if($v==$match[0][$i]){
					if($value===NULL){
						$exp[$i] = 'null';
					}elseif(strpos( $v, '&__ne_')!==0){
						$exp[$i] =  $this->escapeString($value);
					}else{
						$exp[$i] =  $value;
					}
					$bind = true;
					break;
				}
			}
			if(!$bind){
				$exp[$i] = 'null';
				$bind = true;
				//return false;
			}
		}
		$exp[] = '';
		$ret = '';
		for($i=0; $i<count($blocks); $i++){
			$ret .= $blocks[$i].$exp[$i];
		}

		return $ret;
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
		//$sql = formatSQL(removeBlank($sql));
		$this->nextid = 0;
		$this->affected = 0;
		$sql = $this->bindParams($sql, $params);
        file_put_contents('/var/udblogs/'.date("Ymd").'sql.log',date("H:i:s")."\t".$sql."\n",FILE_APPEND);
		//echo $sql."\n\n";
		//debug_output($sql);
		$this->beginTransaction($transaction);
		$this->cur = @mysql_query($sql, $this->con);
		if(!$this->cur){
			/*You must get the error msg before you rollback trans!*/
			$error_msg=$this->getErrorMsg();
			$this->rollback($transaction);
			if(useEacclerator()){
				trigger_error("SQL Error:" 
							.new DBException(self::TYPE ,$error_msg.":".$sql, $this->getErrorNo())
							, 512);
			}else{
				//throw new DBException(self::TYPE ,$error_msg.":".$sql, $this->getErrorNo());
				trigger_error("SQL Error:" 
							.new DBException(self::TYPE ,$error_msg.":".$sql, $this->getErrorNo())
							, 512);
			}
		}else{
			$this->affected = mysql_affected_rows($this->con);
			$this->nextid = mysql_insert_id($this->con);
			$this->commit($transaction);
		}
		return $this->cur;
	}
	
	/**
	 * 业务开始
	 *
	 * @param boolean $transaction
	 */
	public function beginTransaction($transaction = true){
		if($transaction){
			mysql_query('BEGIN', $this->con);
		}
	}
	
	/**
	 * 业务提交
	 *
	 * @param boolean $transaction
	 */
	public function commit($transaction = true){
		if($transaction){
			mysql_query('COMMIT', $this->con);
		}
	}
	
	/**
	 * 业务回滚
	 *
	 * @param boolean $transaction
	 */
	public function rollback($transaction = true){
		if($transaction){
			mysql_query('ROLLBACK');
		}
	}

	/**
	 * 影响行数  
	 * 在使用update语句的情况下，MySQL不会将原值和新值一样的列更新，
	 * 所以函数返回值不一定就是WHERE条件所符合的记录数，只有真正被修改的记录数才会做为返回值
	 * becoder
	 * @return int
	 */
	public function affectCount(){
		/*$count = mysql_affected_rows($this->con);
		return $count;*/
		return $this->affected;
	}
	
	/**
	 * 结果集记录数
	 *
	 * @return int
	 */
	public function numrows(){
		if($this->cur){
			return mysql_num_rows($this->cur);
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
		$sql = preg_replace("/\n+/"," ", $sql);
		$regx = "|select\s+(.*)\s+from\s+(.*)|Ui";
		$sql = preg_replace($regx,
		'
select count(*) as rcount from \\2
			',
		$sql, 1);
		
		//$sql = "select count(*) as RCOUNT from($sql)__temp";
		
		$row = $this->fetchRow($sql, $params);
		$count = ($this->uppercase)?$row['RCOUNT']:$row["rcount"];
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
	 * 跳转到指定行
	 *
	 * @param int $pos - 从0开始
	 * 
	 * @return boolean
	 */
	public function seek($pos){
		$num = mysql_num_rows($this->cur);
		$flag = false;
		if($num>0){
			$flag = @mysql_data_seek($this->cur, $pos);
		}

		return $flag;
	}

	/**
	 * 返回单行结果
	 *
	 * @return string[]
	 */
	public function next(){
		$row = mysql_fetch_assoc($this->cur);
		if(!$row) return NULL;
		return array_change_key_case($row, ($this->uppercase>0)?CASE_UPPER:CASE_LOWER);
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
		$this->query($sql .' limit 1', $params);
		return $this->next();
	}
	
	public function fetchField($sql, $fieldName, $params=NULL){
		$row = $this->fetchRow($sql, $params);
		if(!$row){
			return false;
		}else{
			return $row[$this->uppercase?strtoupper($fieldName):strtolower($fieldName)];
		}
		
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
		return $this->nextid;
	}
	
	public function getInsertID(){
		return $this->nextid;
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
		return self::getPageSql($sql, $page, $pageSize);
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
		while($field = mysql_fetch_field($this->cur)){
			$ret[] = $field;
		}
		return $ret;
	}

	/**
	 * 获得错误代码
	 *
	 * @return string
	 */
	public function getErrorNo(){
		return mysql_errno($this->con);
	}

	/**
	 * 获得错误信息
	 *
	 * @return string
	 */
	public function getErrorMsg(){
		return mysql_error($this->con);
	}

	/**
	 * escape the string 
	 * 
	 * @return string
	 */
	public static function escapeString($str){
		if(gettype($str)!='string' && $str!=NULL) return $str;
		return "'".addslashes($str)."'";
	}
	
	/**
	 * 获得分页sql
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @return string
	 */
	public static function getPageSql($sql,$page, $pageSize){
		return $sql.' limit '.((($page>0)?($page-1):0)*$pageSize).','.($pageSize);
	}
}
/*try{
	$db = new Mysql('localhost', 3306, 'root', '993000', 'xguild');
	//$db->query("set names 'gbk'");
	$sql = 'show tables';
	$db->query($sql);
	//$db->next();
	var_dump($db->getColumns());
//	while($row = $db->next()){
//		var_dump($row);
//	}
	var_dump($db->fetchAll($sql));
}catch(DBException $e){
	echo $e;
}*/
?>
