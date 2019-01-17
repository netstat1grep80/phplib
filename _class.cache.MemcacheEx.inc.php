<?php

class MemcacheEx extends Memcache{
	var $failure = false;
	public function __destruct(){
		if(!$this->failure)
			$this->close();
	}
}