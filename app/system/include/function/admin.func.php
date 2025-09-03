<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 页面跳转
 */
function turnover($url = '', $text = '', $success=1)
{
    global $_M;
    if (!$text) {
        $text = $_M['word']['jsok'];
    }
    if ($text == 'No prompt') {
        $text = '';
    }

    $text = urlencode($text);
    echo("<script type='text/javascript'>location.href='{$url}&turnovertext={$text}&turnovertype=".$success."';</script>");
    exit;
}

/**
 * 获取当前管理员信息
 * @return array  $user 返回记录当前管理员信息和有权限操作的栏目的数组
 */
function admin_information()
{
    global $_M;
    met_cooike_start();
    $metinfo_admin_name = get_met_cookie('metinfo_admin_name');
    $query = "SELECT * from {$_M['table']['admin_table']} WHERE admin_id = '{$metinfo_admin_name}'";
    $user = DB::get_one($query);

//    $query = "SELECT id,name from {$_M['table']['column']} WHERE access <= '{$user['usertype']}' AND lang = '{$_M['lang']}'";
//    $column = DB::get_all($query);
//    $user['column'] = $column;
    return $user;
}

/**
 * 获取当前管理员的权限
 * @return array  $privilege 返回记录当前管理员管理功能权限的数组
 * （metinfo--后台所有功能管理权限;s开头--系统功能;c开头--为内容管理中，前台栏目管理权限;a开头--应用管理权限）
 */
//function background_privilege()
//{
//    global $_M;
//    if (!$_M['privilege']) {
//        $metinfo_admin_name = $_M['user']['admin_name'];
//        $query = "SELECT * from {$_M['table']['admin_table']} WHERE admin_id = '{$metinfo_admin_name}'";
//        $user = DB::get_one($query);
//
//        $privilege = array();
//        $privilege['admin_op'] = $user['admin_op'];
//        if (strstr($user['langok'], "metinfo")) {
//            $privilege['langok'] = $_M['langlist']['web'];
//        } else {
//            $langok = explode('-', $user['langok']);
//            foreach ($langok as $key=>$val) {
//                if ($val) {
//                    $privilege['langok'][$val] = $_M['langlist']['web'][$val];
//                }
//            }
//        }
//        if (strstr($user['admin_type'], "metinfo")) {
//            $privilege['navigation'] = "metinfo";
//            $privilege['column'] = "metinfo";
//            $privilege['application'] = "metinfo";
//            $privilege['see'] = "metinfo";
//        } else {
//            $allidlist = explode('-', $user['admin_type']);
//            foreach ($allidlist as $key=>$val) {
//                if (strstr($val, "s")) {
//                    $privilege['navigation'].= str_replace('s', '', $val)."|";
//                }
//                if (strstr($val, "c")) {
//                    $privilege['column'].= str_replace('c', '', $val)."|";
//                }
//                if (strstr($val, "a")) {
//                    $privilege['application'].= str_replace('a', '', $val)."|";
//                }
//                if ($val == 9999) {
//                    $privilege['see'] = "metinfo";
//                }
//            }
//            $privilege['navigation'] = trim($privilege['navigation'], '|');
//            $privilege['column'] = trim($privilege['column'], '|');
//            $privilege['application'] = trim($privilege['application'], '|');
//        }
//        $_M['privilege'] = $privilege;
//    }
//    return $_M['privilege'];
//}

/**
 * 获取当前管理员有权限操作的栏目信息
 * @return array  $column 返回记录当前管理员有权限操作的栏目信息的数组
 */
