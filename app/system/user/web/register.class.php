<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('user/web/class/userweb');

class register extends userweb
{
    public $paralist;
    public $paraclass;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        if (!$_M['config']['met_member_register']) okinfo($_M['url']['login'], $_M['word']['regclose']);

        isset($_SESSION) ? "" : load::sys_class('session', 'new');
        $this->paraclass = load::sys_class('para', 'new');
        $this->paralist = $this->paraclass->get_para_list(10);
        $_M['paralist'] = $this->paralist;
        $_M['paraclass'] = $this->paraclass;

        $this->user_database = load::mod_class('user/user_database', 'new');
        $this->user_handle = load::mod_class('user/user_handle', 'new');
    }

    /**
     * @param $pid
     * @return bool
     */
    public function checkLogin()
    {
    }

    public function doindex()
    {
        global $_M;
        if ($_M['user']['id']) okinfo($_M['url']['user_home']);

        $this->add_input('page_keywords', $_M['config']['met_keywords']);
        $this->add_input('page_description', $_M['config']['met_description']);
        $this->add_input('page_title', $_M['word']['memberReg'] . $this->input['page_title']);
        if ($_M['config']['met_memberlogin_code']) {//开启系统验证码
            $this->add_input('captcha', 1);
            $this->add_input('random', random(4, 1));
        }
        $this->view('app/register', $this->input);
    }

    /**
     * @return void
     */
    public function dosave()
    {
        global $_M;
        $form = $_M['form'];
        $user_para = $this->paraclass->form_para($form, 10);
        self::checkParaField();
        
        switch ($_M['config']['met_member_vecan']) {
            case 1://邮箱注册
                self::registByEmail($user_para);
                break;
            case 3://手机注册
                self::registByTel($user_para);
                break;
            default ://默认注册
                self::registDefault($user_para);
                break;
        }
        return;
    }

    /**
     * 邮箱注册
     * @param array $info
     */
    private function registByEmail($user_para = array())
    {
        global $_M;
        $username = $email = $_M['form']['username'];
        $password = $_M['form']['password'];
        self::captchCheck();

        $user = $this->user_database->getOneByUsername($username);
        if ($user) {
            if ($user['valid']) okinfo(-1, $_M['word']['emailhave']);
            $user_id = $user['id'];
        } else {
            $new_id = $this->user_handle->createUser($username, $password, $email);
            if (!$new_id) okinfo(-1, $_M['word']['regfail']);
            $this->user_handle->setUsetPara($new_id, $user_para);
        }

        //发送激活邮件
        $valid = load::mod_class('user/web/class/valid', 'new');
        if ($valid->sendEmail($_M['form']['username'], 'register')) {
            //管理员通知
            self::adminNotice($_M['form']['username']);

            $this->userclass->login_by_password($username, $password);
            okinfo($_M['url']['profile'], $_M['word']['emailchecktips1']);
        } else {
            okinfo(-1, $_M['word']['emailfail']);
        }
        return true;
    }

    /**
     * 手机注册
     * @param array $info
     */
    private function registByTel($user_para = array())
    {
        global $_M;
        $username = $tel = $_M['form']['phone'];
        $password = $_M['form']['password'];
        $phonecode = $_M['form']['phonecode'];

        if ($_M['config']['met_captcha_open']) {//高级图形验证码
            $checkCode = load::app_class('met_captcha/include/captcha', 'new')->checkCode($_REQUEST['Ticket'], $_REQUEST['Randst']);
            if (!$checkCode) okinfo($_M['url']['user_home'], $_M['word']['membercode']);
        }

        //手机验证码检测
        $valid = load::mod_class('user/web/class/valid', 'new');
        $checkSmsCoode = $valid->checkSmsCoode($phonecode, $tel);
        if (!$checkSmsCoode) okinfo(-1, $valid->errormsg);

        $new_id = $this->user_handle->createUser($username, $password, '', $tel, 1);
        if (!$new_id) okinfo(-1, $_M['word']['regfail']);
        $this->user_handle->setUsetPara($new_id, $user_para);
        //管理员通知
        self::adminNotice($_M['form']['username']);

        $this->userclass->login_by_password($username, $password);
        okinfo($_M['url']['profile'], $_M['word']['regsuc']);
        return true;
    }

    /**
     * 用户名注册
     * @param array $info
     */
    private function registDefault($user_para = array())
    {
        global $_M;
       
        $username = $tel = $_M['form']['username'];
        $password = $_M['form']['password'];
        
        self::captchCheck();
        
        $valid = $_M['config']['met_member_vecan'] == 2 ? 0 : 1;
        $new_id = $this->user_handle->createUser($username, $password, '', '', $valid);
        
        if (!$new_id) okinfo(-1, $_M['word']['regfail'].$this->user_handle->error);
        $this->user_handle->setUsetPara($new_id, $user_para);
        if ($new_id) {
            //管理员通知
            self::adminNotice($_M['form']['username']);

            $this->userclass->login_by_password($username, $password);
            $turnovertext = $_M['config']['met_member_vecan'] == 2 ? $_M['word']['js25'] : $_M['word']['regsuc'];
            okinfo($_M['url']['profile'], $turnovertext);
        } else {
            okinfo(-1, $_M['word']['regfail']);
        }
        return true;
    }

    /**
     * 图形验证码检测
     * @return bool
     */
    protected function captchCheck()
    {
        global $_M;
        //验证码
        if ($_M['config']['met_memberlogin_code']) {
            $pinok = load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random']);
            if (!$pinok) okinfo(-1, $_M['word']['membercode']);
        } elseif ($_M['config']['met_captcha_open']) {//高级图形验证码
            $checkCode = load::app_class('met_captcha/include/captcha', 'new')->checkCode($_REQUEST['Ticket'], $_REQUEST['Randst']);
            if (!$checkCode) okinfo($_M['url']['user_home'], $_M['word']['membercode']);
        }
        return true;
    }

    /**
     * 管理员通知
     * @param $usernaem
     * @return void
     */
    private function adminNotice($usernaem = '')
    {
        global $_M;
        //管理员通知
        $this->notice = $other = load::mod_class('user/web/class/notice', 'new');;
        $this->notice->notice_by_emial($usernaem);
        $this->notice->notice_by_sms($usernaem);
    }

    /**
     * 字段检测
     * @return bool
     */
    private function checkParaField()
    {
        global $_M;
        $parameters = load::mod_class('parameter/parameter_database', 'new')->get_parameter(10);

        //必填属性验证
        foreach ($parameters as $para) {
            $para_val = $_M['form']['info_' . $para['id']];
            if ($para['wr_ok'] == 1 && $para['access'] == 0 ) {
                if (empty($para_val)) {
                    $info = "【{$para['name']}】" . $_M['word']['noempty'];
                    okinfo('javascript:history.back();', $info);
                }
            }
        }
        return true;
    }

    /**
     * 检测用户名
     * @return false|void
     */
    public function douserok()
    {
        global $_M;
        $username = $_M['form']['username'];
        $valid = true;

        $has_admin = $this->user_database->hasAdminMember($username);
        if ($has_admin) return false;

        $has_user = $this->user_database->getOneByUsername($username);
        if ($has_user && $has_user['valid']) return false;

        $data = array('valid' => $valid);
        $this->ajaxReturn($data);
    }

    /**
     * 获取短信验证码
     */
    public function dophonecode()
    {
        global $_M;
        $tel = $_M['form']['phone'];
        $pinok = load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random']);
        if (!$pinok) $this->error($_M['word']['membercode']);

        if ($this->user_database->getOneByUsername($tel)) {
            $this->error($_M['word']['telreg']);
        }

        $valid = load::mod_class('user/web/class/valid', 'new');
        $res = $valid->smsCode($tel);
        if (!$res){
            $this->error($valid->error);
            $this->error($_M['word']['getFail'], $valid->error);
        }
        $this->success('', $_M['word']['getOK']);
    }

    /**
     * 邮箱激活
     * @return void
     */
    public function doemailvild()
    {
        global $_M;
        $auth = load::sys_class('auth', 'new');
        $username = $auth->decode($_M['form']['p']);
        $username = sqlinsert($username);
        if (!$username) okinfo($_M['url']['register'], $_M['word']['emailvildtips2']);

        $has_admin = $this->user_database->hasAdminMember($username);
        if ($has_admin) okinfo($_M['url']['register'], $_M['word']['emailhave']);

        $user = $this->user_database->getOneByUsername($username);
        if ($user && !$user['valid']) {
            $this->user_database->setUserAttr($user, 'valid', 1);
            okinfo($_M['url']['login'], $_M['word']['activesuc']);
        }else{
            okinfo($_M['url']['register'], $_M['word']['emailvildtips1']);
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>