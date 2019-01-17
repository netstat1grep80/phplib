<?
/**
 * @package database
 * @author Mars <tempzzz>
 */
require_once('_interface.IDatabase.inc.php');
require_once('_class.database.DBException.inc.php');
require_once('_class.database.AbstractDB.inc.php');
require_once('_func.text.string.inc.php');

/**
 * Sqlrelay Handler
 */
class Sqlrelay extends AbstractDB {
	private $con;
	private $cur;
	private $curpos = 0;
	private $output = array();

	private $host;
	private $port;
	private $username;
	private $password;
	private $retryTime;
	private $tries;
	private $debug;
	
	const TYPE = 'Sqlrelay';
	
	/**
	 * ���췽��
	 * 
	 * @param string $host - ����ַ
	 * @param string $port - ����˿�
	 * @param string $username - �û���
	 * @param string $password - ����
	 * @param int $retryTime - ��������l�ӵ�ʱ����
	 * @param int $tries - ��������l�Ӵ���
	 * @param boolean $debug - �Ƿ��debug��Ϣ
	 */
	public function __construct($host, $port, $username, $password, $retryTime=1, $tries=0, $debug=false){
		$this->host			= $host;
		$this->port 		= $port;
		$this->username 	= $username;
		$this->password 	= $password;
		$this->retryTime 	= $retryTime;
		$this->tries 		= $tries;
		$this->debug 		= $debug;

		$this->connect();
	}
	
	/**
	 * �����
	 *
	 */
	public function __destruct(){
		$this->freeResult();
		$this->close();
	}

	/**
	 * l����ݿ�
	 * 
	 * @return resource_id
	 */
	public function connect(){
		$this->con = sqlrcon_alloc(
			$this->host,
			$this->port,
			"",
			$this->username,
			$this->password,
			$this->retryTime,
			$this->tries
		);
		if(!$this->con){
			throw new DBException(self::TYPE, $this->getErrorMsg(), $this->getErrorNo());
		}
		
		$this->cur = sqlrcur_alloc($this->con);
		if(!$this->cur){
			throw new DBException(self::TYPE, $this->getErrorMsg(), $this->getErrorNo());
		}
			
		$this->query("alter session set nls_date_format = 'yyyy-mm-dd hh24:mi:ss'");

		if($this->debug){
			sqlrcon_debugOn($this->con);
		}

		return $this->con;
	}
	
	public function getConnection(){
		return $this->con;
	}

	/**
	 * �ر���ݿ�
	 *
	 * @return boolean
	 */
	public function close(){
		$this->commit();
		return sqlrcon_free($this->con);
	}

	/**
	 * �ͷŲ�ѯ���
	 *
	 * @return boolean
	 */
	public function freeResult(){
		if($this->cur){
			return @sqlrcur_free($this->cur);
		}else{
			return true;
		}
	}

	/**
	 * �󶨲���
	 * 
	 * @access private
	 *
	 * @param string $key
	 * @param string $value
	 * 
	 * @return string
	 */
	private function bindParam($name, &$value){
		$paramname = ($name[0]==":")?substr($name, 1):$name;
		$regex = '/out_(int|str)_[\w\d]+/i';
		preg_match($regex, $paramname, $match);
		if($match!=NULL && count($match)==2){ // is output bind
			//array_push($output, $name);
			$this->output[$name] = $match[1];
			if($match[1] == 'int'){
				sqlrcur_defineOutputBindInteger($this->cur, $paramname);
			}else{
				sqlrcur_defineOutputBindString($this->cur,$paramname,10);
			}
		}else{
			sqlrcur_inputBind($this->cur, $paramname, $value);
		}
		return true;
	}

