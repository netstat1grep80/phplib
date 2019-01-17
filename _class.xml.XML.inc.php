<?php
require_once('_class.xml.ListXMLGenerator.inc.php');
require_once('_class.xml.ListXMLParser.inc.php');
require_once('_class.xml.RowXMLGenerator.inc.php');
require_once('_class.xml.RowXMLParser.inc.php');
require_once('_class.xml.ResXMLGenerator.inc.php');
require_once('_class.xml.ResXMLParser.inc.php');
require_once('_class.xml.RSSParser.inc.php');
require_once('_class.xml.RSSGenerator.inc.php');
require_once('_class.xml.XMLGenerator.inc.php');
require_once('_class.xml.XMLParser.inc.php');

/**
 * XML 聚合类
 * @package xml
 * @author Mars <tempzzz>
 */
class XML{
	/**
	 * 从单行数据生成xml
	 *
	 * @param array $data
	 * @param string $version
	 * @param string $encoding
	 * @param boolean $formatOutput
	 * @param string $charsetEncoding
	 * @return string
	 */
	public static function generateRowXML($data, $version='1.0', $encoding='GB2312', 
								$formatOutput=false, $charsetEncoding='GB18030'){
		$generator = new RowXMLGenerator($data, $version, $encoding, $formatOutput);
		$generator->generate($charsetEncoding);
		return $generator->saveXML();
	}
	
	/**
	 * 从列表数据生成xml
	 *
	 * @param array $data
	 * @param string $version
	 * @param string $encoding
	 * @param boolean $formatOutput
	 * @param string $charsetEncoding
	 * @return string
	 */
	public static function generateListXML($data, $version='1.0', $encoding='GB2312', 
								$formatOutput=false, $charsetEncoding='GB18030'){
		$generator = new ListXMLGenerator($data, $version, $encoding, $formatOutput);
		$generator->generate($charsetEncoding);
		return $generator->saveXML();
	}
	
	/**
	 * 从路径分析xml单行数据
	 *
	 * @param string $path
	 * @param string $encoding
	 * @return array
	 */
	public static function parseRowByPath($path, $encoding='GB18030'){
		$parser = new RowXMLParser($encoding);
		$data = $parser->parse(array('type'=>RowXMLParser::TYPE_FILE, 'data'=>$path));
		return $data;	
	}
	
	/**
	 * 从路径分析xml列表数据
	 *
	 * @param string $path
	 * @param string $encoding
	 * @return array
	 */
	public static function parseListByPath($path, $encoding='GB18030'){
		$parser = new ListXMLParser($encoding);
		$data = $parser->parse(array('type'=>ListXMLParser::TYPE_FILE, 'data'=>$path));
		return $data;	
	}
	
	/**
	 * 从xml字符串分析单行数据
	 *
	 * @param string $text
	 * @param string $encoding
	 * @return array
	 */
	public static function parseRowByText($text, $encoding='GB18030'){
		$parser = new RowXMLParser($encoding);
		$data = $parser->parse(array('type'=>RowXMLParser::TYPE_TEXT, 'data'=>&$text));
		return $data;	
	}
	
	/**
	 * 从xml字符串分析列表数据
	 *
	 * @param string $text
	 * @param string $encoding
	 * @return array
	 */
	public static function parseListByText($text, $encoding='GB18030'){
		$parser = new ListXMLParser($encoding);
		$data = $parser->parse(array('type'=>ListXMLParser::TYPE_TEXT , 'data'=>&$text));
		return $data;	
	}
	
	/**
	 * 从单行数据生成xml
	 *
	 * @see generateRowXML
	 * @deprecated 已经过期, 请使用 {@link generateRowXML} 方法
	 * @param array $arr
	 * @return string
	 */
	public static function generateProp($arr){
		return XML::generateRowXML($arr);
	}
	
	/**
	 * 从列表数据生成xml
	 * 
	 * @see generateListXML
	 * @deprecated 已经过期, 请使用 {@link generateListXML} 方法
	 * @param array $arr
	 * @return string
	 */
	public static function generateList($arr){
		return XML::generateListXML($arr);
	}
	
