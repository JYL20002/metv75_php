<?php
defined('IN_MET') or exit('No permission');

load::sys_class('admin');
load::sys_class('nav');

/** 基本信息设置 */
class info extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    /**
     * 获取基本信息列表
     * @return void
     */
    public function doGetInfo()
    {
        global $_M;
        buffer::clearConfig();

        //网站基本信息
        $info = array();
        $info['met_webname'] = $_M['config']['met_webname'] ?  : '';
        $info['met_logo'] = $_M['config']['met_logo']  ? : '';
        $info['met_mobile_logo'] = $_M['config']['met_mobile_logo']  ? : '';
        $info['met_weburl'] = self::get_weburl();;
        $info['met_keywords'] = isset($_M['config']['met_keywords']) ? $_M['config']['met_keywords'] : '';
        $info['met_description'] = isset($_M['config']['met_description']) ? $_M['config']['met_description'] : '';
        $info['data_key'] = isset($_M['config']['met_webkeys']) ? md5(md5(substr($_M['config']['met_webkeys'], 0, 8))) : '';
        //版权
        $info['met_copyright_type'] = $_M['config']['met_copyright_type'] ? intval($_M['config']['met_copyright_type']) : 0;
        $agents = array();
        $agents[] = str_replace(array('$metcms_v', '$m_now_year'), array($_M['config']['metcms_v'], date('Y', time())), $_M['config']['met_agents_copyright_foot']);
        $agents[] = str_replace(array('$metcms_v', '$m_now_year'), array($_M['config']['metcms_v'], date('Y', time())), $_M['config']['met_agents_copyright_foot1']);
        $agents[] = str_replace(array('$metcms_v', '$m_now_year'), array($_M['config']['metcms_v'], date('Y', time())), $_M['config']['met_agents_copyright_foot2']);
        $info['agents'] = $agents;

        //底部信息
        $info['met_footright'] = $_M['config']['met_footright'] ?: '';
        $info['met_footaddress'] = $_M['config']['met_footaddress'] ?: '';
        $info['met_foottel'] = $_M['config']['met_foottel'] ?: '';
        $info['met_footother'] = $_M['config']['met_footother'] ?: '';
        //备案信息
        $info['met_icp_info'] = $_M['config']['met_icp_info'] ?: '';    //icp
        $info['met_beian_info'] = $_M['config']['met_beian_info'] ?: '';    //公安备案代码

        $this->success($info);
    }

    /**
     * 保存网站基本信息
     * @return void
     */
    public function doSaveInfo()
    {
        global $_M;
        //设置网站Ico
        self::setIco();

        //保存配置信息
        $configlist = array();
        $configlist[] = 'met_webname';
        $configlist[] = 'met_logo';
        $configlist[] = 'met_mobile_logo';
        $configlist[] = 'met_keywords';
        $configlist[] = 'met_description';
        $configlist[] = 'met_footright';
        $configlist[] = 'met_footaddress';
        $configlist[] = 'met_foottel';
        
        $configlist[] = 'met_copyright_type';
        $configlist[] = 'met_icp_info';
        $configlist[] = 'met_beian_info';
        $configlist[] = 'met_footother';
        configsave($configlist,$_M['form']);

        //写日志
        logs::addAdminLog('website_information', 'save', 'jsok', 'doSaveInfo');

        buffer::clearConfig();
        $this->success('', $_M['word']['jsok']);
    }

    /**
     * 设置网站Ico
     * @return void
     */
    private function setIco()
    {
        global $_M;
        $met_ico = $_M['form']['met_ico'] ?: null;
        if (!$met_ico) {
            delfile('../favicon.ico');
            return;
        }

        if ($_M['form']['met_ico'] != '../favicon.ico') {
            $pattern = '/^(\.\.\/upload\/)[\w\/]+\/\w+(\.ico)$/';
            $res = preg_match($pattern, $met_ico, $match);
            if ($res) {
                copy($met_ico, '../favicon.ico');
            }
        }
        return;
    }

    /** 获取站点网址
     * @param  string $lang 语言
     * @return string
     */
    private function get_weburl()
    {
        global $_M;
        if ($_M['langlist']['web'][$_M['lang']]['link']) {
            return $_M['langlist']['web'][$_M['lang']]['link'];
        }

        $query = "SELECT met_weburl FROM {$_M['table']['lang']} WHERE lang = '{$_M['lang']}'";
        $get_lang = DB::get_one($query);
        $web_url = isset($get_lang['met_weburl']) ? $get_lang['met_weburl'] : '';
        if (!$web_url) {
            $web_url = isset($_M['config']['met_weburl']) ? $_M['config']['met_weburl'] : $_M['url']['web_site'];
        }
        return $web_url;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
