<?php
/**
 * JasonZ Framework
 *
 * @category   JasonZ
 * @package    JasonZ
 * @copyright  1565166@qq.com
 * @version    $Id$
 */


class ZZZ_Pdo extends AbstractDB //为了防止和其他框架pdo类名冲突
{
    public $con;
    public $cur;
    private $username;
    private $password;
    private $dbName;
    private $charset;
    private $writeLog;
    public $debug;

    public $affected;
    public $nextid;

    public $sql;
    static $query_times;
    const TYPE = 'Pdo';


    /**
     * 构造方法
     *
     */
    public function __construct($host, $port, $username, $password, $dbName, $charset='utf8',$debug=false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $dbName;
        $this->charset = $charset;

        $this->debug 	= $debug;
        $this->writeLog = false;
    }

    public function initialize()
    {
        $this->connect();
        $this->con->exec('set names '.$this->charset);
    }

    public function setWriteLog($b){
        $this->writeLog = $b;
    }
    /**
     * 析构方法
     *
     */
    public function __destruct()
    {
        if($this->con)
            $this->close();
    }

    /**
     * 连接数据库
     *
     * @return resource_id
     */
    public function connect()
    {
        try {
            $this->con = new PDO(
                'mysql:dbname='.$this->dbName.';host='.$this->host.';charset='.$this->charset,
                $this->username,
                $this->password
            );
        }catch (PDOException $e){
            echo 'Connection failed: ' . $e->getMessage();
            exit;
//            throw new JasonZ_Exception(mysqli_connect_errno(), JasonZ_Exception::CODE_DB );
        }
        $this->con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $this->con;
    }

    public function getConnection()
    {
        if($this->con)
            return $this->con;
        else
            return null;
    }

    /**
     * 选择数据库
     *
     * @param string $dbName
     * @return boolean
     *
     * @throws DBException
     */
    public function selectDB($dbName=NULL)
    {
        if(!$this->con) $this->initialize();
        $dbName = ($dbName==NULL)?$this->dbName:$dbName;
        $flag = mysql_select_db($dbName, $this->con);
        if(!$flag)
        {
            throw new JasonZ_Exception($this->getErrorNo().":".$this->getErrorMsg(), JasonZ_Exception::CODE_DB );
        }
        return $flag;
    }

    /**
     * 关闭数据库
     *
     * @return boolean
     */
    public function close()
    {
//		return mysql_close($this->con);
    }

