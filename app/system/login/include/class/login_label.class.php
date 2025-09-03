<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class login_label
{
    public function __construct()
    {
        global $_M;
    }

    public function login($admin = array())
    {
        global $_M;
        $met_cookie = array();
        $met_cookie['time'] = time();
        $met_cookie['metinfo_admin_name'] = urlencode($admin['admin_id']);
        $met_cookie['metinfo_admin_pass'] = $admin['admin_pass'];
        $met_cookie['metinfo_admin_id'] = $admin['id'];
        $met_cookie['metinfo_admin_type'] = $admin['usertype'];
        $met_cookie['metinfo_admin_time'] = time();
        $met_cookie['metinfo_admin_lang'] = $admin['langok'];
        $met_cookie['languser'] = isset($_M['form']['langset']) ? $_M['form']['langset'] : ($admin['admin_login_lang'] ?: $_M['config']['met_admin_type']);
        $m_now_date = date('Y-m-d H:i:s');
        $m_user_ip = get_userip();
        $json = jsonencode($met_cookie);
        $query = "UPDATE {$_M['table']['admin_table']} SET cookie='{$json}',admin_modify_date='{$m_now_date}',admin_login=admin_login+1,admin_modify_ip='{$m_user_ip}' WHERE admin_id = '{$admin['admin_id']}'";
        DB::query($query);
        $met_key = random(7);
        $admin['admin_pass'] = md5($admin['admin_pass']);

        $auth = authcode("{$admin['admin_id']}\t{$admin['admin_pass']}", 'ENCODE', $_M['config']['met_webkeys'] . $met_key, 86400);
        setcookie('met_auth', $auth, 0, '/');
        setcookie('met_key', $met_key, 0, '/');
        setcookie('summarize', '', 0, '/');

        // 设置账号管理的语言
        $lang_ok = explode('-', $admin['langok']);
        $admin_lang = $admin['langok'] == 'metinfo' ? $_M['lang'] : (in_array($_M['lang'], $lang_ok) ? $_M['lang'] : $lang_ok[0]);

        $cookie = array();
        $cookie['met_auth'] = $auth;
        $cookie['met_key'] = $met_key;
        $cookie['admin_lang'] = $admin_lang;

        $query = "UPDATE {$_M['table']['config']} SET `value`=0 WHERE `name`='met_safe_prompt'";
        DB::query($query);

        return $cookie;
    }

    public function loginOut()
    {
        global $_M;
        setcookie('met_auth', '', 0, '/');
        setcookie('met_auths', '', 0, '/');
        setcookie('met_key', '', 0, '/');
        setcookie('page_iframe_url', '', 0, '/');
        setcookie('admin_lang', '', 0, '/');
        setcookie('summarize', '', 0, '/');

        //写日志
        logs::addAdminLog('logintitle', 'indexloginout', 'out_of_success', 'dologinout');
        if (is_mobile()) {
            $this->success('', $_M['word']['out_of_success']);
        } else {
            header('Location: ' . $_M['url']['site_admin']);
        }
    }

    /**
     * @param $admin_id
     * @param $password
     * @return array|null
     */
    public function checkPassword($admin_id, $password)
    {
        global $_M;
        $md5_pass = md5($password);
        $query = "SELECT * FROM {$_M['table']['admin_table']} WHERE admin_id = '{$admin_id}' AND admin_pass = '{$md5_pass}'";
        $admin = DB::get_one($query);
        return $admin ?: null;
    }

    /**
     * 检测账号登录限制
     * @param $username
     * @return bool
     */
    public function checkUserLock($username)
    {
        global $_M;
        $username = addslashes(stripslashes($username));
        $sql = "SELECT * FROM {$_M['table']['admin_logs']} 
                WHERE `module` = 'adminuser' 
                AND `name` = 'loginbypassword' 
                AND username = '{$username}' 
                ORDER BY time DESC 
                LIMIT 3";
        $logs = DB::get_all($sql);

        if ($logs) {
            $result_arr = arrayColumn($logs, 'result');
            if (count($logs) >= 3 && !in_array('success', $result_arr)) {
                return true;
            }
        }
        return false; //未锁定
    }

}


# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.