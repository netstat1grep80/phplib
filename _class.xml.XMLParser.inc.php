<?php
/**
 * @package xml
 * @author Mars <tempzzz>
 */
require_once("_const.xml.inc.php");

/**
 * XML分析器，分析xml文档生成对应的数组
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
 * $parser = new XMLParser("http://localhost/books.xml");
 * $parser->parse();
 * print_r($parser->document);
 * </code>
 */
class XMLParser{
	private $parser;
	/**
	 * xml地址
	 *
	 * @var unknown_type
	 */
	private $filePath;
	/**
	 * 储存分析结果
	 *
	 * @var array
	 */
	public $document;
	/**
	 * 当前正在分析的tag
	 *
	 * @var array
	 */
	private $currentTag;
	/**
	 * 层计数器
	 *
	 * @var int
	 */
	private $level;
	/**
	 * 正在处理未完成的tag
	 *
	 * @var array
	 */
	private $processingStack;
	
	/**
	 * 构造方法
	 *
	 * @param string $path - xml路径/URL
	 */
	public function __construct($path){
		$this->parser = xml_parser_create();
		$this->filePath = $path;
		$this->document = array();
		$this->currentTag = array();
		$this->processingStack = array();
		$this->level = 0;
	}
	
	/**
	 * 执行分析xml文档
	 *
	 * @return boolean
	 */
	public function parse(){
		xml_set_object($this->parser, $this);
		xml_set_character_data_handler($this->parser, "dataHandler");
		xml_set_element_handler($this->parser, 'startHandler', 'endHandler');
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		
	   if(!($fp = fopen($this->filePath, "r"))) {
		   die("Cannot open XML data file: $this->filePath");
		   return false;
		}
		
		while($data = fread($fp, 4096)) {
		   if(!xml_parse($this->parser, $data, feof($fp))) {
			   die(sprintf("XML error: %s at line %d",
				   xml_error_string(xml_get_error_code($this->parser)),
				 xml_get_current_line_number($this->parser)));
		 }
	   }
	   
	   fclose($fp);
	   xml_parser_free($this->parser);
	   return true;
	}
	
	/**
	 * datahandler
	 * @internal 
	 * @param parser handler $parser
	 * @param string $data - 标签数据，如<tag>some crap</tag>, $data="some crap"
	 */
	public function dataHandler($parser, $data){
		//echo trim($data);
		$array = &$this->processingStack[count($this->processingStack)-1];
		//print(trim(iconv('utf-8','gb2312',$data))."\r\n");
		$array[XML_ARRAY_NAME_TEXT] .= iconv('utf-8','gb18030',$data);
	}
	
	/**
	 * 当处理到tag开头时……
	 *
	 * @param parser handler $parser
	 * @param string $name - tag name
	 * @param array $attribs - tag attributes
	 */
	public function startHandler($parser, $name, $attribs){
		$this->currentTag = array();
		$this->currentTag = array(XML_ARRAY_NAME_TAGNAME => $name,
								  XML_ARRAY_NAME_ATTRIBUTES => array(),
								  XML_ARRAY_NAME_CHILDREN => array(),
								  XML_ARRAY_NAME_TEXT => "");
		$this->currentTag[XML_ARRAY_NAME_ATTRIBUTES] = & $attribs;
		
		array_push($this->processingStack, $this->currentTag);
		//$this->processingStack[$this->level] = & $this->currentTag;
		
		$this->level++;

	}
	
	/**
	 * 当处理到tag结束时
	 *
	 * @param parser hanlder $parser
	 * @param string $name - tag name
	 */
	public function endHandler($parser, $name){
		$array = array_pop($this->processingStack);
		$this->level--;
		if($this->level >0){
			$parent = & $this->processingStack[count($this->processingStack) -1][XML_ARRAY_NAME_CHILDREN];
		}else{
			$parent = & $this->document;
		}
		$parent[] = $array;
		//$parent[XML_ARRAY_NAME_CHILDREN]
		
	}
}
?>