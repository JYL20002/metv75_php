<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin.class.php');
load::sys_class('nav.class.php');
load::sys_func('file');

class login extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->login_label = load::mod_class('login/login_label', 'new');
        $this->sys_session = load::sys_class('session', 'new');
    }

    //获取后台基本信息
    private function get_info()
    {
        global $_M;
        $met_langadmin = DB::get_all("select * from {$_M['table']['lang_admin']} where lang !='metinfo' AND useok = 1");

        $met_admin_logo = "{$_M['url']['site']}" . str_replace('../', '', $_M['config']['met_agents_logo_index']);
        $met_agents_logo_login = "{$_M['url']['site']}" . str_replace('../', '', $_M['config']['met_agents_logo_login']);;

        $data = array(
            'met_agents_linkurl' => $_M['config']['met_agents_linkurl'] ? : 'https://www.mituo.cn',
            'met_agents_logo_login' => $met_agents_logo_login,
            'langset' => $_M['langset'],
            'met_langadmin' => $met_langadmin,
            'met_login_code' => $_M['config']['met_login_code'],
            'url' => $_M['url'],
            'met_admin_type_ok' => $_M['config']['met_admin_type_ok'],
            'met_admin_logo' => $met_admin_logo,
            'lang' => $_M['lang'],
        );

        $sys_json = parent::sys_json();
        $data = array_merge($data, $sys_json);

        return $data;
    }

    /**
     * @return void
     */
    public function doindex()
    {
        global $_M;
        if (get_met_cookie('metinfo_admin_name')) {
            header("Location: {$_M['url']['site_admin']}?lang={$_M['lang']}&n=ui_set");
        }
        $data = $this->get_info();
        $data['referrer'] = HTTP_REFERER;
        if(strstr($data['referrer'],'n=login&')){
            $data['referrer']='';
        }
        $data['random'] = random(4, 1);

        $check_times = $this->sys_session->get('admin_login_eorror_times');//
        if ($_M['config']['met_login_code']) {
            $data['captcha'] = 1;
        } elseif ($_M['config']['met_captcha_open_admin']) {
            $data['captcha'] = 0;
        } elseif ($check_times > 3) {
            $data['captcha'] = 1;
        }

        $_M['url']['own'] = $_M['url']['site'] . 'app/system/login/admin/';
        $_M['url']['own_tem'] = $_M['url']['own'] . 'templates/';
        $_M['url']['own_name'] = $_M['url']['site_admin'] . '?n=login&';
        $_M['url']['own_form'] = $_M['url']['own_name'] . 'c=login&';
        $_M['url']['get_pass'] = $_M['url']['own_name'] . 'c=getpassword&a=doindex&langset=' . urlencode($_M['langset']);
        $login_code = random(12);
        $data['qrcode'] = load::mod_class('weixin/weixinapi', 'new')->getAdminWxLoginImg($login_code);
        $data['login_code']=$login_code;
        if (is_mobile()) {
            $this->view('sys/mobile/admin/templates/index', $data);
        } else {
            $this->view('sys/login/admin/templates/index', $data);
        }
    }

    /**
     * @return void
     */
    public function doGetInfo()
    {
        global $_M;
        $data = $this->get_info();
        $data['config'] = array(
            'met_agents_metmsg' => $_M['config']['met_agents_metmsg'],
            'app_url' => $_M['config']['app_url'],
            'templates_url' => $_M['config']['templates_url'],
            'official_url' => 'https://www.metinfo.cn/',
        );
//        $data['auth'] = self::get_auth();
        $data['auth'] = get_auth($this->admin_member);

        $this->success($data);
    }

    public function doGetWxStatus() {
        global $_M;
        $login_code = $_M['form']['login_code'];
        if(!$login_code){
            $this->error("no code");
        }
        $openid = cache::get("weixin/".$login_code);
        if($openid){
            $res = load::sys_class('user', 'new')->get_admin_by_openid($openid);
            $cookie = $this->login_label->login($res);

            setcookie('page_iframe_url', '', 0, '/');
            if (!isset($_M['form']['submit_type'])) {
                if (is_mobile()) {
                    header('location:./');
                    die;
                } else {
                    header('location:./?n=ui_set');
                    die;
                }
            }
            $this->success($cookie, $_M['word']['log_successfully']);
        }else{
            $this->success('');
        }
    }

    /**
     * @return void
     */
    public function dologin()
    {
        global $_M;
        if ($_M['config']['met_weixinwork_open'])  $this->error('403');//开启企业微信登录后原登录方法失效
        $username = isset($_M['form']['login_name']) ? daddslashes(authcode($_M['form']['login_name'])) : '';
        $password = isset($_M['form']['login_pass']) ? daddslashes(authcode($_M['form']['login_pass'])) : '';

        $check_times = $this->sys_session->get('admin_login_eorror_times') ?: 0;
        $locked = $this->login_label->checkUserLock($username);

        if ($_M['config']['met_login_code']) {
            $captcha_res = load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random']);
            if (!$captcha_res) $this->error($_M['word']['logincodeerror']);
        } elseif ($_M['config']['met_captcha_open_admin']) {// 高级图形验证码
            $checkCode = load::app_class('met_captcha/include/captcha', 'new')->checkCode($_REQUEST['Ticket'], $_REQUEST['Randst']);
            if (!$checkCode) $this->error($_M['word']['logincodeerror']);
        } elseif ($check_times > 3 || $locked) {
            if($locked) $this->sys_session->set('admin_login_eorror_times', 4);
            $captcha_res = load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random']);
            if (!$captcha_res) $this->error($_M['word']['logincodeerror'],array('captcha' => 1));
        }

        if ($_M['config']['met_captcha_open_admin']) {
            $captcha = 0;
        } elseif ($check_times >= 3) {
            $captcha = 1;
        };
        $check_times++;
        $this->sys_session->set('admin_login_eorror_times', $check_times);

        $res = $this->login_label->checkPassword($username, $password);
        if (!$res) {
            //登录日志
            logs::addLog('adminuser', 'loginbypassword', 'failed', $username);
            $this->error($_M['word']['loginpass'], array('captcha' => $captcha));
        }

        logs::addLog('adminuser', 'loginbypassword', 'success', $username);
        $this->sys_session->del('admin_login_eorror_times'); //删除验证次数
        $cookie = $this->login_label->login($res);

        setcookie('page_iframe_url', '', 0, '/');
        if (!isset($_M['form']['submit_type'])) {
            if (is_mobile()) {
                header('location:./');
                die;
            } else {
                header('location:./?n=ui_set');
                die;
            }
        }

        $this->success($cookie, $_M['word']['log_successfully']);
    }

    /**
     * @return void
     */
    public function dologinout()
    {
        global $_M;
        $this->login_label->loginout();
        return;
    }

    public function check()
    {
    }

    //获取所有语言
    public function doGetAllLanguage()
    {
        global $_M;
        $this->success($_M['word']);
    }
}


# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.