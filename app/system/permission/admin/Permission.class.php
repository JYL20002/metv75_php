<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('admin');

class Permission extends admin
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = load::mod_class('permission/adminTable_database', 'new');
        $this->permissions_handle = load::mod_class('permission/permissions_handle', 'new');
    }

    /**
     * 获取所有系统权限
     * @return void
     */
    public function doSysPermissions()
    {
        $list = $this->permissions_handle->permissionsList();
        $this->success($list);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
