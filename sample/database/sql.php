<?php
define('MAIN_DB_TYPE', 'PDO' );
define('MAIN_DB_HOST', '10.1.1.149');
define('MAIN_DB_PORT', '3306');
define('MAIN_DB_USER', 'applanet_user');
define('MAIN_DB_PASSWORD', 'applanet_user2111579711B()^');
define('MAIN_DB_CHARSET', 'utf8');
define('MAIN_DB_NAME', 'mzw');


require_once 'db.inc.php';

$db = SampleDB::getDB();

$params = array('uid'=>8832975,'username'=>'wuliaodezhuce');
$sql = "select * from mzw_users where fld_UId = &uid and fld_UserName=&username";

$row = $db->fetchRow($sql,$params);
$rows = $db->fetchAll($sql,$params);

var_dump($row);
var_dump($rows);
