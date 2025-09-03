<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('common');


class permissions_handle extends common
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    /**
     * @param $member
     * @return array
     */
    public function memberPermissions($member)
    {
        global $_M;
        //成员权限
        $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']}  
        WHERE `uid`='{$member['id']}' ";
        $list = DB::get_all($sql);
        if (!$list) {
            //角色权限
            $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']}  
            WHERE  `role_id`='{$member['role']['id']}'";
            $list = DB::get_all($sql);
        }

        $Permissions = array();
        foreach ($list as $row) {
            $row['p_code'] = $p_code = "{$row['type']}_{$row['aid']}";
            $row['p_value'] = $p_value= $row['access'];
            $Permissions[$p_code] = $p_value ? intval($p_value) : 0;
        }
        return $Permissions;
    }

    /**
     * @param $role
     * @return array
     */
    public function rolePermissions($role)
    {
        global $_M;
        //角色权限
        $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']}  
            WHERE  `role_id`='{$role['id']}'";
        $list = DB::get_all($sql);

        $Permissions = array();
        foreach ($list as $row) {
            $row['p_code'] = $p_code = "{$row['type']}_{$row['aid']}";
            $row['p_value'] = $p_value= $row['access'];
            $Permissions[$p_code] = $p_value ? intval($p_value) : 0;
        }
        return $Permissions;
    }

    /**
     * 可设置权限列表
     * @return array
     */
    public function permissionsList()
    {
        $s_list = self::permissionsSys();
        $f_list = self::permissionsFunc();
        $m_list = self::permissionsMenus();
        $l_list = self::permissionsLang();
        $c_list = self::permissionsColumns();
        $a_list = self::permissionsApp();

        $redata = array();
        $redata['sys'] = $s_list;
        $redata['func'] = $f_list;
        $redata['menus'] = $m_list;
        $redata['web_lang'] = $l_list;
        $redata['columns'] = $c_list;
        $redata['app'] = $a_list;

        return $redata;
    }

    /**
     * 系统功能权限
     * @return mixed
     */
    private function permissionsSys()
    {
        global $_M;
        $slq = "SELECT * FROM {$_M['table']['admin_permissions']} WHERE `type`='s'";
        $list = DB::get_all($slq);

        $tree = array();
        $tree = getTree($tree, $list);
        foreach ($tree as $key => $value) {
            $one = array();
            $one['id'] = $value['id'];
            $one['pid'] = $value['pid'];
            $one['p_name'] = $value['name'];
            $one['aid'] = $value['aid'];
            $one['p_type'] = 's';
            $one['p_code'] = "s_{$value['aid']}";
            $one['level'] = $value['level'];
            $tree[$key] = $one;
        }

        return $tree;
    }

    /**
     * 内容栏目权限
     * @return array
     */
    private function permissionsColumns()
    {
        global $_M;
        $redata = array();
        $lang_arr = $_M['langlist']['web'];
        foreach ($lang_arr as $row) {
            $slq = "SELECT id,name,no_order,lang,module,foldername, bigclass as pid FROM {$_M['table']['column']} WHERE lang = '{$row['lang']}' ORDER BY no_order ASC";
            $list = DB::get_all($slq);
            $tree = array();
            $tree = getTree($tree, $list);

            foreach ($tree as $key => $value) {
                $one = array();
                $one['id'] = $value['id'];
                $one['pid'] = $value['pid'];
                $one['p_name'] = $value['name'];
                $one['aid'] = $value['id'];
                $one['p_type'] = 'c';
                $one['p_code'] = "c_{$value['id']}";
                $one['level'] = $value['level'];
                $tree[$key] = $one;
            }
            $redata[$row['lang']] = $tree;
        }
        return $redata;
    }

    /**
     * 应用权限
     * @return array
     */
    private function permissionsApp()
    {
        global $_M;
        $slq = "SELECT id,no,m_name,appname FROM {$_M['table']['applist']} WHERE `no`!=0";
        $list = DB::get_all($slq);

        foreach ($list as $key => $value) {
            $one = array();
            $one['p_name'] = $value['appname'];
            $one['aid'] = $value['no'];
            $one['p_type'] = 'a';
            $one['p_code'] = "a_{$value['no']}";
            $list[$key] = $one;
        }
        return $list;
    }

    /**
     * 多语言权限
     * @return array
     */
    private function permissionsLang()
    {
        global $_M;
        $slq = "SELECT * FROM {$_M['table']['lang']}";
        $list = DB::get_all($slq);

        foreach ($list as $key=>$value) {
            $one = array();
            $one['p_name'] = $value['name'];
            $one['aid'] = $value['id'];
            $one['p_type'] = 'l';
            $one['p_code'] = "l_{$value['id']}";
            $list[$key] = $one;
        }
        return $list;
    }

    /**
     * 操作权限
     * @return mixed
     */
    public function permissionsFunc()
    {
        global $_M;
        $slq = "SELECT * FROM {$_M['table']['admin_permissions']} WHERE `type`='f' ";
        $list = DB::get_all($slq);

        $tree = array();
        $tree = getTree($tree, $list);
        foreach ($tree as $key => $value) {
            $one = array();
            $one['id'] = $value['id'];
            $one['p_name'] = $value['name'];
            $one['pid'] = $value['pid'];
            $one['aid'] = $value['aid'];
            $one['p_type'] = 'f';
            $one['p_code'] = "f_{$value['aid']}";
            $one['level'] = $value['level'];
            $tree[$key] = $one;
        }
        return $tree;
    }

    /**
     * @return array
     */
    public function permissionsMenus()
    {
        global $_M;
        $redata = array();
        $arr = array('left' => 1, 'top' => 2, 'ui_set' => 3);
        foreach ($arr as $type => $val) {
            $slq = "SELECT * FROM {$_M['table']['admin_menus']} WHERE `type`='{$val}' AND display = 1 ORDER BY sort";
            $list = DB::get_all($slq);

            $tree = array();
            $tree = getTree($tree, $list);
            foreach ($tree as $key => $value) {
                $one = array();
                $one['id'] = $value['id'];
                $one['p_name'] = get_word($value['menu_lang']);
                $one['pid'] = $value['pid'];
                $one['aid'] = $value['aid'];
                $one['p_type'] = 'm';
                $one['p_code'] = "m_{$value['aid']}";
                $one['level'] = $value['level'];
                $tree[$key] = $one;
            }
            $redata[$type] = $tree;
        }

        return $redata;
    }

}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
