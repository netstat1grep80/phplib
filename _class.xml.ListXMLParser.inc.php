<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once('_class.xml.AbstractParser.inc.php');

/**
 * 分析列表数据的xml
 */
class ListXMLParser extends AbstractParser {
	const TAG_LEVEL1 = 'RESULT';
	const TAG_LEVEL2_INFO = 'INFO';
	const TAG_LEVEL2_RECORDS = 'RECORDS';
	const TAG_LEVEL3 = 'RECORD';
	
	public function __construct($encoding='GB18030'){
		parent::__construct($encoding);
	}
	
	public function dataHandler($parser, $data){
		if($this->level<4 && trim($data)==''){
			  return ;// skip
		}
		
		if(strtoupper($this->encoding)!='UTF-8'){
			$data = iconv('UTF-8', $this->encoding, $data);
		}
		if($this->level==2){
			if($this->current==self::TAG_LEVEL2_INFO){
				$this->stack = array();
			}
		}elseif($this->level==3){
			if($this->current==self::TAG_LEVEL3){
				$this->stack = array();
			}else{
				$this->stack[$this->current] = $data; // info data
				
			}
		}elseif($this->level==4){
			if(isset($this->stack) && array_key_exists($this->current, $this->stack)){
				$this->stack[$this->current] .= $data; // row data
			}else{
				$this->stack[$this->current] = $data;
			}
			//echo $data."\n";
		}
	}

	public function startHandler($parser, $name, $attributes){
		$this->current = $name;
		$this->level++;
	}

	public function endHandler($parser, $name){
		if($this->level==2 && $name == self::TAG_LEVEL2_INFO ){
			$this->result[self::TAG_LEVEL2_INFO] = & $this->stack;
			unset($this->stack);
		}else if($this->level==3 && $name == self::TAG_LEVEL3){
			$this->result['ROWS'][] = &$this->stack;
			unset($this->stack);
		}else if($this->level==4 ){
			if(!isset($this->stack[$name])){
				$this->stack[$name] = '';
			}
		}
		$this->level--;
	}
}

//$parser = new ListXMLParser();
//var_export($parser->parse(array('type'=>AbstractParser::TYPE_FILE,
//					 'data'=>'e:/web/moyu/list.xml')));
?>