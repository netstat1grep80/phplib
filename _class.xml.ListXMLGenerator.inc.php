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
final class ListXMLGenerator extends AbstractGenerator {
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
				$element->appendChild($this->createTextNode($value));
				$row->appendChild($element);
			}
			$rows->appendChild($row);
		}
		$top->appendChild($rows);
		
		$this->document->appendChild($top);
		return $top;
	}
}

/*$data = array (
  'INFO' => 
  array (
    'COUNT' => '623',
    'PAGECOUNT' => '15',
    'PAGE' => '2',
    'PAGESIZE' => '42',
  ),
  'ROWS' => 
  array (
    0 => 
    array (
      'RNM' => '43',
      'FLD_GROUPID' => '914',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '吾真道场',
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
    ),
    1 => 
    array (
      'RNM' => '44',
      'FLD_GROUPID' => '894',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '阳泉牛友',
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
    ),
    2 => 
    array (
      'RNM' => '45',
      'FLD_GROUPID' => '892',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '光棍',
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
    ),
    3 => 
    array (
      'RNM' => '46',
      'FLD_GROUPID' => '890',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '开心笑',
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
    ),
    4 => 
    array (
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
    ),
    5 => 
    array (
      'RNM' => '48',
      'FLD_GROUPID' => '886',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '飞碟',
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
    ),
    6 => 
    array (
      'RNM' => '49',
      'FLD_GROUPID' => '885',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '日照爱情',
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
    ),
    7 => 
    array (
      'RNM' => '50',
      'FLD_GROUPID' => '884',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '岁',
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
    ),
    8 => 
    array (
      'RNM' => '51',
      'FLD_GROUPID' => '825',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '梦想-原创',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    9 => 
    array (
      'RNM' => '52',
      'FLD_GROUPID' => '826',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '德国队hc盟',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    10 => 
    array (
      'RNM' => '53',
      'FLD_GROUPID' => '827',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '华北cs',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    11 => 
    array (
      'RNM' => '54',
      'FLD_GROUPID' => '828',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '天涯橙汁四海一家',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    12 => 
    array (
      'RNM' => '55',
      'FLD_GROUPID' => '832',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '东光人学习交流吧',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    13 => 
    array (
      'RNM' => '56',
      'FLD_GROUPID' => '834',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '受伤创可贴',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    14 => 
    array (
      'RNM' => '57',
      'FLD_GROUPID' => '836',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => 'edison',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    15 => 
    array (
      'RNM' => '58',
      'FLD_GROUPID' => '838',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '⌒诱祸',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    16 => 
    array (
      'RNM' => '59',
      'FLD_GROUPID' => '840',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '向鼎影音吧',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    17 => 
    array (
      'RNM' => '60',
      'FLD_GROUPID' => '849',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '贝壳里的海',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    18 => 
    array (
      'RNM' => '61',
      'FLD_GROUPID' => '848',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '哈利同人',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    19 => 
    array (
      'RNM' => '62',
      'FLD_GROUPID' => '847',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '我爱小~~说',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    20 => 
    array (
      'RNM' => '63',
      'FLD_GROUPID' => '846',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => 'doublehj',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    21 => 
    array (
      'RNM' => '64',
      'FLD_GROUPID' => '845',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '口袋中心',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    22 => 
    array (
      'RNM' => '65',
      'FLD_GROUPID' => '844',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '冒险冰雷',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    23 => 
    array (
      'RNM' => '66',
      'FLD_GROUPID' => '843',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '潮起潮落渤海湾',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    24 => 
    array (
      'RNM' => '67',
      'FLD_GROUPID' => '842',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '随心所欲',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    25 => 
    array (
      'RNM' => '68',
      'FLD_GROUPID' => '841',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '神侃',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    26 => 
    array (
      'RNM' => '69',
      'FLD_GROUPID' => '858',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '灌水吧!',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    27 => 
    array (
      'RNM' => '70',
      'FLD_GROUPID' => '857',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '妖精世界',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    28 => 
    array (
      'RNM' => '71',
      'FLD_GROUPID' => '856',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '勤迷西游记',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    29 => 
    array (
      'RNM' => '72',
      'FLD_GROUPID' => '855',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '紫月璇梦',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    30 => 
    array (
      'RNM' => '73',
      'FLD_GROUPID' => '854',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => 'fw',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    31 => 
    array (
      'RNM' => '74',
      'FLD_GROUPID' => '853',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '何方',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    32 => 
    array (
      'RNM' => '75',
      'FLD_GROUPID' => '852',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '心殇',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    33 => 
    array (
      'RNM' => '76',
      'FLD_GROUPID' => '851',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '赫秀',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    34 => 
    array (
      'RNM' => '77',
      'FLD_GROUPID' => '850',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '闽南语',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    35 => 
    array (
      'RNM' => '78',
      'FLD_GROUPID' => '875',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '狂王蓝斯',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    36 => 
    array (
      'RNM' => '79',
      'FLD_GROUPID' => '874',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '帅哥悦',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    37 => 
    array (
      'RNM' => '80',
      'FLD_GROUPID' => '873',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '静月影',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    38 => 
    array (
      'RNM' => '81',
      'FLD_GROUPID' => '872',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '冰',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    39 => 
    array (
      'RNM' => '82',
      'FLD_GROUPID' => '871',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '夜航班',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    40 => 
    array (
      'RNM' => '83',
      'FLD_GROUPID' => '870',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '羌族双煞',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
    41 => 
    array (
      'RNM' => '84',
      'FLD_GROUPID' => '869',
      'FLD_OWNERID' => '37',
      'FLD_GROUPNAME' => '风影吧',
      'FLD_GROUPINFO' => '新建圈子',
      'FLD_GROUPTYPE' => '0',
      'FLD_OPEN' => '1',
      'FLD_JOIN' => '4',
      'FLD_PROVINCEID' => '0',
      'FLD_CITYID' => '0',
      'FLD_CREATETIME' => '2007-01-31 14:19:26',
      'FLD_IMAGECOUNT' => '0',
      'FLD_MUSICCOUNT' => '0',
      'FLD_TYPENAME' => '无',
    ),
  ),
);

$generator = new ListXMLGenerator($data);
$generator->generate('GB18030');
echo $generator->saveXML();*/
?>