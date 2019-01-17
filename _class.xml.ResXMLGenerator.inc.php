<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once('_class.xml.AbstractGenerator.inc.php');
require_once('_func.util.common.inc.php');
/**
 * 生成列表xml
 */
final class ResXMLGenerator extends AbstractGenerator {
	const TAG_TOP 	= 'RESULT';
	const TAG_INFO 	= 'INFO';
	const TAG_ROWS 	= 'RECORDS';
	const TAG_ROW 	= 'RECORD';

	const NODE_INFO = 'INFO';
	const NODE_ROWS	= 'ROWS';

	public function __construct($data,
	$version = '1.0',
	$encoding = 'GB2312',
	$formatOutput = true)
	{
		parent::__construct($data, $version, $encoding, $formatOutput);
	}

	/**
	 * 生成xml
	 *
	 * @param string $charsetEncoding
	 * @return xml
	 */
	public function generate($charsetEncoding = 'UTF-8'){
		$this->charsetEncoding  = $charsetEncoding;
		$top = $this->createElement(self::TAG_TOP );

		$info = $this->createElement(self::TAG_INFO );
		$ninfo = & $this->data[self::NODE_INFO ];
		if(asserting($ninfo, true)){
			foreach($ninfo AS $key=>$value){
				$element = $this->createElement($key);
				$element->appendChild($this->createTextNode($value));
				$info->appendChild($element);
			}
		}
		$top->appendChild($info);

		$rows = $this->createElement(self::TAG_ROWS );
		$nrows = & $this->data[self::NODE_ROWS ];
		for($i=0; $i<count($nrows); $i++){
			$row = $this->createElement(self::TAG_ROW );
			$nrow = & $nrows[$i];
			foreach ($nrow as $key=>$value){
				$element = $this->createElement($key);
				if(strtoupper($key) == 'DATA'){
					$data = & $nrow[$key];
					foreach ($data AS $datakey => $datavalue){
						$datanode = $this->createElement('URL'.$datakey);
						$datanode->appendChild($this->createTextNode($datavalue));
						$element->appendChild($datanode);
					}
				}else{
					$element->appendChild($this->createTextNode($value));
				}
				$row->appendChild($element);
			}
			$rows->appendChild($row);
		}
		$top->appendChild($rows);

		$this->document->appendChild($top);
		return $top;
	}
}

//$data = array (
//'ROWS'=>array(
//0 =>
//array (
//'FLD_PICID' => '118',
//'FLD_PICNAME' => '美媚在宾馆自拍4',
//'FLD_PICCID' => NULL,
//'FLD_ALBUMID' => '17',
//'FLD_ALBUMNAME' => '美媚在宾馆自拍',
//'FLD_PICSIZEID' => '0',
//'FLD_LEVEL' => '0',
//'FLD_URL' => '',
//'FLD_TAG' => NULL,
//'FLD_PIC' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=103&id=',
//'FLD_PIC_TYPE' => '',
//'FLD_W_H' => '_',
//'FLD_IS_DESKTOP' => '0',
//'FLD_PICSIZE' => '0',
//'FLD_UPLOADID' => '10964',
//'FLD_UPLOADNAME' => 'randallho',
//'FLD_STATUS' => '0',
//'FLD_CREATEDATE' => '2007-02-02 03:59:03',
//'FLD_UPDATEDATE' => '2007-02-02 03:59:03',
//'FLD_CPID' => NULL,
//'FLD_MOBILENUM_VIEW' => NULL,
//'FLD_INTERNUM_VIEW' => NULL,
//'FLD_COMPUTER_DOWN' => NULL,
//'FLD_MOBILE_DOWN' => NULL,
//'FLD_COLLECTSNUM' => NULL,
//'FLD_GRADES_SORT' => NULL,
//'FLD_GRADESNUM' => NULL,
//'FLD_LIKENUM' => NULL,
//'FLD_NOTLIKENUM' => NULL,
//'FLD_PREVIEWID1' => NULL,
//'FLD_PREVIEWID2' => NULL,
//'FLD_SORT1' => NULL,
//'FLD_SORT2' => NULL,
//'FLD_SORT3' => NULL,
//'FLD_SPARE1' => NULL,
//'FLD_LANGUAGE' => NULL,
//'FLD_RESINFO' => NULL,
//'DATA' =>
//array (
//'_148_96' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&id=1l19l19',
//'__' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&id=1l19l20',
//'_208_260' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&id=1l19l21',
//'_128_80' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&id=1l19l22',
//'_176_132' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&id=1l19l23',
//'_208_144' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=500&id=1l19l24',
//),
//),
//1 =>
//array (
//'FLD_PICID' => '119',
//'FLD_PICNAME' => '美媚在宾馆自拍5',
//'FLD_PICCID' => NULL,
//'FLD_ALBUMID' => '17',
//'FLD_ALBUMNAME' => '美媚在宾馆自拍',
//'FLD_PICSIZEID' => '0',
//'FLD_LEVEL' => '0',
//'FLD_URL' => '',
//'FLD_TAG' => NULL,
//'FLD_PIC' => 'http://60.28.197.40/web_svn/web/show_file.php?zy_type=103&id=',
//'FLD_PIC_TYPE' => '',
//'FLD_W_H' => '_',
//'FLD_IS_DESKTOP' => '0',
//'FLD_PICSIZE' => '0',
//'FLD_UPLOADID' => '10964',
//'FLD_UPLOADNAME' => 'randallho',
//'FLD_STATUS' => '0',
//'FLD_CREATEDATE' => '2007-02-02 03:59:29',
//'FLD_UPDATEDATE' => '2007-02-02 03:59:29',
//'FLD_CPID' => NULL,
//'FLD_MOBILENUM_VIEW' => NULL,
//'FLD_INTERNUM_VIEW' => NULL,
//'FLD_COMPUTER_DOWN' => NULL,
//'FLD_MOBILE_DOWN' => NULL,
//'FLD_COLLECTSNUM' => NULL,
//'FLD_GRADES_SORT' => NULL,
//'FLD_GRADESNUM' => NULL,
//'FLD_LIKENUM' => NULL,
//'FLD_NOTLIKENUM' => NULL,
//'FLD_PREVIEWID1' => NULL,
//'FLD_PREVIEWID2' => NULL,
//'FLD_SORT1' => NULL,
//'FLD_SORT2' => NULL,
//'FLD_SORT3' => NULL,
//'FLD_SPARE1' => NULL,
//'FLD_LANGUAGE' => NULL,
//'FLD_RESINFO' => NULL,
//),
//)
//);
//
//$generator = new ResXMLGenerator($data);
//$generator->generate('GB18030');
//echo $generator->saveXML();
?>