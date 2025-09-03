<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('database');

class user_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['user']);
    }

    public function table_para()
    {
        return 'id|username|password|head|email|tel|groupid|register_time|login_count|login_time|login_ip|valid|source|lang|idvalid|admin_approval_date|reidinfo';
    }

    public function getOneById($id)
    {
        $id = intval($id);
        $sql = "SELECT * FROM {$this->table} WHERE id='{$id}'";
        return DB::get_one($sql);
    }

    /**
     * @param $username
     * @return array|false
     */
    public function getOneByUsername($username)
    {
        global $_M;
        if(empty($username)) return false;
        $sql = "SELECT * FROM {$this->table} WHERE (username='{$username}' OR email='{$username}' OR tel='{$username}') AND lang='{$_M['lang']}' ";
        return DB::get_one($sql);
    }

    /**
     * @param $user
     * @param $value
     * @return bool
     */
    public function hasOthers($user , $value)
    {
        global $_M;
        if(empty($value)) return false;
        $sql = "SELECT * FROM {$this->table} WHERE (username='{$value}' OR email='{$value}' OR tel='{$value}') AND lang='{$_M['lang']}' AND id!={$user['id']} ";
        return DB::get_one($sql);
    }

    /**
     * @param $value
     * @return false|void
     */
    public function hasAdminMember($value)
    {
        global $_M;
        if(empty($value)) return false;
        $sql = "SELECT * FROM {$_M['table']['admin_table']} WHERE admin_id='{$value}' ";
        return DB::get_one($sql);
    }

    /**
     * @param $user
     * @param $attr
     * @param $value
     * @return false|int|mixed
     */
    public function setUserAttr($user ,$attr = '', $value = '')
    {
        $attrbute_list = array('email', 'tel', 'valid' ,'groupid', 'password');
        if(!$user) return false;
        if(!in_array($attr,$attrbute_list)) return false;

        $sql = "UPDATE {$this->table} SET {$attr}='{$value}' WHERE id='{$user['id']}'";
        return DB::query($sql);
    }

    /**
     * @param $uid
     * @return bool
     */
    public function delUserByid($uid)
    {
        global $_M;
        $query = "DELETE FROM {$this->table} WHERE id='{$uid}'";
        DB::query($query);
        $query = "DELETE FROM {$_M['table']['user_other']} WHERE met_uid='{$uid}'";
        DB::query($query);
        $query = "DELETE FROM {$_M['table']['user_list']} WHERE listid='{$uid}'";
        DB::query($query);
        return true;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>