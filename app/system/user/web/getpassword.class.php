<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('user/web/class/userweb');

class getpassword extends userweb
{

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->user_database = load::mod_class('user/user_database', 'new');
        $this->user_handle = load::mod_class('user/user_handle', 'new');
    }

    public function checkLogin()
    {
    }

    public function doindex()
    {
        global $_M;
        $usernaem = $_M['form']['username'] ?: '';
        if (!$usernaem) $this->view('app/getpassword', $this->input);//加载模板

        $user = $this->user_database->getOneByUsername($usernaem);
        if (!$user) okinfo($_M['url']['getpassword'], $_M['word']['NoidJS']);

        if (!load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random']) && $_M['config']['met_memberlogin_code']) okinfo($_M['url']['getpassword'], $_M['word']['membercode']);

        if (is_email($usernaem)) {
            self::sendEmail($usernaem);
            return;
        }
        if (is_phone($usernaem)) {
            okinfo($_M['url']['find_pass_by_sms'] . "&username=" . urlencode($usernaem));
        }
    }

    /**
     * @param $email
     * @return void
     */
    public function sendEmail($email)
    {
        global $_M;
        $valid = load::mod_class('user/web/class/valid', 'new');
        $res = $valid->sendEmail($email, 'getpassword');
        if ($res) {
            okinfo($_M['url']['login'], $_M['word']['emailsucpass']);
        } else {
            okinfo($_M['url']['getpassword'], $_M['word']['emailfail']);
        }
    }

    /**
     * @return void
     */
    public function doFindPassByEmail()
    {
        global $_M;
        $p = $_M['form']['p'];
        $valid = load::mod_class('user/web/class/valid', 'new');
        $res = $valid->checkEmailCode($p);
        if (!$res) okinfo($_M['url']['login'], $_M['word']['emailvildtips2']);
        $email = $valid->result['email'];

        $auth = load::sys_class('auth', 'new');
        $code = $auth->encode($email, '', 60);

        $this->input['code'] = $email;
        $this->input['title'] = $_M['word']['getTip5'];
        require_once $this->view('app/getpassword_mailset', $this->input);
    }

    /**
     * @return void
     */
    public function doSetPassByEmail()
    {
        global $_M;
        $code = $_M['form']['code'];
        $password = $_M['form']['password'];
        $email = load::sys_class('auth', 'new')->decode($code);

        if (!$email) okinfo($_M['url']['login'], $_M['word']['opfail']);
        if (!$password) okinfo($_M['url']['login'], $_M['word']['opfail']);

        $user = $this->userclass->get_user_by_email($email);
        if (!$user) okinfo($_M['url']['login'], $_M['word']['NoidJS']);

        $res = $this->user_handle->setUserPassword($user, $password);
        if ($res) {
            okinfo($_M['url']['login'], $_M['word']['modifypasswordsuc']);
        } else {
            okinfo($_M['url']['getpassword'], $_M['word']['opfail']);
        }
    }

    /******SMS*****/
    /**
     * @return void|null
     */
    public function doTel()
    {
        global $_M;
        $username = $_M['form']['username'];
        if(!is_phone($username))  okinfo($_M['url']['login'], $_M['word']['NoidJS']);

        $this->input['username'] = $username;
        return $this->view('app/getpassword_telset', $this->input);
    }

    /**
     * 发送找回密码手机验证码
     * @return void
     */
    public function doSendSMS()
    {
        global $_M;
        $tel = $_M['form']['phone'];
        if (!load::sys_class('pin', 'new')->check_pin($_M['form']['code'],$_M['form']['random']) && $_M['config']['met_memberlogin_code'])$this->error($_M['word']['membercode']);

        $user = $this->user_database->getOneByUsername($tel);
        if (!$user)  $this->error($_M['word']['NoidJS']);


        $valid = load::mod_class('user/web/class/valid', 'new');
        if ($valid->smsCode($tel)) {
            $this->success('ok');
        } else {
            $this->error($_M['word']['getFail'], $valid->error);
        }
    }

    /**
     * @return void
     */
    public function doSetPassBySms()
    {
        global $_M;
        $tel = $_M['form']['username'] ?: '';
        $phonecode = $_M['form']['phonecode'] ?: '';
        $password = $_M['form']['password'];

        $valid = load::mod_class('user/web/class/valid', 'new');
        $checkSmsCoode = $valid->checkSmsCoode($phonecode, $tel);
        if (!$checkSmsCoode) okinfo(-1, $valid->errormsg);

        $user = $this->user_database->getOneByUsername($tel);
        if (!$user) okinfo($_M['url']['getpassword'], $_M['word']['NoidJS']);

        $res = $this->user_handle->setUserPassword($user, $password);
        if ($res) {
            okinfo($_M['url']['login'], $_M['word']['modifypasswordsuc']);
        } else {
            okinfo($_M['url']['getpassword'], $_M['word']['opfail']);
        }
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>