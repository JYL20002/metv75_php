<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class history_relation_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['history_relation']);
    }

    public function table_para()
    {
        return 'id|hid|aid|module|relation_id|relation_module|lang';
    }

    /**
     * @param string $listid
     * @return int|mixed
     */
    public function delByAid($aid = '', $module = 0)
    {
        global $_M;
        $query = "DELETE FROM {$this->table} WHERE aid='{$aid}' AND module = '{$module}'";
        return DB::query($query);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
