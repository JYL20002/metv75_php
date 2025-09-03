<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class myapp_op
{
    public function __construct()
    {
        global $_M;
    }

    public function get_app()
    {
        global $_M;
        $app_out = $this->get_outapp();
        return $app_out;
    }

    public function get_outapp()
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['applist']} WHERE m_name != '' ORDER BY id DESC";
        $list = DB::get_all($query);
        foreach ($list as $val) {
            if (!hasPermission('a', $val['no'])) {
                continue;
            }
            $val = $this->standard($val);
            $app[$val['no']] = $val;
        }
        return $app;
    }

    /**
     * @param $no
     * @return bool|mixed
     */
    public function get_oneapp($no)
    {
        global $_M;
        if ($no < 2000) return false;
        $query = "SELECT * FROM {$_M['table']['applist']} WHERE no='{$no}'";
        $app = DB::get_one($query);
        if (!$app) return false;
        return $this->standard($app);
    }

    public function standard($list)
    {
        global $_M;
        if (!$list['appname']) {
            if ($list['field']) {
                $list['appname'] = get_word($list['name']);
                $list['m_name'] = $list['field'];
                $list['url'] = "{$_M['url']['site_admin']}{$list['url']}&lang={$_M['lang']}";
                $list['ico'] = "{$_M['url']['tem']}myapp/images/{$list['icon']}";
            } else {
                $list['appname'] = get_word($list['name']);
                $list['m_name'] = $list['file'];
                if (file_exists(PATH_WEB . "{$_M['config']['met_adminfile']}/app/{$list['file']}/setapp.php")) {
                    $set_url = "{$_M['url']['site_admin']}app/{$list['file']}/setapp.php";
                } else {
                    $set_url = "{$_M['url']['site_admin']}app/dlapp/setapp.php";
                }
                $list['url'] = "{$set_url}?lang={$_M['lang']}&id={$list['id']}&n={$list['file']}";
                $list['ico'] = "{$_M['url']['site_admin']}app/dlapp/img/{$list['img']}";
                $list['uninstall'] = "{$_M['url']['own_name']}c=myapp&a=dodelapp&no={$list['no']}";
                if ($list['no'] > 10000) {
                    $list['update'] = "{$_M['url']['adminurl']}n=appstore&c=appstore&a=doappdetail&type=app&no={$list['no']}";
                }

            }
        } else {
            $list['appname'] = get_word($list['appname']);
            $list['url'] = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n={$list['m_name']}&c={$list['m_class']}&a={$list['m_action']}";
            $list['ico'] = "{$_M['url']['app']}{$list['m_name']}/icon.png";
            if ($list['depend'] == 'sys') {
                $list['ico'] = "{$_M['url']['site']}app/system/{$list['m_name']}/icon.png";
            }
            $list['uninstall'] = "{$_M['url']['own_name']}c=myapp&a=dodelapp&no={$list['no']}";
            if ($list['no'] > 10000) {
                $list['update'] = "{$_M['url']['adminurl']}n=appstore&c=appstore&a=doappdetail&type=app&no={$list['no']}";
            }

        }
        return $list;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