    /**
     * 释放查询结果
     *
     * @return boolean
     */
    public function freeResult()
    {
        if($this->cur){
            return mysql_freeresult($this->cur);
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
     * @param string[] $params
     *
     * @return bool
     */

    public function bindParams($sql,$params){
//        $presql = str_replace('&',':',$sql);
        $presql = preg_replace('/&(\w+)/',':$1',$sql);//因为要考虑到msyql的非运算符的问题
//        echo $presql."<br />";
        try{
            $this->cur = $this->con->prepare($presql);
        }catch(PDOException $e){
            var_dump($e);
            return false;
        }
        if(!$this->cur){
            if($this->debug){
                echo $sql."\n";
                var_dump($params);
            }
            echo 'error .';exit;
//            throw new JasonZ_Exception('sql error.', JasonZ_Exception::CODE_DB );
        }
        $regex = '|\&[a-z\_][a-z\_\d]*|i';
        preg_match_all($regex, $sql, $match);
        $exp = array();
        for($i=0; $i<count($match[0]); $i++){
            $bind = false;
            foreach($params AS $key => $value){
                $v = '&'.$key;
                if($v==$match[0][$i]){
                    if($value===NULL){
                        $exp[$key] = '';
                    }else{
                        $exp[$key] =  $value;
                    }
                    break;
                }
            }
        }
//        echo $presql."<br />";
        if($params != NULL){
            foreach($exp as $key => $value){
                if(is_int($value))
                    $paramType = PDO::PARAM_INT;
                elseif(is_bool($value))
                    $paramType = PDO::PARAM_BOOL;
                elseif(is_null($value))
                    $paramType = PDO::PARAM_NULL;
                elseif(is_string($value))
                    $paramType = PDO::PARAM_STR;
                else
                    $paramType = PDO::PARAM_STR;

//echo ":$key,$value,$paramType<br />";
                $this->cur->bindValue(":$key",$value,$paramType);
            }
        }

        return true;
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
    public function query($sql, $params=NULL, $transaction=false)
    {

        self::$query_times++;
        if(!$this->con) $this->initialize();

        $this->nextid = 0;
        $this->affected = 0;

        if($this->bindParams($sql, $params)){
            if($this->writeLog){
//		@file_put_contents('/var/sdkpaylogs/sdk_sql_'.date("Y-m-d").".log", date("H:i:s").':'.getClientIP().':'.$_SERVER['REQUEST_URI'].':'.$sql."\n", FILE_APPEND);
            }
            $query = $this->cur->execute();

            if(!$query){
                echo "\nsql error = ".$sql."<br />\n";
                print_r($this->getErrorMsg())."<br />\n";
//                throw new JasonZ_Exception($this->getErrorNo().":".$this->getErrorMsg()."\n".$sql, JasonZ_Exception::CODE_DB );
                return false;
            }else{
                $this->affected = $this->cur->rowCount();
                $this->nextid = $this->con->lastInsertId();
                return $query;
            }

        }else{
            return false;
        }
    }


    /**
     * 影响行数
     * 在使用update语句的情况下，MySQL不会将原值和新值一样的列更新，
     * 所以函数返回值不一定就是WHERE条件所符合的记录数，只有真正被修改的记录数才会做为返回值
     * @return int
     */
    public function affectCount(){
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
    public function queryPageVariables($sql, $page, $pageSize, $params=NULL){
        $sql = preg_replace("/\n+/"," ", $sql);
        $regx = "|select\s+(.*)\s+from\s+(.*)|Ui";
        $sql = preg_replace($regx,
            '
    select count(*) as rcount from \\2
                ',
            $sql, 1);

        $row = $this->fetchAll($sql, $params);
        $row = $row[0];
        $count = $row["rcount"];
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
     * escape the string
     *
     * @return string
     */
    public static function escapeString($str){
        if(gettype($str)!='string' && $str!=NULL) return $str;
        return "'".addslashes($str)."'";
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
        if($num>0)
        {
            $flag = mysql_data_seek($this->cur, $pos);
        }

        return $flag;
    }

    /**
     * 返回单行结果
     *
     * @return string[]
     */
    public function next()
    {
        $row = $this->cur->fetch(PDO::FETCH_ASSOC);
        if(!$row) return NULL;
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
    public function fetchRow($sql, $params=NULL, $single=false)
    {
        $this->query($sql.($single?'':' limit 1'), $params);
        return $this->next();
    }

    public function fetchField($sql, $fieldName, $params=NULL)
    {
        $row = $this->fetchRow($sql, $params);
        if(!$row)
        {
            return false;
        }
        else
        {
            return $row[$fieldName];
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
    public function fetchAll($sql, $params=NULL)
    {
        $this->query($sql,$params);
        return $this->cur->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNextID()
    {
        return $this->nextid;
    }

    public function getInsertID()
    {
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
    public function createPageSQL($sql, $page, $pageSize)
    {
        return self::getPageSql($sql, $page, $pageSize);
    }

    /**
     * 获得错误代码
     *
     * @return string
     */
    public function getErrorNo()
    {
        return $this->cur->errorCode();
    }

    /**
     * 获得错误信息
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->cur->errorInfo();
    }

    /**
     * 获得分页sql
     *
     * @param string $sql
     * @param int $page
     * @param int $pageSize
     * @return string
     */
    public static function getPageSql($sql,$page, $pageSize)
    {
        return $sql.' limit '.((($page>0)?($page-1):0)*$pageSize).','.($pageSize);
    }

    public function fetchPage($sql, $page, $pageSize, $params=null)
    {
        $info = $this->queryPageVariables($sql, $page, $pageSize, $params);
        $rows['INFO'] = $info;
        $sql = $this->createPageSQL($sql, $info['PAGE'], $info['PAGESIZE']);
        $rows['ROWS'] = $this->fetchAll($sql, $params);
        return $rows;
    }
}