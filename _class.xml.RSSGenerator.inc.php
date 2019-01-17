<?php
require_once('_class.xml.AbstractGenerator.inc.php');


/**
 * 生成RSS
 *
 * @author Mars <tempzzz>
 */
final class RSSGenerator extends AbstractGenerator {
	const TAG_RSS 					= 'rss';
	const TAG_CHANNEL 				= 'channel';
	const TAG_CHANNEL_CHILDREN 		= 'title, description, link, language, generator, ttl, copyright, pubDate, category, image';
	const TAG_CHANNEL_ITEM 			= 'item';
	const TAG_CHANNEL_ITEM_CHILDREN = 'title, link, author, guid, category, pubDate, comments, description';
	
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
							$encoding = 'UTF-8',
							$formatOutput = TRUE)
	{
		parent::__construct($data, $version, $encoding, $formatOutput);
	}
	
	/**
	 * 生成xml
	 *
	 * @param string $charsetEncoding
	 * @return string
	 */
	public function generate($charsetEncoding = 'UTF-8'){
		$this->document->appendChild($this->createXSLInstruction('rss.xsl'));
		
		$this->charsetEncoding  = $charsetEncoding;
		$rss = $this->createElement(self::TAG_RSS);
		$rss->setAttribute('version', '2.0');
		$channel = $this->createElement(self::TAG_CHANNEL );
		foreach($this->data AS $key=>$value){
			if($key == 'item'){
				
				for($i=0; $i<count($value); $i++){
					$item = $this->createElement(self::TAG_CHANNEL_ITEM );
					foreach ($value[$i] AS $itemkey=>$itemvalue){
						$element = $this->createElement($itemkey);
						$element->appendChild($this->createTextNode($itemvalue));
						$item->appendChild($element);
					}
					$channel->appendChild($item);
				}
				
			}elseif($key == 'image'){
				$image = $this->createElement($key);
				foreach ($value AS $imagekey=>$imagevalue){
					$element = $this->createElement($imagekey);
					$element->appendChild($this->createTextNode($imagevalue));
					$image->appendChild($element);
				}
				$channel->appendChild($image);
			}else{
				$element = $this->createElement($key);
				$element->appendChild($this->createTextNode($value));
				$channel->appendChild($element);
			}
		}
		$rss->appendChild($channel);
		$this->document->appendChild($rss);
		
		return $rss;
	}
}

