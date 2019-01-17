<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once('_class.xml.AbstractParser.inc.php');

/**
 * 分析单行数据的xml
 * @package xml
 * @author Mars <tempzzz>
 *
 */
final class RowXMLParser extends AbstractParser {
	const TAG_LEVEL1 = 'RECORD';
	
	public function __construct($encoding='GB18030'){
		parent::__construct($encoding);
	}
	
	public function dataHandler($parser, $data){
		if($this->level<2 && trim($data)==''){
			  return ;// skip
		}
		
		if(strtoupper($this->encoding)!='UTF-8'){
			$data = iconv('UTF-8', $this->encoding, $data);
		}
		if($this->level==2){
			if(isset($this->result) && array_key_exists($this->current, $this->result)){
				$this->result[$this->current] .= $data;
			}else{
				$this->result[$this->current] = $data;
			}
		}
	}

	public function startHandler($parser, $name, $attributes){
		$this->current = $name;
		$this->level++;
	}

	public function endHandler($parser, $name){
		if(!isset($this->result[$name])){
			$this->result[$name] = '';
		}
		$this->level--;
	}
}

/*$parser = new RowXMLParser();
var_export($parser->parse(array('type'=>RowXMLParser::TYPE_FILE,
					 'data'=>'e:/web/moyu/row.xml')));*/
?>