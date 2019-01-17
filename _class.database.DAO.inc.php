<?php 
/**
 * @package database
 * @author Mars <tempzzz>
 */
require_once("_class.database.DBFactory.inc.php");

/**
 * Data Access Object
 * 为了兼容旧版数据库处理对象
 * 
 * 需要定义以下常量
 * <code>
 * 	define("DB_NAME", "MOYU");
 *	define("DB_USERNAME", "username");
 *	define("DB_PASSWORD", "password");
 *	define("DB_TYPE", "oracle");
 *	define("DB_HOST", "");
 *	define("DB_CHARSET", "");
 *	define("DB_PORT", 9000);
 *	define("DB_RETRYTIME", 1);
 *	define("DB_TRIES", 0);
 *	define("DB_DEBUG", false);
 * </code>
 */ 
class DAO
{	
	/**
	 * db handler
	 *
	 * @var IDatabase
	 */
	private $dbc = null;
	private $dbhost = '';
	private $dbname = '';
	private $dbuser = '';
	private $dbpasswd = '';
	private $charset = '';
	private $sqltype = '';
	private $port='';
	private $retryTime = 1;
	private $tries = 0;
	private $debug = false;
	private static $singleton = NULL;
	
	/**
	 * 构造方法
	 * 
	 * @param string $dbhost
	 * @param string $dbname
	 * @param string $dbuser
	 * @param string $dbpasswd
	 * @param string $charset
	 * @param string $sqltype
	 */ 
	function __construct($dbhost='', $dbname='', $dbuser='', $dbpasswd='', $charset='', $sqltype='', $port='',
						 $retryTime=1, $tries=0, $debug=false)
	{
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpasswd = $dbpasswd;
		$this->charset = $charset;
		$this->sqltype = strtolower($sqltype);
		$this->port = $port;
		$this->retryTime = $retryTime;
		$this->tries = $tries;
		$this->debug = $debug;
		if (!$this->sqltype) $this->sqltype="mysql";
		$this->useDb();
	}
	
	/**
	 * 
	 *
	 * @return DAO
	 */
	public static function singleton(){
		if(self::$singleton == NULL){
			self::$singleton = new DAO(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD, 
										DB_CHARSET, DB_TYPE, DB_PORT, DB_RETRYTIME, DB_TRIES, DB_DEBUG);
		}
		return self::$singleton;
	}
	
	/**
	 * 连接DB
	 * 
	 * @return mixed
	 */ 
	function useDb()
	{
		if (!$this->dbc)
		{
			$this->dbc = $this->connectDb();
			if (!$this->dbc) die("Can not connect to database.");
			if ($this->charset != "")
			{
				if ($this->sqltype == "mysql") @$this->dbc->query("set names ".$this->charset.";");
			}
		}
		return $this->dbc;
	}
	
	/**
	 * 连接DB
	 * 
	 * @return DB Connection
	 */ 
	function connectDb()
	{
		switch(strtoupper($this->sqltype))
		{
			case DBFactory::TYPE_DB_MYSQL :
			case DBFactory::TYPE_DB_ORACLE :
			case DBFactory::TYPE_DB_SQLRELAY :
				$dbc = DBFactory::getInstance(strtoupper($this->sqltype), $this->dbhost, $this->port, $this->dbname, $this->dbuser, 
											$this->dbpasswd, $this->retryTime, $this->tries, $this->debug);
				break;
			case "mssql":	
			default:
				die ("database not supported.");
		}
		if(!$dbc->getConnection()) return null;
		return $dbc;
	}
	
	/**
	 * 查询返回记录数组
	 * 
	 * @param string $sql
	 * @return array
	 */ 
//	function selectArray($sql)
	function selectArray($sql, $params = null) //add by Jeff Wang
	{
		if (!$sql) return null;
		$this->useDb();
		$rows = $this->dbc->fetchAll($sql, $params);
		if (count($rows) == 0) $rows = null;
		return $rows;                   
	}
	
	/**
	 * 选择一行数据
	 * 
	 * @param string $sql
	 * @return array
	 */ 
//	function selectRow($sql)
	function selectRow($sql, $params = null) //add by Jeff Wang
	{
		if (!$sql) return null;
		$this->useDb();
		if (!($row = $this->dbc->fetchRow($sql, $params)))
		{
			return null;
		}
		return $row;
	}
	
	/**
	 * 执行sql
	 * 
	 * @param string $sql
	 * @param array $params
	 * @return boolean
	 */ 
//	function execSql($sql)
	function execSql($sql, $params = null) //modify by Jeff Wang
	{
		if (!$sql) return null;
		$this->useDb();
		if (!($link = $this->dbc->query($sql,$params)))
		{
			return null;
		}
		else
		{
			return 1;
		}
	}