	/**
	 * �󶨲���
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
			$this->bindParam($key, $value);
		}
		return true;
	}

	/**
	 * ִ��sql
	 *
	 * @param string $sql
	 * @param string[] $params
	 * @param boolean $transaction
	 * 
	 * @return resource_id
	 * 
	 * @throws DBException
	 */
	public function query($sql, $params=NULL, $transaction=false){
		$this->curpos= 0;
		$this->output = array();
		$sql = formatSQL(removeBlank($sql));
		/*Tells the server not to send any column info (names, types, sizes). 
			If you don't need that info, you should call 'sqlrcur_dontGetColumnInfo' to improve performance*/
		sqlrcur_dontGetColumnInfo($this->cur);
		sqlrcur_prepareQuery($this->cur, $sql);
	
		$this->bindParams($params);
		
		$this->beginTransaction($transaction);
		$flag = sqlrcur_executeQuery($this->cur);
		
		if(count($this->output)>0){
			foreach($this->output AS $name=>$type){
				$paramname = ($name[0]==":")?substr($name, 1):$name;
				if($type=='int'){
					$value = sqlrcur_getOutputBindInteger($this->cur, $paramname);
				}else{
					$value = sqlrcur_getOutputBindString($this->cur, $paramname);
				}
				$params[$name]=$value;
			}
		}
		sqlrcur_clearBinds($this->cur);
		
		if(!$flag){
			$this->rollback($transaction);
			throw new DBException(self::TYPE ,$this->getErrorMsg(), $this->getErrorNo());
		}else{
			$this->commit($transaction);
		}
		
		return $this->cur;
	}
	
	/**
	 * ҵ��ʼ
	 *
	 * @param boolean $transaction
	 */
	public function beginTransaction($transaction = true){
		// not implemented
	}
	
	/**
	 * ҵ���ύ
	 *
	 * @param boolean $transaction
	 */
	public function commit($transaction = true){
		if($transaction){
			sqlrcon_commit($this->con);
		}
	}
	
	/**
	 * ҵ��ع�
	 *
	 * @param boolean $transaction
	 */
	public function rollback($transaction = true){
		if($transaction){
			sqlrcon_rollback($this->cur);
		}
	}

	/**
	 * Ӱ������
	 *
	 * @return int
	 */
	public function affectCount(){
		if($this->cur){
			return count(sqlrcur_affectedRows($query_id));
		}
		return NULL;
	}
	
	/**
	 * ����¼��
	 *
	 * @return int
	 */
	public function numrows(){
		if($this->cur){
			return count(sqlrcur_rowCount($this->cur));
		}
		return NULL;
	}

	/**
	 * ��ѯpage��ز���
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

		return array(
			"COUNT"=>$count,
			"PAGECOUNT"=>$pageCount,
			"PAGE"=>$page,
			"PAGESIZE"=>$pageSize
		);
	}

	/**
	 * ��ת��ָ����
	 *
	 * @param int $pos - ��0��ʼ
	 * 
	 * @return boolean
	 */
	public function seek($pos){
		$this->curpos = $pos;
		return true;
	}

	/**
	 * ���ص��н��
	 *
	 * @return string[]
	 */
	public function next(){
		if($this->cur){
			return sqlrcur_getRowAssoc($this->cur, $this->curpos++);
		}else{
			return NULL;
		}
	}

	/**
	 * ���ص��н��
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
	 * ���ز�ѯ�����н��
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
	 * ��ɷ�ҳSQL
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
	 * �õ�8λ��Ϣ
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
		if(!$this->cur){
			return NULL;
		}
		$ret = array();
		
		for($i=0; $i<count(sqlrcur_getColumnNames($this->cur)); $i++){
			$obj = new stdClass();
			$obj->name = sqlrcur_getColumnName($this->cur, $i);
			$obj->type = sqlrcur_getColumnType($this->cur, $i);
			$obj->size = sqlrcur_getColumnLength($this->cur, $i);
			$obj->isnull = sqlrcur_getColumnIsNullable($this->cur, $i);
			$obj->ispk = sqlrcur_getColumnIsPrimaryKey($this->cur, $i);
			$ret[] = $obj;
		}
		return $ret;
	}

	/**
	 * ��ô������
	 *
	 * @return string
	 */
	public function getErrorNo(){
		$err = -1;
		return $err["code"];
	}

	/**
	 * ��ô�����Ϣ
	 *
	 * @return string
	 */
	public function getErrorMsg(){
		$err = sqlrcur_errorMessage($this->cur);
		return $err['message'];
	}

	/**
	 * escape the string 
	 * 
	 * @return string
	 */
	public static function escapeString($str){
		if(gettype($str)!='string') return $str;
		return "'".str_replace("'", "''", $str)."'";
	}
}

?>