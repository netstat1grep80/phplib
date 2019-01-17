<?php

//控制将key映射到server的散列函数 crc32
ini_set('memcache.hash_function', 'crc32');

//控制将key映射到server的策略 consistent 
ini_set('memcache.hash_strategy', 'consistent');

//数据过期
defined('LOGIN_ERROR_EXPIRES') or define('LOGIN_ERROR_EXPIRES', 0x01);
//数据错误
defined('LOGIN_ERROR_DATA') or define('LOGIN_ERROR_DATA', 0x02);
//没有SID
defined('LOGIN_ERROR_NO_SID') or define('LOGIN_ERROR_NO_SID', 0x04);
//旧加密串
define('ENCRYPT_DISTURB_OLD', '12345678');
//加密串
define('ENCRYPT_DISTURB', 'ed1c4b6c');
//md5加密串
define('MD5_ENCRYPT_DISTURB', '4b21e6f4');
//过期时间
defined('LOGIN_LIFE') or define('LOGIN_LIFE', 1800);
//cookie作用域
defined('COOKIE_DOMAIN') or define('COOKIE_DOMAIN', 'muzhiwan.com');

defined('COOKIE_EXPIRE_TIME') or define('COOKIE_EXPIRE_TIME',86400);
//key
defined('DISCUZ_AUTH_KEY') or define('DISCUZ_AUTH_KEY','MZW');
defined('CLIENT_SEND_MAIL_KEY') or define('CLIENT_SEND_MAIL_KEY','!@p^&&mzw_)_(_v__)(+^%mail');

//发送短信或者邮件的间隔时间和次数
defined('SEND_CODE_LIMIT_TWICE') or define('SEND_CODE_LIMIT_TWICE',30);
defined('SEND_CODE_LIMIT_TOTAL_COUNTS') or define('SEND_CODE_LIMIT_TOTAL_COUNTS',10);
defined('SEND_CODE_MAX_REQUEST_IP_COUNT') or define('SEND_CODE_MAX_REQUEST_IP_COUNT',20);//每ip每天的发送短信数

//短信代码类型
defined('SMS_CODE_TYPE_SDK_REG') or define('SMS_CODE_TYPE_SDK_REG',1);
defined('SMS_CODE_TYPE_SDK_LOGIN') or define('SMS_CODE_TYPE_SDK_LOGIN',2);
defined('SMS_CODE_TYPE_SDK_REESTPWD') or define('SMS_CODE_TYPE_SDK_REESTPWD',3);
defined('SMS_CODE_TYPE_SDK_CHANGE_PHONE') or define('SMS_CODE_TYPE_SDK_CHANGE_PHONE',4);
defined('SMS_CODE_TYPE_SDK_BIND_PHONE') or define('SMS_CODE_TYPE_SDK_BIND_PHONE',5);
defined('SMS_CODE_TYPE_SDK_UNBIND_PHONE') or define('SMS_CODE_TYPE_SDK_UNBIND_PHONE',6);
defined('SMS_CODE_TYPE_SDK_FIND_PWD') or define('SMS_CODE_TYPE_SDK_FIND_PWD',7);

defined('SMS_CODE_TYPE_CLIENT_REG') or define('SMS_CODE_TYPE_CLIENT_REG',51);
defined('SMS_CODE_TYPE_CLIENT_LOGIN') or define('SMS_CODE_TYPE_CLIENT_LOGIN',52);
defined('SMS_CODE_TYPE_CLIENT_REESTPWD') or define('SMS_CODE_TYPE_CLIENT_REESTPWD',53);
defined('SMS_CODE_TYPE_CLIENT_CHANGE_PHONE') or define('SMS_CODE_TYPE_CLIENT_CHANGE_PHONE',54);
defined('SMS_CODE_TYPE_CLIENT_BIND_PHONE') or define('SMS_CODE_TYPE_CLIENT_BIND_PHONE',55);
defined('SMS_CODE_TYPE_CLIENT_UNBIND_PHONE') or define('SMS_CODE_TYPE_CLIENT_UNBIND_PHONE',56);
defined('SMS_CODE_TYPE_CLIENT_FIND_PWD') or define('SMS_CODE_TYPE_CLIENT_FIND_PWD',57);

defined('SMS_CODE_TYPE_WEBSITE_REG') or define('SMS_CODE_TYPE_WEBSITE_REG',1001);

