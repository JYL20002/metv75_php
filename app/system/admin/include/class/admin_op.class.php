<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

/**
 * 系统标签类
 */

class admin_op
{
    /**
     * @param string $column_id 栏目ID或者app编号
     * @param string $accsess_type 权限类型 s后台栏目 | c栏目 | a应用
     * @param string $type add增加 | del去除
     */
    public function modifyAccess($column_id = '', $accsess_type = '', $type = '')
    {
        global $_M;
        if (!is_numeric($column_id) ||!$accsess_type || !$type) {
            return false;
        }
        $column_sty = $accsess_type . $column_id;

        if ($accsess_type == 'a') { //app
            $query = "SELECT * FROM {$_M['table']['admin_table']} WHERE admin_group NOT IN ('10000','1','2')";
        }else{
            $query = "SELECT * FROM {$_M['table']['admin_table']} WHERE admin_group!='10000'";
        }
        $admin_list = DB::get_all($query);

        foreach ($admin_list as $admin) {
            self::adminTypeUpdate($admin, $type, $column_sty);
        }
        return;
    }

    protected function adminTypeUpdate($admin, $type = 'add', $column_sty = '')
    {
        global $_M;
        $admin_type = explode('-', trim($admin['admin_type'], '-'));
        if ($type == 'add') {
            $admin_type[] = $column_sty;
            $admin_type = array_unique($admin_type);
        } else {
            $admin_type = array_unique($admin_type);
            $key = array_search($column_sty, $admin_type);
            unset($admin_type[$key]);
        }

        sort($admin_type);
        $admin_type = implode('-', $admin_type);
        $admin_type = "-{$admin_type}-";
        $query = "UPDATE {$_M['table']['admin_table']} SET `admin_type` = '{$admin_type}' WHERE id = '{$admin['id']}'";
        DB::query($query);
        return;
    }
}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