	/**
	 * 执行sql
	 * 
	 * @param string $sql
	 * @param array $params 必须包括："CURSOR"=>xxx游标名
	 * @return array
	 */ 
	function selectCursor($sql, $params = null) //add by Jeff Wang
	{
		if (!$sql) return null;
		$this->useDb();
		return $this->dbc->query($sql,$params);
	}

	/**
	 * 获取下一id的值
	 * 
	 * @param  string $sql
	 * @return  string $seq
	 */ 
	function nextId($seq = '')
	{
		$this->useDb();
		if(strtolower($this->sqltype) != "oracle")
			return $this->dbc->sql_nextid($seq);
		else
			return $this->dbc->getNextId($seq);
	}
	
	/**
	 * 执行存储过程mssql 2005 only
	 */ 
	function execSp($sql)
	{
		$this->useDb();
		$sql = "declare @r int; exec @r=$sql; select @r as result;";
		$result = $this->selectRow($sql);
		if ($result) $result = $result["result"];
		return $result;
	}
	
	/**
	 * 返回错误信息
	 * 
	 * @return string
	 */ 
	function Error()
	{
		return $this->dbc->getErrorNo().":".$this->dbc->getErrorMsg();
	}
	
	/**
	 * 返回指定页的记录
	 * 
	 * @param string $sql
	 * @param int $pageid
	 * @param  int $pagesize
	 * @return array
	 */ 
	function getPagedSql($sql, $pageid=1, $pagesize=0)
	{
		//for mssql 2005 only
		//sql语句中不能有row_number(), 并且必须包含order by
		if ($pagesize <= 0) return $sql;
		if ($pageid <= 0) $pageid = 1;
		if (strpos($sql, "row_number()")) return $sql;
		if (!strpos($sql, " order by ")) return $sql;
		$sql = preg_replace('/(.+?)from(.+) order by ([^;]+)[; ]*/i', "\$1, row_number() over (order by \$3) as fld_RowId from\$2", $sql);
		$sql = "select * from ($sql) a where fld_RowId between ".(($pageid - 1)* $pagesize + 1)." and ".($pageid * $pagesize).";";
		return $sql;
	}
	
	/**
	 * 生成顺序浏览的分页sql
	 * 记录数较少时更有效
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @return string
	 */
	function getOrderedPageSql($sql, $page, $pageSize){
		$replacement = "{CHILD_QUERY_REPLACEMENT}";
		$sql = formatSqlToLine($sql);
		$childQuery = getTopChildQuery($sql);
		if($childQuery!=""){
			$sql = str_replace($childQuery, $replacement, $sql);
		}
		$sql .= "{SQL_END}";
		//var_dump($sql);
		$regx = "/select\s+(.*)\s+from\s+(.*)\s+where\s+(.*)\s+order\s+by\s+(.*){SQL_END}/i";
		preg_match($regx, $sql, $out);
		//var_dump($out);
		$sql = '
select '.$out[1].' from
	(
		select rownum rnm, a.* from 
		(
			select * from '.$out[2].' 
			where '.$out[3].' 
			order by '.($out[4]==""?'rownum':$out[4]).'
		)a where rownum <= '.($pageSize*$page).' 
	)where rnm > '.($pageSize*($page-1)).' 
';
		
/*		$sql = preg_replace($regx,
		'
select \\1 from
	(
		select rownum rnm, a.* from 
		(
			select * from \\2 
			where \\3 
			order by \\4
		)a where rownum <= '.($pageSize*$page).' 
	)where rnm > '.($pageSize*($page-1)).' 
						', 
		$sql);
*/		
		if($childQuery!=""){
			$sql = str_replace($replacement, $childQuery, $sql);
		}
		return $sql;
	}
	
	/**
	 * 生成随机浏览的分页sql
	 * 记录越多越有效, 不能用在view或者有distinct, group by等条件下
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @return string
	 */
	function getRandomPageSql($sql, $page, $pageSize){
		$sql = formatSqlToLine($sql);
		$sql .= "{SQL_END}";
		$regx = "/select\s+(.*)\s+from\s+(.*)\s+where\s+(.*)\s+order\s+by\s+(.*){SQL_END}/Ui";
		preg_match($regx, $sql, $out);
		//var_dump($out);
		//var_dump($out);
		
		$sql = '
select '.$out[1].' from '.$out[2].'
where rowid in
	(
		select rid from 
		(
			select rownum rnm, rowid rid from
			(
				select rowid from '.$out[2].'
				where '.$out[3].'
				order by '.($out[4]==""?"rowid":$out[4]).'
			)where rownum <= '.($pageSize*$page).' 
		)where rnm > '.($pageSize*($page-1)).' 
	)
';
/*		
		$sql = preg_replace($regx,
		'
select \\1 from \\2
where rowid in
	(
		select rid from 
		(
			select rownum rnm, rowid rid from
			(
				select rowid from \\2
				where \\3
				order by \\4
			)where rownum <= '.($pageSize*$page).' 
		)where rnm > '.($pageSize*($page-1)).' 
	)
						', 
		$sql);
*/		
		return $sql;
	}
	
