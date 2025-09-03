<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin.class.php');
load::mod_class('content/class/module');

class sys_product extends module
{
    public $errorno;
    public $table;
    public $tablename;
    public $paraclass;
    public $module;

    public function __construct()
    {
        global $_M;
        $this->tablename = $_M['table']['product'];
        $this->module = 3;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
