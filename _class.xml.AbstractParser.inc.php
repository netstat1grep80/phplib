<?
/**
 * @package xml
 * @author Mars <tempzzz>
 */
/**
 * xml parser 抽象类
 * 使用SAX API解析xml
 * 
 */
abstract class AbstractParser{
	public $encoding;
	public $result;
	public $level;
	public $current;
	public $stack;
	public $parser;
	
	const BUFFER_SIZE=4096;
	const TYPE_FILE = 'file';
	const TYPE_TEXT = 'text';
	
	/**
	 * 构造方法
	 *
	 * @param string $encoding
	 */
	public function __construct($encoding='GB18030'){
		$this->encoding 	= $encoding;
		$this->result 		= array();
		$this->level 		= 0;
		$this->current 		= NULL;
		$this->stack 		= array();
	}
	
	/**
	 * 分析xml文档
	 *
	 * @param array $option
	 * @return array
	 */
	public function parse($option){
		$this->parser = xml_parser_create();
		
		$data = '';
		if($option['type']==self::TYPE_FILE){
			$data = $this->readFile($option['data']);
		}else if($option['type']==self::TYPE_TEXT ){
			$data = &$option['data'];
		}

		// prepare xml handlers and settings
		xml_set_object($this->parser, $this);
		xml_set_character_data_handler($this->parser, "dataHandler");
		xml_set_element_handler($this->parser, 'startHandler', 'endHandler');
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		
		if(!xml_parse($this->parser, $data, true)) {
			// @todo throws xmlexception
//			trigger_error(sprintf("XML error: %s at line %d",
//				xml_error_string(xml_get_error_code($this->parser)),
//				xml_get_current_line_number($this->parser)), E_USER_WARNING);
			xml_parser_free($this->parser);
			return false;
		}
		xml_parser_free($this->parser);
		
		$ret = & $this->result;
		unset($this->result);
		$this->result = array();
		return $ret;
	}
	
	/**
	 * 读取xml文件
	 *
	 * @param string $filePath
	 * @return string
	 */
	public function readFile($filePath){
		$ret = '';
		if(!($fp = fopen($filePath, "r"))) {
			//@todo throws ioexception
			//trigger_error("Cannot open XML data file: $filePath", E_USER_WARNING);
			return false;
		}
		while($data = fread($fp, self::BUFFER_SIZE)) {
			$ret .= $data;
		}
		fclose($fp);
		return $ret;
	}
	
	/**
	 * 处理数据handler
	 *
	 * @param resource $parser
	 * @param string $data
	 */
	abstract public function dataHandler($parser, $data);
	/**
	 * 开始处理xml tag时的handler
	 *
	 * @param resource $parser
	 * @param string $name
	 * @param array $attributes
	 */
	abstract public function startHandler($parser, $name, $attributes);
	/**
	 * 结束xml tag时的handler
	 *
	 * @param resource $parser
	 * @param string $name
	 */
	abstract public function endHandler($parser, $name);
}
?>