<?php
/**
 * @package http
 */
/**
 * Cookie类
 *
 */
class Cookie{
	private $encryptFunction = NULL;
	private $decryptFunction = NULL;
	
	public function __construct(){
		
	}
	
	public function getRawCookie(){
		return $_COOKIE;
	}
	
	public function setCookie($name, $value=NULL, $expire=3600, $path=NULL, $domain=NULL){
		if($value && $this->encryptFunction){
			$value = $this->encryptFunction($value);
		}
		setcookie($name, $value, $expire+time(), $path, $domain);
	}
	
	/**
	 * 设置加密回调方法
	 *
	 * @param callback_func $funcName
	 */
	public function setEncryptHandler($funcName){
		$this->encryptFunction = $func;
	}
	
	/**
	 * 设置解密回调方法
	 *
	 * @param callback_func $funcName
	 */
	public function setDecryptHandler($funcName){
		$this->decryptFunction = $func;
	}
	
	public function get($cookiename){
		if($this->decryptFunction){
			return $this->decryptFunction($_COOKIE[$cookiename]);
		}else{
			return $_COOKIE[$cookiename];
		}
	}
}
?>