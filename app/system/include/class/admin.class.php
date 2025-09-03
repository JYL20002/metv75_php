<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
#根据《《中华人民共和国著作权法》第四十七条至第五十条的规定表明若用户存在侵犯著作权事实，应当根据情况，承担停止侵害、消除影响、赔礼道歉、赔偿损失等民事责任.......情节严重的，著作权行政管理部门还可以没收主要用于制作侵权复制品的材料、工具、设备等；构成犯罪的，依法追究刑事责任。因而，若用户未经许可，擅自进行修改、删除等侵犯长沙米拓信息技术有限公司著作权的侵权行为，长沙米拓信息技术有限公司有权对该行为通过法律途径追究侵权责任。

defined('IN_MET') or exit('No permission');
defined('IN_ADMIN') or exit('No permission');

load::sys_class('common');
load::sys_class('nav');
load::sys_func('admin');

class admin extends common
{
    public $admin_member;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        met_cooike_start();
        $this->load_language();
        $this->check();
        $this->lang_switch();
        $this->load_help_url();
        self::adminMember();

        load::plugin('doadmin');
    }

    protected function adminMember()
    {
        global $_M;
        $admin_auth = load::mod_class('permission/permission_op', 'new');
        $this->admin_member = $admin_auth->currentMember();
        $_M['user']['langok'] = $this->admin_member['langok'];
        return $this->admin_member;
    }

    /**
     * 获取管理员信息 兼容应用插件
     * @return mixed
     */
    public function getMetAdmin()
    {
        return $this->admin_member;
    }

    protected function load_url_site()
    {
        global $_M;
        $http = $this->checkHttps();
        $_M['url']['admin_site'] = $http . str_replace(array('/index.php', '/Index.php'), '', HTTP_HOST . PHP_SELF) . '/';
        $_M['url']['web_site'] = preg_replace('/(\/[^\/]*\/$)/', '', $_M['url']['admin_site']) . '/';
        $_M['url']['site_admin'] = str_replace($_M['url']['web_site'], '../', $_M['url']['admin_site']);
        $_M['config']['met_weburl'] = $_M['url']['admin_site'];
    }

    protected function load_url_unique()
    {
        global $_M;
        $_M['url']['adminurl'] = $_M['url']['site_admin'] . "index.php?lang={$_M['lang']}" . '&';
        $_M['url']['own_name'] = $_M['url']['adminurl'] . 'n=' . M_NAME . '&';
        $_M['url']['own_form'] = $_M['url']['own_name'] . 'c=' . M_CLASS . '&';
    }

    protected function load_help_url()
    {
        global $_M;
        $code = @file_get_contents(PATH_WEB . 'config/code.txt');
        $str = '';
        if ($code) {
            $str .= "&metinfo_code=" . trim($code);
        }
        $_M['config']['metinfo_code'] = $code;
        $fields = array('help', 'edu', 'kf', 'qa', 'templates', 'app', 'market', 'copyright');
        foreach ($fields as $val) {
            $_M['config'][$val . '_url'] = "https://u.mituo.cn/api/metinfo?type={$val}" . $str;
        }
        $_M['config']['license_url'] = "../upload/file/license.html" . $str;
    }

    protected function load_language()
    {
        global $_M;
        $admin = admin_information();
        $_M['langset'] = $_M['form']['langset'] ? strip_tags($_M['form']['langset']) : ($admin['admin_login_lang'] ? $admin['admin_login_lang'] : get_met_cookie('languser'));

        $res = preg_match('/^\w+$/', $_M['langset']);
        if (!$res) {
            $_M['langset'] = $_M['config']['met_admin_type'];
        }

        if (!$_M['langset'] || $_M['langset'] == 'metinfo') {
            $_M['langset'] = $_M['config']['met_admin_type'];
        }

        $this->load_word($_M['langset'], 1);
        $this->load_agent_word($_M['langset']);
    }

    protected function load_agent_word($lang)
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['config']} WHERE lang='{$lang}-metinfo'";
        $result = DB::get_all($query);
        foreach ($result as $row) {
            $lang_agents[$row['name']] = $row['value'];
        }
        $_M['word']['indexthanks'] = $lang_agents['met_agents_thanks'];
        $_M['word']['metinfo'] = $lang_agents['met_agents_name'];
        $_M['word']['copyright'] = $lang_agents['met_agents_copyright'];
        $_M['word']['oginmetinfo'] = $lang_agents['met_agents_depict_login'];
    }

    protected function filter_config($value)
    {
        //$value = str_replace('"', '&#34;', str_replace("'", "&#39;", $value));
        $value = parent::filter_config($value);
        return $value;
    }

    protected function lang_switch()
    {
        global $_M;
        if ($_M['form']['switch']) {
            $url = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}";
            if ($_M['form']['a'] != 'dohome') {
                $url .= "&switchurl=" . urlencode(HTTP_REFERER) . "#metnav_" . $_M['form']['anyid'];
            }
            echo "
			<script>
				window.parent.location.href='{$url}';
			</script>
			";
            die();
        }
    }

    protected function gologin()
    {
        global $_M;
        if (M_NAME == 'index') {
            load::mod_class('login/admin/login', 'new')->doindex();
        } else {
            if (is_mobile()) {
                //http_response_code(401);
                $this->error('', '', 401);
            }

            Header("Location: " . $_M['url']['site_admin']);
        }
    }

    /**
     * @return void
     */
    protected function check()
    {
        global $_M;
        $metinfo_admin_name = get_met_cookie('metinfo_admin_name');
        $metinfo_admin_pass = get_met_cookie('metinfo_admin_pass');
        $query = "SELECT * FROM {$_M['table']['admin_table']} WHERE admin_id = '{$metinfo_admin_name}' AND admin_pass = '{$metinfo_admin_pass}'";
        $admin_info = DB::get_one($query);

        if (!$admin_info) {
            met_cooike_unset();
            $this->gologin();
            exit;
        }

        //如果是pc端则跳转链接
//        $this->checkAuth($admin_info['admin_op'], $admin_info['admin_type']);
        $this->checkMemberAuth();
    }

    /**
     * 访问权限控制
     * @param $m_type
     * @param $m_name
     * @param $m_class
     * @param $m_action
     * @return bool|null
     */
    protected function checkMemberAuth($m_type = M_TYPE, $m_name = M_NAME, $m_class = M_CLASS, $m_action = M_ACTION)
    {
        global $_M;
        switch (strtolower($m_type)) {
            case 'system':
                $res = self::checkSysAuth($m_name, $m_class, $m_action);
                if(!$res) return $this->returnData('-1', $_M['word']['js81']);

                $res = self::checkActionAuth($m_name, $m_class, $m_action);
                if(!$res) return $this->returnData('-1', $_M['word']['js81']);
                break;
            case 'app':
                $res = self::checkAppAuth($m_name, $m_class, $m_action);
                if(!$res) return $this->returnData('-1', $_M['word']['js81']);
                break;
        }
        return true;
    }

    /**
     * @param $m_name
     * @param $m_class
     * @param $m_action
     * @return void
     */
    protected function checkSysAuth($m_name, $m_class, $m_action)
    {
        global $_M;
        $mod = "about|admin|banner|column|databack|download|feedback|files|history|img|imgmanage|job|language|link|manage|menu|message|myapp|news|online|parameter|product|partner|recycle|safe|search|seo|sitemap|tags|ui_set|update|user|webset|template|permission";
        $mod_list = explode('|', $mod);
        if (!in_array($m_name, $mod_list)) return true;
        $sql = "SELECT * FROM {$_M['table']['admin_permissions']} WHERE `module`='{$m_name}' ";
        $one = DB::get_one($sql);
        if(!$one)return true;
        return hasPermission('s', $one['aid']);
    }

    /**
     * @param $m_name
     * @param $m_class
     * @return false|void
     */
    protected function checkAppAuth($m_name, $m_class, $m_action)
    {
        global $_M;
        $m_name = strtolower($m_name);
        $query = "SELECT no FROM {$_M['table']['applist']} WHERE m_name = '{$m_name}'";
        $app = DB::get_one($query);
        if(!$app) return true;
        return hasPermission('a', $app['no']);
    }

    /**
     * 操作权限控制
     * @param $m_name
     * @param $m_class
     * @param $m_action
     * @return bool
     */
    protected function checkActionAuth($m_name, $m_class, $m_action)
    {
        global $_M;
        $mod = "about|download|feedback|img|job|message|news|product|recycle";
        $mod_list = explode('|', $mod);
        if (!in_array($m_name, $mod_list)) return true;

        $has = true;
        switch (strtolower($m_action)) {
            case 'doadd':
            case 'doaddsave':
                $has = hasPermission('s', 1802);    //添加内容权限
            break;
            case 'doeditor':
            case 'dolistsave':
            case 'doeditorsave':
                $has = hasPermission('s', 1803);    //编辑内容权限
                break;
            case 'dodel':
                $has = hasPermission('s', 1804);    //删除内容权限
                break;
        }
        return $has;
    }

    /**
     * 管理员权限检测
     * @param $admin_op
     * @param $admin_type
     * @param string $m_type
     * @param mixed|string $m_name
     * @param mixed|string $m_class
     * @param mixed|string $m_action
     * @param string $url
     * @return array
     */
    protected function checkAuth($admin_op, $admin_type, $m_type = M_TYPE, $m_name = M_NAME, $m_class = M_CLASS, $m_action = M_ACTION, $url = '')
    {
        global $_M;
        if (!strstr($admin_op, "metinfo")) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                $return_url = "";
            } else {
                $return_url = "javascript:window.history.back();";
            }
            if (stristr($m_action, 'add')) {
                if (!strstr($admin_op, "add")) {
                    return $this->returnData('-1', $_M['word']['loginadd']);
                }
            }
            if (stristr($m_action, 'editor') || stristr($_M['form']['sub_type'], 'editor')) {
                if (!strstr($admin_op, "editor")) {
                    return $this->returnData($return_url, $_M['word']['loginedit']);
                }
            }
            if (stristr($m_action, 'del') || stristr($_M['form']['submit_type'], 'del')) {
                if (!strstr($admin_op, "del")) {
                    return $this->returnData($return_url, $_M['word']['logindelete']);
                }
            }
            if (stristr($m_action, 'all')) {
                if (!strstr($admin_op, "metinfo")) {
                    return $this->returnData($return_url, $_M['word']['loginall']);
                }
            }

            if (stristr($m_action, 'save')) {
                if ($_M['form']['submit_type'] == 'del') {
                    if (!strstr($admin_op, "del")) {
                        return $this->returnData($return_url, $_M['word']['logindelete']);
                    }
                } else {
                    if (isset($_M['form']['id']) && $_M['form']['id']) {
                        if (!strstr($admin_op, "editor")) {
                            return $this->returnData($return_url, $_M['word']['loginadd']);
                        }
                    } else {
                        if (!strstr($admin_op, "add")) {
                            return $this->returnData($return_url, $_M['word']['loginadd']);
                        }
                    }
                }
            }

            //可视化
            if ($m_action == 'doset_text_content') {
                if (!strstr($admin_op, "editor")) {
                    return $this->returnData($return_url, $_M['word']['loginedit']);
                }
            }
        }
        $n = $m_name;


        $field = '-';
        if ($n == 'myapp' && $m_class == 'index' && $m_action == 'doAction') {
            if ($_M['form']['handle'] == 'install') {
                $n = 'appinstall';
            } else {
                $n = 'appuninstall';
            }
        }

        if ($m_type == 'app') {
            $query = "SELECT no FROM {$_M['table']['applist']} WHERE m_name = '{$n}'  AND m_class = '{$m_class}'";
            $applist = DB::get_one($query);
            if ($applist) {
                $field = $applist['no'];
            }
        } else {
            if (is_mobile()) {
                $route = "(url='{$n}' OR url='{$n}/')";
            } else {
                if (!$url) {
                    $route = "(url='{$n}' OR url='{$n}/')";
                } else {
                    $route = "(url='{$url}' OR url='{$n}')";
                }
            }
            $query = "SELECT field FROM {$_M['table']['admin_column']} WHERE {$route}";
            $admin_column = DB::get_one($query);

            if ($admin_column) {
                $field = $admin_column['field'];
            }
        }
        $field = strval($field);

        //!!1603 管理員
        if (!stristr($admin_type, $field) && $admin_type != 'metinfo' && $field != 1603) {
            return $this->returnData('-1', $_M['word']['js81']);
        }

        if (stristr($m_name, 'column') && stristr($m_action, 'add')) {
            if (!stristr($admin_type, 's9999') && $admin_type != 'metinfo') {
                return $this->returnData('-1', $_M['word']['js81']);
            }
        }
        $redata = array(
            'status' => 1,
        );
        return $redata;
    }

    /**
     * 使用JS方式页面跳转
     * @param  string $url 跳转地址
     * @param  string $langinfo 跳转时alert弹窗内容
     * @param  string $type 1：pc端 2：手机端
     */
    protected function returnData($url, $langinfo)
    {
        if (M_CLASS == 'loadtemp') {
            return false;
        } else {
            if (($_SERVER["HTTP_X_REQUESTED_WITH"] && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") || $_SERVER["REQUEST_METHOD"] == 'POST') {
                $this->error($langinfo, '', 403);
            } else {
                if ($langinfo) {
                    $langstr = "alert('{$langinfo}');";
                }

                if ($url == '-1') {
                    $js = "window.history.back();";
                } else {
                    $js = "location.href='{$url}';";
                }
                echo ("<script type='text/javascript'>{$langstr} {$js} </script>");
                die();
            }
        }
    }

    /**
     * @param int $access
     * @return array
     */
    public function access_option($access = 0)
    {
        global $_M;
        $group_list = load::sys_class('group', 'new')->get_group_list();
        if ($access) {
            
            $access = load::sys_class('user', 'new')->get_group_access($access);
            if ($access == '-1') {
                $max_access = load::sys_class('group', 'new')->get_max_access();
                $access_val = $max_access + 1;
            }
        }

        $list = array();
        if ($access) {
            foreach ($group_list as $key => $val) {
                if ($val['access'] >= $access_val) {
                    $arr = array('name' => $val['name'], 'val' => $val['id']);
                    $list[] = $arr;
                }
            }
        } else {
            $list[] = array('name' => $_M['word']['unrestricted'], 'val' => 0);
            foreach ($group_list as $key => $val) {
                $arr = array('name' => $val['name'], 'val' => $val['id']);
                $list[] = $arr;
            }
        }

        //管理员
        ##$admin_id = $val['id'] + 1;
        #$admin_id = '-1';
        #$list[] = array('name' => $_M['word']['metadmin'], 'val' => $admin_id);

        return $list;
    }


    /**
     * [js系统变量]
     * @return [type] [description]
     */
    public function sys_json()
    {
        global $_M;
        $_M['config']['metinfo_version'] = str_replace('.', '', $_M['config']['metcms_v']);
        $langprivelage = $_COOKIE['arrlanguage'] ? 1 : 0;

        $met_para = array(
            'met_ai_open' => $_M['config']['met_ai_open'],
            'met_editor' => $_M['config']['met_editor'],
            'met_keywords' => $_M['config']['met_keywords'],
            'met_alt' => $_M['config']['met_alt'],
            'met_atitle' => $_M['config']['met_atitle'],
            'metcms_v' => $_M['config']['metcms_v'],
            'patch' => $_M['config']['patch'],
            'tem' => $_M['config']['met_skin_user'],
            'met_agents_metmsg' => $_M['config']['met_agents_metmsg'],
            'langprivelage' => $langprivelage,
            'url' => array(
                'admin' => $_M['url']['site_admin'],
                'adminurl' => $_M['url']['adminurl'],
                'api' => $_M['url']['api'],
                'own_form' => $_M['url']['own_form'],
                'own_name' => $_M['url']['own_name'],
                'own' => $_M['url']['own'],
                'own_tem' => $_M['url']['own_tem'],
            ),
        );
        $met_para = json_encode($met_para);

        $copyright = str_replace('$metcms_v', $_M['config']['metcms_v'], $_M['config']['met_agents_copyright_foot']);
        $copyright = str_replace('$m_now_year', date('Y', time()), $copyright);
        $copyright = str_replace('&#34;', '', $copyright);
        if (strstr($copyright, 'www.mituo.cn') || strstr($copyright, 'www.metinfo.cn')) {
            $copyright = preg_replace_callback('/\/\/([a-zA-Z0-9-_\.\?&]+)/', function ($match) use ($_M) {
                if ($match && $match[1]) {
                    if (strstr($match[1], '?')) {
                        $type = '&';
                    } else {
                        $type = '?';
                    }

                    if (strstr($match[0], 'www.mituo.cn') || strstr($match[0], 'www.metinfo.cn')) {
                        return $match[0] . $type . 'metinfo_code=' . $_M['config']['metinfo_code'];
                    } else {
                        return $match[0];
                    }
                }
            }, $copyright);
        }

        $sys_json = array(
            'copyright' => $copyright,
            'met_para' => $met_para,
        );
        return $sys_json;
    }

    // 根据《中华人民共和国著作权法》及《计算机软件保护条例》，此处为权利人的技术保护措施，切勿删除或修改！违反者须承担侵权后果！
    public function getPackageInfo()
    {
        global $_M;
        $data = array('show'=>0,'package'=>'免费版');
        $local = array('localhost','127.0.0','192.168.');
        foreach ($local as $url) {
            $info = explode('/',trim(str_replace(array('https://','http://'),'',$_M['url']['web_site']),'/'));
            if(strstr($info[0],$url)){
                return $data;
            }
        }

        $appno = array();
        $applist = DB::get_all("SELECT no FROM {$_M['table']['applist']}");
        foreach ($applist as $item) {
            $appno[] = $item['no'];
        }

        $hasCopyright = self::checkCopyright();

        $license_file = PATH_WEB . "config/metinfo.txt";

        $url = "https://api.mituo.cn/license";
        $data = array(
            'action' => 'getPackageInfo',
            'copyright'=>$hasCopyright,
            'appno'=>json_encode($appno),
            'skin_name'=>$_M['config']['met_skin_user']
        );
        $res = api_curl($url, $data);
        $res = json_decode($res, true);

        if ($res['code'] == 0) {
            $data = $res['data'];
            if(isset($data['license'])){
                file_put_contents($license_file,$data['license']);
            }
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function getImgList()
    {
        $url = 'https://api.mituo.cn/client';
        $data = array(
            'action' => 'getImgList'
        );
        $res = api_curl($url, $data);
        $res = json_decode($res, true);
        if($res['code'] == 0){
            return $res['data'];
        }
        return array();
    }

    /**
     * @return bool
     */
    protected function checkCopyright()
    {
        global $_M;
        //页面验证
        $url = $_M['url']['web_site'];
        $index_ = file_get_contents($url);
        if (!$index_) {
            return true;
        }
        $index_ = str_replace(array("\n"), '', $index_);

        //basic js
        $Document = new DOMDocument('1.0', 'UTF-8');
        $Document->loadHTML($index_);
        $page_js = $Document->getElementById('met-page-js');//->getAttribute('src')
        if (!$page_js) {
            return true;
        }

        //copyright
        $preg = '/\<div class=[\'\"]powered_by_metinfo[\'\"]\>.*?\<\/div\>/i';  //<div>
        preg_match($preg, $index_, $mutch);
        if ($mutch) {
            $preg = '/<a[^>]*href[=\"\'\s]+([^\"\']*)[\"\']?[^>]*>/i';    //<a>
            preg_match_all($preg, $mutch[0], $mutch2);
            foreach ($mutch2[1] as $row) {
                $row = strtolower($row);
                if (strpos($row, 'metinfo.cn') || strpos($row, 'mituo.cn')) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    public function __destruct()
    {
        global $_M;
        // TODO: Implement __destruct() method.
        load::plugin('doadminend');
        #buffer::clearConfig();
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
