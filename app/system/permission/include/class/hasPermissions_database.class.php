<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class hasPermissions_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['admin_has_permissions']);
    }

    public function table_para()
    {
        return 'id|role_id|uid|aid|type|access|info';
    }

    /**
     * 获取成员权限列表
     * @param $member
     * @return array
     */
    public function memberPermissions($member_id)
    {
        global $_M;
        //成员权限
        $sql = "SELECT * FROM {$this->table} WHERE `uid`='{$member_id}' ";
        $list = DB::get_all($sql);

        $Permissions = array();
        foreach ($list as $row) {
            $row['p_code'] = $p_code = "{$row['type']}_{$row['aid']}";
            $row['p_value'] = $p_value = $row['access'];
            $Permissions[$p_code] = $p_value ? intval($p_value) : 0;
        }
        return $Permissions;
    }

    /**
     * 获取角色权限列表
     * @param $role_id
     * @return array
     */
    public function rolePermissions($role_id)
    {
        global $_M;
        $sql = "SELECT * FROM {$this->table}  
            WHERE  `role_id`='{$role_id}'";
        $list = DB::get_all($sql);

        $Permissions = array();
        foreach ($list as $row) {
            $row['p_code'] = $p_code = "{$row['type']}_{$row['aid']}";
            $row['p_value'] = $p_value = $row['access'];
            $Permissions[$p_code] = $p_value ? intval($p_value) : 0;
        }
        return $Permissions;
    }

    /**
     * @param $role_id
     * @param $uid
     * @param $type
     * @param $aid
     * @param $access
     * @param $info
     * @return void
     */
    public function setPermissions($role_id = 0, $uid = 0, $type = 's', $aid = 0, $access = 1, $info = '')
    {
        global $_M;
        $sql = "SELECT * FROM {$this->table} WHERE 
                        `aid`='{$aid}' AND 
                        `type`='{$type}' AND 
                        `role_id` ='{$role_id}' AND 
                        `uid`='{$uid}'
                        ";
        $has = DB::get_one($sql);

        $info = addslashes($info);
        if (!$has) {
            $data = array(
                'role_id' => intval($role_id),
                'uid' => intval($uid),
                'aid' => intval($aid),
                'type' => $type,
                'access' => $access,
                'info' => $info,
            );
            $res = $this->insert($data);
        } else {
            $data = array(
                'id' => $has['id'],
                'access' => $access,
                'info' => $info,
            );
            $res = $this->update_by_id($data);
        }
        return $res;
    }

    /**
     * @param $member_id
     * @return void
     */
    public function syncRolePermissions($member_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE  `uid`='{$member_id}'";
        return DB::query($sql);
    }

}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
