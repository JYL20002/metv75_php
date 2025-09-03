<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class session
{
    public function __construct()
    {
        global $_M;
        self::start();
    }

    public function start()
    {
        session_start();

//        session_id();
//        session_regenerate_id();
//        $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
//        setcookie(session_name(), session_id(), 0, '/','', $secure, true);
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $_SESSION[$name];
    }

    /**
     * @param $name
     */
    public function del($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * session destroy
     */
    public function destroy()
    {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setCookie(session_name(), "", time()-3600, "/");
        }
        session_destroy();
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>