<?php
/**
 * @package io
 *
 */
require_once('_class.http.Cookie.inc.php');
require_once('_func.text.sig.inc.php');
require_once('_func.http.page.inc.php');

//define('VCODE_RESOURCE_PATH', 'e:/web/authimg/');
//define('VCODE_BACKGROUNDS', '200511_a_1600.jpg,200506_horde_1600.jpg,1000-needles-1600x.jpg');
//define('VCODE_FONTS', 'ariblk.ttf,ninab.ttf,trebucbi.ttf');

/**
 * 生成验证码图片
 * <br/>需要定义以下常量
 * <code>
 * define('VCODE_RESOURCE_PATH', 'e:/web/authimg/'); // 资源根目录
 * define('VCODE_BACKGROUNDS', '200511_a_1600.jpg,200506_horde_1600.jpg,1000-needles-1600x.jpg'); // 背景图片资源列表，半角逗号分割
 * define('VCODE_FONTS', '1.ttf,2.ttf,3.ttf,4.ttf'); // 字体资源列表，半角逗号分割
 * </code>
 * 
 * 
 */
class VerificationCode{
	/**
	 * 图片句柄
	 *
	 * @var int
	 */
	private $image;
	/**
	 * 宽度
	 *
	 * @var int
	 */
	private $width;
	/**
	 * 高度
	 *
	 * @var int
	 */
	private $height;
	/**
	 * 背景色
	 *
	 * @var int
	 */
	private $backgroundColor;
	/**
	 * 边框色
	 *
	 * @var int
	 */
	private $borderColor;
	/**
	 * 文本颜色
	 *
	 * @var int
	 */
	private $textColor;
	/**
	 * 文本边框颜色
	 *
	 * @var int
	 */
	private $textBorderColor;
	/**
	 * 滤镜集合
	 *
	 * @var int[][]
	 */
	private $filters;
	/**
	 * 验证码文本
	 *
	 * @var string
	 */
	private $text;
	/**
	 * 过期时间(秒)
	 *
	 * @var int
	 */
	private $expire;
	
	public static $rand_string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * 构造方法
	 *
	 * @param string $text
	 * @param int $cell 单格宽高
	 * @param int[] $bgc 背景色
	 * @param int[] $bc 边框颜色
	 * @param int[] $tc 文本颜色
	 * @param int[] $tbc 文本边框颜色
	 */
	public function __construct($text, $cell, 
								$bgc = array(255, 0, 0, 0),
								$bc = array(255, 255, 255, 64),
								$tc = array(0, 0, 0, 0),
								$tbc = array(255, 255, 255, 0),
								$randstr = ''
								){
		$this->text = $text;
		
		$this->width = $cell * strlen($this->text) *1.2;
		$this->height = $cell*1.2;
		$this->image = imagecreatetruecolor($this->width, $this->height);
		$this->backgroundColor = imagecolorallocatealpha($this->image, $bgc[0], $bgc[1], $bgc[2], (isset($bgc[3])?$bgc[3]:0));
		
		$this->borderColor = imagecolorallocatealpha($this->image, $bc[0], $bc[1], $bc[2], (isset($bc[3])?$bc[3]:0));
		
		$this->textColor = imagecolorallocatealpha($this->image, $tc[0], $tc[1], $tc[2], (isset($tc[3])?$tc[3]:0));
		$this->textBorderColor = imagecolorallocatealpha($this->image, $tbc[0], $tbc[1], $tbc[2], (isset($tbc[3])?$tbc[3]:0));
		
		$this->filters = array(
			array(1=>IMG_FILTER_NEGATE),
			array(1=>IMG_FILTER_GAUSSIAN_BLUR),
			array(1=>IMG_FILTER_EMBOSS),
			array(1=>IMG_FILTER_GRAYSCALE),
			array(1=>IMG_FILTER_MEAN_REMOVAL),
			array(1=>IMG_FILTER_SMOOTH, 20),
			array(1=>IMG_FILTER_COLORIZE, 255, 0, 0),
			array(1=>IMG_FILTER_COLORIZE, 0, 255, 0),
			array(1=>IMG_FILTER_COLORIZE, 0, 0, 255)
		);
		
		
		
	}
	
	/**
	 * 构析方法
	 *
	 */
	public function __destruct(){
		imagedestroy($this->image); // destroy
	}
	
	/**
	 * 输出图片
	 *
	 * @param string $type 可选值：jpeg/jpg/gif/png
	 * @return void
	 */
	public function output($type='jpg'){
		switch($type){
			case 'jpg':
			case 'jpeg':
				self::outputHTTP('jpeg', "imagejpeg", $this->image);
				break;
			case 'gif':
				self::outputHTTP('gif', "imagegif", $this->image);
				break;
			case 'png':
				self::outputHTTP('png', "imagepng", $this->image);
				break;
		}
	}
	
