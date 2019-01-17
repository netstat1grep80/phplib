<?php

class SoapClientEx extends SoapClient {
	var $cookie_enable = true;
	var $parent;
	

	
	public function __doRequest($request='', $location='', $action='', $version='', $one_way = 0){
		$args = func_get_args();
		$ret = call_user_func_array(array($this, 'parent::__doRequest'), $args);
		$this->handle_cookies();
		return $ret;
	}
	
	private function handle_cookies(){
		$headers = $this->__getLastResponseHeaders();
		$cookie_sign = 'Set-Cookie: ';
		if(!empty($headers)){
			$lines = explode("\n", $headers);
			foreach($lines as $line){
				$pos = strpos($line, $cookie_sign);
				if($pos === 0){
					$pairs = explode(';', substr($line, strlen($cookie_sign)));
					$cookie = array();
					list($name, $value) = explode("=", $pairs[0]);
					$cookie['name'] = trim($name);
					$cookie['value'] = urldecode(trim($value));
					for($i=1; $i<count($pairs); $i++){
						list($name, $value) = @explode('=', $pairs[$i]);
						$cookie[trim($name)] = trim($value);
					}
					$this->setcookie($cookie);
				}
			}
		}
	}	
	
	private function setcookie($cookie){
		if(isset($cookie['expires']) and !empty($cookie['expires'])){
			$cookie['expires'] = gmtdateParse($cookie['expires']);
		}
		setcookie($cookie['name'], $cookie['value'], isset($cookie['expires'])?$cookie['expires']:null, '/', isset($cookie['domain'])?$cookie['domain']:null);
	}
}