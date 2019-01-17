<?php
require_once('_class.xml.AbstractParser.inc.php');
require_once('_func.util.common.inc.php');
/**
 * 解析RSS
 * @package xml
 * @author Mars <tempzzz>
 *
 */
final class RSSParser extends AbstractParser {
	private $raw;
	const TAG_LEVEL1 = 'RECORD';
	
	public function __construct($encoding='UTF-8'){
		parent::__construct($encoding);
	}
	
	public function dataHandler($parser, $data){
		if(strtoupper($this->encoding)!='UTF-8'){
			$data = iconv('UTF-8', $this->encoding, $data);
		}
		
		$array = &$this->stack[count($this->stack)-1];
		$array['value'] .= $data;
		/*
		if($this->level==2){
			$this->result[$this->current] = $data;
		}
		*/
	}

	public function startHandler($parser, $name, $attributes){
		$this->current = array('name'=>$name, 'value'=>'', 'children'=>array());
		array_push($this->stack, $this->current);
		$this->level++;
	}

	public function endHandler($parser, $name){
		$array = array_pop($this->stack);
		$this->level--;
		if($this->level >0){
			$parent = & $this->stack[count($this->stack) -1]['children'];
		}else{
			$parent = & $this->raw;
		}
		$parent[] = $array;
		
		if($this->level==0){
			$this->result = $this->processing($this->raw);
			$this->result = & $this->result['rss'][0]['channel'][0];
			if(asserting($this->result['image'], true)){
				$this->result['image'] = &$this->result['image'][0];
			}
		}
	}
	
	private function & processing(&$array){
		$ret = array();
		for($i=0; $i<count($array); $i++){
			$element = & $array[$i];
			if(trim($element['value']) == '' && asserting($element['children'], true)){
				$ret[$element['name']][] = $this->processing($element['children']);
			}else{
				$ret[$element['name']] = trim($element['value']);
			}
		}
		return $ret;
	}
}

/*$parser = new RSSParser();
$data = $parser->parse(array('type'=>RSSParser::TYPE_FILE,
					 'data'=>'e:/web/xguild/tech.rss'));
					 
require_once('_class.xml.RSSGenerator.inc.php');
$rss = new RSSGenerator($data);
$rss->generate();
ECHO ($rss->saveXML());*/

?>