//function operation_column($lang = '')
//{
//    global $_M;
//    $lang = $lang ?: $_M['lang'];
//    $jurisdiction = background_privilege();
//    if ($jurisdiction['column'] == "metinfo") {//创始人权限
//        $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' ORDER BY no_order ASC, id DESC";
//        $admin_column = DB::get_all($query);
//    } else {//不同管理员权限
//        $column_id = explode('|', $jurisdiction['column']);
//        $sql_id = ' AND ( id IN (' . implode(',', $column_id) . ' )) ';
//
//        $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' {$sql_id} ORDER BY no_order ASC, id DESC";
//        $admin_column_1 = DB::get_all($query);
//
//        $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' {$sql_id} AND classtype!=1 AND releclass=0 ORDER BY no_order ASC, id DESC";
//        $admin_column_2 = DB::get_all($query);
//
//        foreach ($admin_column_1 as $key => $val) {
//            $admin_column[] = $val;
//        }
//        foreach ($admin_column_2 as $key => $val) {
//            $admin_column[] = $val;
//        }
//    }
//    foreach ($admin_column as $key => $val) {
//        $column[$val['id']] = $admin_column[$key];
//    }
//    return $column;
//}

function adminFolderSafe()
{
    global $_M;
    $result = true;
    if (!$_M['config']['met_safe_prompt']) {
        //判断后来路径是否包含admin和网站关键词
        if (preg_match("/\/admin\/$/", $_M['url']['site_admin'])) {
            $result = false;
        }

        $site_arr = explode('/', rtrim($_M['url']['site_admin'], '/'));
        $admin_name = array_pop($site_arr);
        if ($admin_name == $_M['config']['met_keywords'] && $_M['config']['met_keywords']) {
            $result = false;
        }
    }
    return $result;
}


    /**
     * 检测成员权限
     * @param $type s;f;m;c;a;l
     * @param $aid
     * @param $access
     * @return mixed
     */
function hasPermission($type, $aid, $access = 1)
{
    $member = admin_information();
    $admin_auth = load::mod_class('permission/permission_op', 'new');
    return $admin_auth->ini($member)->hasPermission($type, $aid, $access);
}

/**
 * 获取成员权限
 * @param $type s;c;a
 * @param int $access 0 ,1 ,2
 */
function getPermissions($type, $access = 1)
{
    $member = admin_information();
    $admin_auth = load::mod_class('permission/permission_op', 'new');
    return $admin_auth->ini($member)->getPermissions($type, $access);
}

/**
 * 对当前管理员有权限操作的栏目信息进行整理；
 * @param  int    $type		1；按模块生成;2：按栏目生成
 * @return array  $column	返回把记录当前管理员有权限操作的栏目信息的数组按模块归类或栏目归类整理后的数组
 */
function column_sorting($type = '', $lang = '')
{
    global $_M;
    $lang = $lang ?: $_M['lang'];
//    $information = operation_column($lang);
    $column_list = operationColumn($lang);

    switch ($type) {
        case 1:
            foreach ($column_list as $key=>$val) {
                if ($val['releclass'] != 0) {
                    $sorting[$val['module']]['class1'][$key] = $column_list[$key];
                    $column_classtype[] = $val['id'];
                } else {
                    if ($val['classtype'] == 1) {
                        $sorting[$val['module']]['class1'][$key] = $column_list[$key];
                    }
                    if ($val['classtype'] == 2) {
                        $sorting[$val['module']]['class2'][$key] = $column_list[$key];
                    }
                }
            }
            foreach ($column_list as $key=>$val) {
                $i = 0;
                if ($val['classtype'] == 3) {
                    foreach ($column_classtype as $key1=>$val1) {
                        if ($val['bigclass'] == $val1) {
                            $i = 1;
                        }
                    }
                    if ($i == 1) {
                        $sorting[$val['module']]['class2'][$key] = $column_list[$key];
                    } else {
                        $sorting[$val['module']]['class3'][$key] = $column_list[$key];
                    }
                }
            }
            break;
        case 2:
            foreach ($column_list as $key=>$val) {
                if ($val['classtype'] == 1) {
                    $sorting['class1'][$key] = $column_list[$key];
                }
                if ($val['classtype'] == 2) {
                    $sorting['class2'][$val['bigclass']][$key] = $column_list[$key];
                }
                if ($val['classtype'] == 3) {
                    $sorting['class3'][$val['bigclass']][$key] = $column_list[$key];
                }
            }
            break;
    }

    if (is_array($sorting)) {
        ksort($sorting);
    }
    return $sorting;
}

