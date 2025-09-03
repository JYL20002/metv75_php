<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('database');

class adminTable_database extends database
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
     * @param $member
     * @return mixed
     */
    public function getMemberList($member)
    {
        $list = array();
        self::getSub($member, $list);
        $tree = array();
        $tree = getTree($tree, $list, $member['id']);
        return $tree;
    }

    /**
     * @param $member
     * @param $list
     * @return array|mixed
     */
    private function getSub($member, &$list = array())
    {
        $fields = "*";
        $query = "SELECT {$fields} FROM {$this->table} WHERE pid = '{$member['id']}'";
        $res = DB::get_all($query);
        foreach ($res as $key => $one) {
            self::getSub($one, $list);
            unset($one['admin_pass']);
            $list[] = $one;
        }
        return $list;
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
     * 检测用户关键字段
     * @param $str
     * @param $own_id
     * @return array
     */
    public function checkUniqueMember($str = '', $own_id = null)
    {
        $sql = "SELECT * FROM {$this->table} 
         WHERE (admin_id = '{$str}' 
         OR admin_mobile = '{$str}' 
         OR admin_email = '{$str}'
         OR admin_mobile = '{$str}'
         )";

        if ($own_id) {
            $sql .= " AND id!='{$own_id}'";
        }
        return DB::get_one($sql);
    }

    /**
     * 删除成员并将被删除成员的子级移动至当前成员下
     * @param $member_id
     * @param $own_id
     * @return bool
     */
    public function deleteMemberById($member_id, $own_id)
    {
        $sql = "UPDATE {$this->table} SET pid ='{$own_id}' WHERE pid = '{$member_id}'";
        DB::query($sql);

        $sql = "DELETE FROM {$this->table} WHERE id='{$member_id}'";
        DB::query($sql);
        return true;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
