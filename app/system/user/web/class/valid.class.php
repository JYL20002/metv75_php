<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class valid
{
    public $error;
    public $result;

    /**
     * @param $email
     * @return mixed
     * 兼容商城小程序
     */
    public function get_email($email,$type = 'register')
    {
        return $this->sendEmail($email, $type);
    }

    /**
     * 注册邮件通知
     * @param $email
     * @param $type
     * @return mixed
     */
    public function sendEmail($email, $type = 'register', $session_name = 'USER_EMAIL_SESSION')
    {
        global $_M;
        switch ($type) {
            case 'register':
                $title = $_M['config']['met_member_email_reg_title'];
                $body = $_M['config']['met_member_email_reg_content'];
                $url = $_M['url']['valid_email'];
                break;
            case 'getpassword':
                $title = $_M['config']['met_member_email_password_title'];
                $body = $_M['config']['met_member_email_password_content'];
                $url = $_M['url']['find_pass_by_email'];
                break;
//            case 'emailedit':
//                $title = $_M['config']['met_member_email_safety_title'];
//                $body = $_M['config']['met_member_email_safety_content'];
//                $url = $_M['url']['emailedit'];
//                break;
//            case 'emailadd':
//                $title = $_M['config']['met_member_email_safety_title'];
//                $body = $_M['config']['met_member_email_safety_content'];
//                $url = $_M['url']['profile_safety_emailadd'];
//                break;
            case 'emailadd':
            case 'emailbind':
                $title = $_M['config']['met_member_email_safety_title'];
                $body = $_M['config']['met_member_email_safety_content'];
                $url = $_M['url']['checkBindeEmail'];
                break;
            default:
                jsoncallback('Type error');
        }

        //生成加密字符串
        $session = load::sys_class('session', 'new');
        $user_email_session = array(
            'expires' => time() + 300,
            'email' => $email,
        );
        $session->set($session_name, $user_email_session);

        //发邮件
        $auth = load::sys_class('auth', 'new');
        $code = $auth->encode($email, '', 300);
        $url = "{$url}&p=" . urlencode($code);
        $url = str_replace('../', $_M['url']['web_site'], $url);

        $touser = $email;
        $title = self::repalce_email($title, $url);
        $body = self::repalce_email($body, $url);
        $jmail = load::sys_class('jmail', 'new');
        return $jmail->send_email($touser, $title, $body);
    }

    /**
     * @param $str
     * @param $url
     * @return array|string|string[]
     */
    private function repalce_email($str, $url)
    {
        global $_M;
        $web_site = str_replace(HTTP_HOST, $_SERVER['SERVER_NAME'], $_M['url']['web_site']);
        $str = str_replace('{webname}', $_M['config']['met_webname'], $str);
        $str = str_replace('{weburl}', $web_site, $str);
        $str = str_replace('{opurl}', $url, $str);
        return $str;
    }

    public function checkEmailCode($code , $session_name = 'USER_EMAIL_SESSION')
    {
        $auth = load::sys_class('auth', 'new');
        $session = load::sys_class('session', 'new');

        $session_data = $session->get($session_name);
        $email = $auth->decode($code);

        if ($session_data['expires'] < time()) {
            $this->error = '验证信息错误或已超时';
            return false;
        }

        if (!$email || $email != $session_data['email']) {
            $this->error = '验证信息错误或已超时';
            return false;
        }

        $session->del($session_name);
        $this->result['email'] = $email;
        return true;
    }

    /**
     * @param $tel
     * @return bool
     */
    public function smsCode($tel, $session_name = 'USER_SMS_SESSION')
    {
        global $_M;
        $session = load::sys_class('session', 'new');
        $session_data = $session->get($session_name);
//        if ($session_data && time() < $session_data['expires']) {
//            //重复发送
//            $this->error = '请勿重复发送';
//            return false;
//        }

        $code = random(6, 1);
        $bind_tel_session = array(
            'expires' => time() + 60,
            'tel' => $tel,
            'code' => $code,
        );
        $session->set($session_name, $bind_tel_session);

        $sms = load::sys_class('sms', 'new');
        $sms_msg = "{$_M['word']['usesendcode']}{$code}{$_M['word']['usesendcodeinfo']}";
        $res = $sms->sendsms($tel, $sms_msg);
        if ($res['status'] == 200) {
            return true;
        }
        $this->error = $res['msg'] ?: 'SMS Api error';
//        $this->error = $sms_msg;
        return false;

    }


    /**
     * @param $code
     * @param $tel
     * @return bool
     */
    public function checkSmsCoode($code, $tel, $session_name = 'USER_SMS_SESSION')
    {
        global $_M;
        $session = load::sys_class('session', 'new');
        $session_data = $session->get($session_name);
        if (time() > $session_data['expires']) {
            $session->del($session_name);
            $this->error = '验证码超时';
            $this->errormsg = $_M['word']['codetimeout'];
            return false;
        }

        if ($session_data['tel'] == $tel && $session_data['code'] == $code) {
            $session->del($session_name);
            return true;
        }
        $this->errormsg = $_M['word']['membercode'];
        $session->del($session_name);
        $this->error = '验证码错误';
        return false;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>