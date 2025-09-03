<?php
// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

if (!defined('IN_MET')) {
    define('IN_MET', true);
}

require_once '../app/system/include/function/web.func.php';
require_once '../app/system/include/function/admin.func.php';
require_once 'install_mysql.php';
require_once 'install_dmsql.php';

class install
{
    public $error;
    protected $sys_ver;

    public function __construct()
    {
        global $_M, $siteurl;
        header('Content-type: text/html;charset=utf-8');
        date_default_timezone_set('UTC');
        error_reporting(E_ERROR | E_PARSE);
        @set_time_limit(0);
        ini_set('magic_quotes_runtime', 0);
        session_start();

        $this->sys_ver = "8.0";
        $this->skin_name = "metv75";
        $this->error = array();
        $siteurl = $this->geturl();

        self::checkInstallLock();
        self::checkVer();
        self::getFormData();
        self::deldir_in('../cache', 1);
    }

    private function checkInstallLock()
    {
        if (file_exists('../config/install.lock')) {
            exit('对不起，该程序已经安装过了。<br/>
	      如你要重新安装，请手动删除config/install.lock文件。');
        }
    }

    public function checkVer()
    {
        $sys_entrance = '../app/system/entrance.php';
        if (!file_exists($sys_entrance)) exit('安装包文件缺失');
        $file_data = file_get_contents($sys_entrance);

        $pattern = "/\(\'SYS_VER\'.+\'(.+)\'\)\;/";
        preg_match($pattern, $file_data, $match);

        $file_ver = isset($match[1]) ? $match[1] : null;
        if (!version_compare($file_ver, $this->sys_ver, '=')) {
            $body  = "当前安装版本与系统文件版本不匹配，请下载<a href='https://www.metinfo.cn/download'  arget='_blank'>米拓企业建站系统v {$file_ver}</a>安装包";
            exit($body);
        }
    }

    public function geturl()
    {
        if ($_SERVER['SERVER_PORT'] == 443 || $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 || $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https' || $_SERVER['HTTP_FROM_HTTPS'] == 'on' || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || $_SERVER['HTTP_SCHEME'] == 'https') {
            $http = 'https://';
        } else {
            $http = 'http://';
        }

        return $http . $_SERVER['HTTP_HOST'] . preg_replace("/[0-9A-Za-z-_]+\/\w+\.php$/", '', $_SERVER['PHP_SELF']);
    }

    /**
     * 检查米拓API.
     * @param $post
     * @param $timeout
     * @return mixed
     */
    public function curl_post($post, $timeout = 20)
    {
        $post['referer'] = $referer = $this->geturl();
        $post['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $api = 'https://u.mituo.cn/api/metinfo/install';
        $result = '';

        if (get_extension_funcs('curl') && function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_close')) {
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $api);
            curl_setopt($curlHandle, CURLOPT_REFERER, $referer);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($curlHandle);
            curl_close($curlHandle);
        }
        return json_decode(trim($result), true);;
    }

    public function doInstall()
    {
        $action = $_GET['action'];
        switch ($action) {
            case 'apitest':
                $post = array('domain' => $_SERVER['HTTP_HOST']);
                $res = self::curl_post($post, 15);
                if (isset($res['status'])) {
                    exit('ok');
                } else {
                    exit('nohost');
                }
                break;
            case 'skipInstall':
                if (!class_exists('SQLite3')) {
                    exit('你的环境不支持使用SQLite数据库');
                }
                self::setInstallLock();
                $db = array('db_type' => 'sqlite', 'tablepre' => 'met_');
                define('PATH_CONFIG', '../config/');
                setDbConfig($db);
                header('location:../index.php');
                break;
            default:
                $_SESSION['install'] = 'metinfo';
                $m_now_time = time();
                $m_now_date = date('Y-m-d H:i:s', $m_now_time);
                $nowyear = date('Y', $m_now_time);
                $sys_ver = $this->sys_ver;
                include $this->template('index');
                break;
        }
    }

    /**
     * 系统环境检测.
     */
    private function inspect()
    {
        global $_M;
        if ($_SERVER['SERVER_PORT'] == 443 || $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 || $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https' || $_SERVER['HTTP_FROM_HTTPS'] == 'on' || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $http = 'https://';
        } else {
            $http = 'http://';
        }

        $db_type = in_array(strtolower($_M['form']['db_type']), array('mysql', 'dmsql')) ? strtolower($_M['form']['db_type']) : 'mysql';

        $site_url = str_ireplace('/index.php', '', $http . $_SERVER['HTTP_HOST'] . preg_replace("/[0-9A-Za-z-_]+\/\w+\.php$/", '', $_SERVER['PHP_SELF']));
        require_once '../app/system/include/class/handle.class.php';
        $handle = new handle();
        $data = $handle->checkFunction($site_url);

        if ($db_type == 'dmsql') {
            $data[] = array(
                0 => 'dm_connect',
                1 => function_exists('dm_connect') ? 'success' : 'danger',
                2 => function_exists('dm_connect') ? '支持' : '不支持达梦数据库连接<a href="https://www.mituo.cn/qa" target="_blank">[帮助]</a>',
            );
        }
        $dirs = $handle->checkDirs();


        include $this->template('inspect');
    }

    private function db_setup()
    {
        global $_M;
        if ($_M['form']['db_type'] == 'mysql') {
            $install_mysql = new install_mysql();
            $install_mysql->db_setup_mysql();
//            self::db_setup_mysql();
        } elseif ($_M['form']['db_type'] == 'dmsql') {
            $install_mysql = new install_dmsql();
            $install_mysql->db_setup_dmsql();
//            self::db_setup_dmsql();
        }
    }

    private function adminsetup()
    {
        global $_M;
        if ($_M['form']['db_type'] == 'mysql') {
            $install_mysql = new install_mysql();
            $install_mysql->adminsetup_mysql();
//            self::adminsetup_mysql();
        } elseif ($_M['form']['db_type'] == 'dmsql') {
            $install_dmsql = new install_dmsql();
            $install_dmsql->adminsetup_dmsql();
//            self::adminsetup_dmsql();
        }
    }

    protected function setInstallLock()
    {
        global $_M;
        $fp = @fopen('../config/install.lock', 'w');
        $str = "";
        @fwrite($fp, $str);
        @fclose($fp);
        @chmod('../config/install.lock', 0554);

         $data = array(
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $this->sys_ver,
            'db_type' => $_M['form']['db_type'],
            'info' => json_encode(array('php_ver' => PHP_VERSION)),
        );
        self::curl_post($data, 20);
        return;
    }


    /***************/
    /**
     * 报错.
     */
    public function error()
    {
        global $_M;
        $error_data = '';
        foreach ($this->error as $row) {
            $error_data .= '<li class="danger">' . $row . '</li>';
        }
        include $this->template('error');
        die();
    }

    /**
     * 获取表单内容.
     */
    private function getFormData()
    {
        global $_M;
        isset($_REQUEST['GLOBALS']) && exit('Access Error');
        foreach ($_COOKIE as $key => $val) {
            $_M['form'][$key] = self::daddslashes($val);
        }
        foreach ($_GET as $key => $val) {
            $_M['form'][$key] = self::daddslashes($val);
        }
        foreach ($_POST as $key => $val) {
            $_M['form'][$key] = self::daddslashes($val);
        }
    }

    /**
     * 安装空白模板
     * @param $db
     * @param $templates
     * @param $skin_name
     * @param $lang
     */
    public function installTagTemplates($db, $templates, $skin_name, $lang)
    {
        $template_json = "../templates/{$skin_name}/install/template.json";
        if (!file_exists($template_json)) return;

        $configs = json_decode(file_get_contents($template_json), true);
        $query = "DELETE FROM {$templates} WHERE no = '{$skin_name}' AND lang = '{$lang}'";
        $db->query($query);
        foreach ($configs as $k => $v) {
            $cid = $v['id'];
            $sub = $v['sub'];
            $v['lang'] = $lang;
            unset($v['id'], $v['sub']);
            $v['no'] = $skin_name;
            $area_sql = $this->get_sql($v, 'insert');
            $query = "INSERT INTO {$templates} {$area_sql}";
            $db->query($query);
            $area_id = $db->insert_id();

            foreach ($sub as $m => $s) {
                unset($s['id']);
                $s['bigclass'] = $area_id;
                $s['lang'] = $lang;
                $s['no'] = $skin_name;
                $sub_sql = $this->get_sql($s, 'insert');
                $sub_query = "INSERT INTO {$templates} {$sub_sql}";
                $db->query($sub_query);
            }
        }
        return true;
    }

    /***************/
    /**
     * 加载模板
     *
     * @param $template
     * @param string $ext
     *
     * @return string
     */
    public function template($template, $ext = 'php')
    {
        global $met_skin_user, $skin;
        unset($GLOBALS['con_db_id'], $GLOBALS['con_db_pass'], $GLOBALS['con_db_name']);
        $path = "templates/$template.$ext";

        return $path;
    }

    /**
     * 参数过滤转义.
     * @param $string
     * @param int $force
     * @return array|string
     */
    public function daddslashes($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = daddslashes($val);
            }
        } else {
            $string = self::sqlinsert($string);
            $string = addslashes($string);
        }

