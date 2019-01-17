<?php
/**
 * @package http
 */
require_once('_func.util.common.inc.php');
require_once('_func.http.page.inc.php');
require_once('_func.text.string.inc.php');

class RegexPage2{
	/**
	 * 单次匹配块
	 * <regexblock>...</regexblock>
	 *
	 */
	const BLOCK_START_TAG = "<regexblock>";
	const BLOCK_END_TAG = "</regexblock>";
	
	/**
	 * 循环匹配块
	 * <regexloop id='{id}'>...</regexloop>
	 * id 唯一标识
	 */
	const LOOP_START_TAG = '<regexloop\s+(?:((?i)[a-z]+)=[\"\']([^>\'\"]+)[\"\']\s*)>';
	const LOOP_END_TAG = '</regexloop>';
	
	/**
	 * 匹配子模式
	 * <regex id='{id}' adjust='{adjust}' key='true|false'>
	 * id 唯一标识 如果id属性不存在则此规则不以子模式匹配
	 * adjust 为匹配子模式修正符，例如不区分大小写并匹配多行值应为'im'
	 * key 是否为关键字段
	 */
	const PATTERN_START_TAG = '<regex\s*(?:((?i)id)=[\"\']([^>\'\"]+)[\"\']\s*){0,1}(?:((?i)adjust)=[\"\']([^>\'\"]+)[\"\']\s*){0,1}(?:((?i)key)=[\"\']([^>\'\"]+)[\"\']\s*){0,1}>';
	const PATTERN_END_TAG = '</regex>';
	
	/**
	 * 处理匹配模板时的替换值
	 *
	 */
	const PATTERN_DONE_START_TAG = '<regexdone>';
	const PATTERN_DONE_END_TAG = '</regexdone>';
	
	/**
	 * 默认字符集
	 *
	 */
	const DEFAULT_CHARSET = 'UTF-8';
	
	/**
	 *
	 * @var __RegexBlock[]
	 */
	public $blocks;
	/**
	 * 
	 *
	 * @var __RegexLoop[]
	 */
	public $loops;
	/**
	 * 匹配模板
	 *
	 * @var string
	 */
	private $template;
	
	const SIGN = '`';
	
	/**
	 * 构造方法
	 *
	 * @param string $template
	 */
	public function __construct($template){
		$this->template = $template;
		
		$this->parse();
	}
	
	/**
	 * 解析模板
	 *
	 */
	public function parse(){
		$pos = 0;
		while(($pos = strpos($this->template, self::BLOCK_START_TAG , $pos)) !== false){
			$template = substr($this->template, $pos);
			$block = substrByString($template, self::BLOCK_START_TAG , self::BLOCK_END_TAG );
			$pos = $pos + strlen($block);
			$this->blocks[] = $this->parseBlock($block);
		}
		
		$regex = "|".self::LOOP_START_TAG."|" ;
		preg_match_all($regex, $this->template, $match);
		for($i=0; $i<count($match[0]); $i++){
			$loop = new __RegexLoop();
			$loop->id = $match[2][$i];
			$start = $match[0][$i];
			$end = self::LOOP_END_TAG ;
			$loop->data = substrByString($this->template, $start, $end);
			$this->loops[] = $this->parseLoop($loop);
		}
	}
	
	/**
	 * 解析单次匹配块
	 *
	 * @param string $str
	 * @return __RegexBlock
	 */
	public function parseBlock($str){
		$block = new __RegexBlock();
		$block->data = $str;
		$block->regexes = $this->parseRegex($block->data);
		$block->pattern = $this->getPattern($block);
		return $block;
	}
	
	/**
	 * 解析循环匹配块
	 *
	 * @param __RegexLoop $loop
	 * @return __RegexLoop
	 */
	public function parseLoop(&$loop){
		$loop->regexes = $this->parseRegex($loop->data);
		$loop->pattern = $this->getPattern($loop);
		return $loop;
	}
	
	/**
	 * 解析子模式
	 *
	 * @param string $str
	 * @return __Regex[]
	 */
	public function parseRegex($str){
		$regex = "|".self::PATTERN_START_TAG."|" ;
		$flag = preg_match_all($regex, $str, $match);
		if(!$flag){
			return array();
		}
		$ret = array();
		$pos = 0;
		for($i=0; $i<count($match[0]); $i++){
			$ret[$i] = new __Regex();
			if(strtoupper($match[1][$i]) == 'ID'){
				$ret[$i]->id = $match[2][$i];
			}else{
				$ret[$i]->ignore = true;
			}
			
			if(strtoupper($match[3][$i]) == 'ADJUST'){
				$ret[$i]->adjust = $match[4][$i];
			}
			
			if(strtoupper($match[5][$i]) == 'KEY'){
				$ret[$i]->key = ($match[6][$i]==true);
			}
			
			$start = $match[0][$i];
			$end = self::PATTERN_END_TAG ;
			$pos = strpos($str, $start, $pos);
			$temp = substr($str, $pos);
			$value = substrByString($temp, $start, $end);
			$ret[$i]->data = $ret[$i]->pattern = $value;
			$pos += strlen($value);
			if($ret[$i]->adjust){
				$ret[$i]->pattern = '(?'.$ret[$i]->adjust.')'.$ret[$i]->pattern;
			}
			
			if(!$ret[$i]->ignore){
				$ret[$i]->pattern = "(".$ret[$i]->pattern.")";
			}
		}
		return $ret;
	}
	