//短信代码类型
defined('EMAIL_CODE_TYPE_SDK_REG') or define('EMAIL_CODE_TYPE_SDK_REG',101);
defined('EMAIL_CODE_TYPE_SDK_LOGIN') or define('EMAIL_CODE_TYPE_SDK_LOGIN',102);
defined('EMAIL_CODE_TYPE_SDK_REESTPWD') or define('EMAIL_CODE_TYPE_SDK_REESTPWD',103);
defined('EMAIL_CODE_TYPE_SDK_CHANGE') or define('EMAIL_CODE_TYPE_SDK_CHANGE',104);
defined('EMAIL_CODE_TYPE_SDK_BIND') or define('EMAIL_CODE_TYPE_SDK_BIND',105);
defined('EMAIL_CODE_TYPE_SDK_UNBIND') or define('EMAIL_CODE_TYPE_SDK_UNBIND',106);
defined('EMAIL_CODE_TYPE_SDK_FIND_PASSWORD') or define('EMAIL_CODE_TYPE_SDK_FIND_PASSWORD',107);

defined('EMAIL_CODE_TYPE_CLIENT_REG') or define('EMAIL_CODE_TYPE_CLIENT_REG',151);
defined('EMAIL_CODE_TYPE_CLIENT_LOGIN') or define('EMAIL_CODE_TYPE_CLIENT_LOGIN',152);
defined('EMAIL_CODE_TYPE_CLIENT_REESTPWD') or define('EMAIL_CODE_TYPE_CLIENT_REESTPWD',153);
defined('EMAIL_CODE_TYPE_CLIENT_CHANGE') or define('EMAIL_CODE_TYPE_CLIENT_CHANGE',154);
defined('EMAIL_CODE_TYPE_CLIENT_BIND') or define('EMAIL_CODE_TYPE_CLIENT_BIND',155);
defined('EMAIL_CODE_TYPE_CLIENT_UNBIND') or define('EMAIL_CODE_TYPE_CLIENT_UNBIND',156);
defined('EMAIL_CODE_TYPE_CLIENT_FIND_PASSWORD') or define('EMAIL_CODE_TYPE_CLIENT_FIND_PASSWORD',157); //如果此类定义有增加，到class.user.v4.inc.php client_send_email_verifycode方法做相应修改

defined('EMAIL_CODE_TYPE_SDK_V2_FIND_PASSWORD') or define('EMAIL_CODE_TYPE_SDK_V2_FIND_PASSWORD',207); //v4发邮件正文同新版sdk和v6都不同

defined('REG_FROM_SDK_421') or define('REG_FROM_SDK_421',50);//老版本sdk，对应服务器上的mzw421接口


defined('REG_FROM_SDK_NORMAL') or define('REG_FROM_SDK_NORMAL',100);//普通注册
defined('REG_FROM_SDK_QQ') or define('REG_FROM_SDK_QQ',101);//qq注册
defined('REG_FROM_SDK_SINAWEIBO') or define('REG_FROM_SDK_SINAWEIBO',102);//新浪微博
defined('REG_FROM_SDK_DEVICED') or define('REG_FROM_SDK_DEVICED',103);//游客登陆注册
defined('REG_FROM_SDK_SMS') or define('REG_FROM_SDK_SMS',104);//短信快速登陆注册
defined('REG_FROM_SDK_PHONE') or define('REG_FROM_SDK_PHONE',105);//手机标准注册
defined('REG_FROM_SDK_KUAIFA') or define('REG_FROM_SDK_KUAIFA',106);//手机标准注册


defined('REG_FROM_CLIENT_NORMAL') or define('REG_FROM_CLIENT_NORMAL',200);//普通注册
defined('REG_FROM_CLIENT_QQ') or define('REG_FROM_CLIENT_QQ',201);//qq注册
defined('REG_FROM_CLIENT_SINAWEIBO') or define('REG_FROM_CLIENT_SINAWEIBO',202);//新浪微博
defined('REG_FROM_CLIENT_DEVICED') or define('REG_FROM_CLIENT_DEVICED',203);//游客登陆注册
defined('REG_FROM_CLIENT_SMS') or define('REG_FROM_CLIENT_SMS',204);//短信快速登陆注册
defined('REG_FROM_CLIENT_PHONE') or define('REG_FROM_CLIENT_PHONE',205);//手机标准注册
defined('REG_FROM_CLIENT_EMAIL') or define('REG_FROM_CLIENT_EMAIL',206);//邮箱注册
defined('REG_FROM_CLIENT_WEIXIN') or define('REG_FROM_CLIENT_WEIXIN',207);//邮箱注册


