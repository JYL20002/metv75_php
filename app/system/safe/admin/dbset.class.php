<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('admin');
load::sys_func('file');

/**
 * 数据库切换
 */
class dbset extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    /**
     * @return void
     */
    private function checkDBType()
    {
        global $_M;
        $db_type = strtolower($_M['form']['db_type']);
        if ($db_type == $_M['config']['db_type']) {
            $this->success('', $_M['word']['jsok']);
        }

        if (!in_array($db_type, array('mysql', 'sqlite', 'dmsql'))) {
            $this->error('参数错误');
        }
        return true;
    }


    /**
     * @return void
     */
    private function checkDbSet()
    {
        global $_M;
        $form = $_M['form'];
        $db_set1 = array('db_host', 'db_username', 'db_name', 'db_prefix');
        foreach ($db_set1 as $name) {
            if (empty($form[$name])) {
                $this->error("Date error: [{$name}] can not empty");
            }
        }

        $db_set2 = array('db_host', 'db_username', 'db_pass', 'db_name', 'db_prefix');
        foreach ($db_set2 as $name) {
            if (strpos($form[$name], "/")) {
                $this->error("Date error : [{$name}] invalidate");
            }
        }
        return true;
    }

    /**
     * 切换数据库
     */
    public function doSaveDatabase()
    {
        global $_M;
        self::checkDBType();

        $db_type = strtolower($_M['form']['db_type']);
        $chtype = strtolower($_M['config']['db_type'] . $db_type);
        switch ($chtype) {
            case 'mysqlsqlite':
                self::mySqlToSqlite();
                logs::addAdminLog('数据库切换', 'mySqlToSqlite', '操作成功', 'doSaveDatabase');
                break;
            case 'sqlitemysql':
                self::checkDbSet();
                self::sqliteToMySql();
                logs::addAdminLog('数据库切换', 'sqliteToMySql', '操作成功', 'doSaveDatabase');
                break;
            case 'mysqldmsql':
                self::checkDbSet();
                self::mySqlToDmSql();
                logs::addAdminLog('数据库切换', 'mySqlToDmSql', '操作成功', 'doSaveDatabase');
                break;
            case 'dmsqlmysql':
                self::checkDbSet();
                self::dmSqlToMySql();
                logs::addAdminLog('数据库切换', 'dmSqlToMySql', '操作成功', 'doSaveDatabase');
                break;
            default:
                $this->error('参数错误');
                break;
        }

        $this->success('', $_M['word']['jsok']);
    }

    /**
     * MySQL->Sqlite
     */
    private function mySqlToSqlite()
    {
        global $_M;
        if ($_M['config']['db_type'] !== 'mysql') {
            $this->error('无法切数据库');
        }

        if (!class_exists('SQLite3')) {
            $this->error('不支持连接SQLite数据库');
        }

        if (!file_exists(PATH_WEB . $_M['config']['db_name'])) {
            $fp = fopen(PATH_WEB . $_M['config']['db_name'], 'w');
            if (!$fp) {
                $this->error(PATH_WEB . $_M['config']['db_name'] . ' File creation failed');
            }
            fclose($fp);
        }
        load::mod_class('databack/transfer', 'new')->mysqlExportSqlite();

        $config['db_type'] = 'sqlite';
        setDbConfig($config);
        return;
    }

    /**
     *Sqlite->MySQL
     */
    private function sqliteToMySql()
    {
        global $_M;
        if ($_M['config']['db_type'] !== 'sqlite') {
            $this->error('无法切数据库');
        }
        if (!function_exists('mysqli_connect')) {
            $this->error('不支持连接MySQL数据库');
        }

        $db_host = $_M['form']['db_host'];
        $db_username = $_M['form']['db_username'];
        $db_pass = $_M['form']['db_pass'];
        $db_name = $_M['form']['db_name'];
        $db_prefix = $_M['form']['db_prefix'];

        if (strstr($db_host, ':')) {
            $arr = explode(':', $db_host);
            $db_host = $arr[0];
            $db_port = $arr[1] ? intval($arr[1]) : 3306;
        } else {
            $db_host = trim($db_host);
            $db_port = 3306;
        }

        $db_prefix = trim($db_prefix);
        $pattern = "/^\w+_$/is";
        $res = preg_match($pattern, $db_prefix);
        if (!$res) $this->error('数据表前缀仅支持数字字母和下划线且使用“_”结尾');

        $db = mysqli_connect($db_host, $db_username, $db_pass, '', $db_port);
        if (!$db) $this->error(mysqli_connect_error()); //数据库连接错误

        if (!@mysqli_select_db($db, $db_name)) {
            $res = mysqli_query($db, "CREATE DATABASE {$db_name} ");
            if (!$res) $this->error('创建数据库失败: ' . mysqli_error($db));   //数据表创建失败
        }

        $mysqli = @new mysqli($db_host, $db_username, $db_pass, $db_name, $db_port);
        if ($mysqli->connect_errno) $this->error($mysqli->connect_error);
        mysqli_select_db($db, $db_name);

        $config['con_db_host'] = $db_host;
        $config['con_db_port'] = $db_port;
        $config['con_db_id'] = $db_username;
        $config['con_db_pass'] = $db_pass;
        $config['con_db_name'] = $db_name;
        $config['tablepre'] = $db_prefix;
        $config['db_type'] = 'mysql';

        load::mod_class('databack/transfer', 'new')->sqliteExportMysql($config);
        setDbConfig($config);
        return;
    }

    /**
     * MySQL->DMSQL
     */
    private function mySqlToDmSql()
    {
        global $_M;
        if ($_M['config']['db_type'] !== 'mysql') {
            $this->error('无法切数据库');
        }

        if (!function_exists('dm_connect')) {
            $this->error('不支持连接达梦数据库');
        }

        $db_username = $_M['form']['db_username'];
        $db_pass = $_M['form']['db_pass'];
        $db_name = $_M['form']['db_name'];
        $db_prefix = $_M['form']['db_prefix'];
        $db_host = $_M['form']['db_host'];

        if (strstr($db_host, ':')) {
            $arr = explode(':', $db_host);
            $db_host = $arr[0];
            $db_port = $arr[1] ? $arr[1] : 5236;
        } else {
            $db_host = trim($db_host);
            $db_port = '5236';
        }

        $db_prefix = trim($db_prefix);
        $pattern = "/^\w+_$/is";
        $res = preg_match($pattern, $db_prefix);
        if (!$res) {
            $this->error('数据表前缀仅支持数字字母和下划线且使用“_”结尾');
        }

        $link = dm_connect("{$db_host}:{$db_port}", $db_username, $db_pass);

        if (!$link) {
            halt(dm_error() . ':' . dm_errormsg());
        }

        $config['con_db_host'] = $db_host;
        $config['con_db_port'] = $db_port;
        $config['con_db_id'] = $db_username;
        $config['con_db_pass'] = $db_pass;
        $config['con_db_name'] = $db_name;
        $config['tablepre'] = $db_prefix;
        $config['db_type'] = 'dmsql';
        load::mod_class('databack/transfer', 'new')->mySQLExportDMSQL($config);

        setDbConfig($config);
        return;
    }

    /**
     * DMSQL->MySQL
     */
    private function dmSqlToMySql()
    {
        global $_M;
        if ($_M['config']['db_type'] !== 'dmsql') {
            $this->error('无法切数据库');
        }

        if (!function_exists('mysqli_connect')) {
            $this->error('不支持连接MySQL数据库');
        }

        $db_username = $_M['form']['db_username'];
        $db_pass = $_M['form']['db_pass'];
        $db_name = $_M['form']['db_name'];
        $db_prefix = $_M['form']['db_prefix'];
        $db_host = $_M['form']['db_host'];

        if (strstr($db_host, ':')) {
            $arr = explode(':', $db_host);
            $db_host = $arr[0];
            $db_port = $arr[1] ? $arr[1] : 3306;
        } else {
            $db_host = trim($db_host);
            $db_port = 3306;
        }

        $db_prefix = trim($db_prefix);
        $pattern = "/^\w+_$/is";
        $res = preg_match($pattern, $db_prefix);
        if (!$res) {
            $this->error('数据表前缀仅支持数字字母和下划线且使用“_”结尾');
        }


        $db = mysqli_connect($db_host, $db_username, $db_pass, '', $db_port);
        if (!$db) {
            $this->error(mysqli_connect_error());
        }

        if (!@mysqli_select_db($db, $db_name)) {
            $res = mysqli_query($db, "CREATE DATABASE {$db_name} ");
            if (!$res) {
                $this->error('创建数据库失败: ' . mysqli_error($db));
            }
        }
        $mysqli = @new mysqli($db_host, $db_username, $db_pass, $db_name, $db_port);
        if ($mysqli->connect_errno) {
            $this->error($mysqli->connect_error);
        }

        mysqli_select_db($db, $db_name);

        $config['con_db_host'] = $db_host;
        $config['con_db_port'] = $db_port;
        $config['con_db_id'] = $db_username;
        $config['con_db_pass'] = $db_pass;
        $config['con_db_name'] = $db_name;
        $config['tablepre'] = $db_prefix;
        $config['db_type'] = 'mysql';
        $res = load::mod_class('databack/transfer', 'new')->dmSQLExportMySQL($config);

        setDbConfig($config);
        return;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