	/**
	 * 分析xml单行数据
	 * 
	 * @see parseRowByPath
	 * @deprecated 已经过期, 请使用 {@link parseRowByPath} 方法
	 * @param string $path - xml文件路径
	 * @return array
	 */
	public static function getPropArray($path){
		return XML::parseRowByPath($path);
	}
	
	/**
	 * 分析xml 列表数据
	 * 
	 * @deprecated 已经过期, 请使用 {@link parseListByPath} 方法
	 * @see parseListByPath
	 * @param string $path - xml文件路径
	 * @return array
	 */
	public static function getListArray($path){
		return XML::parseListByPath($path);
	}
	
	public static function generateRSS($data){
		$rss = new RSSGenerator($data);
		$rss->generate();
		return $rss->saveXML();
	}
	
	public static function parseRSSByPath($path, $encoding='GB18030'){
		$parser = new RSSParser();
		$data = $parser->parse(array('type'=>RSSParser::TYPE_FILE,
							 'data'=>$path));
		return $data;
	}
	
	public static function parseRSSByText($text, $encoding='GB18030'){
		$parser = new RSSParser();
		$data = $parser->parse(array('type'=>RSSParser::TYPE_TEXT ,
							 'data'=>$text));
		return $data;
	}
	
	public static function parseResByPath($path, $encoding='GB18030'){
		$parser = new ResXMLParser($encoding);
		$data = $parser->parse(array('type'=>RowXMLParser::TYPE_FILE, 'data'=>$path));
		return $data;	
	}
	
	public static function parseResByText($text, $encoding='GB18030'){
		$parser = new ResXMLParser($encoding);
		$data = $parser->parse(array('type'=>RowXMLParser::TYPE_TEXT , 'data'=>$text));
		return $data;	
	}
	
	public static function getResArray($path){
		return self::parseResByPath($path);
	}
	
	public static function generateResListXML(
								$data, $version='1.0', $encoding='GB2312', 
								$formatOutput=false, $charsetEncoding='GB18030'){
		$generator = new ResXMLGenerator($data, $version, $encoding, $formatOutput);
		$generator->generate($charsetEncoding);
		return $generator->saveXML();
	}
	
	public static function genResList($arr){
		self::generateResListXML($arr);
	}
	
	/**
	 * 分析xml文档生成数组
	 *
	 * @param string $path - xml doc uri
	 * @return array
	 */
	public static function parse($path){
		$xml = new XMLParser($path);
		$xml->parse();
		return $xml->document;
	}

	/**
	 * 从array生成xml文档
	 *
	 * @param array $arr
	 * @return string
	 */
	public static function generate($arr){
		$doc = new XMLGenerator($arr);
		$doc->generate();
		return $doc->saveXML();
	}
	
	/**
	 * 根据路径转换MultiXML为数组
	 *
	 * @param string $path
	 * @param string $encoding
	 * @return array
	 */
	public static function parseMultiByPath($path, $encoding='GB18030') {
		$parser = new MultiXMLParser($encoding);
		return $parser->parse(array('type' => MultiXMLParser::TYPE_FILE, 'data' => $path));
	}
	
	/**
	 * 根据文本转换MultiXML为数组
	 *
	 * @param string $text
	 * @param string $encoding
	 * @return array
	 */
	public static function parseMultiByText($text, $encoding='GB18030') {
		$parser = new MultiXMLParser($encoding);
		return $parser->parse(array('type' => MultiXMLParser::TYPE_TEXT, 'data'=> &$text));
	}
	
}

//$xml = new XML();
//var_dump(XML::getListArray('http://60.28.197.40/web_svn/web/pic/pic_url_if.php?album_id=2308&whsize=120x120&return_type=xml'));
//var_dump($xml->parseListByPath('http://tag.moyu.com/interface/data.tag.php?model=get_top_tags_user_visited&count=10'));
//print_r(XML::parseMultiByPath('http://59.151.45.71/xmls.asp?TreeId=1012000100070000,1012000100130000,1012000100010001,1007000100010003,1007000100010004,1012000100020001,1012000100020001,1012000100020002'));
?>