defined('FORBIDDEN_TYPE_GETGIFT') or define('FORBIDDEN_TYPE_GETGIFT',1);//禁止领礼包
defined('FORBIDDEN_TYPE_LOGIN') or define('FORBIDDEN_TYPE_LOGIN',2);//禁止及登陆
defined('FORBIDDEN_TYPE_REGISTER') or define('FORBIDDEN_TYPE_REGISTER',4);//禁止注册
defined('FORBIDDEN_TYPE_PAY') or define('FORBIDDEN_TYPE_PAY',8);//禁止支付

defined('USER_GENDER_MALE') or define('USER_GENDER_MALE',1);//男性
defined('USER_GENDER_FEMALE') or define('USER_GENDER_FEMALE',2);//女性

$_SERVER['config']['login_session_cache'] = array(
    array('host'=>'10.1.1.3', 'port'=>'11211'),
    array('host'=>'10.1.1.3', 'port'=>'11212'),
    array('host'=>'10.1.1.3', 'port'=>'11211'),
    array('host'=>'10.1.1.3', 'port'=>'11212'),
);
//$_SERVER['config']['login_session_cache'] = array(array('host'=>'localhost', 'port'=>'11211'));

require_once 'login/class.soapclientfactory.inc.php' ;
require_once 'login/class.soapMessage.inc.php' ;
require_once '_func.text.sig.inc.php';
require_once '_class.http.SoapClientEx.inc.php';

class SecureLogin{
    /**
     * 钥匙
     *
     * @var unknown_type
     */
    private $encrypt;
    /**
     * 加密类型
     *
     * @var string
     */
    private $cipher = MCRYPT_BLOWFISH;
    /**
     * 分隔符
     *
     * @var unknown_type
     */
    private $sep	= '<>';
    /**
     *
     * @var SoapClientEx
     */
    var $soap_client;
    /**
     *
     *
     * @var MemcacheGroup
     */
    var $memcache;


    static $instance = null;



    public function __construct(){

    }

    public static function getQuestionNameByCode($code){
        $__questions =  soapMessage::$__questions;
        return isset($__questions[$code])?$__questions[$code] : false;
    }

    public static function getQuestionCodeByName($text){

        $__questions =  soapMessage::$__questions;
        foreach($__questions as $k => $v){
            if($v == $text){
                return $k;
            }
        }
        return $code;
    }

    public static function getRequestMessageByCode($code){
        $str = '未知错误('.$code.')';
        $__message =  soapMessage::$__message;
        foreach($__message as $k => $v){
            if($v['code'] == $code){
                return $v['text'];
            }
        }

        return $str;
    }

    public static function getRequestCodeByMessage($text){

        $__message =  soapMessage::$__message;
        $code = isset($__message[$text]) ? $__message[$text]['code']: SecureLogin::getRequestCodeByMessage('UNKNOW_ERROR');;

        return $code;
    }

    public static function getRequestTextByMessage($text){
        $__message =  soapMessage::$__message;
        $code = isset($__message[$text]) ? $__message[$text]['text']:'未知错误';

        return $code;
    }
    /**
     * 获得memcache对象
     *
     * @return Memcache
     */
    public function getMemcache(){
        if(!$this->memcache){
            $this->memcache = new Memcache();
            foreach($_SERVER['config']['login_session_cache'] as $m){
                $this->getMemcache()->addServer($m['host'], $m['port']);
            }
        }
        return $this->memcache;
    }

    /**
     * 登出
     *
     */
    public function logout(){
        if(!empty($_COOKIE['_sid'])){
            $this->getMemcache()->delete($this->get_local_key($_COOKIE['_sid']));
        }
    }

    /**
     * 解密串
     * @deprecated
     * @param  string $str
     * @return []
     */
    function info_decode($str=null){
        if(empty($str) and array_key_exists('_info',$_COOKIE)){
            $str = $_COOKIE['_info'];
        }
        list($encrypt, $rand) = explode("_", $str);
        if(!($encrypt && $rand)){
            return false;
        }
        $v = explode($this->sep, $this->decode($encrypt, $rand, ENCRYPT_DISTURB_OLD));
        if(count($v)==3){
            return array('uid'=>$v[0], 'email'=>$v[1], 'nickname'=>$v[2], 'old'=>1, 'timestamp'=>$rand);
        }
        return false;
    }

