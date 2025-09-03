<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('database');
/**
 * 更新迁移数据
 * 更新迁移数据
 */
class update_database extends database
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var
     */
    private $colum_label;

    public $update79;

    public function __construct()
    {
        global $_M;
        $this->version = '8.0';
        $this->colum_label = load::sys_class('label', 'new')->get('column');
        $this->tables = load::mod_class('databack/tables', 'new');
        $this->update79 = load::mod_class('update/update_79', 'new');
    }

    public function update_system($version = '')
    {
        global $_M;
        //检测新增数据表和字段
        $this->diff_fields();
        //强制更新表字段
        $this->alter_table();
        //恢复用户数据
        $this->recovery_data();
        //更新配置
        $this->add_config();
        //注册数据表
        $this->table_regist();
        //商城开关
        $this->check_shop();
        //更新applist
        $this->update_app_list();
        //更新语言
        $this->update_language($this->version);
        $this->update79 ->defaultDate();
        $this->update79 ->setRolesPermissions();
        $this->update79 ->setMemberPermissions();

    }

    /**
     * 对比数据库结构
     * @return void
     */
    public function diff_fields()
    {
        global $_M;
        $this->tables->diff_fields($this->version);
    }

    /**
     * 更新表字段默认值
     * @return void
     */
    public function alter_table()
    {
        global $_M;
        $this->tables->alter_table($this->version);
    }

    /**
     * 注册数据表
     */
    public function table_regist()
    {
        global $_M;
        add_table('admin_column|admin_has_permissions|admin_logs|admin_menus|admin_permissions|admin_roles|admin_table|app_config|app_plugin|applist|column|config|cv|download|feedback|files|flash|flash_button|flist|history|history_plist|history_relation|ifcolumn|ifcolumn_addfile|ifmember_left|img|infoprompt|job|label|lang|lang_admin|language|link|menu|message|mlist|news|online|para|parameter|plist|product|relation|skin_table|tags|templates|ui_config|ui_list|user|user_group|user_group_pay|user_list|user_other|weixin_reply_log');
    }

    /**
     * 备份用户临时数据
     * @return array
     */
    public function temp_data()
    {
        global $_M;

        $data = array();
        $data['met_secret_key'] = $_M['config']['met_secret_key'];
        $data['last_version'] = $_M['config']['metcms_v'];
        $data['tablename'] = $_M['config']['met_tablename'];

        $query = "SELECT * FROM {$_M['table']['applist']}";
        $data['applist'] = DB::get_all($query);

        //用户数据缓存
        Cache::put('temp_data', $data);
        return $data;
    }

    /**
     * 恢复用户数据
     */
    public function recovery_data()
    {
        global $_M;
        if (file_exists(PATH_WEB . 'cache/temp_data.php')) {
            $data = Cache::get('temp_data');
            add_table($data['tablename']);

            //恢复注册数据表
            $query = "SELECT value FROM {$_M['table']['config']} WHERE name = 'met_tablename'";
            $config = DB::get_one($query);
            $_Mettables = explode('|', $config['value']);
            foreach ($_Mettables as $key => $val) {
                $_M['table'][$val] = $_M['config']['tablepre'] . $val;
            }

            //恢复用户TOKEN
            $query = "UPDATE {$_M['table']['config']} SET value = '{$data['met_secret_key']}' WHERE name = 'met_secret_key'";
            DB::query($query);

            //恢复系统版本
            $query = "UPDATE {$_M['table']['config']} SET value = '{$data['last_version']}' WHERE name = 'metcms_v'";
            DB::query($query);

            //恢复应用列表数据
            foreach ($data['applist'] as $app) {
                $query = "SELECT id FROM {$_M['table']['applist']} WHERE m_name='{$app['m_name']}'";
                if (!DB::get_one($query) && file_exists(PATH_WEB . 'app/app/' . $app['m_name'])) {
                    unset($app['id']);
                    $sql = self::get_sql($app);
                    $query = "INSERT INTO {$_M['table']['applist']} SET {$sql}";
                    DB::query($query);
                }
            }
        }

        //删除临时数据文件
        Cache::del('temp_data');
    }

    /**
     * @param $version
     * @return void
     */
    public function update_language($version = '')
    {
        global $_M;
        $version = $version ?: $this->version;
        self::update_admin_lang($version);
        self::update_web_lang($version);
    }

    /**
     * 更新后台语言
     * @param $version
     */
    public function update_admin_lang($version)
    {
        global $_M;
        //添加管理员语言
        $query = "SELECT * FROM {$_M['table']['lang_admin']} WHERE lang = 'cn' AND mark = 'cn'";
        $res = DB::get_one($query);
        if (!$res) {
            $query = "INSERT INTO {$_M['table']['lang_admin']} SET name = '简体中文', useok = '1', no_order = '1', mark = 'cn', synchronous = 'cn',  link = '', lang = 'cn' ";
            DB::query($query);
        }

        //本地指纹
        $path_cn = __DIR__ . "/v{$version}lang_admin_cn.json";
        $path_en = __DIR__ . "/v{$version}lang_admin_en.json";

        $sql = "SELECT * FROM {$_M['table']['lang_admin']} ";
        $admin_lang_list = DB::get_all($sql);
        foreach ($admin_lang_list as $row) {
            $lang = $row['lang'];
            //语言
            if ($lang != 'en') {
                $path = $path_cn;
            } else {
                $path = $path_en;
            }

            //获取语言对照文件
            $lang_json = file_get_contents($path);
            $lang_data = json_decode($lang_json, true);

            if (is_array($lang_data)) {
                $sql = "DELETE FROM {$_M['table']['language']} WHERE lang = '{$lang}' AND  site = '1' AND app = '0' ";
                DB::query($sql);
                foreach ($lang_data as $lang_row) {
                    if ($lang_row['site'] == 1) {
                        self::add_language($lang_row);
                    }
                }
            }
        }
    }

    /**
     * 更新前台语言
     * @param $version
     */
    public function update_web_lang($version)
    {
        global $_M;
        //本地指纹
        $path_cn = __DIR__ . "/v{$version}lang_web_cn.json";
        $path_en = __DIR__ . "/v{$version}lang_web_en.json";

        $sql = "SELECT * FROM {$_M['table']['lang']} ";
        $web_lang_list = DB::get_all($sql);
        foreach ($web_lang_list as $row) {
            $lang = $row['lang'];
            //语言
            if ($lang != 'en') {
                $path = $path_cn;
            } else {
                $path = $path_en;
            }

            //获取语言对照文件
            $lang_json = file_get_contents($path);
            $lang_data = json_decode($lang_json, true);

            if (is_array($lang_data)) {
                $query = "SELECT `id`,`name` FROM {$_M['table']['language']} WHERE lang = '{$lang}' AND site = '0'";
                $web_lang = DB::get_all($query);

                $old_lang_index = array();
                foreach ($web_lang as $lang_item) {
                    $old_lang_index[] = $lang_item['name'];
                }

                $new_lang_index = array_keys($lang_data);
                $diff_lang_idnex = array_diff($new_lang_index, $old_lang_index);

                foreach ($diff_lang_idnex as $name) {
                    if ($lang_data[$name]['site'] == 0) {
                        self::add_language($lang_data[$name]);
                    }
                }

                //js语言
                $js_word = array('confirm', 'cancel');
                foreach ($js_word as $word) {
                    $sql = "UPDATE {$_M['table']['language']} SET app = 1 WHERE name = '{$word}' AND site = 0";
                    DB::query($sql);
                }
            }
        }
    }

    /**
     * 更新applist
     */
    public function update_app_list()
    {
        global $_M;
        $query = "UPDATE {$_M['table']['applist']} SET display = '1' WHERE no = '50002'";
        DB::query($query);
    }

    /**
     * 更新系统配置
     */
    public function add_config()
    {
        global $_M;
        foreach (array_keys($_M['langlist']['web']) as $lang) {
            //tags
            self::_insert_config('tag_show_range', '0', 0, $lang);
            self::_insert_config('tag_show_number', '4', 0, $lang);
            self::_insert_config('tag_search_type', 'module', 0, $lang);
            //logs
            self::_insert_config('met_logs', '0', 0, $lang);
            //logo
            self::_insert_config('met_logo_keyword', "{$_M['config']['met_webname']}", 0, $lang);
            //safe
            self::_insert_config('access_type', '1', 0, $lang);
            self::_insert_config('met_auto_play_pc', '0', 0, $lang);
            self::_insert_config('met_auto_play_mobile', '0', 0, $lang);
            //member
            self::_insert_config('met_login_box_position', '', 0, $lang);
            self::_insert_config('met_weixin_gz_token', '', 0, $lang);
            self::_insert_config('met_auto_register', '', 0, $lang);
            self::_insert_config('met_member_agreement', '', 0, $lang);
            self::_insert_config('met_member_agreement_content', '', 0, $lang);
            self::_insert_config('met_member_bg_range', '', 0, $lang);
            self::_insert_config('met_login_box_position', '', 0, $lang);
            self::_insert_config('met_new_registe_email_notice', '', 0, $lang);
            self::_insert_config('met_to_admin_email', '', 0, $lang);
            self::_insert_config('met_new_registe_sms_notice', '', 0, $lang);
            self::_insert_config('met_to_admin_sms', '', 0, $lang);
            self::_insert_config('met_google_open', '', 0, $lang);
            self::_insert_config('met_google_appid', '', 0, $lang);
            self::_insert_config('met_google_appsecret', '', 0, $lang);
            self::_insert_config('met_facebook_open', '', 0, $lang);
            self::_insert_config('met_facebook_appid', '', 0, $lang);
            self::_insert_config('met_facebook_appsecret', '', 0, $lang);
            //webset
            self::_insert_config('met_data_null', '', 0, $lang);
            self::_insert_config('met_404content', '', 0, $lang);
            self::_insert_config('met_icp_info', '', 0, $lang);
            self::_insert_config('met_beian_info', '', 0, $lang);
            //水印图设置
            self::_insert_config('met_wate_img_scale', '0', 0, $lang);
            self::_insert_config('met_wate_img_gif_hold', '0', 0, $lang);
            //信息安全声明
            self::_insert_config('met_info_security_statement_open', '0', 0, $lang);
            self::_insert_config('met_info_security_statement_title', '', 0, $lang);
            self::_insert_config('met_info_security_statement_content', '', 0, $lang);
            self::_insert_config('met_info_security_statement_modal_title', '', 0, $lang);
            //安全与效率
            self::_insert_config('met_auto_close', '', 0, $lang);
            self::_insert_config('met_auto_show', '', 1, $lang);
            //静态页设置
            self::_insert_config('met_html_auto', '0', 0, $lang);
            //seo
            self::_insert_config('met_seo_canonical', '0', 0, $lang);
        }

        //global
        self::_update_config('met_api', 'https://u.mituo.cn/api/client', 0, 'metinfo');
        self::_update_config('met_301jump', '0', 0, 'metinfo');
        self::_update_config('disable_cssjs', '0', 0, 'metinfo');
        self::_update_config('met_uiset_guide', '1', 0, 'metinfo');
        self::_update_config('met_copyright_nofollow', '1', 0, 'metinfo');
        self::_update_config('met_https', '0', 0, 'metinfo');
        self::_update_config('met_copyright_type', '0', 0, 'metinfo');
        self::_update_config('met_agents_copyright_foot1', '本站基于 <b><a href=https://www.metinfo.cn target=_blank title=CMS>米拓企业建站系统搭建 $metcms_v</a></b> &copy;2008-$m_now_year', 0, 'metinfo');
        self::_update_config('met_agents_copyright_foot2', '技术支持：<b><a href=https://www.mituo.cn target=_blank title=CMS>米拓建站 $metcms_v</a></b> &copy;2008-$m_now_year', 0, 'metinfo');
        //global-agents
        self::_update_config('met_agents_type', '1', 0, 'metinfo');
        self::_update_config('met_agents_pageset_logo', '1', 0, 'metinfo');
        self::_update_config('met_agents_update', '1', 0, 'metinfo');
        self::_update_config('met_agents_linkurl', '', 0, 'metinfo');
        //fonts
        self::_update_config('met_text_fonts', '../public/third-party/fonts/Roboto/Roboto-Regular.ttf', 0, 'metinfo');
    }

    /**
     * 配置变更
     */
    public function modify_config()
    {
        global $_M;
        //met_agents_type 前台版权标识
        $query = "SELECT * FROM {$_M['table']['config']} WHERE name='met_agents_type' AND lang = 'metinfo' ORDER BY ID DESC";
        $met_agents_type = DB::get_one($query);
        $query = "DELETE FROM {$_M['table']['config']} WHERE name='met_agents_type' AND lang = 'metinfo'";
        DB::query($query);

        if (!$met_agents_type || $met_agents_type['value'] == '0') {
            self::_update_config('met_agents_type', '1', 0, 'metinfo');
        } elseif ($met_agents_type['value'] == '2') {
            self::_update_config('met_agents_type', '0', 0, 'metinfo');
        } else {
            self::_update_config('met_agents_type', '1', 0, 'metinfo');
        }

        //met_agents_logo_login 后台登陆logo
        $query = "SELECT * FROM {$_M['table']['config']} WHERE name='met_agents_logo_login' AND lang = 'metinfo' ORDER BY ID DESC";
        $met_agents_logo_login = DB::get_one($query);
        if (!$met_agents_logo_login || !strstr($met_agents_logo_login['value'], 'upload')) {
            self::_update_config('met_agents_logo_login', '../public/images/login-logo.png', 0, 'metinfo');
        }

        //met_agents_logo_index 后台logo
        $query = "SELECT * FROM {$_M['table']['config']} WHERE name='met_agents_logo_index' AND lang = 'metinfo' ORDER BY ID DESC";
        $met_agents_logo_index = DB::get_one($query);
        if (!$met_agents_logo_index || !strstr($met_agents_logo_index['value'], 'upload')) {
            self::_update_config('met_agents_logo_index', '../public/images/logo.png', 0, 'metinfo');
        }
    }

    /**
     * tags数据迁移
     */
    public function update_tags()
    {
        global $_M;

        $tags = load::sys_class('label', 'new')->get('tags');
        $modules = array(2 => 'news', 3 => 'product', 4 => 'download', 5 => 'img');
        foreach ($modules as $mod => $table) {
            $query = "SELECT * FROM {$_M['table'][$table]} WHERE tag != '' AND tag IS NOT NULL AND recycle = 0";
            $list = DB::get_all($query);
            foreach ($list as $v) {
                if (!trim($v['tag'])) {
                    continue;
                }
                $tagStr = $v['tag'];
                $_M['lang'] = $v['lang'];
                $tags->updateTags($tagStr, $mod, $v['class1'], $v['id'], 1);
            }
        }
    }

    /*****************************商城及支付接口应用更新******************************/
    public function check_shop()
    {
        global $_M;
        if (!file_exists(PATH_WEB . 'app/app/shop')) {
            $query = "DELETE FROM {$_M['table']['applist']} WHERE no = 10043";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['app_config']} WHERE appno = 10043";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['app_plugin']} WHERE no = 10043";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['ifmember_left']} WHERE no = 10043";
            DB::query($query);
        } else {
            $file = PATH_WEB . 'app/app/shop/admin/install.class.php';
            if (file_exists($file)) {
                include $file;
                $install = new install();
                if (method_exists($install, 'appcheke')) {
                    $install->appcheke();
                }
            }
        }

        if (!file_exists(PATH_WEB . 'app/system/pay')) {
            $query = "DELETE FROM {$_M['table']['applist']} WHERE no = 10080";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['app_config']} WHERE appno = 10080";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['app_plugin']} WHERE no = 10080";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['ifmember_left']} WHERE no = 10080";
            DB::query($query);
           
        }
        /*else {
            $file = PATH_WEB . 'app/system/pay/admin/install.class.php';
            if (file_exists($file)) {
                include $file;
                $install = new install();
                if (method_exists($install, 'appcheke')) {
                    $install->appcheke();
                }
            }
        }*/
    }

    /*****************************工具方法******************************/
    private function _insert_config($name = '', $value = '', $cid = '', $lang = '')
    {
        global $_M;
        $query = "SELECT id FROM {$_M['table']['config']} WHERE  name='{$name}' AND lang = '{$lang}'";
        $config = DB::get_one($query);
        if (!$config) {
            $query = "INSERT INTO {$_M['table']['config']} (name,value,mobile_value,columnid,flashid,lang)VALUES ('{$name}', '{$value}', '', '{$cid}', '0', '{$lang}')";
            DB::query($query);
        }
        return;
    }
    /**
     * 更新配置
     * @param $name
     * @param $value
     * @param $cid
     * @param $lang
     */
    private function _update_config($name = '', $value = '', $cid = '', $lang = '')
    {
        global $_M;
        $query = "SELECT id FROM {$_M['table']['config']} WHERE  name='{$name}' AND lang = '{$lang}'";
        $config = DB::get_one($query);
        if (!$config) {
            $query = "INSERT INTO {$_M['table']['config']} (name,value,mobile_value,columnid,flashid,lang)VALUES ('{$name}', '{$value}', '', '{$cid}', '0', '{$lang}')";
            DB::query($query);
        } else {
            $query = "UPDATE {$_M['table']['config']} SET name = '{$name}',value = '{$value}', columnid = '{$cid}' ,lang = '{$lang}' WHERE id = '{$config['id']}'";
            DB::query($query);
        }
    }

    /**
     * 更新 插入语言
     * @param array $lang_data
     */
    private function add_language($lang_data = array())
    {
        global $_M;
        $name = $lang_data['name'];
        $value = $lang_data['value'];
        $site = $lang_data['site'];
        $lang = $lang_data['lang'];
        $js = $lang_data['array'] ? 1 : 0;
        $app = $lang_data['app'];

        if ($site == 1) {
            $query = "INSERT INTO {$_M['table']['language']} (id, name, value, site, no_order, array, app, lang) VALUES (null, '{$name}', '{$value}', {$site}, 0, '{$js}', {$app}, '{$lang}');";
            $res = DB::query($query);
        }

        if ($site == 0) {
            $query = "INSERT INTO {$_M['table']['language']} (id, name, value, site, no_order, array, app, lang) VALUES (null, '{$name}', '{$value}', {$site}, 0, '{$js}', {$app}, '{$lang}');";
            $res = DB::query($query);
        }
        return;
    }

    /**
     * 获取sql
     * @param $data
     * @return string
     */
    public function get_sql($data)
    {
        global $_M;
        $sql = "";
        foreach ($data as $key => $value) {
            if (strstr($value, "'")) {
                $value = str_replace("'", "\'", $value);
            }
            $sql .= " {$key} = '{$value}',";
        }
        return trim($sql, ',');
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
