<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

/**
 * Class userweb
 */
class userweb extends web {
	public $userclass;

	public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->input['module'] = 10;
        $this->checkLogin();
        $this->userclass = load::sys_class('user', 'new');
        // 页面基本信息
        $_M['config']['app_no'] = 0;
        $data = $this->input_class();
        $this->add_input('page_title', '-' . $data['name'] . $this->input['page_title']);
        $this->add_input('name', $data['name']);
    }

    /**
     * @return bool|void
     */
	public function checkLogin() {
		global $_M;
		$user = $this->get_login_user_info();
        if($user) return true;

        $lang = $_M['form']['lang'] ?  : $_M['lang'];
        $gourl = $_M['form']['gourl'] ?  : '';
        $url = "{$_M['url']['web_site']}member/login.php?";
        $url .= $lang ? "lang={$lang}" : '';
        $url .= $gourl ? "&gourl={$gourl}" : '';
//        $_M['word']['please_login']
        okinfo($url);
	}

	/**
	  * 重写web类的load_url_unique方法，获取前台特有URL
	  */
	protected function load_url_unique() {
		global $_M;
		parent::load_url_unique();
		// load::mod_class('user/user_url', 'new')->insert_m();
	}
}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