	function getMysqlPageSql($sql, $page, $pageSize){
		return $sql.' limit '.(($page-1)*$pageSize).','.($pageSize);
	}
	
	/**
	 * 返回有关分页的参数
	 *
	 * @param string $sql
	 * @param int $page
	 * @param int $pageSize
	 * @return array
	 */
	function formatPageVar($sql, $page, $pageSize, $params=NULL){
		$sql = formatSqlToLine($sql);
		$regx = "|select\s+(.*)\s+from\s+(.*)|Ui";
		$sql = preg_replace($regx, 
		'
select count(*) as rcount from \\2
		',
		$sql);
		$row = $this->selectRow($sql, $params);
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

	/**
	 * 分析重新生成查询条件
	 * @param string $filters
	 * @return string
	 */ 
	function parseFilters($filters)
	{
		if (is_string($filters)) return $filters;
		if (!is_array($filters)) return null;
		$where = array();
		if ($filters)
		{
			foreach($filters as $key=>$value)
			{
				if(preg_match('/^\\s*fld_[a-zA-Z0-9]+\\s*[=><]{1,2}.+$/', $value)) $where[] = $value;
				else $where[] = ((strpos($key, "fld_") === 0) ? "" : "fld_").str_replace(" ", "", $key)."=".((preg_match('/\\s*\'.*\'\\s*/', $value)) ? $value : "'$value'");
			}
		}
		if (!$where) return null;
		$where = implode(" and ", $where);
		return $where;
	}
	
	/**
	 * 处理sql中的'
	 *
	 * @param string $text
	 * @return string
	 */
	function db_escape_string($text)
	{
		
		return $this->dbc->escapeString($text);
	}
	//@todo
	function cache_selectArray($sql, $params){
		
	}
	

}

function mssql_escape_string($text)
{
	return str_replace("'", "''", $text);
}

function oracle_escape_string($text)
{
	//return str_replace("'", "''", str_replace('----&','\&',$text));
	return str_replace("'", "''", $text);
}

function formatSqlToLine($sql){
	//消除多余空格
	$sql = preg_replace("/\n+/"," ", $sql);
	return $sql;
}

function getTopChildQuery($sql){
	//var_dump($sql);
	$regx = "|select .* from (\([^\)]*\))|Ui";
	preg_match_all($regx, $sql, $output);
	$match = isset($output[1][0])?$output[1][0]:"";
	if($match=='') return '';
	//var_dump($match);
	preg_match_all("/\(/", $match, $output);
	$n = count($output[0]);
	//var_dump($n);
	$start = strpos($sql, $match);
	$pos = 0;
	for($i=0; $i<$n; $i++){
		$offset = ($pos==0)?$start:$pos+1;
		$pos = strpos($sql, ")", $offset);
		$temp = substr($sql, $start, $pos-$start+1);
		//var_dump($temp);
		preg_match_all("/\(/", $temp, $output);
		$n = count($output[0]);
	}
	if($pos-$start==0){
		return "";
	}else{
		return substr($sql, $start, $pos-$start+1);
	}
}
/*
$db = new DAO('localhost', 'xguild', 'root', '993000', 'gbk', 'mysql', '3306');
var_dump($db->selectArray('show tables'));
*/
//$sql = '
//select count(*) as rcount from 
//		VIEW_TAG_RESOURCE where FLD_TAG_NAME=&tagName and FLD_RESOURCE_TYPE=&resourceType order by FLD_RESOURCE_ORDER 
//';
//
//$db = new DAO('60.28.197.59', 'MOYU', 'tag_moyu', 'tag_moyu602819752', 'gbk', 'sqlrelay', '9000',1, 0, true);
//$sql = 'begin SP_ADD(:n1, :n2, :out_int_n3);  end;';
//$n = 1000;
//$db->execSql($sql, array('n1'=>1, 'n2'=>2, 'out_int_n3'=>&$n));
//echo $n;
//var_dump($db->selectRow($sql, array('tagName'=>'拉萨', 'resourceType'=>800)));
//var_dump($db->selectArray('select * from tab'));
//$db->execSql('insert into tab_test2 values(&id, &name)', array('id'=>'98', 'name'=>'大西瓜'));
?>
