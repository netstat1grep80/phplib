<?php
/**
 * @package io
 * @subpackage file
 * 处理文件相关
 */
require_once('_func.io.file.inc.php');

/**
 * 上载类
 * 1. 移动上载文件时自动创建目标路径
 * 2. 移动上载文件时, 目标路径上如果已经有同名文件自动更名
 */
Class UploaderHelper{
	/**
	 * 上传文件数组
	 * @var string[]
	 */
	private $files;
	/**
	 * 目标路径
	 * @var string
	 */
	private $target;
	
	private $charset;
	
	/**
	 * 构造方法
	 *
	 * @param string $target
	 * @param string[] $files 如果为NULL, 则使用$_FILES
	 */
	public function __construct($target=NULL, $files = NULL, $charset='GB18030'){
		if($files==NULL){
			$this->files = & $_FILES;
		}else{
			$this->files = & $files;
		}
		$this->target = $target;
		$this->charset = $charset;
	}
	
	/**
	 * 获得上传文件名数组
	 *
	 * @return string[]
	 */
	public function getFileNames(){
		return array_keys($this->files);
	}
	
	/**
	 * 返回上传文件中的某一个文件信息
	 *
	 * @param string $fieldName 表单字段名
	 * @return string[]
	 */
	public function &getFile($fieldName){
		if(isset($_FILES[$fieldName])){
			if(is_array($_FILES[$fieldName]['error'])){
				$files = self::convertOldFiles($_FILES[$fieldName]);
				return $files;
			}else{
				if($_FILES[$fieldName]['error']==UPLOAD_ERR_OK){
					return $_FILES[$fieldName];
				}else{
					$this->triggerError($this->getErrorMessage($_FILES[$fieldName]['error']));
					return NULL;
				}
			}
		}else{
			$this->triggerError('表单中没有这个字段');
			return NULL;
		}
	}
	
	/**
	 * 触发错误
	 *
	 * @param string $msg
	 * @param int $errorType
	 */
	public function triggerError($msg, $errorType=E_USER_WARNING){
		trigger_error($msg, $errorType);	
	}
	
	/**
	 * 错误消息
	 *
	 * @param int $errorCode
	 * @return string
	 */
	public function getErrorMessage($errorCode){
		$ret = '';
		switch($errorCode){
			case UPLOAD_ERR_OK:
				$ret = '上传成功';
				break;
			case UPLOAD_ERR_INI_SIZE:
				$ret = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$ret = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
				break;
			case UPLOAD_ERR_PARTIAL:
				$ret = '文件上传不完整';
				break;
			case UPLOAD_ERR_NO_FILE:
				$ret = '没有文件被上传';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$ret = '找不到临时文件夹';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$ret = '文件写入失败';
				break;
			default:
				break;
		}
		return $ret;
	}
	
	/**
	 * 把上传文件移动到指定文件夹
	 *
	 * @param string $fieldName 	表单字段名
	 * @param string $targetName 	目标文件名
	 * @param string $target 		目标路径, 如果为空则使用$this->target
	 * @param boolean $override		是否覆盖目标，如果false，并且目标存在，将检查下一个可用的用户名
	 * @return string 				目标文件名
	 */
	public function move($fieldName, $targetName=NULL, $target=NULL, $override=false){
		$file = $this->getFile($fieldName);
		if($target==NULL){
			$target = $this->target;
			$target = fixDir($target);
		}	
		
		if(!file_exists($target)){
			if(strtoupper($this->charset) != 'GB18030'){
				$flag = mkdirPro(iconv($this->charset, 'GB18030', $target));
			}else{
				$flag = mkdirPro($target);
			}
			
		}
		
		if(isset($file['name'])){
			$files[] = $file;
		}else{
			$files = $file;
		}
		$targetNames = array();
		foreach($files AS $file){
			$tn = ($targetName==NULL)?$file['name']:$targetName;
			if(!$override){
				$tn = checkAvailibleFileName($tn, $target);
			}
			$targetNames[] = $tn;
			$path = $target.$tn;
			if(strtoupper($this->charset) != 'GB18030'){
				$path = iconv($this->charset, 'GB18030', $path);
			}
			$flag = move_uploaded_file($file['tmp_name'], $path);
		}
		
		if($flag){
			return $targetNames;
		}else{
			return NULL;
		}
	}
	
	/**
	 * 移动全部文件到目标路径
	 *
	 * @param string $target
	 */
	public function moveAll( $target=NULL){
		$ret = array();
		foreach($this->files AS $field=>$file){
			$names = $this->move($field, NULL, $target);
			$ret[$field] = $names;
		}
		return $ret;
	}
	
	public static function convertOldFiles($files){
		$ret = array();
		for($i=0; $i<count($files['error']); $i++){
			if($files['name'][$i]){
				$ret[$i]['name'] = $files['name'][$i];
				$ret[$i]['type'] = $files['type'][$i];
				$ret[$i]['tmp_name'] = $files['tmp_name'][$i];
				$ret[$i]['error'] = $files['error'][$i];
				$ret[$i]['size'] = $files['size'][$i];
			}
		}
		return $ret;
	}
	
}