	/**
	 * 生成图片
	 *
	 * @param boolean $withBackground 	是否画背景
	 * @param boolean $withBorder		是否画边框
	 * @param boolean $withFilter		是否使用滤镜渲染图片
	 * @return void
	 */
	public function draw($withBackground=true, $withBorder=true, $withFilter=true){
		imagefill($this->image, 0, 0, $this->backgroundColor);
		if($withBackground){
			$this->drawBackground();
		}
		
		if($withBorder){
			$this->drawBorder();
		}
		$this->drawText();
		if($withFilter){
			$this->renderFilter();
		}
	}
	
	/**
	 * 画背景
	 * 
	 * @return void
	 */
	public function drawBackground(){
		$backgrounds = split(',', VCODE_BACKGROUNDS);
		$background = VCODE_RESOURCE_PATH.$backgrounds[rand(0, count($backgrounds)-1)];
	
		$bg = imagecreatefromjpeg($background);
		$bx = imagesx($bg);
		$by = imagesy($bg);
		$posx = rand(0, $bx-$this->width);
		$posy = rand(0, $by-$this->height);
		imagecopy($this->image, $bg, 0, 0, $posx, $posy, $this->width, $this->height);
		imagedestroy($bg);
		
	}
	
	/**
	 * 画边框
	 * 
	 * @return void
	 */
	public function drawBorder(){
		self::line($this->image, 0, 0, $this->width, 0, $this->borderColor, $this->height/20);
		self::line($this->image, 0, 0, 0, $this->height, $this->borderColor, $this->height/20);
		self::line($this->image, $this->width, 0, $this->width, $this->height, $this->borderColor, $this->height/20);
		self::line($this->image, 0, $this->height, $this->width, $this->height, $this->borderColor, $this->height/20);
		
		for($i=1; $i<strlen($this->text); $i++){
			self::line($this->image, $this->height*$i, 0, $this->height*$i, $this->height, $this->borderColor, $this->height/20);
		}
	}
	
	/**
	 * 画文字
	 * 
	 * @return void
	 */
	public function drawText(){
		$fonts = split(',', VCODE_FONTS);
		$size = (int)($this->height * 0.6);
		for($i=0; $i<strlen($this->text); $i++){
			$angle = rand(-30, 30);
			$font = VCODE_RESOURCE_PATH.$fonts[rand(0, count($fonts)-1)];
			$x = $i*$this->height + $this->height*0.2;
			$y = $this->height * 0.8;
			$this->drawChar($size, $angle, $x, $y, $font, $this->text[$i]);
		}
		
	}
	
	/**
	 * 画单个字符
	 *
	 * @param int $size 		字体大小
	 * @param int $angle		旋转角度
	 * @param int $x			x位置
	 * @param int $y			y位置
	 * @param string $font		ttf字体路径
	 * @param string $c			字符
	 * @return void
	 */
	public function drawChar($size, $angle, $x, $y, $font, $c){
		$border = 2;
		//imagettftext($this->image, $size, $angle, $x+2, $y+2, $this->textColor1, $font, $c);
		imagettftext($this->image, $size, $angle, $x+$border, $y, $this->textBorderColor, $font, $c);
		imagettftext($this->image, $size, $angle, $x-$border, $y, $this->textBorderColor, $font, $c);
		imagettftext($this->image, $size, $angle, $x, $y+$border, $this->textBorderColor, $font, $c);
		imagettftext($this->image, $size, $angle, $x, $y-$border, $this->textBorderColor, $font, $c);
		imagettftext($this->image, $size, $angle, $x, $y, $this->textColor, $font, $c);
	}
	
	/**
	 * 使用随机滤镜渲染
	 * 
	 * @return void
	 *
	 */
	public function renderFilter(){
		if(function_exists("imagefilter")){
			$filterIndex = rand(0, count($this->filters)-1);
			$filter = $this->filters[$filterIndex];
			$filter = array_merge(array($this->image), $filter);
			call_user_func_array('imagefilter', $filter);
		}
		//imagefilter($this->image, IMG_FILTER_NEGATE);
	}
	/**
	 * not implemented
	 *
	 */
	public function drawNoise(){
		
	}
	
	/**
	 * http输出图片
	 *
	 * @param string $type		图片类型
	 * @param string $func		输出的函数名
	 * @param int $resource 	图片句柄
	 */
	public static function outputHTTP($type, $func, $resource){
		header("Content-type: image/$type");
		header("Expire: -1");
		header("Pragma: no-cache");
		header("Cache-control: no-store");
		
		$func($resource);
	}
	
