<?php
/**
 * @package text
 * @subpackage sig
 * 加密解密相关
 */
define("ENCRYPTION_KEY", "xxxkeyxxx");
define("ENCRYPTION_MOD", 17);
define("ENCRYPTION_ADJ", 5.17);
define("MZW_AUTH_KEY","MZW");

function discuz_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)

{

    $ckey_length = 4;

    // 随机密钥长度 取值 0-32;

    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。

    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方

    // 当此值为 0 时，则不产生随机密钥



    $key = md5($key ? $key : MZW_AUTH_KEY);

    $keya = md5(substr($key, 0, 16));

    $keyb = md5(substr($key, 16, 16));

    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';



    $cryptkey = $keya.md5($keya.$keyc);

    $key_length = strlen($cryptkey);



    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;

    $string_length = strlen($string);



    $result = '';

    $box = range(0, 255);



    $rndkey = array();

    for($i = 0; $i <= 255; $i++)

    {

        $rndkey[$i] = ord($cryptkey[$i % $key_length]);

    }



    for($j = $i = 0; $i < 256; $i++)

    {

        $j = ($j + $box[$i] + $rndkey[$i]) % 256;

        $tmp = $box[$i];

        $box[$i] = $box[$j];

        $box[$j] = $tmp;

    }



    for($a = $j = $i = 0; $i < $string_length; $i++)

    {

        $a = ($a + 1) % 256;

        $j = ($j + $box[$a]) % 256;

        $tmp = $box[$a];

        $box[$a] = $box[$j];

        $box[$j] = $tmp;

        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));

    }



    if($operation == 'DECODE')

    {

        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))

        {

            return substr($result, 26);

        }

        else

        {

            return '';

        }

    }

    else

    {

        return $keyc.str_replace('=', '', base64_encode($result));

    }

}

/**
 * 加密整型数(不可逆)
 *
 * @param int $id - 待加密的整型数
 * @return string - 加密以后的字符串
 */
function sigEncode($id)
{
	return strtoupper(md5(substr(EncodeStrStatic(-$id*7),-8)));
}

/**
 * 判断整型数与密码是否匹配
 *
 * @param int $id - 待判断的整型数
 * @param string $sig - 密码
 * @return boolean - true 匹配, false 不匹配
 */
function sigIsMatch($id, $sig)
{
	return (strtoupper($sig)==sig_encode($id));
}

/**
 * 加密整型数(包含IP, 日期, 不可逆)
 * 
 * sample
 * <code>
 * $sid = ssn_encode(517);
 * </code>
 *
 * @param int $id - 待加密的整型数
 * @param timestamp $time - 时间戳, 缺省为当前时间
 * @param string $ip - IP地址, 缺省为remote addr
 * @return string - 加密以后的session标识
 */
function ssnEncode($id, $time=null, $ip=null)
{
	if (!$time) $time=time();
	if (!$ip) $ip=GetIp();
	return strtoupper(substr(md5(substr(EncodeStrStatic((-$id*7).date("md",$time).$ip),-8)),-10));
}

/**
 * 判断整型数与session标识是否匹配, 
 * 并可返回当前session标识(每天早上5点之前会再判断是否与前一天匹配, 以便延续人传统"天"的概念)
 * <code>
 * 	if ($sid = ssn_isvalid($id, $sid))
 *		continue_do_sth;
 *	else
 *		not_valid_session_id;
 * </code>
 *
 * @param int $id - 待判断的整型数
 * @param string $ssn - session标识
 * @return string - 若匹配, 返回以当前时间为准的session标识, null 不匹配
 */
function ssnIsValid($id, $ssn)
{
	$time=time();
	$timeinfo=getdate($time);
	$ssnalt=strtoupper($ssn);
	if (($timeinfo["hours"]<5) && (ssn_encode($id, $time-86400)==$ssnalt))
		return ssn_encode($id);
	if (ssn_encode($id)==$ssnalt)
		return $ssnalt;
	return null;
}

function CheckIpIsVaild($ip){
    return !strcmp(long2ip(sprintf('%u',ip2long($ip))),$ip) ? true : false;
}
/**
 * 解析ip
 *
 * @return string
 */
/*
function getIp()
{
	return $_SERVER['REMOTE_ADDR'];
}
*/