    /**
     * 加密串
     * @deprecated
     * @param int $uid
     * @param string $email
     * @param string $nickname
     * @param string $timestamp
     * @return string
     */
    function info_encode($uid, $email, $nickname, $timestamp){
        return $this->encode(implode($this->sep, array($uid, $email, $nickname)), $timestamp, ENCRYPT_DISTURB_OLD)."_".$timestamp;
    }

    /**
     * 更新info cookie
     *
     * @param int $uid
     * @param string $email
     * @param string $nickname
     * @param int $timestamp
     * @param int $noexpire
     */
    function refresh_info_cookie($uid, $email, $nickname, $timestamp, $noexpire=null){
        $info = $this->info_encode($uid, $email, $nickname, $timestamp);
        $expire_time = time() + $noexpire;
        setcookie("_info", $info, $noexpire?$expire_time:null, '/', COOKIE_DOMAIN);
    }

    /**
     * 获取memcache中的sid key
     *
     * @param string $sid
     * @return string
     */
    function get_local_key($sid){
        return "login_sid_".$sid;
    }


    /**
     * 判断用户是否登录
     *
     * @return mixed
     */
    function is_login(){
        if(!array_key_exists('_sid', $_COOKIE) or empty($_COOKIE['_sid'])) {
            return false;//LOGIN_ERROR_NO_SID;
        }
        $login = $this->decode_user_cookie() ;
        if($login){
            if(0 and date("d", $login['timestamp'])!=date("d") or (isset($login['old']) && $login['old']==1) ){

                $session = $this->check_session();
                if(!$session or $session['uid']!=$login['uid']){
                    return false;
                }
                $this->refresh_user_cookie($login['uid'], $login['email'], $login['nickname'], time(),
                    isset($_COOKIE['_e'])?$_COOKIE['_e']:false);
            }
        }else{ // deprecated
            return false;
        }
        return $login;
    }

    function check_session($sid=null){
        $sid = $sid?$sid:$_COOKIE['_sid'];
        $session = $this->getMemcache()->get($this->get_local_key($sid));
        if(!$session){
            $session = $this->soap_get_user_info($sid);
        }
        if(!$session){
            return false;
        }else{
            return $session;
        }
    }

/*    function set_secure_cookie($uid, $email, $nickname, $password, $ip, $timestamp, $noexpire=null){
//		header('P3P: CP=CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR');
        header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        header('P3P:CP=CAO PSA OUR');
        $expire_time = time() + $noexpire;

//        setcookie("_sid", $sid, $noexpire?$expire_time:COOKIE_EXPIRE_TIME, '/', COOKIE_DOMAIN,false,true);
//		setcookie("_info", $info, $noexpire?$expire_time:null, '/', $this->config['cookie_domain']);
//		$SL->refresh_info_cookie($uid, $email, $nickname, $timestamp, $noexpire?$expire_time:null);
        $this->refresh_user_cookie($uid, $email, $nickname, $timestamp, $noexpire?$expire_time:COOKIE_EXPIRE_TIME);
        setcookie("_l", time(), time()+31536000, '/', COOKIE_DOMAIN);
        setcookie("_mzw",  $uid."#".$email."#".$nickname, $noexpire?$expire_time:COOKIE_EXPIRE_TIME, '/', COOKIE_DOMAIN);
        setcookie("_e", $noexpire, $noexpire?$expire_time:COOKIE_EXPIRE_TIME, '/', COOKIE_DOMAIN);
    }*/

    function refresh_user_cookie($uid, $email, $nickname, $rand, $noexpire=null){
        header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        header('P3P:CP=CAO PSA OUR');
//        file_put_contents('var/udblogs/log.txt',"_i, $str, $noexpire?$expire_time:$default, '/', ".COOKIE_DOMAIN.")\n",FILE_APPEND);
        $str = $this->encode_user_cookie($uid, $email, $nickname, $rand);
        $expire_time = time() + $noexpire;
        $default =  time() + 86400 ;
        setcookie("_i", $str, $noexpire?$expire_time:$default, '/', COOKIE_DOMAIN);
    }