	/**
	 * 画带宽度的直线
	 *
	 * @param int $img			图片句柄
	 * @param int $start_x		起始点x坐标
	 * @param int $start_y		起始点y坐标
	 * @param int $end_x		终点x坐标
	 * @param int $end_y		终点y坐标
	 * @param int $color		线条颜色
	 * @param int $thickness	线条宽度
	 */
	public static function line($img,$start_x, $start_y, $end_x, $end_y, $color, $thickness)
	{
		//$color = imagecolorallocatealpha($img, $color[0], $color[1], $color[2], (isset($color[3])?$color[3]:0));
		$angle=(atan2(($start_y - $end_y),($end_x - $start_x)));

		$dist_x=$thickness*(sin($angle));
		$dist_y=$thickness*(cos($angle));

		$p1x=ceil(($start_x + $dist_x));
		$p1y=ceil(($start_y + $dist_y));
		$p2x=ceil(($end_x + $dist_x));
		$p2y=ceil(($end_y + $dist_y));
		$p3x=ceil(($end_x - $dist_x));
		$p3y=ceil(($end_y - $dist_y));
		$p4x=ceil(($start_x - $dist_x));
		$p4y=ceil(($start_y - $dist_y));

		$array=array(0=>$p1x,$p1y,$p2x,$p2y,$p3x,$p3y,$p4x,$p4y);
		imagefilledpolygon ( $img, $array, (count($array)/2), $color );
	}
	
	/**
	 * 加密
	 *
	 * @param string $value
	 * @param string $expire
	 * @param string $encrypt_key
	 * @param string $encrypt_vector
	 * @return string
	 */
	public static function encode($value, $expire, $encrypt_key, $encrypt_vector){
		// setcookie
		//$cookie = new Cookie();
		//$cookie->setCookie($name, tripleEncode($value.time(), $encrypt_key, $encrypt_vector), $expire, NULL, $domain);
		return tripleEncode($value.(time()+$expire), $encrypt_key, $encrypt_vector);
	}
	
	/**
	 * 解密
	 *
	 * @param string $code
	 * @param string $code_length
	 * @param string $encrypt_key
	 * @param string $encrypt_vector
	 * @return string
	 */
	public static function decode($code, $code_length, $encrypt_key, $encrypt_vector){
		$decode = tripleDecode($code, $encrypt_key, $encrypt_vector);
		$value = substr($decode, 0, $code_length);
		$expire = substr($decode, $code_length);
		if(((int)$expire==$expire) && $expire>time()){
			return $value;
		}else{
			return false;
		}
	}
	
	/**
	 * 验证
	 *
	 * @param string $input
	 * @param string $cookieName
	 * 
	 * @return boolean
	 */
	public static function validate($input, $code, $key, $vector){
		$len = strlen($input);
		$decode = tripleDecode($code, $key, $vector);
		//echo $decode;
		$str = substr($decode, 0, $len);
		$expire = substr($decode, $len);
		return ((int)$expire==$expire) && (strtoupper($input)==$str) && ($expire>time());
	}
	
	public static function randomVcode($len=4){
		$str = !empty(self::$rand_string)?strtoupper(self::$rand_string):'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$ret = '';
		for($i=0; $i<$len; $i++){
			$ret .= $str[rand(0, strlen($str)-1)];
		}
		return $ret;
	}
}


/**
 * 
 * @param string $vcode
 * @param IDatabase $db
 * @param string $table
 * @param string $cookie_name
 * @param int $cookie_life
 * @param string $cookie_domain
 * @param string $cookie_path
 * @param string $field_id
 * @param string $field_value
 * @param string $field_createtime
 * 
 * @return boolean
 */
function vcode_set($vcode,
                   $db = null,
                   $table = null,
                   $cookie_name,
                   $cookie_life = null,
                   $cookie_domain = null,
                   $cookie_path = null,
                   $field_id="vc_sid",
                   $field_value="vc_value",
                   $field_createtime="vc_createtime")
{
    if(!$db) return false;
    if(isset($_COOKIE[$cookie_name])){
        vcode_delete($db, $table, $cookie_name, $cookie_life, $cookie_domain, $cookie_path, $field_id);
    }
    $cookie_value = substr(md5(uniqid(rand())), -8);
    $sql = "insert into ${table} (${field_id}, ${field_value}, ${field_createtime}) values (&id, &value, &time)";
    $params = array(
        "id"=>$cookie_value,
        "value"=>$vcode,
        "time"=>time()
    );
    $flag = $db->query($sql, $params);
    if($flag){
        setcookie($cookie_name, $cookie_value, null, $cookie_path, $cookie_domain );
        return true;
    }else
        return false;
}

