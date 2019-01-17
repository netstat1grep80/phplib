<?php

include_once("_class.database.DBFactory.inc.php");

class SampleDB{
    /**
     * singleton db instance
     *
     * @var IDatabase
     */
    public static $dbs;

    /**
     * get db instance
     *
     * @return IDatabase
     */
    public static function getDB($dbType="main"){
        if(!is_array(self::$dbs)) self::$dbs = array();
        if(array_key_exists($dbType, self::$dbs)){
            return self::$dbs[$dbType];
        }

        self::$dbs[$dbType] = DBFactory::createInstance(MAIN_DB_TYPE,
            MAIN_DB_HOST, MAIN_DB_PORT, MAIN_DB_NAME, MAIN_DB_USER, MAIN_DB_PASSWORD,
            MAIN_DB_CHARSET, 0, 0, 0);

        self::$dbs[$dbType]->uppercase = 0;
        return self::$dbs[$dbType];
    }
}