<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class myapp
{
    public $power;

    public function __construct()
    {
        global $_M;
    }

    /**
     * 我的应用，如果已经登录应用商店，显示购买的应用.
     */
    public function listApp()
    {
        global $_M;
        $data = $this->localApp();
        $localApp = arrayColumn($data, 'no');        // 本地已安装的应用

        if ($_M['config']['met_secret_key'] && $_M['config']['met_agents_metmsg']) {
            $online = $this->onlineApp();
            foreach ($online as $val) {
                if (in_array($val['no'], $localApp)) continue;

                //PHP版本验证
                $php_ver = PHP_VERSION;
                $val['enabled'] = 1;
                if (!version_compare($val['php_version'], $php_ver, '<=')) {
                    $val['enabled'] = 0;
                    $val['btn_text'] = "应用需要PHP{$val['php_version']}及以上版本，当前网站PHP版本为：{$php_ver},请切换您网站的PHP程序版本";
                }
                $data[] = $val;
            }
        }

        return $data;
    }

    /**
     * @param $type
     * @return array
     */
    public function typeList($type)
    {
        global $_M;
        $url = 'https://u.mituo.cn/api/site/applications';
        $result = api_curl($url, array('type' => $type));
        $res = json_decode($result, true);
        $online_list = $res['data']['data'];

        $redata = array();
        $localApp = arrayColumn($this->localApp(), 'no');        // 本地已安装的应用
        foreach ($online_list as $val) {
            if (in_array($val['product_code'], $localApp)) continue;

            //PHP版本验证
            $php_ver = PHP_VERSION;
            $val['enabled'] = 1;
            if (!version_compare($val['php_version'], $php_ver, '<=')) {
                $val['btn_text'] = "应用需要PHP{$val['php_version']}及以上版本，当前网站PHP版本为：{$php_ver},请切换您网站的PHP程序版本";
                $val['enabled'] = 0;
            }
            $val['show_url'] .= $_M['config']['metinfo_code'] ? "?metinfo_code={$_M['config']['metinfo_code']}" : '';
            $redata[] = $val;
        }
        return $redata;
    }

    //本地应用
    public function localApp()
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['applist']} WHERE `display` > 0 ORDER BY id DESC";
        $applist = DB::get_all($query);

        if ($applist && $_M['config']['met_secret_key']) {
            $data = array(
                'action' => 'checkUpdateApp',
                'applist' => base64_encode(json_encode($applist)),
            );
            $res = api_curl($_M['config']['met_api'], $data);
            $result = json_decode($res, true);

            if ($result['status'] == 200) {
                $applist = $result['data'];
            }
        }

        $system = array('0', '10070', '50002');
        foreach ($applist as $key => $val) {
            if ($val['no']) {
                if (!hasPermission('a', $val['no'])) {
                    unset($applist[$key]);
                    continue;
                }

                //版权 短信插件
                if (!$_M['config']['met_agents_sms'] && in_array($val['no'], array(10070))) {
                    unset($applist[$key]);
                    continue;
                }
            }

            if (file_exists(PATH_WEB . "app/app/{$val['m_name']}/config.json")) {
                $applist[$key]['newapp'] = '1'; //新框架
                $applist[$key]['url'] = $_M['url']['site_admin'] . "#/app/{$val['m_name']}/?c={$val['m_class']}&a={$val['m_action']}";
            } else {
                $applist[$key]['url'] = $_M['url']['site_admin'] . "?n={$val['m_name']}&c={$val['m_class']}&a={$val['m_action']}";
            }

            $module = $val['m_name'] == 'pay' ? 'system' : 'app';
            $applist[$key]['icon'] = $_M['url']['site'] . "app/{$module}/{$val['m_name']}/icon.png";
            $applist[$key]['system'] = in_array($val['no'], $system);
            $applist[$key]['install'] = 1;
        }

        $applist = array_values($applist);
        return $applist;
    }

    //已购买插件
    private function onlineApp()
    {
        global $_M;
        $data = array(
            'action' => 'getMyApp',
        );
        $result = api_curl($_M['config']['met_api'], $data);

        if ($result) {
            $data = json_decode($result, true);
            $applist = $data['data'];
            if (!isset($data['data'])) {
                return array();
            }

            foreach ($applist as $key => $val) {
                if (!hasPermission('a', $val['no'])) {
                    unset($applist[$key]);
                    continue;
                }


                $applist[$key]['icon'] = $val['icon'];
                $applist[$key]['install'] = 0;
            }

            return $applist;
        }

        return array();
    }

    //检测更新
    public function checkAppUpdate()
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['applist']} WHERE display > 0 ORDER BY id DESC";
        $applist = DB::get_all($query);

        if ($applist && $_M['config']['met_secret_key']) {
            $data = array(
                'action' => 'checkUpdateApp',
                'applist' => base64_encode(json_encode($applist)),
            );
            $res = api_curl($_M['config']['met_api'], $data);
            $result = json_decode($res, true);

            if ($result['status'] == 200) {
                $applist = $result['data'];

                $update_list = array();
                foreach ($applist as $app) {
                    if ($app['new_ver']) {
                        $update_list[] = $app;
                    }
                }
            }
            return $update_list;
        }
    }

    //安装
    public function install($data)
    {
        global $_M;
        $appno = $data['appno'];
        $name = $data['name'];
        $file = PATH_WEB . 'app/app/' . $name . '/admin/install.class.php';
        if ($appno == 10080) {
            $file = PATH_WEB . 'app/system/' . $name . '/admin/install.class.php';
        }
        // if(file_exists($file)){
        //     $install = load::app_class($name . '/admin/install.class.php', 'new');
        //     $install->dosql();
        //     return true;
        // }
        $data = array(
            'action' => 'installApp',
            'appno' => $appno,
        );

        if (!is_writable(PATH_WEB . 'cache')) {
            //写日志
            logs::addAdminLog('myapp', 'appinstall', 'templatefilewritno', 'doAction');
            error(PATH_WEB . 'cache' . $_M['word']['templatefilewritno']);
        }

        $appzip = PATH_WEB . 'cache/' . $appno . '.zip';
        if (file_exists($appzip)) {
            @unlink($appzip);
        }

        $result = down_curl($_M['config']['met_api'], $data, $appzip);
        if ($result !== true) {
            //写日志
            logs::addAdminLog('myapp', 'appinstall', 'opfailed', 'doAction');
            error($result);
        }

        if (!file_exists($appzip)) {
            //写日志
            logs::addAdminLog('myapp', 'appinstall', 'opfailed', 'doAction');
            error($_M['word']['file_download_failed']);
        }

        $zip = new ZipArchive();
        if ($zip->open($appzip) === true) {
            $zip->extractTo(PATH_WEB); //假设解压缩到在当前路径下images文件夹的子文件夹php
            $zip->close(); //关闭处理的zip文件
        } else {
            //写日志
            logs::addAdminLog('myapp', 'appinstall', 'webupate4', 'doAction');
            error($_M['word']['webupate4']);
        }

        if (!file_exists($file)) {
            //写日志
            logs::addAdminLog('myapp', 'appinstall', 'dltips5', 'doAction');
            error($_M['word']['dltips5']);
        }

        if ($appno == 10080) {
            include $file;
            $install = new install();
        } else {
            $install = load::app_class($name . '/admin/install.class.php', 'new');
        }
        $install->dosql();
        //写日志
        logs::addAdminLog('myapp', 'appinstall', 'jsok', 'doAction');

        //更改管理员应用权限
        $permission_op = load::mod_class('permission/permission_op', 'new');
        $permission_op->setPermissions('a', $appno, 1);

        return $result;
    }

    //卸载
    public function uninstall($data)
    {
        global $_M;
        $appno = $data['appno'];
        $query = "SELECT m_name FROM {$_M['table']['applist']} WHERE no = '{$appno}'";
        $app = DB::get_one($query);
        if (!$app) {
            //写日志
            logs::addAdminLog('myapp', 'dlapptips6', 'dltips5', 'doAction');
            error($_M['word']['dltips5']);
        }

        if ($appno == 10080) {
            $file = PATH_WEB . 'app/system/' . $app['m_name'] . '/admin/uninstall.class.php';
            if (!file_exists($file)) {
                return;
            }
            include $file;
            $uninstall = new uninstall();
            $uninstall->dodel();

            return true;
        }

        $file = PATH_WEB . 'app/app/' . $app['m_name'] . '/admin/uninstall.class.php';

        if (!file_exists($file)) {
            if ($app['m_name'] == 'met_template') {
                //写日志
                logs::addAdminLog('myapp', 'dlapptips6', 'met_template_nodelet', 'doAction');
                error($_M['word']['met_template_nodelet']);
            }
            // 卸载文件不存在
            $query = "DELETE FROM {$_M['table']['applist']} WHERE no = '{$appno}'";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['app_config']} WHERE no = '{$appno}'";
            DB::query($query);
            $query = "DELETE FROM {$_M['table']['app_plugin']} WHERE no = '{$appno}'";
            DB::query($query);
            deldir(PATH_WEB . 'app/app/' . $app['m_name']);
            //写日志
            logs::addAdminLog('myapp', 'dlapptips6', 'jsok', 'doAction');

            return true;
        }
        //写日志
        logs::addAdminLog('myapp', 'dlapptips6', 'jsok', 'doAction');
        $uninstall = load::app_class($app['m_name'] . '/admin/uninstall', 'new');
        $uninstall->dodel();

        //更改管理员应用权限
        $permission_op = load::mod_class('permission/permission_op', 'new');
        $permission_op->setPermissions('a', $appno, 0);

        return true;
    }

    public function update($appno)
    {
    }


    public function getAppModule()
    {
        global $_M;
        $sql = "SELECT * FROM {$_M['table']['applist']} WHERE no > 10000";
        $list = DB::get_all($sql);
        return $list;
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