/**
 * 
 * @param string $vcode
 * @param string $cookie_name
 * @param int $cookie_life
 * @param string $cookie_domain
 * @param string $cookie_path
 * @param IDatabase $db
 * @param string $table
 * @param string $field_id
 * @param string $field_value
 * @param string $field_createtime
 * 
 * @return boolean
 */
function vcode_get($db = null,
                   $table = null,
                   $cookie_name,
                   $cookie_life = null,
                   $cookie_domain = null,
                   $cookie_path = null,
                   $field_id="vc_sid",
                   $field_value="vc_value",
                   $field_createtime="vc_createtime")
{
    if(!$db) return false;
    if(!$_COOKIE[$cookie_name]) return false;
    $sql = "select ${field_id} as id, ${field_value} as value, ${field_createtime} as createtime from ${table} where ${field_id}=&sid";
    $params = array(
        "sid"=>$_COOKIE[$cookie_name]
    );
    $row = $db->fetchRow($sql, $params);
    $ret = false;
    if($row){
        $row = array_change_key_case($row);
        if($row["createtime"]+$cookie_life>time()){
            $ret = $row["value"];
        }
        vcode_delete($db, $table, $cookie_name, $cookie_life, $cookie_domain, $cookie_path, $field_id);
        return $ret;
    }
}


/**
 * 
 *
 * @param IDatabase $db
 * @param string $table
 * @param int $cookie_life
 * @param string $cookie_domain
 * @param string $cookie_path
 * @param string $field_id
 */
function vcode_delete( $db = null,
                       $table = null,
                       $cookie_name = null,
                       $cookie_life = null,
                       $cookie_domain = null,
                       $cookie_path = null,
                       $field_id="vc_sid" )
{
    $sql = "delete from ${table} where ${field_id}=&sid";
    $params = array(
        "sid"=>$_COOKIE[$cookie_name]
    );
    $db->query($sql, $params);
    setcookie($cookie_name, null, time()-1, $cookie_path, $cookie_domain);
}

/**
 * 
 * @param string $vcode
 * @param Zend_Cache_Core $cache
 * @param string $cookie_name
 * @param int $cookie_life
 * @param string $cookie_domain
 * @param string $cookie_path
 * 
 * @return boolean
 */
function vcode_set2($vcode,
				   $cache, 
				   $cookie_name, 
				   $cookie_life = null, 
				   $cookie_domain = null, 
				   $cookie_path = '/')
{
	if(!$cache) return false;
	if(isset($_COOKIE[$cookie_name])){
		vcode_delete2($cache, $cookie_name, $cookie_domain, $cookie_path);
	}
	$cookie_value = substr(md5(uniqid(rand())), -8);
	$cache_key = 'VCODE_'.$cookie_value;
	$flag = $cache->save($vcode, $cache_key, array(), $cookie_life);
	if($flag){
	#	setcookie($cookie_name, $cookie_value, null, $cookie_path, $cookie_domain );
		setcookie($cookie_name, $cookie_value, null, $cookie_path, $cookie_domain );
		return true;
	}else {
		return false;
	}
}

/**
 * @param Zend_Cache_Core $cache
 * @param string $cookie_name
 * @param string $cookie_domain
 * @param string $cookie_path
 * 
 * @return boolean
 */
function vcode_get2($cache = null, 
				   $cookie_name, 
				   $cookie_domain = null, 
				   $cookie_path = '/')
{
	if(!$cache) return false;
	if(!isset($_COOKIE[$cookie_name])) return false;
	$cache_key = 'VCODE_'.$_COOKIE[$cookie_name];
	$ret = $cache->load($cache_key);
	if($ret){
		vcode_delete2($cache, $cookie_name, $cookie_domain, $cookie_path);
		return $ret;
	}
}

function vcode_get_string($cache, $name){
	if(!$cache) return false;
	$cache_key = 'VCODE_'.$name;
	$ret = $cache->load($cache_key);
	if($ret){
		$cache->remove($cache_key);
		return $ret;
	}
}

/**
 * 
 *
 * @param Zend_Cache_Core $cache
 * @param string $cookie_name
 * @param int $cookie_life
 * @param string $cookie_domain
 * @param string $cookie_path
 * @param string $field_id
 */
function vcode_delete2( $cache=null,
					   $cookie_name = null,
					   $cookie_domain = null, 
					   $cookie_path = null )
{
	if(!$cache) return false;
	$cache_key = 'VCODE_'.$_COOKIE[$cookie_name];
	$cache->remove($cache_key);
	setcookie($cookie_name, null, time()-1, $cookie_path, $cookie_domain);					   	
}

//$value = '1234CD';
//$vcode = new VerificationCode($value, 40);
//VerificationCode::setcookie('verification_code', $value, 60);
//$vcode->draw(true, true, true);
//$vcode->output();
?>