        return $string;
    }

    private function sqlinsert($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::sqlinsert($val);
            }
        } else {
            $string_old = $string;
            $string = str_ireplace('*', '/', $string);
            $string = str_ireplace('%5C', '/', $string);
            $string = str_ireplace('%22', '/', $string);
            $string = str_ireplace('%27', '/', $string);
            $string = str_ireplace('%2A', '/', $string);
            $string = str_ireplace('~', '/', $string);
            $string = str_ireplace('select', "\sel\ect", $string);
            $string = str_ireplace('insert', "\ins\ert", $string);
            $string = str_ireplace('update', "\up\date", $string);
            $string = str_ireplace('delete', "\de\lete", $string);
            $string = str_ireplace('union', "\un\ion", $string);
            $string = str_ireplace('into', "\in\to", $string);
            $string = str_ireplace('load_file', "\load\_\file", $string);
            $string = str_ireplace('outfile', "\out\file", $string);
            $string = str_ireplace('sleep', "\sle\ep", $string);
            $string = strip_tags($string);
            if ($string_old != $string) {
                $string = '';
            }
            $string = str_ireplace('\\', '/', $string);
            $string = trim($string);
        }

        return $string;
    }

    public function readover($filename, $method = 'rb')
    {
        if ($handle = @fopen($filename, $method)) {
            flock($handle, LOCK_SH);
            $filedata = @fread($handle, filesize($filename));
            fclose($handle);
        }

        return $filedata;
    }

    /**
     * @param $length
     * @return string
     */
    public function met_rand_i($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; ++$i) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /**
     * @param $i
     * @return string
     */
    public function randStr($i)
    {
        $str = 'abcdefghijklmnopqrstuvwxyz';
        $finalStr = '';
        for ($j = 0; $j < $i; ++$j) {
            $finalStr .= substr($str, mt_rand(0, 25), 1);
        }

        return $finalStr;
    }

    private function deldir_in($fileDir, $type = 0)
    {
        @clearstatcache();
        $fileDir = substr($fileDir, -1) == '/' ? $fileDir : $fileDir . '/';
        if (!is_dir($fileDir)) {
            return false;
        }
        $resource = opendir($fileDir);
        @clearstatcache();
        while (($file = readdir($resource)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($fileDir . $file)) {
                self::delfile_in($fileDir . $file);
            } else {
                self::deldir_in($fileDir . $file);
            }
        }
        closedir($resource);
        @clearstatcache();
        if ($type == 0) {
            rmdir($fileDir);
        }

        return true;
    }

    private function delfile_in($fileUrl)
    {
        @clearstatcache();
        if (file_exists($fileUrl)) {
            unlink($fileUrl);

            return true;
        } else {
            return false;
        }
        @clearstatcache();
    }

    /**
     * @param array $data
     * @param int $type
     * @return string
     */
    public function get_sql($data = array(), $type = '')
    {
        switch ($type) {
            default:
            case 'set':
                $sql = '';
                foreach ($data as $key => $value) {
                    if (strstr($value, "'")) {
                        $value = str_replace("'", "\'", $value);
                    }
                    $sql .= " {$key} = '{$value}',";
                }
                $sql = trim($sql, ',');
                break;
            case 'insert':
                $keys_str = implode(' ,',array_keys($data));

                $values_str = '';
                foreach ($data as $key => $val) {
                    $val = addslashes(stripslashes($val));
                    $values_str .= "'{$val}',";
                }
                $values_str = trim($values_str, ',');
                $sql = " ({$keys_str}) VALUES ({$values_str}) ";
        }
        return $sql;
    }
}

function error($msg = '', $status = 0, $data = '', $json_option = 0)
{
    header('Content-Type:application/json; charset=utf-8');
    $error['msg'] = $msg;
    $error['status'] = $status;
    if ($data) {
        $error['data'] = $data;
    }
    $return_data = json_encode($error, $json_option);
    exit($return_data);
}

$install = new install();
$install->doInstall();

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
