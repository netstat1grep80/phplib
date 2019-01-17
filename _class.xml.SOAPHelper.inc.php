<?php
/** 
 * @package xml
 * @author Mars <tempzzz>
 */
/**
 * 参考Gorge Blog的WSDL_gen.php重写<br/>
 *
 * SOAP的相关处理：<br/>
 * 1. 自动生成wsdl<br/>
 * 2. 注册服务器<br/>
 * 
 * 改进:<br/>
 * 1. wsdl生成方式简化, 通过在服务地址后加?genwsdl获得<br/>
 * 2. 支持自定义类(复杂参数)做参数, 支持各类型数组<br/>
 * 
 * sample<br/>
 * 问我要..
 * 
 */
class SOAPHelper{
	/**
	 * custom namespace
	 *
	 * @var string
	 */
	private $urn;
	/**
	 * 服务地址
	 *
	 * @var string
	 */
	private $endpoint;
	/**
	 * soap 绑定类型
	 * default : rpc
	 * 
	 * @var string 
	 */
	private $bindingStyle;
	/**
	 * wsdl地址
	 *
	 * @var string
	 */
	private $wsdlurl;
	/**
	 * soap encoding 
	 * default: GB18030
	 *
	 * @var string
	 */
	private $soapencoding;
	/**
	 * 是否提供服务
	 * default: true
	 *
	 * @var boolean
	 */
	private $isService;
	/**
	 * wsdl DOMDocument
	 *
	 * @var DOMDocument
	 */
	private $wsdl;
	/**
	 * Definition node
	 *
	 * @var DOMElement
	 */
	private $definitions;
	/**
	 * Types node
	 *
	 * @var DOMElement
	 */
	private $types;
	/**
	 * schema 区块
	 *
	 * @var DOMElement
	 */
	private $typesSchema;
	/**
	 * 要导出的方法数组
	 *
	 * @var ReflectionFunction[]
	 */
	private $functionStack;
	/**
	 * 分析完的方法信息
	 *
	 * @var array
	 */
	private $functions;
	/**
	 * 分析完的complexType
	 *
	 * @var array
	 */
	private $complexTypes;
	/**
	 * simple types in wsdl
	 *
	 * @var array
	 */
	private $arrayTypes;
	/**
	 * array types
	 *
	 * @var array
	 */
	private $simpleTypes = array('string','int','float','bool','integer','boolean'
			,'varstring','varint','varfloat','varbool','varinteger','varboolean');
			
	/**
	 * consts
	 *
	 */
    const SOAP_XML_SCHEMA_VERSION = 'http://www.w3.org/2001/XMLSchema';
    const SOAP_XML_SCHEMA_INSTANCE = 'http://www.w3.org/2001/XMLSchema-instance';
    const SOAP_SCHEMA_ENCODING = 'http://schemas.xmlsoap.org/soap/encoding/';
    const SOAP_ENVELOP = 'http://schemas.xmlsoap.org/soap/envelope/';
    const SCHEMA_SOAP_HTTP = 'http://schemas.xmlsoap.org/soap/http';
    const SCHEMA_SOAP = 'http://schemas.xmlsoap.org/wsdl/soap/';
    const SCHEMA_WSDL = 'http://schemas.xmlsoap.org/wsdl/';    
	
    /**
     * 构造方法
     * 属性: urn, endpoint, bindingStyle, wsdlurl, encoding
     * @param string[] $options
     */
	public function __construct($options){
		$this->complexTypes = array();
		$this->arrayTypes = array();
		$this->isService = true;
		
		$this->urn = (isset($options['urn'])&&($options['urn']!=''))
		? $options['urn']
		:'myurn';
	$this->endpoint = (isset($options['endpoint'])&&($options['endpoint']!=''))
			? $options['endpoint']
			: $this->getEndpointUrl();
	$this->bindingStyle = (isset($options['bindingStyle'])&&($options['bindingStyle']!=''))
			? $options['bindingStyle']
			:'rpc';
	$this->wsdlurl = (isset($options['wsdlurl'])&&($options['wsdlurl']!=''))
			? $options['wsdlurl']
			:NULL;
	$this->soapencoding = (isset($options['encoding'])&&($options['encoding']!=''))
			? $options['encoding']
			:"UTF-8";
			
	$this->functionStack = array();
	}
	
	public function getEndpointUrl(){
		$str = "http://";
		$str.= $_SERVER["HTTP_HOST"];
		if($_SERVER["SERVER_PORT"]!="80"){
			$str .= ":".$_SERVER["SERVER_PORT"];
		}
		$str .= $_SERVER["SCRIPT_NAME"];
		return $str;
	}
	