/**
 * @param string $lang
 */
function operationColumn($lang = '')
{
    global $_M;
    $lang = $lang ?: $_M['lang'];
    $permission = getPermissions('c', 1);
    $aid_list = arrayColumn($permission, 'aid');
    $in_id = implode(',', $aid_list);

    $query = "SELECT * FROM {$_M['table']['column']} WHERE lang = '{$lang}' AND id IN ({$in_id}) ORDER BY no_order ASC, id DESC";
    $admin_column = DB::get_all($query);

    foreach ($admin_column as $key => $val) {
        $column[$val['id']] = $admin_column[$key];
    }
    return $column;
}

/**
 * 获取后台导航栏目数组
 * @return array 返回记录后台导航栏目信息的数组
 */
function get_adminnav()
{
    global $_M;
    $query = "SELECT * FROM {$_M['table']['admin_column']}  WHERE type = 1 ORDER BY list_order";
    $sidebarcolumn = DB::get_all($query);
    $bigclass = array();

    foreach ($sidebarcolumn as $key => $val) {
        if (!hasPermission('s', $val['field'], 1)) continue;

        $val['name'] = get_word($val['menu_lang']);
        $bigclass[$val['bigclass']] = 1;
        $adminnav[$val['id']] = $val;
    }
    return $adminnav;
}

/**
 * 获取应用列表
 */
function get_applist()
{
    global $_M;
    $query = "SELECT * FROM {$_M['table']['applist']} ORDER BY no";
    $app_list = DB::get_all($query);
    foreach ($app_list as $app) {
        $app['url'] = "{$_M['url']['site_admin']}index.php?lang={$_M['form']['lang']}&n={$app['m_name']}&c={$app['m_class']}&a={$app['m_action']}";
        $applist[$app['id']] = $app;
    }
    return $applist;
}

    /**
     * 注册数据表
     * @param string $tablenames 表名称|表名称
     * @return bool
     */
function add_table($tablenames = '')
{
    global $_M;
    $sql = "SELECT * FROM {$_M['table']['config']} WHERE name = 'met_tablename'";
    $res = DB::get_one($sql);
    $met_tables = explode('|', $res['value']);
    $add_tables = explode('|', $tablenames);

    if ($add_tables) {
        foreach ($add_tables as $table_name) {
            $_M['table'][$table_name] = $_M['config']['tablepre'] . trim($table_name);
            $met_tables[] = trim($table_name);
        }

        sort($met_tables);
        $met_tables = array_unique($met_tables);
        $met_tables = implode('|', $met_tables);
        $query = "UPDATE {$_M['table']['config']} SET value = '{$met_tables}' WHERE name='met_tablename'";
        DB::query($query);
    }
    return true;
}

    /**
     * 注销数据表
     * @param string $tablenames 表名称|表名称
     * @return bool
     */
function del_table($tablenames = '')
{
    global $_M;
    $sql = "SELECT * FROM {$_M['table']['config']} WHERE name = 'met_tablename'";
    $res = DB::get_one($sql);
    $met_tables = explode('|', $res['value']);
    $del_tables = explode('|', $tablenames);

    if ($del_tables) {
        foreach ($met_tables as $key => $table_name) {
            if (in_array($table_name, $del_tables)) {
                unset($met_tables[$key]);
                unset($_M['table'][$table_name]);
            }
        }

        sort($met_tables);
        $met_tables = implode('|', $met_tables);
        $query = "UPDATE {$_M['table']['config']} SET value = '{$met_tables}' WHERE name='met_tablename'";
        DB::query($query);
    }
    return true;
}

/**
 * 保存config表配置
 * @param array $config 需要保存的配置的Name数组
 * @param array $config 需要保存的配置的value数组，键值为Name
 * @param array $config 需要保存的配置的语言
 */
