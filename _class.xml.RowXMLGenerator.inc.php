<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once('_class.xml.AbstractGenerator.inc.php');
require_once('_func.util.common.inc.php');

/**
 * 生成单行数据的xml
 *
 * @author Mars <tempzzz>
 */
final class RowXMLGenerator extends AbstractGenerator {
	const TAG_TOP = 'RECORD';
	/**
	 * 构造方法
	 *
	 * @param array $data
	 * @param string $version
	 * @param string $encoding
	 * @param boolean $formatOutput
	 */
	public function __construct($data,
							$version = '1.0',
							$encoding = 'GB2312',
							$formatOutput = TRUE)
	{
		parent::__construct($data, $version, $encoding, $formatOutput);
	}
	
	/**
	 * 生成xml
	 *
	 * @param string $charsetEncoding
	 * @return unknown
	 */
	public function generate($charsetEncoding = 'UTF-8'){
		$this->charsetEncoding  = $charsetEncoding;
		$record = $this->createElement(self::TAG_TOP);
		if(asserting($this->data, true)){
			foreach($this->data AS $key=>$value){
				$element = $this->createElement(strtoupper($key));
				$element->appendChild($this->createTextNode($value));
				$record->appendChild($element);
			}
		}else{
			
		}
		$this->document->appendChild($record);
		
		return $record;
	}
}

/*$record = array (
  'RNM' => '47',
  'FLD_GROUPID' => '888',
  'FLD_OWNERID' => '37',
  'FLD_GROUPNAME' => '刘心',
  'FLD_GROUPINFO' => '新建圈子',
  'FLD_GROUPTYPE' => '0',
  'FLD_OPEN' => '1',
  'FLD_JOIN' => '4',
  'FLD_PROVINCEID' => '0',
  'FLD_CITYID' => '0',
  'FLD_CREATETIME' => '2007-01-31 14:19:27',
  'FLD_IMAGECOUNT' => '0',
  'FLD_MUSICCOUNT' => '0',
  'FLD_TYPENAME' => '无',
);
$generator = new RowXMLGenerator($record);
$generator->generate('GB18030');
echo $generator->saveXML();*/
?>