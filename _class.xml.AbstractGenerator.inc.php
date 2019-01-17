<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
/**
 * XMLGenerator 抽象类
 */
abstract class AbstractGenerator{
	public $data;
	public $document;
	public $version;
	public $encoding;
	public $charsetEncoding;
	public $formatOutput;

	public function __construct($data,
								$version = '1.0',
								$encoding = 'GB2312',
								$formatOutput = false)
	{
		$this->data 			= $data;
		$this->version 			= $version;
		$this->encoding 		= $encoding;
		$this->formatOutput 	= $formatOutput;
		
		$this->document = new DOMDocument($this->version, 
										  $this->encoding);
		$this->document->formatOutput = $this->formatOutput;
	}
	
	/**
	 * 创建DOMElement
	 *
	 * @param string $tag - tag name
	 * @return DOMElement
	 */
	public function createElement($tag){
		return $this->document->createElement($tag);
	}

	/**
	 * 创建文本类型的节点
	 *
	 * @param string $txt
	 * @return DOMText
	 */
	public function createTextNode($txt){
		if(strtoupper($this->charsetEncoding)!='UTF-8'){
			$txt = iconv($this->charsetEncoding, 'utf-8',$txt);
		}
		return $this->document->createTextNode($txt);
	}

	/**
	 * 创建标签属性
	 *
	 * @param string $key
	 * @param string $value
	 * @return DOMAttribute
	 */
	public function createAttribute($key, $value){
		$attr = $this->document->createAttribute($key);
		$attr->value=$value;
		return $attr;
	}
	
	public function createXSLInstruction($href){
		$xsl = $this->document->createProcessingInstruction('xml-stylesheet', 'href="'.$href.'" type="text/xsl"');
		return $xsl;
	}
	
	/**
	 * 返回xml
	 *
	 * @return string
	 */
	public function saveXML(){
		return $this->document->saveXML();
	}
	
	/**
	 * 生成xml
	 *
	 * @param string $charsetEncoding
	 */
	abstract public function generate($charsetEncoding = 'UTF-8');
}
?>