    function decode_user_cookie($str=null){
        $cookie = $str?$str:$_COOKIE['_i'];
        if($cookie){
            list($encrypt, $md5, $rand) = explode("_", $cookie);
            if(!$encrypt && $md5 && $rand){
                return false;
            }
//            echo "$encrypt, $rand, ".ENCRYPT_DISTURB;
            $decrypt = $this->decode($encrypt, $rand, ENCRYPT_DISTURB);
            $v = explode($this->sep, $decrypt);
//			if(count($v)!=3 or !($v[0] and $v[1] and $v[2])){
            if(count($v)!=3 or !($v[0] and  $v[2])){ //考虑到拇指玩用户email有为空的情况
                return false;
            }
            $ret = array('uid'=>intval($v[0]), 'email'=>$v[1], 'username'=>$v[2], 'timestamp'=>$rand);
            if($md5 != md5(implode($this->sep, $ret).MD5_ENCRYPT_DISTURB)){
                return false;
            }
            return $ret;
        }else{
            return false;
        }
    }

    function encode_user_cookie($uid, $email, $nickname, $rand){
        $origin = $uid.$this->sep.$email.$this->sep.$nickname;
        $encrypt = $this->encode($origin, $rand, ENCRYPT_DISTURB);
        return $encrypt
        ."_".md5($origin.$this->sep.$rand.MD5_ENCRYPT_DISTURB)
        ."_".$rand;
    }

    /**
     * 直接使用已知sid登录
     *
     * @param string $sid
     * @return []
     */
    function soap_inline_login($sid=null){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        if(isset($_COOKIE['_sid'])){
            $client->__setCookie('_sid', $_COOKIE['_sid']);
        }

        $result = $client->soap_inline_login($sid, $_SERVER['REMOTE_ADDR']);
        $ret = array();
        if($result->error[0]!=0){
            return - $result->error[0];
        }else{
            foreach ($result->data as $key=>$value){
                $ret[$key] = $value;
            }
            return $ret;
        }
    }


    /**
     * @param $username
     * @param $password
     * @param string $email
     * @param string $phone
     * @param string $openId
     * @param string $ip
     * @return   $user = array(
    'uid'=>$ret['acc_id'],
    'username'=>$ret['acc_username'],
    'email'=>$ret['acc_email'],
    'phone'=>$ret['acc_phone'],
    'password'=>$ret['acc_password'],
    'openID'=>$openId,
    'formID'=>$fromid
    );
     */
    function soap_user_reg($username,$password,$email='',$phone='',$openId='',$ip='',$fromid=0,$channel='',$uniqueid=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        if($openId && !preg_match('/^[a-z0-9\-\_]+$/si',$openId)){
            return SecureLogin::getRequestCodeByMessage('ERROR_OPENID');//ERROR_OPENID 无效的设备号
        }
//        echo "$username,$password,$email,$phone,$openId,$ip?$ip:getIP(),$fromid,$channel";exit;
        $result = $client->soap_user_reg(trim($username),$password,trim($email),trim($phone),trim($openId),$ip?$ip:getAccessIP(),trim($fromid),trim($channel),$uniqueid);
        if($result->error[0]!=0){
            return  $result->error[0];
        }else{
            foreach ($result->data as $key=>$value){
                $ret[$key] = $value;
            }
            return $ret;
        }
    }

    /**
     * 远程登录
     * @param string $email
     * @param string $password
     * @param boolean $noexpires
     */
    function soap_login($uid, $email, $password,  $ip=null, $noexpires=31536000 ,$passwordmd5=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $client->__setCookie('_sid', null); // 避免刷掉原有窗口的登陆状态
        $result = $client->soap_login($uid, $email, $password, $ip?$ip:getAccessIP(), $noexpires,$passwordmd5 );
        if($result->error[0]!=0){
            return  $result->error[0];
        }else{
            foreach ($result->data as $key=>$value){
                $ret[$key] = $value;
            }
            return $ret;
        }
    }

    /**
     * 客户端sdk登录
     * @param string $email
     * @param string $password
     * @param boolean $noexpires
     */
    function soap_client_login($uid, $email, $password,  $ip=null, $noexpires=31536000 ,$passwordmd5='',$uniqueid=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
//        $client->__setCookie('_sid', null); // 避免刷掉原有窗口的登陆状态
        $result = $client->soap_client_login($uid, $email, $password, $ip?$ip:getAccessIP(), $noexpires,$passwordmd5 ,$uniqueid);
        if($result->error[0]!=0){
            return  $result->error[0];
        }else{
            foreach ($result->data as $key=>$value){
                $ret[$key] = $value;
            }
            return $ret;
        }
    }