/*$data = array (
  'title' => '新浪科技-焦点新闻',
  'image' => 
  array (
    'title' => '科技焦点新闻',
    'link' => 'http://tech.sina.com.cn',
    'url' => 'http://image2.sina.com.cn/home/images/sina_logo2.gif',
  ),
  'description' => '科技焦点新闻',
  'link' => 'http://tech.sina.com.cn',
  'language' => 'zh-cn',
  'generator' => 'WWW.SINA.COM.CN',
  'ttl' => '5',
  'copyright' => 'Copyright 1996 - 2005 SINA Inc. All Rights Reserved',
  'pubDate' => 'Sat, 3 Feb 2007 02:25:03 GMT',
  'category' => '',
  'item' => 
  array (
    0 => 
    array (
      'title' => '十年等待让单向收费成为一个符号',
      'link' => 'http://tech.sina.com.cn/t/2007-02-03/10181368261.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/t/2007-02-03/10181368261.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:18:16 GMT',
      'comments' => '',
      'description' => '　　刘燕
　　争争吵吵十年，单向收费对于我国电信业资费管理仍具标志性意义，但对普通消费者它只是一个符号了。
　　很多年前，没有这么多的套餐，价格被控制得很死，单向收费因此年年成为议论焦点，却年年没有结果。经过很多年等待、讨论，单向收费真的要来了，却失去了价格上....',
    ),
    1 => 
    array (
      'title' => '节前的崩盘 本周10款狂降价手机导购',
      'link' => 'http://tech.sina.com.cn/mobile/n/2007-02-03/1010237546.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/mobile/n/2007-02-03/1010237546.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:10:58 GMT',
      'comments' => '',
      'description' => '作者：刘宇 &nbsp;&nbsp;&nbsp; 
&nbsp;&nbsp;&nbsp; 转眼间2007年的2月份到了，离春节也更进了一步。广大商家激战正酣，新品的推出，价格的下降。每天都在吸引着我们的眼球。正如往年一样，节日来临和学生假期对于经销商来说都是硕大的肥肉，抓住这个机会为自己打开销路和创造声....',
    ),
    2 => 
    array (
      'title' => '3日数码相机报价 索尼T系全面涨价中',
      'link' => 'http://tech.sina.com.cn/digi/2007-02-03/1010237526.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/digi/2007-02-03/1010237526.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:10:53 GMT',
      'comments' => '',
      'description' => '作者：刘超 &nbsp;&nbsp;&nbsp; 索尼T系列数码相机每一季推出的新款都会受到市场的疯狂追捧。在06年，T系列数码相机的价格开始逐渐压低，无论T9，T30，T10还是T50，都更加受到消费者的疯狂追捧。
&nbsp;&nbsp;&nbsp; 近日，索尼T系列的供货开始紧张，T10与T50的价格都有了一些上....',
    ),
    3 => 
    array (
      'title' => '索尼T10、T50不降反升 源于缺货所致',
      'link' => 'http://tech.sina.com.cn/digi/2007-02-03/1010237525.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/digi/2007-02-03/1010237525.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:10:52 GMT',
      'comments' => '',
      'description' => '作者：袁源 &nbsp;&nbsp;&nbsp; 在寒促DC市场的降价浪潮之中，有的产品价格却不降反升。小编日前了解到的两款索尼的热销机型便是如此。据了解，T50已经停产，导致缺货；而T10由于近来卖得太好，加上索尼2007年另有新政策目前供货较少从而导致缺货。目前这两款DC价格都有小幅度的上....',
    ),
    4 => 
    array (
      'title' => '拿左轮来视频 天敏加强版摄像头试用',
      'link' => 'http://tech.sina.com.cn/h/2007-02-03/1010237557.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/h/2007-02-03/1010237557.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:10:42 GMT',
      'comments' => '',
      'description' => '作者：彭辉 
● 拿左轮来视频 天敏加强版摄像头试用
&nbsp;&nbsp;&nbsp; 天敏作为计算机视频产品的专业生产商，其产品除了良好的品质外，设计前卫的外观也令很多消费者印象深刻。06年底天敏发布了外形酷似左轮手枪转轮的左轮摄像头，凭着酷炫外观，实用的功能，这款产品已经在市....',
    ),
    5 => 
    array (
      'title' => '精灵鲨露獠牙 微软光电鼠标79元特卖',
      'link' => 'http://tech.sina.com.cn/h/2007-02-03/1010237562.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/h/2007-02-03/1010237562.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:10:42 GMT',
      'comments' => '',
      'description' => '作者：彭辉 ● 精灵鲨露獠牙 微软光电鼠标79元特卖
&nbsp;&nbsp;&nbsp; 微软的键鼠产品向来以设计精良，品质优异，售后服务周全著称，可是相对偏高的价格令很多人望而止步。可是今天小编在市场里发现，微软光学精灵鲨已经将价格降至79元，而且降价涉及到蓝、白、粉、绿四种颜色的....',
    ),
    6 => 
    array (
      'title' => '德国科研组织主席：能源是未来研究中心内容',
      'link' => 'http://tech.sina.com.cn/d/2007-02-03/10041368252.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/d/2007-02-03/10041368252.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:04:47 GMT',
      'comments' => '',
      'description' => '　　访德国黑尔姆霍尔茨联合会主席于尔根?9?9姆利内克 
　　新华网柏林2月2日电(记者金晶)德国最大的科研组织――黑尔姆霍尔茨联合会的主席于尔根?9?9姆利内克日前在接受新华社记者采访时表示，该联合会根据人类经济社会发展的趋势制定了一份长期研究战略报告。这一报告认为“能源”....',
    ),
    7 => 
    array (
      'title' => 'Sis 6xx、7xx芯片组3.78 WHQL最新驱动',
      'link' => 'http://tech.sina.com.cn/h/2007-02-03/1004237517.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/h/2007-02-03/1004237517.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:04:25 GMT',
      'comments' => '',
      'description' => '　　【IT168 资讯】Sis 650/651/661/662/740/741/760/761 3.78 WHQL驱动放出，包括了XP/2000下的AGP8x驱动，win98/me下的AGP驱动，SIS_LIB.DLL,SISPORT.SYS，SiSUSBrg.exe等修正文件，修复了PCI接口、USB接口等的一些Bug，IDE接口可以支持UDMA 133，加入SisAGP utility工具可以设....',
    ),
    8 => 
    array (
      'title' => 'R600将在3月11日发布 性能超G80没问题',
      'link' => 'http://tech.sina.com.cn/h/2007-02-03/1004237518.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/h/2007-02-03/1004237518.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 02:04:23 GMT',
      'comments' => '',
      'description' => '　　【IT168 资讯】最新消息，AMD将在3月21日的Cebit之前，3月11日的荷兰阿姆斯特丹盛大的“TechDay”上发布DX10芯片R600，届时AMD将会邀请来自于45个国家的200名嘉宾，R600发布会将是最盛大最华丽的图形芯片发布会。

　　来自于AMD ATi的高层人员都将参加R600荷兰阿姆斯特丹发....',
    ),
    9 => 
    array (
      'title' => '戴尔遭投资者起诉 密将英特尔回扣计入财报',
      'link' => 'http://tech.sina.com.cn/roll/2007-02-03/0959237511.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/roll/2007-02-03/0959237511.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 01:59:29 GMT',
      'comments' => '',
      'description' => '　　【赛迪网讯】 戴尔遭投资者起诉 秘密将英特尔回扣计入财报

　　【赛迪网讯】2月3日消息，据国外媒体报道，继CEO凯文?9?9罗林斯辞职后，戴尔日前又遭到了投资者的起诉，称戴尔将数亿美元的英特尔回扣秘密计入公司的季度财报中。

　　据路透社报道，戴尔投资者聘请的律师Wil....',
    ),
    10 => 
    array (
      'title' => '爱立信06净利增8.2% 调低07预期致股票跌',
      'link' => 'http://tech.sina.com.cn/roll/2007-02-03/0959237513.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/roll/2007-02-03/0959237513.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 01:59:29 GMT',
      'comments' => '',
      'description' => '　　【赛迪网讯】2月3日消息，本周五，全球最大的移动电信网络设备提供商爱立信发布2006年全年业绩报告，06年净利润较上年增长8.2%，但同时发布的2007财务预期却让市场大为失望。

　　据国外媒体报道，爱立信调低07年预期对全球电信产业是又一次打击，上一周，爱立信的主要竞争....',
    ),
    11 => 
    array (
      'title' => '四季度亚马逊收入大增 税金导致利润减半',
      'link' => 'http://tech.sina.com.cn/roll/2007-02-03/0959237549.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/roll/2007-02-03/0959237549.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 01:59:00 GMT',
      'comments' => '',
      'description' => '　　天极网2月3日消息(他山石编译)据国外媒体报道，互联网零售商亚马逊本周四公布去年第四季度财务报告称，在至关重要的圣诞假日销售旺季，公司的销售收入增长了34%，但较高的税金费用导致公司的利润下降了一半。

　　亚马逊表示，去年第四季度公司获得的利润为9800万美元，每....',
    ),
    12 => 
    array (
      'title' => '李嘉诚旗下首席科学家亮相 进军农副产品业',
      'link' => 'http://tech.sina.com.cn/it/2007-02-03/09581368246.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/it/2007-02-03/09581368246.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 01:58:15 GMT',
      'comments' => '',
      'description' => '　　创立三安科技集团 携专利食品进京
　　本报讯 (记者 胡笑红) 数年来一直被业内称为“李嘉诚的神秘人物”的张令玉昨天在北京首次公开亮相，而其头衔除了李嘉诚的首席科学家外，还新增加了一个称谓―――北京三安科技集团总裁，同时亮相的还有利用其专利技术产品生产的、各项检....',
    ),
    13 => 
    array (
      'title' => '受股票补偿费用拖累 四季度EA利润下降',
      'link' => 'http://tech.sina.com.cn/roll/2007-02-03/0956237514.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/roll/2007-02-03/0956237514.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 01:56:00 GMT',
      'comments' => '',
      'description' => '　　天极网2月3日消息(孙淑艳编译)据国外媒体报道，在去年第四季度财务报告中，全球最大的视频游戏发行商电子艺界的业绩轻松的超过了分析师预期。

　　电子艺界表示，四季度公司获得的利润为1.6亿美元每股约合50美分，而2005年同期公司的利润为2.59亿美元每股约合83美分。利润....',
    ),
    14 => 
    array (
      'title' => '爱立信06年盈利增8% 公司调低07年增长预测',
      'link' => 'http://tech.sina.com.cn/roll/2007-02-03/0955237515.shtml',
      'author' => 'SINA.com',
      'guid' => 'http://tech.sina.com.cn/roll/2007-02-03/0955237515.shtml',
      'category' => '科技新闻',
      'pubDate' => 'Sat, 3 Feb 2007 01:55:00 GMT',
      'comments' => '',
      'description' => '　　天极网2月3日消息(老沈 编译)，据外电报道，全球最大手机电信网络供应商爱立信公司周五报告称，公司在2006年盈利增长8.2%。但令市场失望的是，爱立信重新调低了2007年的盈利预期。

　　爱立信降低盈利预测的消息是对电信行业的进一步打击，就在上周，爱立信主要竞争对手阿....',
    ),
  ),
);
header('Content-Type: text/xml ');
$rss = new RSSGenerator($data);
$rss->generate('GB18030');
ECHO ($rss->saveXML());*/
?>