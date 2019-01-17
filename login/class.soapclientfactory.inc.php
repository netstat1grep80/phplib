<?php
defined('SOAP_HOST') or define('SOAP_HOST', 'http://account.muzhiwan.com');

class SoapClientFactory{
	/**
	 *
	 * @var SoapClientEx
	 */
	public static $instance;
	
	/**
	 *
	 * @return SoapClientEx
	 */
	static function getInstance(){
		if(empty(self::$instance)){
			self::$instance = $client = new
			    SoapClientEx(
			       null,
			       array(
			        'uri'=> 'http://account.muzhiwan.com',
			        //'location' => 'http://account.178.com/service.php?HTTP_USER_AGENT='.urlencode($_SERVER['HTTP_USER_AGENT']),
//			        'location' => 'http://account3.test.178.com/service.php?HTTP_USER_AGENT='.urlencode($_SERVER['HTTP_USER_AGENT']).'&ip='.$_SERVER['REMOTE_ADDR'],
			        'location' => SOAP_HOST.'/service.php?HTTP_USER_AGENT='.urlencode($_SERVER['HTTP_USER_AGENT']).'&ip='.$_SERVER['REMOTE_ADDR'],
			       	'trace'=>1
			       )
			);
		}
		return self::$instance;
	}
}
