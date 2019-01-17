<?php
class soapMessage{

    static public $__questions = array(
        '100001'=>'我玩的第一款游戏',
        '100002'=>'我玩的第一款网游',
        '100003'=>'我曾就读的小学校名',
        '100004'=>'刻骨铭心的6位数字',
        '100005'=>'最喜欢的一句话是',
        '100006'=>'我最关注的是什么',
        '100007'=>'最想要的物品名称',
        '100008'=>'最喜欢的一款游戏'
    );

    static public  $__message = array(
        'SUCCESS'=>array('code'=>0,'text'=>'request success'),
        'USER_REG_USERNAME_EXISTS'=>array('code'=>110,'text'=>'用户名已存在'),
        'USER_REG_PHONE_BINDED'=>array('code'=>111,'text'=>'手机号已被绑定'),
        'USER_REG_VERIFYCODE_NOT_EXTIST'=>array('code'=>-117,'text'=>'该验证码无效'),
        'USER_REG_VERIFYCODE_IS_WRONG'=>array('code'=>-118,'text'=>'图片验证码错误'),
        'USER_REG_IDCARDINFO_WRONG'=>array('code'=>-119,'text'=>'身份信息错误'),
        'USER_REG_VERIFYCODE_FAILD'=>array('code'=>-112,'text'=>'输入的验证码有误'),
        'USER_REG_VERIFYCODE_EXPIRED'=>array('code'=>-114,'text'=>'该验证码已失效'),
        'USER_REG_CHECK_USERNAME_FAILED'=>array('code'=>107,'text'=>'用户名不符合规范'),
        'USER_REG_CHECK_PWD_FAILED'=>array('code'=>108,'text'=>'密码不符合规范'),
        'USER_REG_DEVICE_BINDED'=>array('code'=>115,'text'=>'该设备已经绑定用户'),
        'EMAIL_IS_ERROR'=>array('code'=>116,'text'=>'该邮箱不存在'),
        'GET_VERIFYCODE_PINFAN'=>array('code'=>-100,'text'=>'过于频繁获取验证码'),
        'GET_VERIFYCODE_CHECK_PHONE_FAILD'=>array('code'=>-101,'text'=>'手机号码不正确'),
        'GET_VERIFYCODE_MAX_REQUEST_IP_COUNT'=>array('code'=>-102,'text'=>'过于频繁获取验证码'),
        'GET_VERIFYCODE_PHONE_NOT_ALLOW'=>array('code'=>-103,'text'=>'不支持该手机号码'),
        'USER_LOGIN_USERNAME_NOTREG'=>array('code'=>-104,'text'=>'该账户不存在'),
        'USER_LOGIN_PHONE_NOTREG'=>array('code'=>105,'text'=>'手机号码未注册'),
        'USER_LOGIN_PWD_ERROR'=>array('code'=>-106,'text'=>'用户名或密码错误'),
        'USER_LOGIN_CHECK_USERNAME_FAILD'=>array('code'=>107,'text'=>'用户名不符合规范'),
        'USER_LOGIN_CHECK_PWD_FAILD'=>array('code'=>108,'text'=>'密码不符合规范'),
        'USER_LOGIN_OPEN_NOTREG'=>array('code'=>109,'text'=>'第三方用户未注册'),
        'ERROR_OPENID'=>array('code'=>-120,'text'=>'无效的设备号'),
        'RESET_PWD_VERIFYCODE_FAILD'=>array('code'=>112,'text'=>'短信验证码错误(3)'),
        'RESET_PWD_CHECK_PWD_FAILD'=>array('code'=>108,'text'=>'请输入7-20位密码'),
        'CHECK_TOKEN_FAILD'=>array('code'=>113,'text'=>'登录超时，请重新登录'),

        'ERROR_SIGN_FAILED'=>array('code'=>-998,'text'=>'签名错误'),
        'ERROR_DEVICE_UNSUPPORT'=>array('code'=>-999,'text'=>'您的设备暂不支持游客登录'),
        'ERROR_MAIL_BINDED'=>array('code'=>-997,'text'=>'邮箱已被绑定过了'),
        'ERROR_USERNAME_ISNUMRIC'=>array('code'=>-900,'text'=>'用户名不能纯数字'),
        'ERROR_USERNAME_SPECIAL_CHAR'=>array('code'=>-901,'text'=>'名称不能包含特殊文字或符号'),
        'ERROR_USERNAME_LENGTH'=>array('code'=>-902,'text'=>'用户名必须2-20个字符之间'),
        'ERROR_ACCOUNT_BAD'=>array('code'=>-903,'text'=>'您的账号存在异常 如有疑问请联系客服QQ：2862196013'),
        'ERROR_SEND_SMS_LIMIT'=>array('code'=>-904,'text'=>'您好，一个手机号每天只能发送10次短信验证码'),
//    'ERROR_ACCOUNT_BAD'=>array('code'=>-905,'text'=>'数据异常无法登陆'),
        'ERROR_NOT_BIND'=>array('code'=>-996,'text'=>'未绑定'),
        'ERROR_BIND_FAILED'=>array('code'=>-995,'text'=>'绑定失败'),

        'ERROR_BIND_BY_OTHERS'=>array('code'=>-994,'text'=>'已被其他用户绑定'),
        'ERROR_REPEAT_PHONE'=>array('code'=>-993,'text'=>'您还有其他的手机号还未解绑'),
        'ERROR_SMS_SEND_FAILED'=>array('code'=>-992,'text'=>'发送失败，请稍候发送'),
        'ERROR_VERIFYCODE'=>array('code'=>-986,'text'=>'验证码错误'),
        'ERROR_MAIL_SEND_FAILED'=>array('code'=>-985,'text'=>'邮件发送失败'),
        'ERROR_MAIL_NOT_BIND'=>array('code'=>-984,'text'=>'邮箱未绑定'),
        'ERROR_GIFT_CODE'=>array('code'=>-983,'text'=>'礼包码错误'),
        'ERROR_LOGIN_LIMIT'=>array('code'=>-201,'text'=>'登录过于频繁，稍后再试'),
        'ERROR_REG_LIMIT'=>array('code'=>-202,'text'=>'注册过于频繁，请稍后注册'),
        'ERROR_REQUEST_LIMIT'=>array('code'=>-203,'text'=>'请求过于频繁，请稍后重试'),
        'ERROR_LOGIN_EXCEPTION'=>array('code'=>-204,'text'=>'登录未知错误'),
        'ERROR_LOGIN_MAX_ERROR_TIMES'=>array('code'=>-205,'text'=>'尝试登陆次数过多,1小时内无法登录。'),


        'ERROR_GET_QUESTION'=>array('code'=>-1000,'text'=>'该账户还没有设置过密保'),//密保设置为空
        'ERROR_PASSWORD_NOT_SAME'=>array('code'=>-1001,'text'=>'两次输入的新密码不一致'),
        'ERROR_RENEW_PWD_OLD_PASSWORD'=>array('code'=>-1002,'text'=>'原密码错误'),

        'ERROR_RENRE_PWD_NEW_PASSWORD_TOO_SIMPLE'=>array('code'=>-1003,'text'=>'新密码过于简单，请尝试复杂的字符组合'),
        'ERROR_SET_QUESTION_EXISTED'=>array('code'=>-1004,'text'=>'已存在密保，无法添加'),
        'ERROR_SET_QUESTION_LENGTH'=>array('code'=>-1005,'text'=>'密保问题或者答案个数不符'),
        'ERROR_QUESTION_CHECK'=>array('code'=>-1006,'text'=>'密保验证失败'),
        'ERROR_EMAIL_VERIFYCODE_EXPIRED'=>array('code'=>-1007,'text'=>'验证码已失效'),
        'ERROR_EMAIL_SPECIAL_CHAR'=>array('code'=>-1008,'text'=>'邮箱格式错误'),
        'ERROR_ACCOUNT_PHONE_BINDED'=>array('code'=>-1009,'text'=>'您的账户已经绑定过手机'),
        'ERROR_PHONE_SPECIAL_CHAR'=>array('code'=>-1010,'text'=>'手机格式错误'),
        'ERROR_REGISTER_PARAMS'=>array('code'=>-1011,'text'=>'参数错误'),
        'ERROR_REGISTER_ACCOUNT_NOT_UNIQUE'=>array('code'=>-1012,'text'=>'用户名重复'),
        'WARNNING_SET_QUESTION'=>array('code'=>-1013,'text'=>'该服务暂不可用'),//密保设置为空
        'ERROR_RENRE_PWD_NEW_PASSWORD_TOO_SHORT'=>array('code'=>-1014,'text'=>'密码长度为不能小于7个字符'),
        'ERROR_USER_RENAME'=>array('code'=>-1015,'text'=>'重命名失败'),
        'ERROR_BAD_USER'=>array('code'=>-1016,'text'=>'请求的服务暂不可用'),
        'ERROR_NEED_UPDATE'=>array('code'=>-1017,'text'=>'该版本过低无法进入游戏，请下载最新版本。'),
        'ERROR_BIND_IDCARD_REPEAT'=>array('code'=>-1019,'text'=>'该账户已进行过实名认证，不需再次提交'),
        'ERROR_BAD_NAMES'=>array('code'=>-1020,'text'=>'此名称禁止使用'),
        'ERROR_CHANNEL_PACKAGE_OR_OPENID'=>array('code'=>-1021,'text'=>'错误的包名或者openid'),
        'UNKNOW_ERROR'=>array('code'=>-9999,'text'=>'未知错误,稍后再试'),

        'SERVER_WILL_OPEN'=>array('code'=>-2001,'text'=>'2017/11/08 10:00正式开服') //新世纪福音战士：破晓

    );
}