	/**
	 * 初始化
	 * @deprecated 
	 * @return void
	 */
	public function init(){
		if(strtoupper($_SERVER["QUERY_STRING"]) == "GENWSDL"){
		$this->isService = false;
		print($this->generate());
	}
	}
	/**
	 * 把服务函数压入堆栈
	 *
	 * @param string $funcName
	 * @return SOAPHelper
	 */
	public function pushFunction($funcName){
		$this->functionStack[] = $funcName;
		return $this;
	}
	/**
	 * 生成wsdl文档
	 *
	 * @return string
	 */
	public function generate(){
		$this->wsdl = new DOMDocument("1.0");
		$this->wsdl->formatOutput = true;
		$this->parseFunctions();
/*			
		var_dump($this->functions);
		var_dump($this->complexTypes);
		var_dump($this->arrayTypes);
*/			
		$this->genDefinitions();
		$this->genTypes();
		$this->genMessages();
		$this->genPortType();
		$this->genBinding();
		$this->genService();
		
		return $this->wsdl->saveXML();
	}
	/**
	 * 注册服务
	 * @return void
	 */
	public function registerServer(){
		if(strtoupper($_SERVER["QUERY_STRING"]) == "GENWSDL"){
			$this->isService = false;
			print($this->generate());
		}
	
		if($this->isService){
			//$this->parseFunctions();
			if($this->wsdlurl){
				$server = new SoapServer($this->wsdlurl, array("encoding"=>$this->soapencoding));
			}else{
				$server = new SoapServer(null, array(
					"encoding"=>$this->soapencoding,
					'uri'=>'http://'.$_SERVER['HTTP_HOST'],
					'location'=>$this->endpoint,
					'bindingStyle'=>$this->bindingStyle,
					'endpoint'=>$this->endpoint
				));
			}

			foreach($this->functionStack AS $f){
				$server->addFunction($f);
			}
			$server->handle();
		}
	}
	/**
	 * 根据类型名分析WSDL类型名
	 *
	 * @param string $typeName
	 * @return string
	 */
	public function parseWSDLType($typeName){
		$wsdlType = "";
		if(in_array($typeName, $this->simpleTypes)){
			$wsdlType = "xsd:".$typeName;
		}else{
			if(substr($typeName, -2, 2)=="[]"){
				$front = substr($typeName, 0, strlen($typeName)-2);
				if(!in_array($front, $this->simpleTypes)){
					$this->parseClass($front);
				}
				$wsdlType = $this->parseArrayTypes($front);
			}else{
				$wsdlType = "typens:".$typeName;
				$this->parseClass($typeName);
			}
		}
		return $wsdlType;
	}
	/**
	 * 分析Class到存储结构中
	 *
	 * @param string $className
	 */
	public function parseClass($className){
		if(!key_exists($className, $this->complexTypes)){
			$class = new ReflectionClass($className);
			$this->complexTypes[$className] = array();
			$props = $class->getProperties();
			foreach($props as $i => $prop){
				$name = $prop->getName();
				$comment = $prop->getDocComment();
				preg_match_all('~@var\s(\S+)~', $comment, $temp);
				$type = $temp[1][0];
				$wsdlType = $this->parseWSDLType($type);
				$this->complexTypes[$className][] = array($name, $type, $wsdlType);
			}	
		}
	}
	/**
	 * 分析数组类型到WSDL类型
	 *
	 * @param string $type
	 * @return string
	 */
	public function parseArrayTypes($type){
		if(!key_exists($type."[]", $this->arrayTypes)){
			$namespace = (in_array($type, $this->simpleTypes))?"xsd:":"typens:";
			$this->arrayTypes[$type."Array"] = array($namespace.$type."[]", "typens:".$type."Array");
		}
		return "typens:".$type."Array";
	}
	/**
	 * 分析函数到存储结构中
	 * @return void
	 *
	 */
	public function parseFunctions(){
		foreach($this->functionStack as $i=>$function){
			$function = new ReflectionFunction($function);
			$fname = $function->getName();
			$this->functions[$fname] = array("params"=>array(), "return"=>array());
			$params = $function->getParameters();
			$comment = $function->getDocComment();
			preg_match_all('~@param\s(\S+)~', $comment, $params2);
			/*
				parse parameter types
			*/
			foreach($params as $k=>$param){
				$varName = $param->getName();
				$varType = $params2[1][$k];
				$wsdlType = $this->parseWSDLType($varType);
				$this->functions[$fname]["params"][] = array($varName, $varType, $wsdlType);
			}
			/*
				parse returnType
			*/
			preg_match_all('~@return\s(\S+)~', $comment, $return);
			$varName = $fname."Return";
			$varType = $return[1][0];
			$wsdlType = $this->parseWSDLType($varType);
			$this->functions[$fname]["return"][] = array($varName, $varType, $wsdlType);
		}
	}
	/**
	 * 生成Definitions段
	 * @return void
	 *
	 */
	public function genDefinitions(){
	$this->definitions = $this->wsdl->createElement('definitions');    
	$this->definitions->setAttribute ('name',$this->urn);
	$this->definitions->setAttribute ('targetNamespace','urn:'.$this->urn);
	$this->definitions->setAttribute ('xmlns:typens','urn:'.$this->urn);
	$this->definitions->setAttribute ('xmlns:xsd',self::SOAP_XML_SCHEMA_VERSION);
	$this->definitions->setAttribute ('xmlns:soap',self::SCHEMA_SOAP);
	$this->definitions->setAttribute ('xmlns:soapenc',self::SOAP_SCHEMA_ENCODING);
	$this->definitions->setAttribute ('xmlns:wsdl',self::SCHEMA_WSDL);
	$this->definitions->setAttribute ('xmlns',self::SCHEMA_WSDL);
	$this->wsdl->appendChild($this->definitions);			
	}
	/**
	 * 生成types段
	 * 
	 * @return void
	 *
	 */
	public function genTypes(){
		$types = $this->wsdl->createElement('types');
		
		$this->typesSchema = $this->wsdl->createElement('xsd:schema');
	$this->typesSchema->setAttribute ('xmlns',self::SOAP_XML_SCHEMA_VERSION);
	$this->typesSchema->setAttribute ('targetNamespace','urn:'.$this->urn);
	
	$import = $this->wsdl->createElement('xsd:import');;
		$import->setAttribute("namespace", "http://schemas.xmlsoap.org/soap/encoding/");
		$this->typesSchema->appendChild($import);
	
	foreach($this->complexTypes AS $name=>$params){
		$complexType = $this->wsdl->createElement("xsd:complexType");
		$complexType->setAttribute("name", $name);
		$all = $this->wsdl->createElement("xsd:all");
		foreach ($params as $param) {
			$element = $this->wsdl->createElement("xsd:element");
			$element->setAttribute("name", $param[0]);
			$element->setAttribute("type", $param[2]);
			$all->appendChild($element);
		}
		$complexType->appendChild($all);
		$this->typesSchema->appendChild($complexType);
	}
	
	foreach ($this->arrayTypes AS $name=>$type){
		$complexType = $this->wsdl->createElement("xsd:complexType");
		$complexType->setAttribute("name", $name);
		$complexContent = $this->wsdl->createElement("xsd:complexContent");
		$restriction = $this->wsdl->createElement("xsd:restriction");
		$restriction->setAttribute("base", "soapenc:Array");
		$attribute = $this->wsdl->createElement("xsd:attribute");
		$attribute->setAttribute("ref", "soapenc:arrayType");
		$attribute->setAttribute("wsdl:arrayType", $type[0]);
		$restriction->appendChild($attribute);
		$complexContent->appendChild($restriction);
		$complexType->appendChild($complexContent);
		$this->typesSchema->appendChild($complexType);
	}
	$types->appendChild($this->typesSchema);
	$this->definitions->appendChild($types);
		
	}
	/**
	 * 生成message段
	 * @return void
	 *
	 */
	public function genMessages(){
		$mTypes = array("Input", "Output");
		foreach($this->functions AS $name=>$info){
			foreach ($mTypes AS $mType){
				$message = $this->wsdl->createElement("message");
				$message->setAttribute("name", "$name".$mType);
				$params = ($mType==$mTypes[0])?$info["params"]:$info["return"];
				foreach ($params AS $param){
					$part = $this->wsdl->createElement("part");
					$part->setAttribute("name", $param[0]);
					$part->setAttribute("type", $param[2]);
					$message->appendChild($part);
				}
				$this->definitions->appendChild($message);
			}
		}
	}
	/**
	 * 生成portType段
	 * 
	 * @return void
	 *
	 */
	public function genPortType(){
		$portType = $this->wsdl->createElement('portType');            
	$portType->setAttribute ('name',$this->urn.'Port');
	foreach($this->functions AS $name=>$info){
	    $operation = $this->wsdl->createElement('operation');
	    $operation->setAttribute ('name',$name);
	    $portType->appendChild($operation);
	    $input = $this->wsdl->createElement('input');
	    $output = $this->wsdl->createElement('output');
	    $input->setAttribute ('message','typens:'.$name.'Input');
	    $output->setAttribute ('message','typens:'.$name.'Output');
	    $operation->appendChild($input);
	    $operation->appendChild($output);
	}
	$this->definitions->appendChild($portType);
	}
	/**
	 * 生成binding段
	 * 
	 * @return void
	 *
	 */
	public function genBinding(){
		$binding = $this->wsdl->createElement('binding');
	$binding->setAttribute ('name',$this->urn.'Binding');
	$binding->setAttribute ('type','typens:'.$this->urn.'Port');
	$soap_binding = $this->wsdl->createElement('soap:binding');
	$soap_binding->setAttribute ('style',$this->bindingStyle);
	$soap_binding->setAttribute ('transport',self::SCHEMA_SOAP_HTTP);
	$binding->appendChild($soap_binding);
	foreach($this->functions AS $name=>$info){
	    $operation = $this->wsdl->createElement('operation');
	    $operation->setAttribute ('name',$name);
	    $binding->appendChild($operation);
	    $soap_operation = $this->wsdl->createElement('soap:operation');
	    $soap_operation->setAttribute ('soapAction','urn:'.$this->urn.'Action');
	    $operation->appendChild($soap_operation);
	    $input = $this->wsdl->createElement('input');
	    $output = $this->wsdl->createElement('output');
	    $operation->appendChild($input);
	    $operation->appendChild($output);
	    $soap_body = $this->wsdl->createElement('soap:body');
	    $soap_body->setAttribute ('use','encoded');
	    $soap_body->setAttribute ('namespace','urn:'.$this->urn);
	    $soap_body->setAttribute ('encodingStyle',self::SOAP_SCHEMA_ENCODING);
	    $input->appendChild($soap_body);
	    $soap_body = $this->wsdl->createElement('soap:body');
	    $soap_body->setAttribute ('use','encoded');
	    $soap_body->setAttribute ('namespace','urn:'.$this->urn);
	    $soap_body->setAttribute ('encodingStyle',self::SOAP_SCHEMA_ENCODING);
	    $output->appendChild($soap_body);
	}
	$this->definitions->appendChild($binding);
	}
	/**
	 * 生成Service段
	 *
	 */
	public function genService(){
		$service = $this->wsdl->createElement('service');
	$service->setAttribute ('name',$this->urn.'Service');
	$port = $this->wsdl->createElement('port');
	$port->setAttribute ('name',$this->urn.'Port');
	$port->setAttribute ('binding','typens:'.$this->urn.'Binding');
	$adress = $this->wsdl->createElement('soap:address');
	$adress->setAttribute ('location',$this->endpoint);
	$port->appendChild($adress);
	$service->appendChild($port);
	$this->definitions->appendChild($service);
	}

}
/****************************************************************************************
Demo
****/
//	class Friend{
//		/**
//		 * Enter description here...
//		 *
//		 * @var Friend[]
//		 */
//		public $friends;
//	}
//	class Person{
//		/**
//		 * Enter description here...
//		 *
//		 * @var string
//		 */
//		public $firstname;
//		/**
//		 * Enter description here...
//		 *
//		 * @var string
//		 */
//		public $lastname;
//		/**
//		 * Enter description here...
//		 *
//		 * @var Friend[]
//		 */
//		public $friends;
//		/**
//		 * Enter description here...
//		 *
//		 * @var Friend
//		 */
//		public $girlfriend;
//		
//		public function __construct($f, $l){
//			$this->firstname = $f;
//			$this->lastname = $l;
//		}
//	}
//	/**
//	 * Enter description here...
//	 *
//	 * @param int $x
//	 * @param float $y
//	 * @return int
//	 */
//	function add($x, $y){
//		return $x + $y;
//	}
//	
//	/**
//	 * Enter description here...
//	 *
//	 * @param string $str
//	 * @return string
//	 */
//	function echoString($str){
//		return "echo:".$str;
//	}
//	
//	/**
//	 * Enter description here...
//	 *
//	 * @return string[]
//	 */
//	function getStringArray(){
//		$arr = array();
//		$arr[] = "哦也";
//		return $arr;
//	}
//	
//	/**
//	 * Enter description here...
//	 *
//	 * @param string $firstName
//	 * @param string $lastName
//	 * @return Person
//	 */
//	function getPerson($firstName, $lastName){
//		return new Person($firstName, $lastName);
//	}
//	
//	/**
//	 * Enter description here...
//	 *
//	 * @param Person $person
//	 * @return string
//	 */
//	function strPerson(Person $person){
//		return $person->firstname.$person->lastname;
//	}
//	
//	$wsdl = new SOAPHelper(array());
//	$wsdl->pushFunction("add")
//		 ->pushFunction("echoString")
//		 ->pushFunction("getStringArray")
//		 ->pushFunction("getPerson")
//		 ->pushFunction("strPerson");
//	print($wsdl->generate());
?>