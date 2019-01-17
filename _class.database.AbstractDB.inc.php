<?php
/**
 * @package database
 *
 */
require_once('_interface.IDatabase.inc.php');
require_once('_class.database.DBException.inc.php');
require_once('_func.util.common.inc.php');

abstract class AbstractDB implements  IDatabase {
	const QUERY_ERROR = 0x0;
	public $uppercase=1;
	
	public function insert($table, $params){
		$sql = 'insert into `'. $table .'` (';
		$fields = array_keys($params);
		$sql .= implode(',', $fields);
		$sql .= ')values(';
		iterator($fields, '__createField');
		$sql .= implode(',', $fields);
		$sql .= ')';
		
		$flag = @$this->query($sql, $params);
		return ($flag)?($this->nextid==0?( (isset($params['id']) && $params['id']) ? $params['id']:$params['acc_id']):$this->nextid):false;
	//	return $flag?true:false;
	}
	public function update($table, $params, $condition_params=array(), $condition='id=&id'){
		$sql = 'update `'.$table.'` set ';
		$fields = array_keys($params);
		iterator($fields, '__createPairs');
		$sql .= implode(',', $fields);
		$sql .= ' where '.$condition;
		$params = array_merge($params, $condition_params);
		$flag = @$this->query($sql, $params);
		//echo $flag;
		//echo $this->affected;
		return ($flag)?$this->affected:false; 
	}
	public function updateSingle($table, $id, $fieldName, $value){
		$sql = 'update `'.$table.'` set '. $fieldName .'=&param where id=&id';
		$params = array('param'=>$value, 'id'=>$id);
		$flag = @$this->query($sql, $params);
		return ($flag)?$this->affected:false;
	}
	public function selectPage($sql, $params, $page, $pageSize){
		try{
			$info = $this->queryPageVar($sql, $page, $pageSize, $params);
			$rows['INFO'] = $info;
			$sql = $this->createPageSQL($sql, $info['PAGE'], $info['PAGESIZE']);
			$rows['ROWS'] = $this->fetchAll($sql, $params);
			return $rows;
		}catch (DBException $e){
			return false;
		}
	}	
	public function remove($table, $id){
		$sql = "delete from `$table` where id=&id";
		$params = array('id'=>$id);
		$flag = $this->query($sql, $params);
		return ($flag)?$this->affected:false;
	}	
	public function delete($table, $params){
		$sql = "delete from `$table` where 1=1 ";
		foreach($params AS $key=>$value){
			$sql .= "and $key='$value'";
		}
		$flag = $this->query($sql, $params);
		return ($flag)?$this->affected:false;
	}
	
	public function forgeRemove($table, $id){
		$params = array(
			'is_delete'=>'Y'
		);
		$cparams = array(
			'id'=>$id
		);
		return $this->update($table, $params, $cparams);
	}
	
	public function fetchField($sql, $fieldName, $params=NULL){
		return false;
	}
	
	public function save($table, $params){
		$sql = 'insert into `'. $table .'` (';
		$fields = array_keys($params);
		$sql .= implode(',', $fields);
		$sql .= ')values(';
		iterator($fields, '__createField');
		$sql .= implode(',', $fields);
		$sql .= ')';
		
		$sql .= 'ON DUPLICATE KEY UPDATE ';
		/*$fields = array_keys($params);
		iterator($fields, '__createPairs');
		$sql .= implode(',', $fields);*/

        /*
         * 此处说明开始
         * 此方法生成的sql格式：insert into xxxx (索引1,索引2,...,索引N)values(:value1,:value2,...,valueN) ON DUPLICATE KEY UPDATE 索引1=:value1,索引2=:value2,...,索引N=:valueN
         * 正常sql拼接出来的结构式 ：   索引：value = 1： 1 ，但是这里是1:2 ，所以单独处理一下以下的sql语句
         */


        $_params = array();
        foreach($params as $k => $v){
            $_params['__'.$k] = $v;
        }


        $_fields = array_keys($_params);
        iterator($_fields, '__createPairs');
        $_sql = implode(',', $_fields);
        $_sql = str_replace('&__',':',$_sql);
        $_sql = str_replace('__','',$_sql);
        $_sql = str_replace(':','&__',$_sql);
        $sql .= $_sql;
        $paramsAll = array_merge($params,$_params);
        /*
         *说明结束
         */
//        echo $sql."\n";
//        print_r($paramsAll);
		$flag = @$this->query($sql, $paramsAll);
		return ($flag)?($this->nextid==0?(isset($params['id']) && $params['id'] ?$params['id']:$params['acc_id']):$this->nextid):false;

	}
}

function &__createPairs(&$field){
	$field = "$field=&$field";
	return $field;
}

function &__createField(&$field){
	$field = '&'.$field;
	return $field;
}
?>