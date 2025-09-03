<?php
    include_once __DIR__ . '/index.php';

    class install_mysql extends install
    {
        public function __construct()
        {
            parent::__construct();
        }

        /******MySQL******/
        public function db_setup_mysql()
        {
            global $_M, $db_prefix;
            $setup = $_M['form']['setup'];
            $db_prefix = $_M['form']['db_prefix'];
            $db_host = $_M['form']['db_host'];
            $db_username = $_M['form']['db_username'];
            $db_pass = $_M['form']['db_pass'];
            $db_name = $_M['form']['db_name'];
            $cndata = $_M['form']['cndata'];
            $endata = $_M['form']['endata'];
            $showdata = $_M['form']['showdata'];
            $admin_cndata = $_M['form']['admin_cndata'];
            $admin_endata = $_M['form']['admin_endata'];
            $lang = $_M['form']['lang'];
            $db_type = 'mysql';

            if ($setup == 1) {
                $db_prefix = trim($db_prefix);
                $pattern = "/^\w+_$/is";
                $res = preg_match($pattern, $db_prefix);
                if (!$res) {
                    $this->error[] = '数据表前缀仅支持数字字母和下划线且使用“_”结尾';
                    $this->error();
                }

                $this->db_prefix = $db_prefix;
                if (strstr($db_host, ':')) {
                    $arr = explode(':', $db_host);
                    $db_host = $arr[0];
                    $db_port = $arr[1];
                } else {
                    $db_host = trim($db_host);
                    $db_port = '3306';
                }
                $db_username = trim($db_username);
                $db_pass = trim($db_pass);
                $db_name = trim($db_name);
                $db_port = trim($db_port);
                $config = "<?php
                   /*
                   db_type = \"mysql\"
                   db_name = \"config/metinfo.db\"
                   con_db_host = \"$db_host\"
                   con_db_port = \"$db_port\"
                   con_db_id   = \"$db_username\"
                   con_db_pass	= \"$db_pass\"
                   con_db_name = \"$db_name\"
                   tablepre    =  \"$db_prefix\"
                   db_charset  =  \"utf8\"
                  */
                  ?>";

                $fp = fopen('../config/config_db.php', 'w+');
                fputs($fp, $config);
                fclose($fp);

                //创建连接
                $db = mysqli_connect($db_host, $db_username, $db_pass, '', $db_port);
                if (!$db) {
                    $this->error[] = '连接数据库失败: ' . mysqli_connect_error();
                    $this->error();
                }

                if (!@mysqli_select_db($db, $db_name)) {//创建数据库
                    $res = mysqli_query($db, "CREATE DATABASE $db_name CHARACTER SET utf8 COLLATE utf8_general_ci;");
                    if (!$res) {
                        $this->error[] = '创建数据库失败: ' . mysqli_error($db);
                        $this->error();
                    }
                }
                //选择数据库
                mysqli_select_db($db, $db_name);

                //设置字符集
                #if (mysqli_get_server_info($db) > 4.1) {
                if (version_compare(mysqli_get_server_info($db), '4.1', '>')) {
                    #mysqli_query($db, 'set names utf8');
                    mysqli_set_charset($db, 'utf8');
                }
                #if (mysqli_get_server_info($db) > '5.0.1') {
                if (version_compare(mysqli_get_server_info($db), '5.0.1', '>')) {
                    mysqli_query($db, "SET sql_mode=''");
                }
                #if (mysqli_get_server_info($db) >= '4.1') {
                if (version_compare(mysqli_get_server_info($db), '4.1', '>')) {
                    mysqli_set_charset($db, 'utf8');
                    $content = self::readover('sql/mysql_install.sql');
                    //$content=preg_replace("/{#(.+?)}/eis",'$lang[\\1]',$content);
                    $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                        return $lang[$r[1]];
                    }, $content);
                    $installinfo = self::creat_table_mysql($content, $db);
                } else {
                    echo "<SCRIPT language=JavaScript>alert('你的mysql版本过低，请确保你的数据库编码为utf-8,官方建议你升级到mysql4.1.0以上');</SCRIPT>";
                    /* $content = self::readover('sql/mysql_install.sql');
                     $content = str_replace('ENGINE=MyISAM DEFAULT CHARSET=utf8', 'TYPE=MyISAM', $content);*/
                }

                //前台语言及配置
                if ($cndata == 'yes') {
                    $content = self::readover('sql/config_cn.sql');
                    //$content=preg_replace("/{#(.+?)}/eis",'$lang[\\1]',$content);
                    $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                        return $lang[$r[1]];
                    }, $content);
                    $installinfo .= self::creat_table_mysql($content, $db);
                }
                if ($endata == 'yes') {
                    $content = self::readover('sql/config_en.sql');
                    //$content=preg_replace("/{#(.+?)}/eis",'$lang[\\1]',$content);
                    $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                        return $lang[$r[1]];
                    }, $content);
                    $installinfo .= self::creat_table_mysql($content, $db);
                }

                //演示数据
                if ($showdata == 'yes') {
                    if ($cndata == 'yes') {
                        $content = self::readover('sql/mysql_demo_cn.sql');
                        //$content=preg_replace("/{#(.+?)}/eis",'$lang[\\1]',$content);
                        $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                            return $lang[$r[1]];
                        }, $content);
                        $installinfo .= self::creat_table_mysql($content, $db);
                    }
                    if ($endata == 'yes') {
                        $content = self::readover('sql/mysql_demo_en.sql');
                        //$content=preg_replace("/{#(.+?)}/eis",'$lang[\\1]',$content);
                        $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                            return $lang[$r[1]];
                        }, $content);
                        $installinfo .= self::creat_table_mysql($content, $db);
                    }
                }
                $met_index_type = 'cn';
                if ($cndata != 'yes' && $endata == 'yes') {
                    $met_index_type = 'en';
                }

                //后台多语言
                if ($admin_cndata == 'yes') {
                    $content = self::readover('sql/admin_lang_cn.sql');
                    $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                        return $lang[$r[1]];
                    }, $content);
                    $installinfo .= self::creat_table_mysql($content, $db);
                }
                if ($admin_endata == 'yes') {
                    $content = self::readover('sql/admin_lang_en.sql');
                    $content = preg_replace_callback('/{#(.+?)}/is', function ($r) use ($lang) {
                        return $lang[$r[1]];
                    }, $content);
                    $installinfo .= self::creat_table_mysql($content, $db);
                }
                $met_admin_type = 'cn';
                if ($admin_cndata != 'yes' && $admin_endata == 'yes') {
                    $met_admin_type = 'en';
                }

                if ($this->error) {
                    $this->error();
                }
                $rand_i = self::met_rand_i(32);
                file_put_contents('../config/config_safe.php', '<?php /*' . $rand_i . '*/?>');
                echo "--><script>location.href=\"index.php?action=adminsetup&cndata={$cndata}&endata={$endata}&met_index_type={$met_index_type}&met_admin_type={$met_admin_type}&showdata={$showdata}&db_type={$db_type}\";</script>";
                exit;
            } else {
                include $this->template('databasesetup');
            }
        }

        /**
         * 创建数据表.
         * @param $content
         * @param $link
         * @return string
         */
        private function creat_table_mysql($content, $link)
        {
            global $installinfo, $db_prefix, $db_setup;
            $sql = explode("\n", $content);
            $query = '';
            $j = 0;
            $i = 0;
            foreach ($sql as $key => $value) {
                $value = trim($value);
                if (!$value || $value[0] == '#') {
                    continue;
                }

                if (preg_match("/\;$/", $value)) {
                    $query .= $value;
                    if (preg_match('/^CREATE/', $query)) {
                        $name = substr($query, 13, strpos($query, '(') - 13);
                        $c_name = str_replace('met_', $db_prefix, $name);
                        ++$i;
                    }
                    $query = str_replace('met_', $db_prefix, $query);
                    $query = str_replace('metconfig_', 'met_', $query);
                    if (!mysqli_query($link, $query) && mysqli_error($link)) {
                        $db_setup = 0;
                        if ($j != '0') {
                            if (!strstr(mysqli_error($link), 'Duplicate entry')) {
                                $this->error[] = '<li class="danger">出错：' . mysqli_error($link) . '<br/>sql:' . $query . '</li>';
                            }
                        }
                    } else {
                        if (preg_match('/^CREATE/', $query)) {
                            $installinfo = $installinfo . '<li class="success"><font color="#0000EE">建立数据表' . $i . '</font>' . $c_name . ' ... <font color="#0000EE">完成</font></li>';
                        }
                        $db_setup = 1;
                    }
                    $query = '';
                } else {
                    $query .= $value;
                }
                ++$j;
            }

            return $installinfo;
        }

        public function adminsetup_mysql()
        {
            global $_M;
            $setup = $_M['form']['setup'];
            $showdata = $_M['form']['showdata'];
            $regname = $_M['form']['regname'];
            $regpwd = $_M['form']['regpwd'];
            $email = $_M['form']['email'];
            $email_scribe = $_M['form']['email_scribe'];
            $tel = $_M['form']['tel'];
            $cndata = $_M['form']['cndata'];
            $endata = $_M['form']['endata'];
            $met_index_type = $_M['form']['met_index_type'];
            $met_admin_type = $_M['form']['met_admin_type'];
            $m_now_date = date("Y:m:d H:i:s", time());
            $db_type = 'mysql';

            if ($setup == 1) {
                if ($regname == '' || $regpwd == '') {
                    echo "<script type='text/javascript'> alert('请填写管理员信息！'); history.go(-1); </script>";
                }

                $regname = trim($regname);
                $regpwd = md5(trim($regpwd));
                $email = trim($email);

                $m_now_time = time();
                $config = parse_ini_file('../config/config_db.php', 'ture');
                @extract($config);
                $con_db_host = $config['con_db_host'];
                $con_db_id = $config['con_db_id'];
                $con_db_pass = $config['con_db_pass'];
                $con_db_name = $config['con_db_name'];
                $con_db_port = $config['con_db_port'];
                $tablepre = $config['tablepre'];

                $webname_cn = $_M['form']['webname_cn'];
                $webkeywords_cn = $_M['form']['webkeywords_cn'];
                $webname_en = $_M['form']['webname_en'];
                $webkeywords_en = $_M['form']['webkeywords_en'];
                $cndata = $_M['form']['cndata'];
                $endata = $_M['form']['endata'];
                $lang_index_type = $_M['form']['lang_index_type'];

                $link = mysqli_connect($con_db_host, $con_db_id, $con_db_pass, $con_db_name, $con_db_port);
                if (!$link) {
                    $this->error[] = '连接数据库失败: ' . mysqli_connect_error();
                    $this->error();
                }
                mysqli_select_db($link, $con_db_name);
                if (mysqli_get_server_info($link) > 4.1) {
                    mysqli_query($link, 'set names utf8');
                }
                if (mysqli_get_server_info($link) > '5.0.1') {
                    mysqli_query($link, "SET sql_mode=''");
                }

                //表名
                $met_admin_table = "{$tablepre}admin_table";
                $met_config = "{$tablepre}config";
                $met_templates = "{$tablepre}templates";
                $met_column = "{$tablepre}column";
                $met_lang = "{$tablepre}lang";
                $met_style_list = "{$tablepre}style_list";
                $met_style_config = "{$tablepre}style_config";

                // @chmod('../config/config_db.php',0554);
                define('IN_MET', true);
                require_once '../app/system/include/class/mysql.class.php';
                $db = new DB();

                $db->dbconn($con_db_host, $con_db_id, $con_db_pass, $con_db_name, $con_db_port);

                //不安装演示数据时安装空模板
                if (!$showdata) {
                    if ($cndata == 'yes') self::installTagTemplates($db, $met_templates, $this->skin_name, 'cn');
                    if ($endata == 'yes') self::installTagTemplates($db, $met_templates, $this->skin_name, 'en');
                }

                //创始人信息
                $query = " INSERT INTO {$met_admin_table} set
                      pid = 0,
                      role_id = 1,
                      admin_id = '{$regname}',
                      admin_pass = '{$regpwd}',
					  admin_introduction = '创始人',
					  admin_group = '10000',
				      admin_type = 'metinfo',
					  admin_email = '{$email}',
					  admin_mobile = '{$tel}',
					  admin_register_date= '{$m_now_date}',
					  admin_modify_date  = '{$m_now_date}',
					  admin_approval_date= '{$m_now_date}',
					  admin_ok  = '1'";
                $db->query($query);

                //更新配置
                $query = " UPDATE {$met_config} set value='{$webname_cn}' where name='met_webname' and lang='cn'";
                $db->query($query);
                $query = " UPDATE {$met_config} set value='{$webkeywords_cn}' where name='met_keywords' and lang='cn'";
                $db->query($query);
                $query = " UPDATE {$met_config} set value='{$webname_en}' where name='met_webname' and lang='en'";
                $db->query($query);
                $query = " UPDATE {$met_config} set value='{$webkeywords_en}' where name='met_keywords' and lang='en'";
                $db->query($query);
                $force = self::randStr(7);
                $query = " UPDATE {$met_config} set value='{$force}' where name='met_member_force'";
                $db->query($query);

                //更新前台默认语言
                if ($lang_index_type) {
                    $query = "update {$met_config} set value='{$lang_index_type}' where name='met_index_type'";
                } else {
                    $query = "update {$met_config} set value='{$met_index_type}' where name='met_index_type'";
                }
                $db->query($query);
                //更新后台默认语言
                $query = "update {$met_config} set value='{$met_admin_type}' where name='met_admin_type'";
                $db->query($query);

                $agents = '';
                if (file_exists('./agents.php')) {
                    include './agents.php';
                    unlink('./agents.php');
                }
                unlink('../cache/langadmin_cn.php');
                unlink('../cache/langadmin_en.php');
                unlink('../cache/lang_cn.php');
                unlink('../cache/lang_en.php');
                $webname = $webname_cn ? $webname_cn : ($webname_en ? $webname_en : '');
                $webkeywords = $webkeywords_cn ? $webkeywords_cn : ($webkeywords_en ? $webkeywords_en : '');

                $data = array();
                $data['info'] = json_encode(array(
                    'php_ver' => PHP_VERSION,
                    'mysql_ver' => mysqli_get_server_info($link),
                ));
                $data['db_type'] = 'mysql';
                $data['version'] = $this->sys_ver;
                self::setInstallLock();

                $metHOST = $_SERVER['HTTP_HOST'];
                $m_now_year = date('Y');
                $metcms_v = $this->sys_ver;
                setcookie('admin_lang', $met_admin_type, 3600, '/');

                include $this->template('finished');
            } else {
                $langnum = ($cndata == 'yes' || $endata == 'yes') ? 2 : 1;
                $lang = $langnum == 2 ? '中文' : ($endata == 'yes' && $cndata != 'yes' ? '英文' : '中文');
                include $this->template('adminsetup');
            }
        }
        /******MySQL******/

    }
