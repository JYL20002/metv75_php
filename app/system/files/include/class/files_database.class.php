<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class files_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['files']);
    }

    public function table_para()
    {
        return 'id|path|md5|keywords|size|extension|type|folder|publisher|create_at';
    }

    public function totalSize()
    {
        $sql = "SELECT SUM(`size`) AS totla FROM {$this->table} ";
        $res = DB::get_one($sql);
        if (!$res) return 0;
        return $res['totla'] ?: 0;
    }

    public function totalNum()
    {
        $sql = "SELECT COUNT(`id`) AS totla FROM {$this->table} ";
        $res = DB::get_one($sql);
        if (!$res) return 0;
        return $res['totla'] ?: 0;
    }

    public function clearLogs()
    {
        $sql = "DELETE FROM {$this->table} ";
        return DB::query($sql);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