    /**
     * 客户端sdk登录
     * @param string $email
     * @param string $password
     * @param boolean $noexpires
     */
    function soap_channel_login($channel,$openid){
        $this->soap_client = $client = SoapClientFactory::getInstance();
//        $client->__setCookie('_sid', null); // 避免刷掉原有窗口的登陆状态
        $result = $client->soap_channel_login($channel,$openid,getAccessIP());
        return $result;
    }


    function soap_channel_userinfo($channel,$uid){
        $this->soap_client = $client = SoapClientFactory::getInstance();
//        $client->__setCookie('_sid', null); // 避免刷掉原有窗口的登陆状态
        $result = $client->soap_channel_userinfo($channel,$uid,getAccessIP());
        return $result;
    }


    /**
     * 客户端sdk登录
     * @param string $email
     * @param string $password
     * @param boolean $noexpires
     */
    function soap_client_open_login($openID,$uniqueId=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_client_open_login($openID,$uniqueId ,getAccessIP());
        if($result->error[0]!=0){
            return  $result->error[0];
        }else{
            foreach ($result->data as $key=>$value){
                $ret[$key] = $value;
            }
            return $ret;
        }
    }

    /**
     * @desc v6客户端专用,注册时候需要发一个连接,sdk已弃用此方法
     * @param $email
     * @param $codeType 获取的短信类型是 （客户端注册、登录等等）
     * @param int $uid
     * @return mixed
     */
    function soap_send_email_verifycode($email,$codeType,$uid=0,$password='',$channel=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_send_email_verifycode($email,$codeType,$uid,$password,$channel);
        return $result;
    }

    /**
     * @desc 至2017年5月起，SDK使用该方法发送邮箱验证码。(客户端不适用此方法)
     * @param $email
     * @param $codeType 获取的短信类型是 （sdk注册、登录)
     * @param int $uid
     * @return mixed
     */
    function soap_send_verifycode_to_email($email,$codeType,$uid=0,$password=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_send_email_verifycode_new($email,$codeType,$uid);
        return $result;
    }

    /**
     * @param $email
     * @param $codeType 校验的短信类型是 （sdk注册、登录、客户端注册、登录等等）
     * @param int $uid
     * @return mixed
     */
    function soap_check_email_verifycode($email,$code,$uid=0,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_check_email_verifycode($email,$code,$uid,($ip?$ip:getAccessIP()));
        return $result;
    }


    /**
     * @param $phone
     * @param $codeType 获取的短信类型是 （sdk注册、登录、客户端注册、登录等等）
     * @param int $uid
     * @return mixed
     */
    function soap_send_phone_verifycode($phone,$codeType,$uid=0,$codelength=0){

        $this->soap_client = $client = SoapClientFactory::getInstance();
        if(!preg_match("/^1[345678]{1}\d{9}$/",$phone)) return SecureLogin::getRequestCodeByMessage('ERROR_SMS_SEND_FAILED');
        $result = $client->soap_send_phone_verifycode($phone,$codeType,$uid,$codelength,getAccessIP());
        return $result;
    }



    /**
     * @param $phone
     * @param $codeType 校验的短信类型是 （sdk注册、登录、客户端注册、登录等等）
     * @param int $uid
     * @return mixed
     */
    function soap_check_phone_verifycode($phone,$code,$uid=0,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_check_phone_verifycode($phone,$code,$uid,($ip?$ip:getAccessIP()));
        return $result;
    }

    /**
     * @desc 短信使用以后要设置为使用状态
     * @param $code
     * @param string $phone
     * @param string $email
     * @return int
     */
    function soap_set_verifycode_finish($code,$phone){
        if((!$phone && !$email) || !$code)  return SecureLogin::getRequestCodeByMessage('UNKNOW_ERROR');
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_set_verifycode_finish($code,$phone);
        return $result;
    }

    /**
     * @desc 更改密码
     * @param $uid
     * @param $oldpassword
     * @param $newpassword
     * @return int  1代表成功 。 负数为错误代码
     */
    function soap_user_renew_password($uid ,$oldpassword,$newpassword){
        if(!$uid || !$oldpassword || !$newpassword)  return SecureLogin::getRequestCodeByMessage('UNKNOW_ERROR');
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_user_renew_password($uid ,$oldpassword,$newpassword,getAccessIP());
        return $result;
    }

    /**
     * @desc 设置密保
     * @param $uid
     * @param $questionIds
     * @param $answers
     * @return mixed
     */
    function soap_set_secure_question($uid,$questionIds,$answers){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_set_secure_question($uid,$questionIds,$answers);
        return $result;
    }

