<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');


class user_group_handle
{
    public $lang;

    public function __construct()
    {
        global $_M;
        $this->user_group_database = load::mod_class('user/user_group_database', 'new');
    }

    public function maxAccess()
    {
        global $_M;
        $query = "SELECT max(`access`) as `access`  FROM {$_M['table']['user_group']} WHERE lang='{$_M['lang']}'";
        $result = DB::get_one($query);

        return $result['access'];
    }


    public function defaultGroup()
    {
        $lsit = $this->user_group_database->groupList();
        return $lsit[0];
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
