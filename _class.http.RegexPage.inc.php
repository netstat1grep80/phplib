<?php
/**
 * @package http
 */
require_once('_func.http.page.inc.php');
require_once('_func.text.string.inc.php');

class RegexPage{
	/**
	 * every block piece length should not over 64k
	 *
	 * @var unknown_type
	 */
	public $blocks;
	public $regexes;
	public $loopregexes;
	public $ids;
	public $lids;
	public $matches;
	public $loopmatches;
	public $loops;
	
	public $template;
	
	public function __construct($template){
		$this->blocks = array();
		$this->matches = array();
		$this->ids = array();
		$this->lids = array();
		$this->regexes = array();
		$this->loopregexes = array();
		
		$this->template = $template;
		
		$this->parseBlocks();
		for($i=0; $i<count($this->blocks); $i++){
			$this->ids = array_merge($this->ids, $this->parseIds($this->blocks[$i]));
		}
		
		$this->parseLoops();
		foreach($this->loops AS $key=>$loop){
			$this->lids[$key] = $this->parseIds($loop);
		}
		
		$this->parseRegexes();
	}
	
	public function parseBlocks(){
		$blocktagStart = '<regexblock>';
		$blocktagEnd = '</regexblock>';
		
		$pos = 0;
		while(($pos = strpos($this->template, $blocktagStart, $pos)) !== false){
			$template = substr($this->template, $pos);
			$block = substrByString($template, $blocktagStart, $blocktagEnd);
			$this->blocks[] = $block;
			$pos = $pos + strlen($block);
		}
		
	}
	
	public function parseLoops(){
		$blocktagStartRegex = '|<regexloop id=[\'\"]([^>]*)[\'\"]>|';
		$blocktagStart ='<regexloop>';
		$blocktagEnd = '</regexloop>';
		
		$pos = 0;
		$flag = preg_match_all($blocktagStartRegex, $this->template, $match);
		if($flag){
			for ($i=0; $i< count($match[0]); $i++){
				$blocktagStart = $match[0][$i];
				$pos = strpos($this->template, $blocktagStart, $pos);
				$template = substr($this->template, $pos);
				$block = substrByString($template, $blocktagStart, $blocktagEnd);
				$this->loops[$match[1][$i]] = $block;
				$pos = $pos + strlen($block);
			}
		}
	}
	
	public function parseIds($txt=NULL){
		$notset = false;
		if($txt==NULL) {
			$txt = $this->template;
			$notset = true;
		}
		$regex = '|<regex id=[\'\"]([^>]*)[\"\']>|';
		preg_match_all($regex, $txt, $out);
		if($notset){
			$this->ids = &$out[1];	
			unset($out);
			return $this->ids;
		}else {
			return $out[1];
		}
	}
	
	public function parseRegexes(){
		foreach ($this->blocks AS $block){
			$this->regexes[] = self::regexEscape($block);
		}
		
		foreach ($this->loops AS $key=>$block){
			$this->loopregexes[$key] = self::regexEscape($block);
		}
	}
	
	public function execute($txt){
		$this->matches = array();
		$this->loopmatches = array();
		
		$i = 0;
		foreach($this->regexes AS $regex){
			$regex = convertspace('|'.$regex.'|');
			$flag = preg_match($regex, ($txt), $match);
			if($flag){
				array_shift($match);
				$this->matches = array_merge($this->matches, $match);
			}else{
				$count = count($this->parseIds($this->blocks[$i]));
				for($k=0; $k<$count; $k++){
					$this->matches[] = NULL;
				}
			}
			$i++;
		}
		
		foreach($this->loopregexes AS $key=>$regex){
			$regex = convertspace('|'.$regex.'|');
			$flag = preg_match_all($regex, ($txt), $match);
			$this->loopmatches[$key] = array();
			if($flag){
				array_shift($match);
				for($k=0; $k<count($match[0]);$k++){
					$i = 0;
					foreach($this->lids[$key] AS $name){
						$this->loopmatches[$key][$k][$name] = $match[$i][$k]; 
						$i++;
					}
				}
			}
		}
		
		$ret = array();
		for($i=0; $i<count($this->ids); $i++){
			$ret[$this->ids[$i]] = $this->matches[$i];
		}
		$ret = array_merge($ret, $this->loopmatches);
		
		return $ret;
	}
	
	public static function regexEscape($str){
		$ops = '\'\"^$.[]|()?*+{}:-&';
		$str = addcslashes($str, $ops);
		//echo $str;
		$regex = "|<regex [^>]*>[^>]*</regex>|";
		preg_match_all($regex, $str, $match);
		foreach($match[0] AS $m){
			$r = stripcslashes(self::stripRegexTag($m));
			$str = str_replace($m, $r, $str);
		}
		//var_dump($match);
		
		return $str;
	}
	
	public static function stripRegexTag($str){
		$str = preg_replace('|<regex [^\>]*>|', '', $str);
		$str = preg_replace('|</regex>|', '', $str);
		//echo $str;
		//$str = htmlstrip($str);
		return $str;
	}
}
?>