<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 会员url类
 */
class user_url
{
    /**
     * 重写web类的load_url_unique方法，获取前台特有URL
     */
    public function insert_m()
    {
        global $_M;
        $lang = "?lang={$_M['lang']}";
        $_M['url']['member'] = $_M['url']['site'] . 'member/';

        //login
        $_M['url']['login'] = "{$_M['url']['member']}login.php{$lang}";
        $_M['url']['login_check'] = "{$_M['url']['login']}&a=dologin";
        $_M['url']['login_out'] = "{$_M['url']['login']}&a=dologout";
        $_M['url']['login_other'] = "{$_M['url']['login']}&a=doother";
        $_M['url']['login_other_register'] = "{$_M['url']['login']}&a=dologin_other_register";
        $_M['url']['login_other_info'] = "{$_M['url']['login']}&a=dologin_other_info";
        $_M['url']['weixin_register'] = "{$_M['url']['login']}&a=doregistwxuser";

        //register
        $_M['url']['register'] = "{$_M['url']['member']}register_include.php{$lang}";
        $_M['url']['valid_email'] = "{$_M['url']['register']}&a=doemailvild";
        $_M['url']['valid_phone'] = "{$_M['url']['register']}&a=dophonecode";
        $_M['url']['register_save'] = "{$_M['url']['register']}&a=dosave";
        $_M['url']['register_userok'] = "{$_M['url']['register']}&a=douserok";

        //邮箱找回密码
        $_M['url']['getpassword'] = "{$_M['url']['member']}getpassword.php{$lang}";//找回密码
        $_M['url']['find_pass_by_email'] = "{$_M['url']['getpassword']}&a=doFindPassByEmail";//邮箱改密
        $_M['url']['set_pass_by_email'] = "{$_M['url']['getpassword']}&a=doSetPassByEmail";//邮箱改密
        //短信找回密码
        $_M['url']['find_pass_by_sms'] = "{$_M['url']['getpassword']}&a=doTel";//短信找回
        $_M['url']['send_sms_find_pass'] = "{$_M['url']['getpassword']}&a=doSendSMS";//发短信
        $_M['url']['set_pass_by_sms'] = "{$_M['url']['getpassword']}&a=doSetPassBySms";

        //profile-basic
        $_M['url']['user_home'] = "{$_M['url']['member']}index.php{$lang}";
        $_M['url']['profile'] = "{$_M['url']['member']}basic.php{$lang}";
        $_M['url']['info_save'] = "{$_M['url']['profile']}&a=doinfosave";   //保存用户基本信息
        $_M['url']['valid_email_repeat'] = "{$_M['url']['profile']}&a=dovalid_email";  //邮箱激活
        $_M['url']['paygroup'] = "{$_M['url']['member']}paygroup.php{$lang}";//付费会员组
        //profile-safety
        $_M['url']['profile_safety'] = "{$_M['url']['profile']}&a=dosafety";    //账号安全
        $_M['url']['pass_save'] = "{$_M['url']['profile']}&a=dopasssave";   //修改密码
        //eamil bind
        $_M['url']['bindEmail']= "{$_M['url']['profile']}&a=doBindEmail";  //绑定邮箱
        $_M['url']['checkBindeEmail']= "{$_M['url']['profile']}&a=doCheckBindeEmail";   //邮箱绑定验证
        $_M['url']['unbindEmail']= "{$_M['url']['profile']}&a=doUnbindEmail";   //解绑邮箱
        //Tel bind
        $_M['url']['bindTelSmsCode']= "{$_M['url']['profile']}&a=doBindTelSmsCode"; //发送手机验证码
        $_M['url']['bindTel']= "{$_M['url']['profile']}&a=doBindTel";   //绑定手机
        $_M['url']['unbindTel']= "{$_M['url']['profile']}&a=doUnbindTel";   //解绑手机
        //实名认证
        $_M['url']['profile_safety_idvalid'] = "{$_M['url']['profile']}&a=dosafety_idvalid";

//        $_M['url']['emailedit'] = "{$_M['url']['profile']}&a=doemailedit";
//        $_M['url']['profile_safety_emailadd'] = "{$_M['url']['profile']}&a=dosafety_emailadd";
//
//        $_M['url']['profile_safety_telok'] = "{$_M['url']['profile']}&a=dosafety_telok";
//        $_M['url']['profile_safety_telvalid'] = "{$_M['url']['profile']}&a=dosafety_telvalid";
//        $_M['url']['profile_safety_teladd'] = "{$_M['url']['profile']}&a=dosafety_teladd";
//        $_M['url']['profile_safety_teledit'] = "{$_M['url']['profile']}&a=dosafety_teledit";


    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
