<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once('_class.xml.AbstractParser.inc.php');

/**
 * 分析列表数据的xml
 */
class ResXMLParser extends AbstractParser {
	const TAG_LEVEL1 = 'RESULT';
	const TAG_LEVEL2_INFO = 'INFO';
	const TAG_LEVEL2_RECORDS = 'RECORDS';
	const TAG_LEVEL3 = 'RECORD';
	const TAG_LEVEL4 = 'DATA';
	
	public function __construct($encoding='GB18030'){
		parent::__construct($encoding);
	}
	
	public function dataHandler($parser, $data){
		if(empty($this->current) || ($this->level<4 && trim($data)=='')){
			  return ;// skip
		}
		
		if(strtoupper($this->encoding)!='UTF-8'){
			$data = iconv('UTF-8', $this->encoding, $data);
		}
		if($this->level == 2){
			if($this->current==self::TAG_LEVEL2_INFO){
				$this->stack = array();
			}
		}elseif($this->level == 3){
			if($this->current==self::TAG_LEVEL3){
				$this->stack = array();
			}else{
				$this->stack[$this->current] = $data; // info data
			}
		}elseif($this->level == 4){
			if($this->current==self::TAG_LEVEL4){
				$this->stack[self::TAG_LEVEL4] = array();
			}else{
				if(isset($this->stack) && array_key_exists($this->current, $this->stack)){
					$this->stack[$this->current] .= $data; // row data
				}else{
					$this->stack[$this->current] = $data;
				}
				//echo $this->stack[$this->current];
			}
		}elseif($this->level == 5){
			$key = substr($this->current, 3);
			if(isset($this->stack) && isset($this->stack['DATA']) && isset($this->stack['DATA'][$key])){
				$this->stack['DATA'][$key] .= $data;
			}else{
				$this->stack['DATA'][$key] = $data;
			}
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
		}else if($this->level==4 && $name != self::TAG_LEVEL4 ){
			if(!isset($this->stack[$name])){
				$this->stack[$name] = '';
			}
		}else if($this->level==5){
			if(!isset($this->stack[self::TAG_LEVEL4 ][substr($name, 3)])){
				$this->stack[self::TAG_LEVEL4 ][substr($name, 3)]='';
			}
		}
		$this->current = '';
		$this->level--;
	}
}

