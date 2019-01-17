<?php
require_once('_func.util.common.inc.php');
class Image{
	private $image;
	/**
	 * 
	 *
	 * @var Graphic
	 */
	private $graphic;
	
	const H_LEFT 	= 0x0;
	const H_RIGHT 	= 0x1;
	const H_CENTER 	= 0x3;
	const V_TOP		= 0x0;
	const V_BOTTOM	= 0x10;
	const V_MIDDLE	= 0x30;
	
	function __construct(){
		$args = func_get_args();
		if(count($args)==1){
			$this->image = ImageIO::read($args[0]);
		}else{
			$this->image = ImageIO::create($args[0], $args[1]);
		}
		
		$this->graphic = new Graphic($this);
	}
	
	function __destruct(){
		if(asserting($this->image)){
			imagedestroy($this->image);
		}
	}
	
	function scale($max_width, $max_height){
		$srcWidth = $this->getWidth();
		$srcHeight = $this->getHeight();
		
		$desWidth = $max_width;
		$desHeight = $max_height;
		
		$whratio = $srcWidth/$srcHeight;
		
		if($max_width >0 && $max_height>0){
			if($srcWidth<$max_width && $srcHeight<$max_height){
				$desWidth = $srcWidth;
				$desHeight = $srcHeight;
			}else if($max_width/$whratio < $max_height){
				$desHeight = (int)($max_width / $whratio);
			}else{
				$desWidth = (int)($max_height * $whratio);
			}
		}elseif($max_width>0 && $max_height<0){
			if($srcWidth<$max_width){
				$desWidth = $srcWidth;
			}
			$desHeight = (int)($srcWidth / $whratio);
		}elseif($max_width<0 && $max_height>0){
			if($srcHeight<$max_height){
				$desHeight = $srcHeight;
			}
			$desWidth = (int)($desHeight * $whratio);
		}else{
			// do nothing
		}
				
		$desIm = new Image($desWidth, $desHeight);
		
		$desIm->getGraphic()->drawImage($this,
			array(0, 0, $srcWidth, $srcHeight),
			array(0, 0, $desWidth, $desHeight)
			);
		return $desIm;
	}
	
	function watermark($wm, $pos){
		$desIm = new Image($this->getWidth(), $this->getHeight());
		$desIm->getGraphic()->drawImage($this);
		$desIm->getGraphic()->drawImage(
			$wm, 
			array(),
			array($pos['x'], $pos['y'])
		);
		return $desIm;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Image $wm
	 * @param int $pos
	 */
	function watermark2($wm, $pos){
		$wm_width = $wm->getWidth();
		$wm_height = $wm->getHeight();
		
		$width = $this->getWidth();
		$height = $this->getHeight();
		
		$hor = $pos % 16;
		$ver = (int)($pos / 16);
		$x = $y = 0;
		switch($hor){
			default:
			case self::H_LEFT :
				$x = 0;
				break;
			case self::H_RIGHT :
				$x = $width - $wm_width;
				break;
			case self::H_CENTER :
				$x = (int)(($width - $wm_width)/2);
				break;
		}
		
		switch ($ver * 16){
			default:
			case self::V_TOP :
				$y = 0;
				break;
			case self::V_BOTTOM :
				$y = $height - $wm_height;
				break;
			case self::V_MIDDLE :
				$y = (int)(($height-$wm_height)/2);
				break;
		}
		
		return $this->watermark($wm, array("x"=>$x, 'y'=>$y));
		
	}
	
	function getWidth(){
		return imagesx($this->image);
	}
	
	function getHeight(){
		return imagesy($this->image);
	}
	
	function getImage(){
		return $this->image;
	}
	/**
	 * Enter description here...
	 *
	 * @return Graphic
	 */
	function getGraphic(){
		return $this->graphic;
	}
}

class ImageIO{
	static function read($path){
		$finfo = finfo_open(FILEINFO_MIME, "c:/php5/image");
		$mime = finfo_file($finfo, $path);
		$image = null;
		switch($mime){
			case 'image/jpeg':
				$image = imagecreatefromjpeg($path);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($path);
				break;
			case 'image/x-png':
				$image = imagecreatefrompng($path);
				break;			
			default:
				break;
		}
		return $image;
	}
	
	static function write($image, $mime, $path=NULL){
		if($image instanceof Image ){
			$image = $image->getImage();
		}
		if($path==NULL){ 	// output to browser
			header("Content-type: " . image_type_to_mime_type($mime));
			switch($mime){
				case IMAGETYPE_GIF:
				case 'image/gif':
					imagegif($image);
					break;
				case IMAGETYPE_JPEG:
				case 'image/jpeg':
					imagejpeg($image);
					break;
				case IMAGETYPE_PNG:
				case 'image/x-png':
					imagepng($image);
					break;
			}
		}else{
			switch($mime){
			case IMAGETYPE_GIF:
			case 'image/gif':
				imagegif($image, $path);
				break;
			case IMAGETYPE_JPEG:
			case 'image/jpeg':
				imagejpeg($image, $path);
				break;
			case IMAGETYPE_PNG:
			case 'image/x-png':
				imagepng($image, $path);
			}	
		}
	}
	
	static function create($width, $height){
		return imagecreatetruecolor($width, $height);
	}
}

class Graphic{
	/**
	 * 
	 *
	 * @var Image
	 */
	private $image;
	function __construct($im){
		$this->image = $im;
	}
	
	function getImage(){
		return $this->image;
	}
	
	function drawImage($src, $srcInfo=NULL, $desInfo=NULL){
		if($desInfo == NULL){$desInfo = array();}
		if($srcInfo == NULL){$srcInfo = array();}
		
		$srcInfo[0] = isset($srcInfo[0])?$srcInfo[0]:0;
		$srcInfo[1] = isset($srcInfo[1])?$srcInfo[1]:0;
		$srcInfo[2] = isset($srcInfo[2])?$srcInfo[2]:$src->getWidth();
		$srcInfo[3] = isset($srcInfo[3])?$srcInfo[3]:$src->getHeight();
		
		imagecopyresampled(
			$this->getImage()->getImage(), 
			$src->getImage(),
			isset($desInfo[0])? $desInfo[0]:$srcInfo[0],
			isset($desInfo[1])? $desInfo[1]:$srcInfo[1],
			$srcInfo[0],
			$srcInfo[1],
			isset($desInfo[2])?$desInfo[2]:$srcInfo[2],
			isset($desInfo[3])?$desInfo[3]:$srcInfo[3],
			$srcInfo[2],
			$srcInfo[3] 
		);
	}
}

//$im = new Image("e:/images/KIF_3992.JPG");
//var_dump($im instanceof Image);

?>