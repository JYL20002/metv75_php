<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

    load::sys_class('admin');
    /**
     * 安全与效率
     */
class index extends admin
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    /**
     * 获取设置
     */
    public function doGetSetup()
    {
        global $_M;
        $conf = DB::get_one("SELECT value FROM {$_M['table']['config']} WHERE lang ='{$_M['lang']}' AND name='met_fd_word' AND columnid = 0");
        $met_fd_word = $conf['value'];

        $list = array();
        //验证码
        $list['met_login_code'] = $_M['config']['met_login_code'] ?  : '';
        $list['met_memberlogin_code'] = $_M['config']['met_memberlogin_code'] ?  : '';
        //上传设置
        $list['met_img_rename'] = $_M['config']['met_img_rename'] ?  : '';
        $list['met_file_maxsize'] = $_M['config']['met_file_maxsize'] ?  : '';
        $list['met_file_format'] = $_M['config']['met_file_format'] ?  : '';
        //日志
        $list['met_logs'] = $_M['config']['met_logs'] ?  : 0;
        $list['access_type'] = $_M['config']['access_type'] ?  : 1;
        $list['met_fd_word'] = $met_fd_word;
        $list['disable_cssjs'] = $_M['config']['disable_cssjs'] ?  : '';
        //播放设置
        $list['met_auto_play_pc'] = $_M['config']['met_auto_play_pc'] ?  : '0';
        $list['met_auto_play_mobile'] = $_M['config']['met_auto_play_mobile'] ?  : '0';
        $list['met_auto_close'] = $_M['config']['met_auto_close'] ?  : '0';
        $list['met_auto_show'] = $_M['config']['met_auto_show'] ?  : '0';

        //信息安全声明
        $list['met_info_security_statement_open'] = $_M['config']['met_info_security_statement_open'] ?  : '0';
        $list['met_info_security_statement_title'] = $_M['config']['met_info_security_statement_title'] ?  : '';
        $list['met_info_security_statement_content'] = $_M['config']['met_info_security_statement_content']  ? : '';
        $list['met_info_security_statement_modal_title'] = $_M['config']['met_info_security_statement_modal_title'] ?  : '';

        $list['met_adminfile'] = $_M['config']['met_adminfile'];
        $list['install'] = is_dir(PATH_WEB . 'install') ? 1 : 0;

        $list['is_root'] = $this->admin_member['role']['code'] == 'root' ? 1 : 0;
        $this->success($list);
    }

    /**
     * 存设置
     */
    public function doSaveSetup()
    {
        global $_M;
        $new_format = array();
        $met_file_format = explode("|", $_M['form']['met_file_format']);
        foreach ($met_file_format as $row) {
            $row = strtolower($row);
            if (strstr($row, 'php')) continue;
            $new_format[] = $row;
        }
        $_M['form']['met_file_format'] = implode('|', $new_format);

        $config_list = array();
        $config_list[] = 'met_img_rename';
        $config_list[] = 'met_login_code';
        $config_list[] = 'met_memberlogin_code';
        $config_list[] = 'met_file_maxsize';
        $config_list[] = 'met_file_format';
        $config_list[] = 'met_fd_word';
        $config_list[] = 'met_logs';
        $config_list[] = 'disable_cssjs';
        $config_list[] = 'access_type';
        $config_list[] = 'met_auto_play_pc';
        $config_list[] = 'met_auto_play_mobile';
        $config_list[] = 'met_auto_close';
        $config_list[] = 'met_auto_show';
        $config_list[] = 'met_info_security_statement_open';
        $config_list[] = 'met_info_security_statement_title';
        $config_list[] = 'met_info_security_statement_content';
        $config_list[] = 'met_info_security_statement_modal_title';
        configsave($config_list);
        logs::addAdminLog('safety_efficiency', 'save', 'jsok', 'doSaveSetup');

        $admin_dir = $_M['form']['met_adminfile'];
        $res = self::resetAadminDir($admin_dir);    //更新后台目录

        $redata = array();
        if ($res) $redata['url'] = $res;
        buffer::clearConfig();
        $this->success($redata, $_M['word']['jsok']);
    }

    /**
     * 删除安装文件
     */
    public function doDelInstallFile()
    {
        global $_M;
        if($this->admin_member['role']['code'] != 'root') $this->error('error');

        $dir = PATH_WEB . 'install';
        if (is_dir($dir)) {
            deldir($dir);
            logs::addAdminLog('safety_efficiency', 'setsafeupdate', 'jsok', 'doDelAdmin');
            $this->success($dir, $_M['word']['jsok']);
        }
        logs::addAdminLog('safety_efficiency', 'setsafeupdate', 'opfailed', 'doDelAdmin');
        $this->error();
    }

    /**
     * @param $new_admin
     * @return false|string
     */
    private function resetAadminDir($new_admin = '')
    {
        global $_M;
        if($this->admin_member['role']['code'] != 'root') return false;

        $new_admin = trim($new_admin);
        if (empty($new_admin)) return false;
        $old_admin = $_M['config']['met_adminfile'];
        if($old_admin==$new_admin) return false;

        //仅包含字母数字
        if (!is_simplestr($new_admin) /*||strlen($new_admin) < 8 */) {
            $this->error($_M['word']['js77']);
        }
        if (!is_dir(PATH_WEB . $old_admin)) {
            $this->error($old_admin . $_M['word']['setdbNotExist']);
        }
        if (is_dir(PATH_WEB . $new_admin)) {
            $this->error($new_admin . $_M['word']['columnerr4']);
        }

        //目录更名
        $res = rename(PATH_WEB . $old_admin, PATH_WEB . $new_admin);
        if (!$res) {
            movedir(PATH_WEB . $old_admin, PATH_WEB . $new_admin);
            if (!is_dir(PATH_WEB . $new_admin)) {
                $this->error($_M['word']['rename_admin_dir']);
            }
        }

        $url = $_M['url']['web_site'] . "{$new_admin}";
        return $url;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