/*$data = '<?xml version="1.0" encoding="GB2312"?>
<RESULT>
  <INFO/>
  <RECORDS>
    <RECORD>
      <FLD_PICID>118</FLD_PICID>
      <FLD_PICNAME>美媚在宾馆自拍4</FLD_PICNAME>
      <FLD_PICCID></FLD_PICCID>
      <FLD_ALBUMID>17</FLD_ALBUMID>
      <FLD_ALBUMNAME>美媚在宾馆自拍</FLD_ALBUMNAME>
      <FLD_PICSIZEID>0</FLD_PICSIZEID>
      <FLD_LEVEL>0</FLD_LEVEL>
      <FLD_URL></FLD_URL>
      <FLD_TAG></FLD_TAG>
      <FLD_PIC>http://60.28.197.40/web_svn/web/show_file.php?zy_type=103&amp;id=</FLD_PIC>
      <FLD_PIC_TYPE></FLD_PIC_TYPE>
      <FLD_W_H>_</FLD_W_H>
      <FLD_IS_DESKTOP>0</FLD_IS_DESKTOP>
      <FLD_PICSIZE>0</FLD_PICSIZE>
      <FLD_UPLOADID>10964</FLD_UPLOADID>
      <FLD_UPLOADNAME>randallho</FLD_UPLOADNAME>
      <FLD_STATUS>0</FLD_STATUS>
      <FLD_CREATEDATE>2007-02-02 03:59:03</FLD_CREATEDATE>
      <FLD_UPDATEDATE>2007-02-02 03:59:03</FLD_UPDATEDATE>
      <FLD_CPID></FLD_CPID>
      <FLD_MOBILENUM_VIEW></FLD_MOBILENUM_VIEW>
      <FLD_INTERNUM_VIEW></FLD_INTERNUM_VIEW>
      <FLD_COMPUTER_DOWN></FLD_COMPUTER_DOWN>
      <FLD_MOBILE_DOWN></FLD_MOBILE_DOWN>
      <FLD_COLLECTSNUM></FLD_COLLECTSNUM>
      <FLD_GRADES_SORT></FLD_GRADES_SORT>
      <FLD_GRADESNUM></FLD_GRADESNUM>
      <FLD_LIKENUM></FLD_LIKENUM>
      <FLD_NOTLIKENUM></FLD_NOTLIKENUM>
      <FLD_PREVIEWID1></FLD_PREVIEWID1>
      <FLD_PREVIEWID2></FLD_PREVIEWID2>
      <FLD_SORT1></FLD_SORT1>
      <FLD_SORT2></FLD_SORT2>
      <FLD_SORT3></FLD_SORT3>
      <FLD_SPARE1></FLD_SPARE1>
      <FLD_LANGUAGE></FLD_LANGUAGE>
      <FLD_RESINFO>xxx</FLD_RESINFO>
      <DATA>
        <URL_148_96>http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&amp;id=1l19l19</URL_148_96>
        <URL__>http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&amp;id=1l19l20</URL__>
        <URL_208_260>http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&amp;id=1l19l21</URL_208_260>
        <URL_128_80>http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&amp;id=1l19l22</URL_128_80>
        <URL_176_132>http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&amp;id=1l19l23</URL_176_132>
        <URL_208_144></URL_208_144>
      </DATA>
    </RECORD>
    <RECORD>
      <FLD_PICID>119</FLD_PICID>
      <FLD_PICNAME>美媚在宾馆自拍5</FLD_PICNAME>
      <FLD_PICCID></FLD_PICCID>
      <FLD_ALBUMID>17</FLD_ALBUMID>
      <FLD_ALBUMNAME>美媚在宾馆自拍</FLD_ALBUMNAME>
      <FLD_PICSIZEID>0</FLD_PICSIZEID>
      <FLD_LEVEL>0</FLD_LEVEL>
      <FLD_URL></FLD_URL>
      <FLD_TAG></FLD_TAG>
      <FLD_PIC>http://60.28.197.40/web_svn/web/show_file.php?zy_type=103&amp;id=</FLD_PIC>
      <FLD_PIC_TYPE></FLD_PIC_TYPE>
      <FLD_W_H>_</FLD_W_H>
      <FLD_IS_DESKTOP>0</FLD_IS_DESKTOP>
      <FLD_PICSIZE>0</FLD_PICSIZE>
      <FLD_UPLOADID>10964</FLD_UPLOADID>
      <FLD_UPLOADNAME>randallho</FLD_UPLOADNAME>
      <FLD_STATUS>0</FLD_STATUS>
      <FLD_CREATEDATE>2007-02-02 03:59:29</FLD_CREATEDATE>
      <FLD_UPDATEDATE>2007-02-02 03:59:29</FLD_UPDATEDATE>
      <FLD_CPID></FLD_CPID>
      <FLD_MOBILENUM_VIEW></FLD_MOBILENUM_VIEW>
      <FLD_INTERNUM_VIEW></FLD_INTERNUM_VIEW>
      <FLD_COMPUTER_DOWN></FLD_COMPUTER_DOWN>
      <FLD_MOBILE_DOWN></FLD_MOBILE_DOWN>
      <FLD_COLLECTSNUM></FLD_COLLECTSNUM>
      <FLD_GRADES_SORT></FLD_GRADES_SORT>
      <FLD_GRADESNUM></FLD_GRADESNUM>
      <FLD_LIKENUM></FLD_LIKENUM>
      <FLD_NOTLIKENUM></FLD_NOTLIKENUM>
      <FLD_PREVIEWID1></FLD_PREVIEWID1>
      <FLD_PREVIEWID2></FLD_PREVIEWID2>
      <FLD_SORT1></FLD_SORT1>
      <FLD_SORT2></FLD_SORT2>
      <FLD_SORT3></FLD_SORT3>
      <FLD_SPARE1></FLD_SPARE1>
      <FLD_LANGUAGE></FLD_LANGUAGE>
      <FLD_RESINFO></FLD_RESINFO>
    </RECORD>
  </RECORDS>
</RESULT>';
echo $data;
$parser = new ResXMLParser();
var_dump($parser->parse(array('type'=>AbstractParser::TYPE_TEXT ,
					 'data'=>$data)));*/
?>