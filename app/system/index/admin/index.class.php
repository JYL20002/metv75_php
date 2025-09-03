<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');
load::sys_func('admin');

class index extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();

    }

    public function ini()
    {
        global $_M;

        $this->input['met_admin_logo'] = (strstr($_M['config']['met_agents_logo_index'], 'http')?'':$_M['url']['site']) . str_replace('../', '', $_M['config']['met_agents_logo_index']);
        return;
    }

    /**
     * 后台框架页
     */
    public function doindex()
    {
        global $_M;
        $this->input = array();
        if (!$_M['form']['noside']) {
            self::ini();

            if (!$_M['form']['sidebar_reload']) {
                //官方消息提醒
                $this->input['msecount'] = DB::counter($_M['table']['infoprompt'], "WHERE (lang='" . $_M['lang'] . "' or lang='metinfo') and see_ok='0'", "*");

                $this->input['admin_pop'] = 0;
                if (hasPermission('f', 8888)) {//可视化权限
                    $this->input['admin_pop'] = 1;
                }

                if ($this->input['langprivelage']) {
                    setcookie("arrlanguage", 1, 0, '/');
                }
            }
        }

        $sys_json = parent::sys_json();
        $this->input = array_merge($this->input, $sys_json);
        $temp = is_mobile() ? 'sys/mobile/admin/templates/index' : 'app/index';
        $this->view($temp, $this->input);
    }

    /**
     * 后台菜单
     * @return void
     */
    public function doGetMenus()
    {
        global $_M;
        $redata['left_nav'] = $this->admin_member['left_nav'];
        $redata['top_nav'] = $this->admin_member['top_nav'];
        $redata['uiset_nav'] = $this->admin_member['uiset_nav'];
        $this->success($redata);
    }

    /**
     * 后台首页
     */
    public function dohome()
    {
        global $_M;
        $query = "SELECT count(1) as total FROM {$_M['table']['feedback']} WHERE readok=0 AND lang = '{$_M['lang']}'";
        $feedback = DB::get_one($query);
        $feedback['name'] = $_M['word']['feedfback'];
        if (!$feedback) {
            $feedback['total'] = 0;
        }

        $query = "SELECT count(1) as total FROM {$_M['table']['message']} WHERE readok=0 AND lang = '{$_M['lang']}'";
        $message = DB::get_one($query);
        $message['name'] = $_M['word']['message'];
        if (!$message) {
            $message['total'] = 0;
        }

        $query = "SELECT count(1) as total FROM {$_M['table']['cv']} WHERE readok=0 AND lang = '{$_M['lang']}'";
        $cv = DB::get_one($query);
        if (!$cv) {
            $cv['total'] = 0;
        }
        $cv['name'] = $_M['word']['job'];

        $query = "SELECT COUNT(1) AS total FROM {$_M['table']['news']} WHERE  lang = '{$_M['lang']}'";
        $news = DB::get_one($query);
        if (!$news) {
            $news['total'] = 0;
        }
        $news['name'] = $_M['word']['upfiletips37'];

        $query = "SELECT COUNT(1) AS total FROM {$_M['table']['product']} WHERE  lang = '{$_M['lang']}'";
        $product = DB::get_one($query);
        if (!$product) {
            $product['total'] = 0;
        }
        $product['name'] = $_M['word']['product'];;

        $home_app_ok = $home_news_ok = $_M['config']['met_agents_metmsg']; //官方信息
        $data = array(
            'admin_folder_safe' => adminFolderSafe(),//后台安全提示
            'home_app_ok' => $home_app_ok,
            'home_news_ok' => $home_news_ok,
            'summarize' => array(
                'news' => $news,
                'product' => $product,
                'feedback' => $feedback,
                'message' => $message,
                'job' => $cv
            )
        );

        if (is_mobile()) {
            $this->success($data);
        } else {
            return $data;
        }
    }

    /**
     * 检测授权文件
     */
    public function doGetPackageInfo()
    {
        $package_info = $this->getPackageInfo();
        $this->success($package_info);
    }

    /**
     * @return bool
     */
    public function doGetImgList()
    {
        $img_list = $this->getImgList();
        $this->success($img_list);
    }

    /**
     * 取消系统安全提示
     */
    public function doNoPrompt()
    {
        $configlist = array();
        $configlist[] = 'met_safe_prompt';
        configsave($configlist, array('met_safe_prompt' => 1));
        $this->success();
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
