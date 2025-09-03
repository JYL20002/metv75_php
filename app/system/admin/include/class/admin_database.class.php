<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

/**
 * 系统标签类
 */

class admin_database extends database
{

    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['admin_table']);
    }

    public function table_para()
    {
        return 'id|pid|role_id|admin_type|admin_id|admin_pass|admin_name|admin_tel|admin_mobile|admin_email|admin_introduction|admin_login|admin_modify_ip|admin_modify_date|admin_register_date|admin_approval_date|admin_ok|admin_op|admin_issueok|admin_group|cookie|lang|langok|admin_login_lang|admin_check';
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneByid($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = '{$id}'";
        return DB::get_one($sql);
    }

    /**
     * @param $admin_id
     * @return array
     */
    public function getOneByName($admin_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE admin_id = '{$admin_id}'";
        return DB::get_one($sql);
    }

    /**
     * @param $admin
     * @param $info
     * @return array|null
     */
    public function hasAnother($admin, $info = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE (admin_id = '{$info}' OR admin_email = '{$info}' OR  admin_mobile = '{$info}' ) AND id != '{$admin['id']}'";
        return DB::get_all($sql);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
