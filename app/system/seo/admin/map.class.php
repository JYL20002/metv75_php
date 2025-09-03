<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin.class.php');
load::sys_class('nav.class.php');

/** 网站地图 */
class map extends admin
{
    protected $config;

    public function __construct()
    {
        parent::__construct();
        $config = array();
        $config[] = 'met_sitemap_auto';
        $config[] = 'met_sitemap_not1';
        $config[] = 'met_sitemap_not2';
        $config[] = 'met_sitemap_lang';
        $config[] = 'met_sitemap_xml';
        $config[] = 'met_sitemap_txt';
        $this->config = $config;
    }

    //获取网站地图设置
    public function doGetSiteMap()
    {
        global $_M;
        $redata = array();
        foreach ($this->config as $name) {
            $redata[$name] = $_M['config'][$name] ?: 0;
        }
        $this->success($redata);
    }

    //保存网站地图设置
    public function doSaveSiteMap()
    {
        global $_M;
        foreach ($this->config as $name) {
            $_M['form'][$name] = $_M['form'][$name] ?: 0;
        }

        configsave($this->config);/*保存系统配置*/
        buffer::clearConfig();

        if (!$_M['form']['met_sitemap_xml']) {
            delfile(PATH_WEB . "/sitemap.xml");
        }
        if (!$_M['form']['met_sitemap_txt']) {
            delfile(PATH_WEB . "/sitemap.txt");
        }

        //写日志
        logs::addAdminLog('htmsitemap', 'submit', 'jsok', 'doSaveSiteMap');
        $this->success('', $_M['word']['jsok']);
    }

    public function doCreateSiteMap()
    {
        global $_M;
        load::sys_class('label', 'new')->get('seo')->site_map();
        $this->success('', $_M['word']['jsok']);
    }

    public function doCreateRobots()
    {
        global $_M;
        load::sys_class('label', 'new')->get('seo')->sitemap_robots();
        $this->success('', $_M['word']['jsok']);
    }

    public function doCreatePage404()
    {
        global $_M;
        load::sys_class('label', 'new')->get('seo')->html404();
        $this->success('', $_M['word']['jsok']);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.