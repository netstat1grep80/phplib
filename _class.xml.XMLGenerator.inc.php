<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once("_const.xml.inc.php");

/**
 * 将array转化为xml
 * 数组范例：
 * <code>
 * $arr = array(
 * 		array(  "tagName"=>"root",
 *				"text"=>"",
 *				"attributes"=>array("attr1"=>"1", "attr2"=>"2"),
 *				"children"=>array(
 *					  			array("tagName"=>"child1",
 *					  				  "text"=>"<a>1</a>",
 *					  				  "attributes"=>array("child1attr1"=>"11", "child1attr2"=>"12"),
 *					  				  "children"=>array()
 *					  				  ),
 *					  		    array("tagName"=>"child2",
 *					  		     	  "text"=>"2",
 *					  				  "attributes"=>array("child1attr1"=>"21", "child1attr2"=>"22"),
 *					  				  "children"=>array()
 *					  				  )
 *					  		     )
 *			   )
 *	);
 * </code>
 * 
 * 使用范例：
 * <code>
 * $document = new XMLGenerator($arr);
 * $document->generate();
 * print($document->saveXML());
 * </code>
 * 
 * @package xml
 * @author Mars <tempzzz>
 */
class XMLGenerator{
	/**
	 * dom document
	 *
	 * @var DOMDocument
	 */
	private $document;
	/**
	 * 编码方式
	 *
	 * @var string
	 */
	private $encoding;
	/**
	 * xml文档版本
	 *
	 * @var string
	 */
	private $version;
	/**
	 * 是否整理格式
	 *
	 * @var boolean
	 */
	private $formatOutput = false;
	/**
	 * 要转化的数组
	 *
	 * @var array
	 */
	public $array;
	
	/**
	 * 构造方法
	 *
	 * @param array $array
	 * @param string $version
	 * @param string $encoding
	 * @param boolean $formatOutput
	 */
	public function __construct( $array=NULL, 
								 $version = "1.0", 
								 $encoding = "GB2312", 
								 $formatOutput = false){
		$this->array = $array;
		$this->version = $version;
		$this->encoding = $encoding;
		$this->formatOutput = $formatOutput;
		
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
	private function createElement($tag){
		return $this->document->createElement($tag);
	}
	
	/**
	 * 创建文本类型的节点
	 *
	 * @param string $txt
	 * @return DOMText
	 */
	private function createTextNode($txt){
		$txt = iconv('gb18030', 'utf-8',$txt);
		return $this->document->createTextNode($txt);
	}
	
	/**
	 * 创建标签属性
	 *
	 * @param string $key
	 * @param string $value
	 * @return DOMAttribute
	 */
	private function createAttribute($key, $value){
		$attr = $this->document->createAttribute($key);
		$attr->value=$value;
		return $attr;
	}
	
	
	
	/**
	 * 依据array生成xml
	 *
	 * @param DOMNode $parent
	 * @param array $array
	 */
	public function generate($parent=NULL, $array=NULL){
		if($parent == NULL){
			$parent = $this->document;
		}
		if($array == NULL){
			$array = $this->array;
		}
		
		$node = NULL;
		
		foreach($array as $key=>$value){
			$type = gettype($value);
			$node = $this->createElement($value[XML_ARRAY_NAME_TAGNAME]);
			
			if($type == "array"){
				$this->getNode($node, $value);
			}
			
			$parent->appendChild($node);
		}
	}
	
	/**
	 * 生成节点
	 *
	 * @param DOMNode $node
	 * @param array $arr
	 */
	private function getNode($node, $arr){
		if(array_key_exists(XML_ARRAY_NAME_TEXT, $arr) && $arr[XML_ARRAY_NAME_TEXT]!=""){
			$node->appendChild($this->createTextNode($arr[XML_ARRAY_NAME_TEXT]));
		}
		
		if(array_key_exists(XML_ARRAY_NAME_ATTRIBUTES, $arr) 
			&& gettype($arr[XML_ARRAY_NAME_ATTRIBUTES])=="array"
			&& count($arr[XML_ARRAY_NAME_ATTRIBUTES])>0){
			foreach($arr[XML_ARRAY_NAME_ATTRIBUTES] as $key=>$value){		
				$node->setAttribute($key, $value);		
			}
		}
		
		if(array_key_exists(XML_ARRAY_NAME_CHILDREN, $arr)
		   && gettype($arr[XML_ARRAY_NAME_CHILDREN]) == "array"
		   && count($arr[XML_ARRAY_NAME_CHILDREN])>0){
			$this->generate($node, $arr[XML_ARRAY_NAME_CHILDREN]);
		}
	}
	
	/**
	 * 获得结果
	 *
	 * @return string
	 */
	public function saveXML(){
		return $this->document->saveXML();
	}
	
	/**
	 * 析构方法
	 *
	 */
	public function __destruct(){
		//$this->document->dump_mem(true);
		$this->document = NULL;
	}
}

?>