	/**
	 * 获得块的匹配正则表达式
	 *
	 * @param mixed $block
	 * @return string
	 */
	public function getPattern($block){
		$regex = "|".self::PATTERN_START_TAG."|" ;
		$flag = preg_match_all($regex, $block->data, $match);
		if(!$flag){
			return $block->data;
		}
		$ret = $block->data;
		for($i=0; $i<count($match[0]); $i++){
			$replace_regex = '|'.$match[0][$i].self::regexEscape($block->regexes[$i]->data).self::PATTERN_END_TAG.'|';
			$replace_value = self::PATTERN_DONE_START_TAG .$i.self::PATTERN_DONE_END_TAG;
			$ret = preg_replace($replace_regex, $replace_value, $ret);
		}
		
		$ret = self::regexEscape($ret);
		
		$regex = '|'.self::PATTERN_DONE_START_TAG."[\d]+".self::PATTERN_DONE_END_TAG.'|';		
		$flag = preg_match_all($regex, $ret, $match);
		if(!$flag){
			return $block->data;
		}
		for($i=0; $i<count($match[0]); $i++){
			$replace = $match[0][$i];
			$replace_value = $block->regexes[$i]->pattern;
			$ret = str_replace($replace, $replace_value, $ret);
		}
		
		return $ret;
	}
	
	/**
	 * 对输入页面进行分析
	 *
	 * @param string $page
	 * @param string $pageChar
	 * @return []
	 */
	public function execute($page, $pageChar='GB18030'){
		if(strtoupper($pageChar) != self::DEFAULT_CHARSET ){
			$page = iconv($pageChar, 'UTF-8', $page);
		}
		$ret = array();
		if($this->blocks){
			foreach ($this->blocks AS &$block){
				$flag = preg_match(convertspace(self::SIGN .$block->pattern.self::SIGN ), $page, $block->match);
				if($flag){
					array_shift($block->match);
					$i = 0;
					foreach ($block->regexes AS $regex){
						if(!$regex->ignore){
							$regex->match = $ret[$regex->id] = $block->match[$i];
							
							$i++;
						}
					}
				}else{
					foreach ($block->regexes AS $regex){
						if(!$regex->ignore){
							$ret[$regex->id] = null;
						}
					}
				}
			}
		}
		
		if($this->loops){
			foreach ($this->loops AS &$block){
				$flag = preg_match_all(convertspace(self::SIGN .$block->pattern.self::SIGN ), $page, $block->match);
				if($flag){
					array_shift($block->match);
					$ret[$block->id] = array();
					
					for($k=0; $k<count($block->match[0]); $k++){
						$i = 0;
						$row = array();
						foreach ($block->regexes AS $regex){
							if(!$regex->ignore){
								$row[$regex->id] = $block->match[$i][$k];
								$i++;
							}
						}
						$ret[$block->id][] = $row;
					}
				}else{
					$ret[$block->id] = array();
				}
			}
		}
		
		return $ret;
	}
	/**
	 * 正则表达式转义
	 *
	 * @param string $str
	 * @return string
	 */
	public static function regexEscape($str){
		$ops = '\'\"^$.[]|()?*+{}:-&';
		$str = addcslashes($str, $ops);
		return $str;
	}
	
	public function toStructString($title){
		$ret = '<dl style="float:left">';
		$ret .= '<dt style="font-size:14px; font-weight:bold">'.$title.'</dt>';
		foreach($this->blocks AS $block){
			foreach($block->regexes AS $regex){
				if(!$regex->ignore){
					$ret .= '<dt parent="'.$title.'">'.$regex->id.'</dt>';
				}
			}
		}
		
		foreach($this->loops AS $loop){
			$ret .= '<dt parent="'.$title.'">'.$loop->id.'</dt>';
			foreach($loop->regexes AS $regex){
				if(!$regex->ignore){
					$ret .= '<dd parent="'.$title.".".$loop->id.'">'.$regex->id.'</dd>';
				}
			}
		}
		$ret .= '</dl>';
		
		return $ret;
	}
	
}

class __RegexBlock{
	var $data;
	var $regexes;
	var $match;
	var $pattern;
}

class __RegexLoop{
	var $data;
	var $regexes;
	var $id;
	var $match;
	var $pattern;
}

class __Regex{
	var $id;
	var $adjust;
	var $pattern;
	var $data;
	var $ignore = false;
	var $iskey = false;
	var $match;
}

//$template = file_get_contents("e:/data/guildwar/rawdata/area_template2.html");
//$template = iconv('GB18030', 'UTF-8', $template);
//
//$page = file_get_contents("e:/data/guildwar/rawdata/area/area_1.html");
//$rp = new RegexPage2($template);
//var_dump($rp);
//$row = $rp->execute($page);
//var_dump($row);
?>