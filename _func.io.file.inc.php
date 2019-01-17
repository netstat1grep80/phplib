<?php
/**
 * @package io
 * @subpackage file
 * 处理文件目录相关
 */
/**
 * 过滤目录
 *
 * @param string $pattern
 * @param string $path
 * @return string[] - 返回匹配的文件名数组
 * 
 * @uses filterDir('*.php','/Data/webapps/application');
 * @uses filterDir('_class.xml.*','/Data/webapps/application/inc');
 */
function filterDir($pattern, $path){
	$ret = array();
	$dir = dir($path);
	$regex = '';
	$pattern = str_replace('.', '\.', $pattern);
	if(substr($pattern, -1)=='*'){
		$regex = '|'.substr($pattern, 0, -1).'.+|';
	}else if(substr($pattern, 0, 1)=='*'){
		$regex = '|.+'.substr($pattern, 1).'|';
	}else{
		$regex = '|'.substr($pattern, 0, -1).'\.inc\.php|';
	}
	
	while($entry = $dir->read()){
		//echo $entry."\n";
		if($entry=='.' || $entry=='..'){/*ignore*/ continue;}
		preg_match($regex, $entry, $match);
		
		if(count($match)>0){
			array_push($ret, $match[0]);
		}
	}
	$dir->close();
	return $ret;
}

//print_r(filterDir('*', 'e:/web/xguild/inc/'));

/**
 * 建立多级目录
 *
 * @param string $path
 * @return boolean
 */
function mkdirPro($path){
	$dirs = explode('/', $path);
	$dir = '';
	$flag = true;
	for($i=0; $i<count($dirs); $i++){
		$dir .= $dirs[$i].'/';
		if(!file_exists($dir)){
			$flag = mkdir($dir);
			if(!$flag){
				return false;
				break;
			}
		}
	}
	return true;
}

/**
 * 补全路径最后的'/'
 *
 * @param string $dir
 * @return string
 */
function fixDir($dir, $file_seperator='/'){
	if(substr($dir, -1)!= $file_seperator){
		$dir .= $file_seperator;
	}
	return $dir;
}

/**
 * 检查可以使用的文件名
 * 如果路径下已经有同名文件, 则自动选择一个编号文件名
 * 例如 abc.jpg已经存在, 则返回结果 abc.[1].jpg
 *
 * @param unknown_type $filename
 * @param unknown_type $path
 * @return unknown
 */
function checkAvailibleFileName($filename, $path){
	$regex = '/[\.]?(\[([\d]*)\])?[\.]?([a-z]*)$/i';
	$path = fixDir($path);
	do{
		$filepath = $path.$filename;
		if(file_exists($filepath)){
			preg_match($regex, $filename, $match);
			if(isset($match) && ($match[0]==$match[3] || 
								 empty($match[0]))){
				$filename = $filename.'[1]';
			}else{
				$right = $match[0];
				$left = substr($filename, 0, -strlen($right));
				$filename = (empty($left)?'':$left.'.')
						.'['.($match[2]+1).']'
						.(empty($match[3])?'':'.'.$match[3]);
			}
		}else{
			break;
		}
	}while(1);
	return $filename;
}

/**
 * 使用wget命令下载文件, 只能用于 linux/unix/bsd系统下
 *
 * @param string[] $urls - 下载地址列表
 * @param string $dir - 下载到的目录路径
 * @return string[] 磁盘上的文件名, 不可用
 */
function wgetFiles($urls, $dir){
	$regex = '/[\d]{2}:[\d]{2}:[\d]{2} URL:.* \[[\d]+[\/]?[\d]+\] -> \"(.*)\" \[\d+\]/i';
	$regex_err = '|[\d]{2}:[\d]{2}:[\d]{2} ERROR \d+:.*|';
	$filenames = array();		
	$cmd = 'wget --no-verbose ';
	foreach($urls AS $url){
		$cmd .= $url.' ';
	}
	exec($cmd, $output);
	
	foreach ($output AS $line){
		preg_match($regex, line, $match);
		if(count($match)==2){
			array_push($filenames, $match[1]);
		}else{
			preg_match($regex_err, line, $match);
			if(!empty($match)){
				array_push($filenames, NULL);
			}
		}
	}
	return $filenames;
}

/**
 * 获得文件路径信息
 *
 * @param string $path
 * @return string[] [0]路径目录 [1]主文件名 [2]扩展名
 */
function getFilePathInfo($path){
	$ret = array();
	$seperator = '/';
	$dot = '.';
	$pos1 = strrpos($path, $seperator);
	$pos2 = strrpos($path, $dot);
	
	if($pos1 !== false){
		$ret['path'] = substr($path, 0, $pos1+1);
		if($pos2 !== false && $pos2 > $pos1){
			$ret['name'] = substr($path, $pos1 +1, $pos2-$pos1-1);
			$ret['extension'] = substr($path, $pos2);
		}else{
			$ret['name'] = substr($path, $pos1 +1);
		}
	}else{
		if($pos2 !== false){
			$ret['name'] = substr($path, 0, $pos2);
			$ret['extension'] = substr($path, $pos2);
		}else{
			$ret['name'] = $path;
		}
	}	
	return $ret;
}

//var_dump(getFilePathInfo("/storage/blog/2007/05/31/2007-05-31-2.html"));

function getMimetype($path){
	$finfo = finfo_open(FILEINFO_MIME, "c:/php5/image");
	return finfo_file($finfo, $path);
}

function getMimetypeFromBuffer($buffer){
	$finfo = finfo_open(FILEINFO_MIME, "c:/php5/image");
	return finfo_buffer($finfo, $buffer);
}

function deltree($path, $charset='UTF-8'){
	if(strtoupper($charset) == 'UTF-8' ){
		$path = iconv('UTF-8', 'GB18030', $path);
	}
	if(file_exists('/root/.bash_profile')){ // linux
		$cmd = "rm $path -Rf";
	}else{ // windows
		$path = str_replace("/", "\\", $path);
		$cmd = "rd $path /S /Q";
	}
	
	exec($cmd, $output);
	return $output;
}

function file_put_contents2($file, $content){
	$info = getFilePathInfo($file);
	//var_dump($info['path']);
	$flag = mkdirPro(iconv('UTF-8', 'GB18030', $info['path']));
	if(!$flag){
		return false;
	}else{
		return file_put_contents(iconv('UTF-8', 'GB18030', $file), $content);
	}
}

?>