function configsave($config = array(), $have = array(), $lang = '')
{
    global $_M;
    if (!$lang) {
        $lang = $_M['lang'];
    }
    if (!$have) {
        $have = $_M['form'];
    }
    $c = copykey($have, $config);
    foreach ($c as $key => $val) {
        $value = mysqlcheck($have[$key]);
        if ($key == 'flash_10001' && $have['mobile'] == '1') {
            if (isset($_M['config'][$key]) && $value != $_M['config'][$key] && (isset($have[$key]) or (isset($have[$key]) && !$have[$key]))) {
                $query = "update {$_M['table']['config']} SET mobile_value = '{$value}' WHERE name = '{$key}' and (lang='{$lang}' or lang='metinfo')";
                DB::query($query);
            }
        } else {
            if (isset($_M['config'][$key]) && $value != $_M['config'][$key] && (isset($have[$key]) or (isset($have[$key]) && !$have[$key]))) {
                $skip = array('met_headstat','met_footstat','met_headstat_mobile','met_footstat_mobile','met_member_email_reg_content','met_member_email_password_content','met_member_email_safety_content','met_agents_copyright_foot','met_agents_copyright_foot1','met_agents_copyright_foot2','met_index_content','met_tools_code','met_404content','met_info_security_statement_content','met_footother','met_member_agreement_content','met_onlinetel','met_foottext','met_seo');
                if(!in_array($key,$skip)){
                    $value = htmlspecialchars($value);
                }
                $query = "update {$_M['table']['config']} SET value = '{$value}' WHERE name = '{$key}' and (lang='{$lang}' or lang='metinfo')";
                DB::query($query);
            }
        }
    }
    buffer::clearConfig();
}

/**
 * 保存应用配置
 * @param array $config  需要保存的配置的Name数组
 * @param string $app_pre 应用名前缀
 * @param string $have   需要保存的配置的value数组，键值为Name
 * @param string $lang   需要保存的配置的语言
 */
function appconfigsave($config = array(), $appno = '', $have = '', $lang = '')
{
    global $_M;
    if ($lang == '') {
        $lang = $_M['lang'];
    }
    if ($have == '') {
        $have = $_M['form'];
    }
    $c = copykey($have, $config);
    foreach ($c as $key => $val) {
        $value = mysqlcheck($have[$key]);
        if ($appno) {
            $query = "SELECT * FROM {$_M['table']['app_config']} WHERE appno='{$appno}' AND name = '{$key}' AND lang = '{$lang}'";
            if (!DB::get_one($query)) {
                $query = "INSERT INTO {$_M['table']['app_config']} SET appno='{$appno}', name = '{$key}', value = '{$val}', lang='{$lang}'";
                DB::query($query);
            } else {
                if (isset($_M['config'][$key]) && $value != $_M['config'][$key] && (isset($have[$key]) or (isset($have[$key]) && !$have[$key]))) {
                    $query = "UPDATE {$_M['table']['app_config']} SET value = '{$value}' WHERE appno='{$appno}' AND name = '{$key}' AND lang='{$lang}'";
                    DB::query($query);
                }
            }
        }
    }
    buffer::clearConfig();
}

/**
 * 保存config表配置
 * @param string $config Name数组
 */
function mysqlcheck($str)
{
    global $_M;
    $str = stripslashes($str);
    $str = str_replace("'", "''", $str);
    $str = str_replace("\\", "\\\\", $str);
    return $str;
}

function setDbConfig($config)
{
    global $_M;
    $file = PATH_CONFIG . 'config_db.php';
    $dbset = parse_ini_file($file);
    foreach ($config as $key => $val) {
        $dbset[$key] = $val;
    }
    if (!isset($dbset['db_type'])) {
        $dbset['db_type'] = 'mysql';
        $dbset['db_name'] = 'config/metinfo.db';
    }

    $string = "<?php\n/*\n";
    foreach ($dbset as $key => $val) {
        $string .= "{$key} = \"{$val}\"\n";
    }
    $string .= '*/?>';
    $fp = fopen($file, 'w+');
    fputs($fp, $string);
    fclose($fp);
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