    /**
     * @desc 解绑安全问题
     * @param $uid
     * @param $questionIds
     * @param $answers
     * @return mixed
     */
    function soap_unbind_secure_question($uid,$questionIds,$answers,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_unbind_secure_question($uid,$questionIds,$answers,$ip);
        return $result;
    }

    /**
     * @获取个人资料
     * @param $uid
     * @return mixed
     */
    function soap_get_secure_settings($uid,$sex=false){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_get_secure_settings($uid);
        if($result && $sex){
                return array('sex'=>$result['sex'],'birthday'=>$result['birthday']);
        }
        return $result;
    }

    /**
     * @写入个人资料
     * @params 字段名称 birthday,sex,idcard_value,true_name
     */
    function soap_set_secure_settings($uid,$params,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_set_secure_settings($uid,$params,$ip?$ip:getAccessIP());
        return $result;
    }

    /**
     * @desc 绑定邮箱
     * @param $uid
     * @param $questionIds
     * @param $answers
     * @return mixed
     */
    function soap_user_bind_email($uid,$email,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_user_bind_email($uid,$email,$ip);
        return $result;
    }
    /**
     * @desc 解除绑定邮箱
     * @param $uid
     * @param $questionIds
     * @param $answers
     * @return mixed
     */
    function soap_user_unbind_email($uid,$email,$code,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_user_unbind_email($uid,$email,$code,$ip);
        return $result;
    }

    /**
     * @desc 绑定手机
     * @param $uid
     * @param $questionIds
     * @param $answers
     * @return mixed
     */
    function soap_user_bind_phone($uid,$phone,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_user_bind_phone($uid,$phone,$ip);
        return $result;
    }
    /**
     * @desc 解除绑定手机
     * @param $uid
     * @param $questionIds
     * @param $answers
     * @return mixed
     */
    function soap_user_unbind_phone($uid,$phone,$code,$ip=''){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_user_unbind_phone($uid,$phone,$code,$ip);
        return $result;
    }
    function soap_set_user_password($uid,$password){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_set_user_password($uid,$password);
        return $result;
    }
    /**
     * @desc 用户改名
     * @param $uid
     * @param $oldpassword
     * @param $newpassword
     * @return int  1代表成功 。 负数为错误代码
     */
    function soap_user_rename($uid ,$newUsername,$ip=''){
        if(!$uid || !$newUsername )  return SecureLogin::getRequestCodeByMessage('UNKNOW_ERROR');
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_user_rename($uid ,$newUsername,getAccessIP());
        return $result;
    }

    /**
     * @desc 融合sdk，子渠道用户改名
     * @param $uid
     * @param $oldpassword
     * @param $newpassword
     * @return int  1代表成功 。 负数为错误代码
     */
    function soap_channel_user_rename($uid ,$newUsername,$channel,$ip=''){
        if(!$uid || !$newUsername )  return SecureLogin::getRequestCodeByMessage('UNKNOW_ERROR');
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_channel_user_rename($uid ,$newUsername,$channel,getAccessIP());
        return $result;
    }

    /**
     * 获取用户信息
     *
     * @param string $sid
     * @return []
     */
    function soap_get_user_info($sid){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        if(isset($_COOKIE['_sid'])){
            $client->__setCookie('_sid', $_COOKIE['_sid']);
        }
        $result = $client->soap_get_user_info($sid);
        if($result->error[0]!=0){
            return  $result->error[0];
        }else{
            if(array_key_exists('password', $result->data)){
                unset($result->data->password);
            }
            foreach ($result->data as $key=>$value){
                $ret[$key] = $value;
            }
            return $ret;
        }
    }

    /**
     * 批量获取用户信息
     *
     * @param Array $uidArray
     * @param Array $usernameArray
     * @return []
     */
    function soap_get_users_info($uidArray=array(),$usernameArray=array()){

        if(empty($uidArray) && empty($usernameArray)){
            return array();
        }
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_get_users_info($uidArray,$usernameArray);
        return $result;
    }


