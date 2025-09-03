<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('web');

class verifier extends web {
    public $error;

    public function __construct() {
        parent::__construct();
        $this->sys_session = load::sys_class('session', 'new');
    }

    /**
     * 发送手机验证码
     * @param null $tel
     * @param null $random
     * @return bool
     */
    public function smsCode($tel = null, $random = null)
    {
        global $_M;
        if (!$tel || !$random) {
            return false;
        }

        $session_key = "{$tel}_{$random}";
        $session = $this->sys_session->get($session_key);
        if (
            isset($session['expires'])
            && $session['expires'] > time()
            && isset($session['tel'])
            && $session['tel'] == $tel
        ) {
            //在有效期内
            $this->error = "请勿重复操作";
            return false;
        }

        $smsTicket = random(6, 1);
        $session_data = array(
            'expires' => time() + 60,
            'tel' => $tel,
            'smsTicket' => $smsTicket,
        );
        $this->sys_session->set($session_key, $session_data);

        $sms_msg = "{$_M['word']['usesendcode']}{$smsTicket}{$_M['word']['usesendcodeinfo']}";
        $res = load::sys_class('sms', 'new')->sendsms($tel, $sms_msg);
        if (isset($res['status'])  && $res['status'] == 200) {
            return true;
        }

        $this->error = $res['msg'] ?: $_M['word']['getFail'];
        return false;
    }

    /**
     * 检测手机验证码
     * @param null $code
     * @param null $session_key
     * @return bool
     */
    public function checkSmsCode($code = null ,$tel = null, $random = null)
    {
        global $_M;
        if (!$code || !$tel || !$random) {
            return false;
        }

        $session_key = "{$tel}_{$random}";
        $session = $this->sys_session->get($session_key);
        if (!$session) {
            $this->error = $_M['word']['telcheckfail'];
            return false;
        };

//        if ($session['expires'] < time()) {
//            $this->sys_session->del($session_key);
//            $this->error = $_M['word']['codetimeout'];
//            return false;
//        }

        if ($code != $session['smsTicket']) {
            $this->error = $_M['word']['telcheckfail'];
            return false;
        }

        $this->sys_session->del($session_key);
        return true;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