/**
 * 文件类
 *
 */
class FileHelper{
	const BUFFER = 4096;
	
	private $handler;
	private $path;
	private $content;
	
	/**
	 * 构造方法
	 *
	 * @param string $path 文件路径
	 */
	public function __construct($path){
		$this->path = $path;
	}
	
	public function __destruct(){
		$this->close();
	}
	
	/**
	 * 获得文件句柄
	 *
	 * @param string $mode {@inline fopen}
	 * @return resource
	 */
	public function openStream($mode='r'){
		$this->close();
		return $this->handler = fopen($this->path, $mode);
	}
	
	/**
	 * 逐行读取文件
	 *
	 * @return string
	 */
	public function readLine(){
		return fgets($this->handler, self::BUFFER );
	}
	
	/**
	 * 按BUFFER读取文件
	 *
	 * @return string
	 */
	public function read(){
		return fread($this->handler, self::BUFFER );
	}
	
	/**
	 * 写操作
	 *
	 * @param string $str
	 */
	public function write($str){
		fwrite($this->handler, $str);
	}
	
	/**
	 * 将缓冲内容输出到文件
	 *
	 */
	public function flush(){
		fflush($this->handler);
	}
	
	/**
	 * 获得整个文件的内容, 同file_get_contents
	 *
	 * @return string
	 */
	public function content(){
		if($this->content != NULL){
			return $this->content;
		}
		while(!$this->eof()){
			$line = $this->readLine();
			$this->content .= $line;
		}
		return $this->content;
	}
	
	/**
	 * 判断是否到文件尾部
	 *
	 * @return boolean
	 */
	public function eof(){
		return feof($this->handler);
	}
	
	/**
	 * 过滤文件内容, 并返回匹配数组
	 *
	 * @param string $pattern
	 * @return string[]
	 */
	public function &filter($pattern){
		preg_match_all($pattern, $this->getContent(), $match);
		return $match;
	}
	
	/**
	 * 复制
	 *
	 * @param string $target
	 * 
	 * @return boolean 
	 */
	public function copy($target){
		return copy($this->path, $target);
	}
	
	/**
	 * 移动
	 *
	 * @param string $target
	 * 
	 * @return boolean
	 */
	public function move($target){
		return rename($this->path, $target);
	}
	
	/**
	 * 删除
	 * 
	 * @return boolean
	 */
	public function delete(){
		return unlink($this->path);
	}
	
	/**
	 * 判断文件是否存在
	 *
	 * @return boolean
	 */
	public function exists(){
		return file_exists($this->path);
	}
	
	/**
	 * 关闭文件流
	 * 
	 * @return boolean
	 *
	 */
	public function close(){
		return @fclose($this->handler);
	}
	
	/**
	 * 是否是目录
	 *
	 * @return boolean
	 */
	public function isDir(){
		return is_dir($this->path);
	}
	
	/**
	 * 返回文件类型
	 *
	 * @return string
	 */
	public function getType(){
		return filetype($this->path);
	}
	
	/**
	 * 获得文件尺寸
	 *
	 * @return int
	 */
	public function getSize(){
		return filesize($this->path);
	}
	
	/**
	 * 获得最后更新时间, 格式为UNIX 时间戳
	 *
	 * @return int
	 */
	public function getModifyTime(){
		return filemtime($this->pah);
	}
	
	/**
	 * 获得文件信息
	 * 
	 * @see stat
	 * @return string[]
	 */
	public function getStat(){
		return stat($this->path);
	}
}
?>