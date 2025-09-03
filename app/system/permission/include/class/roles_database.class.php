<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class roles_database extends database
{
    public $msg;

    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['admin_roles']);
    }

    public function table_para()
    {
        return 'id|name|code|sort|info';
    }

    public function roleList()
    {
        $sql = "SELECT * FROM {$this->table}  WHERE code!='root'";
        return DB::get_all($sql);
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = '{$id}'";
        return DB::get_one($query);
    }

    /**
     * @param $name
     * @return array
     */
    public function getOneByName($name)
    {
        $query = "SELECT * FROM {$this->table} WHERE name = '{$name}'";
        return DB::get_one($query);
    }

    /**
     * @param $role
     * @param $name
     * @return array|null
     */
    public function hasOther($role, $name)
    {
        $query = "SELECT * FROM {$this->table} WHERE name = '{$name}' AND id != '{$role['id']}'";
        return DB::get_all($query);
    }

    /**
     * @param $role
     * @return array|null
     */
    public function roleMembers($role)
    {
        global $_M;
        $sql = "SELECT * FROM {$_M['table']['admin_table']} WHERE role_id = '{$role['id']}'";
        $role_members = DB::get_all($sql);
        return $role_members;
    }

    /**
     * @param $role
     * @return bool
     */
    public function delRole($role)
    {
        global $_M;
        $has = $this->roleMembers($role);
        if ($has) {
            $this->msg = "删除失败，当前角色已启用。";
            return false;
        }

        $sql = "DELETE FROM {$_M['table']['admin_has_permissions']} WHERE role_id = '{$role['id']}'";
        DB::query($sql);

        $sql = "DELETE FROM {$this->table} WHERE id = '{$role['id']}'";
        DB::query($sql);
        return true;
    }

}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
