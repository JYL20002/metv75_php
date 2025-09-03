<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('logs');

/**
 * Class cache
 */
class logs
{
    /** 添加后台日志记录
     * @param $username
     * @param $name
     * @param $result
     * @return bool|mixed
     */
    public static function addAdminLog($module, $name, $result, $method, $username = '')
    {
        global $_M;
        if (!$_M['config']['met_logs']) {
            return false;
        }
        $get_user = get_met_cookie('metinfo_admin_name');
        $username = $get_user ?: $username;
        if (!$username || !$name || !$result) {
            return false;
        }

        $client = is_mobile() ? "Mobile" : "PC";
        $brower = getbrowser();
        $user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);
        $current_url = $_M['url']['own_form'] . "a={$method}";
        $time = time();
        $ip = getip();

        $query = "INSERT INTO  {$_M['table']['admin_logs']} (name,module,username,current_url,user_agent,ip,result,time,client,brower) VALUES ('{$name}','{$module}','{$username}','{$current_url}','{$user_agent}','{$ip}','{$result}','{$time}','{$client}','{$brower}')";
        DB::query($query);

    }

    /**
     * @param $module
     * @param $name
     * @param $result
     * @param $username
     * @return void
     */
    public static function addLog($module, $name, $result, $username = '')
    {
        global $_M;
        $client = is_mobile() ? "Mobile" : "PC";
        $brower = getbrowser();
        $user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);
        $current_url = addslashes($_SERVER['REQUEST_URI']);
        $time = time();
        $ip = getip();
        $username = htmlentities(sqlinsert($username));
        $query = "INSERT INTO  {$_M['table']['admin_logs']} (name,module,username,current_url,user_agent,ip,result,time,client,brower) VALUES ('{$name}','{$module}','{$username}','{$current_url}','{$user_agent}','{$ip}','{$result}','{$time}','{$client}','{$brower}')";
        DB::query($query);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>