function getAccessIP()
{
    //获取IP

    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    else if (isset($_SERVER["HTTP_CLIENT_IP"]))
    {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    else if (isset($_SERVER["REMOTE_ADDR"]))
    {
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    else if (getenv("HTTP_X_FORWARDED_FOR"))
    {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    }
    else if (getenv("HTTP_CLIENT_IP"))
    {
        $ip = getenv("HTTP_CLIENT_IP");
    }
    else if (getenv("REMOTE_ADDR")) //开源代码OSPhP.COm.CN
    {
        $ip = getenv("REMOTE_ADDR");
    }
    else
    {
        $ip = "Unknown";
    }
    return CheckRightIP($ip) ? $ip : "Unkonwn";
}

function CheckRightIP($ip){
    return !strcmp(long2ip(sprintf('%u',ip2long($ip))),$ip) ? true : false;
}
/*
function getIp()
{
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return($ip);
}
*/
/**
 * 加密文本
 *
 * @param string $text
 * @return string
 */
function encodeStrStatic($text)
{
	$crypt = new MyEncryption;
	$crypt->setAdjustment(ENCRYPTION_ADJ);
	$crypt->setModulus(ENCRYPTION_MOD); 
	$encrypt_result = $crypt->encrypt(ENCRYPTION_KEY, $text, strlen($text));  
	return $encrypt_result;
}

/**
 * 解密文本
 *
 * @param string $text
 * @return string
 */
function decodeStrStatic($text)
{
	$crypt = new MyEncryption;
	$crypt->setAdjustment(ENCRYPTION_ADJ);
	$crypt->setModulus(ENCRYPTION_MOD); 
	$decrypt_result = $crypt->decrypt(ENCRYPTION_KEY, $text); 
	return $decrypt_result;
}

function encodeStr($content)
{
/*	$text = serialize($content);
	$key = date("Ymd");
	$pswdlen = strlen($text);
	$adj = date("n.j");
	
	$crypt = new Encryption;
	$crypt->setAdjustment($adj);
	$crypt->setModulus(ENCRYPTION_MOD); 
	$encrypt_result = $crypt->encrypt($key, rawurlencode($text), $pswdlen);  
	return $encrypt_result;*/
	return encodeStrStatic($content);
}

/**
 * 解密字符串
 *
 * @param string $text
 * @param int $backtracedays
 * @return string
 */
function decodeStr($text, $backtracedays=0)
{
	/*$pswlen = strlen($text);
	
	$crypt = new Encryption;
	$backdays = 0;
	$decodeconfirmed = false;
	while (($backdays <= $backtracedays) && (!$decodeconfirmed))
	{
		$key = date("Ymd", time()-86400 * $backdays);
		$adj = date("n.j", time()-86400 * $backdays);
		$crypt->setAdjustment($adj);
		$crypt->setModulus(ENCRYPTION_MOD); 
		$decrypt_result = $crypt->decrypt($key, $text); 
		$decodestr = @unserialize(rawurldecode($decrypt_result));
		if ($decodestr) $decodeconfirmed = true;
		$backdays++;
	}
	return $decodestr;*/
	return decodeStrStatic($text);
}

/**
 * 用key加密txt
 *
 * @param string $txt
 * @param string $key
 * @return string
 */
function encryptByKey($txt, $key)
{
	// 使用随机数发生器产生 0~32000 的值并 MD5()
	srand((double)microtime() * 1000000);
	$encrypt_key = md5(rand(0, 32000));
	// 变量初始化
	$ctr = 0;
	$tmp = '';
	// for 循环，$i 为从 0 开始，到小于 $txt 字串长度的整数
	for($i = 0; $i < strlen($txt); $i++)
	{
		// 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		// $tmp 字串在末尾增加两位，其第一位内容为 $encrypt_key 的第 $ctr 位，
		// 第二位内容为 $txt 的第 $i 位与 $encrypt_key 的 $ctr 位取异或。然后 $ctr = $ctr + 1
		$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
	}
	// 返回结果，结果为 passport_key() 函数返回值的 base65 编码结果
	return base64_encode(cryptKey($tmp, $key));
}

/**
 * 用key解密txt
 *
 * @param string $txt
 * @param string $key
 * @return string
 */
function decryptByKey($txt, $key)
{
	// $txt 的结果为加密后的字串经过 base64 解码，然后与私有密匙一起，
	// 经过 passport_key() 函数处理后的返回值
	$txt = cryptKey(base64_decode($txt), $key);
	// 变量初始化
	$tmp = '';
	// for 循环，$i 为从 0 开始，到小于 $txt 字串长度的整数
	for ($i = 0; $i < strlen($txt); $i++) {
		// $tmp 字串在末尾增加一位，其内容为 $txt 的第 $i 位，
		// 与 $txt 的第 $i + 1 位取异或。然后 $i = $i + 1
		$tmp .= $txt[$i] ^ $txt[++$i];
	}
	// 返回 $tmp 的值作为结果
	return $tmp;
}

/**
 * 对key取md5再对txt加密
 *
 * @param string $txt
 * @param string $encrypt_key
 * @return string
 */
function cryptKey($txt, $encrypt_key)
{
	// 将 $encrypt_key 赋为 $encrypt_key 经 md5() 后的值
	$encrypt_key = md5($encrypt_key);
	// 变量初始化
	$ctr = 0;
	$tmp = '';
	// for 循环，$i 为从 0 开始，到小于 $txt 字串长度的整数
	for($i = 0; $i < strlen($txt); $i++) {
		// 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		// $tmp 字串在末尾增加一位，其内容为 $txt 的第 $i 位，
		// 与 $encrypt_key 的第 $ctr + 1 位取异或。然后 $ctr = $ctr + 1
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	}
	// 返回 $tmp 的值作为结果
	return $tmp;
}

function tripleEncode( $input,
					$key = "this is a secret key",
				    $iv = "i'mscret")
{
   return $encode = base64_encode(mcrypt_encrypt(MCRYPT_3DES, $key, $input, 'ofb', $iv));
}

function tripleDecode( $input,
					$key = "this is a secret key",
				    $iv = "i'mscret")
{
   return $decode = mcrypt_decrypt(MCRYPT_3DES, $key, base64_decode($input), 'ofb', $iv);
}

// ***************************************************************************** 
// Copyright 2003-2004 by A J Marston <http://www.tonymarston.net> 
// Distributed under the GNU General Public Licence 
// ***************************************************************************** 

class MyEncryption {

    var $scramble1;         // 1st string of ASCII characters 
    var $scramble2;         // 2nd string of ASCII characters 
     
    var $errors;            // array of error messages 
    var $adj;               // 1st adjustment value (optional) 
    var $mod;               // 2nd adjustment value (optional) 
     
    // **************************************************************************** 
    // class constructor 
    // **************************************************************************** 
    function encryption () 
    { 
        $this->errors = array(); 
         
        // Each of these two strings must contain the same characters, but in a different order. 
        // Use only printable characters from the ASCII table. 
        // Do not use single quote, double quote or backslash as these have special meanings in PHP. 
        // Each character can only appear once in each string EXCEPT for the first character 
        // which must be duplicated at the end (this gets round a bijou problemette when the 
        // first character of the password is also the first character in $scramble1). 
        $this->scramble1 = '! #$%&()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~!'; 
        $this->scramble2 = 'f^jAE]okIOzU[2&q1{3`h5w_794p@6s8?BgP>dFV=m D<TcS%Ze|r:lGK/uCy.Jx)HiQ!#$~(;Lt-R}Ma,NvW+Ynb*0Xf'; 

        if (strlen($this->scramble1) <> strlen($this->scramble2)) { 
            $this->errors[] = '** SCRAMBLE1 is not same length as SCRAMBLE2 **'; 
        } // if 
         
        $this->adj = 1.75;  // this value is added to the rolling fudgefactors 
        $this->mod = 3;     // if divisible by this the adjustment is made negative 
         
    } // constructor 
     
    // **************************************************************************** 
    function decrypt ($key, $source)  
    // decrypt string into its original form 
    { 
        //DebugBreak(); 
        // convert $key into a sequence of numbers 
        $fudgefactor = $this->_convertKey($key); 
        if ($this->errors) return; 
         
        if (empty($source)) { 
            $this->errors[] = 'No value has been supplied for decryption'; 
            return; 
        } // if 
         
        $target = null; 
        $factor2 = 0; 
         
        for ($i = 0; $i < strlen($source); $i++) { 
            // extract a character from $source 
            $char2 = substr($source, $i, 1); 
             
            // identify its position in $scramble2 
            $num2 = strpos($this->scramble2, $char2); 
            if ($num2 === false) { 
                $this->errors[] = "Source string contains an invalid character ($char2)"; 
                return; 
            } // if 
             
            if ($num2 == 0) { 
                // use the last occurrence of this letter, not the first 
                $num2 = strlen($this->scramble1)-1; 
            } // if 
             
            // get an adjustment value using $fudgefactor 
            $adj     = $this->_applyFudgeFactor($fudgefactor); 
             
            $factor1 = $factor2 + $adj;                 // accumulate in $factor1 
            $num1    = round($factor1 * -1) + $num2;    // generate offset for $scramble1 
            $num1    = $this->_checkRange($num1);       // check range 
            $factor2 = $factor1 + $num2;                // accumulate in $factor2 
             
            // extract character from $scramble1 
            $char1 = substr($this->scramble1, $num1, 1); 
             
            // append to $target string 
            $target .= $char1; 

            //echo "char1=$char1, num1=$num1, adj= $adj, factor1= $factor1, num2=$num2, char2=$char2, factor2= $factor2<br />\n"; 
             
        } // for 
         
        return rtrim($target); 
         
    } // decrypt 
     
    // **************************************************************************** 
    function encrypt ($key, $source, $sourcelen = 0)  
    // encrypt string into a garbled form 
    { 
        //DebugBreak(); 
        // convert $key into a sequence of numbers 
        $fudgefactor = $this->_convertKey($key); 
        if ($this->errors) return; 

        if (empty($source)) { 
            $this->errors[] = 'No value has been supplied for encryption'; 
            return; 
        } // if 
         
        // pad $source with spaces up to $sourcelen 
        while (strlen($source) < $sourcelen) { 
            $source .= ' '; 
        } // while 
         
        $target = null; 
        $factor2 = 0; 
         
        for ($i = 0; $i < strlen($source); $i++) { 
            // extract a character from $source 
            $char1 = substr($source, $i, 1); 
             
            // identify its position in $scramble1 
            $num1 = strpos($this->scramble1, $char1); 
            if ($num1 === false) { 
                $this->errors[] = "Source string contains an invalid character ($char1)"; 
                return; 
            } // if 
             
            // get an adjustment value using $fudgefactor 
            $adj     = $this->_applyFudgeFactor($fudgefactor); 
             
            $factor1 = $factor2 + $adj;             // accumulate in $factor1 
            $num2    = round($factor1) + $num1;     // generate offset for $scramble2 
            $num2    = $this->_checkRange($num2);   // check range 
            $factor2 = $factor1 + $num2;            // accumulate in $factor2 
             
            // extract character from $scramble2 
            $char2 = substr($this->scramble2, $num2, 1); 
             
            // append to $target string 
            $target .= $char2; 

            //echo "char1=$char1, num1=$num1, adj= $adj, factor1= $factor1, num2=$num2, char2=$char2, factor2= $factor2<br />\n"; 
             
        } // for 
         
        return $target; 
         
    } // encrypt 
     
    // **************************************************************************** 
    function getAdjustment ()  
    // return the adjustment value 
    { 
        return $this->adj; 
         
    } // setAdjustment 
     
    // **************************************************************************** 
    function getModulus ()  
    // return the modulus value 
    { 
        return $this->mod; 
         
    } // setModulus 
     
    // **************************************************************************** 
    function setAdjustment ($adj)  
    // set the adjustment value 
    { 
        $this->adj = (float)$adj; 
         
    } // setAdjustment 
     
    // **************************************************************************** 
    function setModulus ($mod)  
    // set the modulus value 
    { 
        $this->mod = (int)abs($mod);    // must be a positive whole number 
         
    } // setModulus 
     
    // **************************************************************************** 
    // private methods 
    // **************************************************************************** 
    function _applyFudgeFactor (&$fudgefactor)  
    // return an adjustment value  based on the contents of $fudgefactor 
    // NOTE: $fudgefactor is passed by reference so that it can be modified 
    { 
        $fudge = array_shift($fudgefactor);     // extract 1st number from array 
        $fudge = $fudge + $this->adj;           // add in adjustment value 
        $fudgefactor[] = $fudge;                // put it back at end of array 
         
        if (!empty($this->mod)) {               // if modifier has been supplied 
            if ($fudge % $this->mod == 0) {     // if it is divisible by modifier 
                $fudge = $fudge * -1;           // make it negative 
            } // if 
        } // if 
         
        return $fudge; 
         
    } // _applyFudgeFactor 
     
    // **************************************************************************** 
    function _checkRange ($num)  
    // check that $num points to an entry in $this->scramble1 
    { 
        $num = round($num);         // round up to nearest whole number 
         
        // indexing starts at 0, not 1, so subtract 1 from string length 
        $limit = strlen($this->scramble1)-1; 
         
        while ($num > $limit) { 
            $num = $num - $limit;   // value too high, so reduce it 
        } // while 
        while ($num < 0) { 
            $num = $num + $limit;   // value too low, so increase it 
        } // while 
         
        return $num; 
         
    } // _checkRange 
     
    // **************************************************************************** 
    function _convertKey ($key)  
    // convert $key into an array of numbers 
    { 
        if (empty($key)) { 
            $this->errors[] = 'No value has been supplied for the encryption key'; 
            return; 
        } // if 
         
        $array[] = strlen($key);    // first entry in array is length of $key 
         
        $tot = 0; 
        for ($i = 0; $i < strlen($key); $i++) { 
            // extract a character from $key 
            $char = substr($key, $i, 1); 
             
            // identify its position in $scramble1 
            $num = strpos($this->scramble1, $char); 
            if ($num === false) { 
                $this->errors[] = "Key contains an invalid character ($char)"; 
                return; 
            } // if 
             
            $array[] = $num;        // store in output array 
            $tot = $tot + $num;     // accumulate total for later 
        } // for 
         
        $array[] = $tot;            // insert total as last entry in array 
         
        return $array; 
         
    } // _convertKey 
     
// **************************************************************************** 
} // end Encryption 
// **************************************************************************** 