    /**
     * 查询用户指定字段的信息
     *
     * @param int $uid
     * @param string $email
     * @param string $type - 可选值nickname, password等
     * @return string
     */
    function soap_query_info($uid, $email='', $type='username'){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        /*if(!($result = $this->getMemcache()->get("user_info_field_".$uid."_".$email."_".$type))){
            $result = $client->soap_query_info($uid, $email, $type);
            $this->getMemcache()->set("user_info_field_".$uid."_".$email."_".$type, $result, 0, 0);
        }*/
        $key = "user_info_field_".$uid."_".$email;
//        $this->getMemcache()->delete($key);
        if(false || !($result = $this->getMemcache()->get($key))){
            $result = $client->soap_query_info($uid, $email, $type);
            $this->getMemcache()->set($key, $result, 0, 0);
        }
        return $result;
    }

    /**
     * 使用uid查询用户基本信息
     *
     * @param int $uid
     * @return []
     */
    function soap_query_user($uid){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        if(!($result=$this->getMemcache()->get('user_info_'.$uid))){
            $result = $client->soap_query_user($uid);
            $this->getMemcache()->set('user_info_'.$uid, $result, 0, 0);
        }
        return $result;
    }

    /**
     * 使用username查询用户信息
     *
     * @param string $name
     * @return []
     */
    function soap_query_user_by_username($name){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_query_user_by_username($name);
        return $result;
    }

    function soap_query_checkusername_unique($username){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_query_checkusername_unique($username);
        return $result;
    }

    function soap_query_checkphone_unique($phone){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_query_checkphone_unique($phone);
        return $result;
    }

    function soap_query_checkemail_unique($email){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_query_checkemail_unique($email);
        return $result;
    }

    function soap_track_login($sid){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_track_login($sid);
        return $result;
    }

    /**
     * @desc 图片验证码的相关写入读取方法
     * @param $params
     * @param $type set:创建一个新的验证码 delete：删除一个验证码  get:读取一个验证码
     */
    function soap_image_verifycode($params,$type){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_image_verifycode($params,$type);
        return $result;
    }
    /**
     * 搜索
     *
     * @param string $key
     * @return []
     */
    function search($key){
        $content = http_request('http://account.muzhiwan.com/q_account.php?_act=search', array('key'=>$key));
        $ret = unserialize($content);
        if($ret){
            return $ret['data'];
        }else{
            return array();
        }
    }

    /**
     * @desc 存储用户头像地址
     * @param array('acc_id'),数组中索引必须同数据库字段拼写相符，因为使用save方法，不同会报错.
     * @return bool
     */
    function soap_user_tbl_account_sub_update($params){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        if($params['uid'] || $params['acc_id']){
            if($params['uid']) {
                $params['acc_id'] = $params['acc_id'] ;
                unset($params['uid']);
            }
            return $result = $client->soap_user_tbl_account_sub_update($params);
        }
        return false;
    }


    /**
     * @desc 已废弃
     * @param $uid
     * @return array
     */
    function soap_old_user_rsync($uid){
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_old_user_rsync($uid);
        return $result;
    }

    /**
     *
     */
    function soap_is_bad_user($params,$badType=0,$ip='',$appkey=''){
        if(!is_array($params) || empty($params)) return false;
        $this->soap_client = $client = SoapClientFactory::getInstance();
        $result = $client->soap_is_bad_user($params,$badType,$ip,$appkey);
        return $result;
    }
    /**
     * 生成密钥
     *
     * @param int $rand
     * @return []
     */
    private function gen_encrypt($rand, $disturb){
        $sig = md5($rand . $disturb);
        $iv_size = mcrypt_get_block_size($this->cipher, 'cbc');

        return $this->encrypt = array(
            'key' 	=> substr($sig, 4, $iv_size*2),
            'iv'	=> substr($sig, -$iv_size),
            'rand'	=> $rand
        );
    }

    /**
     * 加密
     *
     * @param string $str
     * @param int $rand
     * @return string
     */
    private function encode($str, $rand, $disturb){
        $this->gen_encrypt($rand, $disturb);
        return base64_encode(mcrypt_cbc(
                $this->cipher,
                $this->encrypt['key'].$rand,
                trim($str),
                MCRYPT_ENCRYPT,
                $this->encrypt['iv']
            )
        );
    }

    /**
     * 解密
     *
     * @param string $str
     * @return string
     */
    private function decode($str, $rand, $disturb){
        $this->gen_encrypt($rand, $disturb);
        return trim(mcrypt_cbc(
                $this->cipher,
                $this->encrypt['key'].$rand,
                base64_decode($str),
                MCRYPT_DECRYPT,
                $this->encrypt['iv']
            )
        );
    }
    /**
     *
     *
     * @return SecureLogin
     */
    static function getInstance(){
        if(!self::$instance){
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }
}
