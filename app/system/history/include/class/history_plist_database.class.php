<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class history_plist_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['history_plist']);
    }

    public function table_para()
    {
        return 'id|hid|listid|paraid|info|lang|imgname|module';
    }

    /**
     * @param $aid
     * @param $module
     * @param $paraid
     * @param $hid
     * @return array
     */
    public function getPlistListHistory($aid, $module, $paraid, $hid)
    {
        global $_M;
        $query = "SELECT * FROM {$this->table} WHERE listid = '{$aid}' AND paraid='{$paraid}' AND module={$module} AND lang = '{$_M['lang']}'  AND hid ='{$hid}'";
        return DB::get_one($query);
    }

    /**
     * @param string $listid
     * @return int|mixed
     */
    public function delByAid($aid = '', $module = 0)
    {
        global $_M;
        $query = "DELETE FROM {$this->table} WHERE listid='{$aid}' AND module = '{$module}'";

        return DB::query($query);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
