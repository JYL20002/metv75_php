<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('database');
/**
 * 更新迁移数据
 * 更新迁移数据
 */
class update_78 extends database
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var
     */
    private $colum_label;

    public function __construct()
    {
        global $_M;
        $this->version = '7.8';
    }

    public function clearDate()
    {
        global $_M;
        $tables = array('admin_menus', 'admin_permissions', 'admin_roles');
        foreach ($tables as $table) {
            $sql = "DELETE FROM {$_M['table'][$table]}";
            DB::query($sql);
        }
    }

    /**
     * @return false|void
     */
    public function defaultDate()
    {
        file_put_contents(PATH_WEB . 'update78.log', "defaultDate\n", FILE_APPEND);
        $tables = array('admin_menus', 'admin_permissions', 'admin_roles');
        foreach ($tables as $table) {
            $json_path = PATH_WEB . "app/system/update/include/class/{$table}.json";
            if (!file_exists($json_path)) continue;
            $data = json_decode(file_get_contents($json_path), true);
            if (!$data) continue;

            self::insertDefaultDate($table, $data);
        }
    }

    /**
     * @param $table
     * @param $data
     * @return void
     */
    private function insertDefaultDate($table, $data)
    {
        global $_M;
        $query = "DELETE FROM {$_M['table'][$table]} ";
        DB::query($query);

        $dbtype = strtolower($_M['config']['db_type']);
        if (strtolower($dbtype) == 'dmsql') {
            $sql = "SET IDENTITY_INSERT {$_M['table'][$table]} ON;";
            dm_exec(DB::$link, $sql);
            dm_autocommit(DB::$link);
        }

        foreach ($data as $row) {
            $sql = get_sql($row);
            $query = "INSERT INTO {$_M['table'][$table]} SET {$sql}";
            DB::query($query);
        }

        if (strtolower($dbtype) == 'dmsql') {
            $sql = "SET IDENTITY_INSERT {$_M['table'][$table]} OFF;";
            dm_exec(DB::$link, $sql);
            dm_commit(DB::$link);
        }
    }


    /**
     * 设置角色或用户权限
     * @return void
     */
    public function setRolesPermissions()
    {
        global $_M;
        file_put_contents(PATH_WEB . 'update78.log', "setRolesPermissions\n", FILE_APPEND);
        $sql = "SELECT * FROM {$_M['table']['admin_roles']} ";
        $role_list = DB::get_all($sql);

        foreach ($role_list as $role) {
            switch ($role['code']) {
                case 'sys_admin_1':
                    self::roleSystem1($role);
                    break;
                case 'sys_admin_2':
                    self::roleSystem2($role);
                    break;
            }
        }
    }

    /**
     * 系统管理员
     * @param $role
     * @return void
     */
    private function roleSystem1($role)
    {
        $permissions = self::permissionsList();
        foreach ($permissions as $permission_type) {
            foreach ($permission_type as $permission) {
                self::permissionsInsert($role['id'], 0, $permission['type'], $permission['aid'], 1);
            }
        }
    }

    /**
     * 内容管理员
     * @param $role
     * @return void
     */
    private function roleSystem2($role)
    {
        $json_path = PATH_WEB . "app/system/update/include/class/role_2.json";
        $json = file_get_contents($json_path);
        $m_s_list = json_decode($json, true);
        foreach ($m_s_list as $permission) {
            self::permissionsInsert($role['id'], 0, $permission['type'], $permission['aid'], $permission['access']);
        }

        $c_list = self::permissionsColumns();
        foreach ($c_list as $permission) {
            self::permissionsInsert($role['id'], 0, $permission['type'], $permission['aid'], $permission['access']);
        }

        $l_list = self::permissionsLang();
        foreach ($l_list as $permission) {
            self::permissionsInsert($role['id'], 0, $permission['type'], $permission['aid'], $permission['access']);
        }
    }

    /**
     * @return void
     */
    public function setMemberPermissions()
    {
        global $_M;
        file_put_contents(PATH_WEB . 'update78.log', "setMemberPermissions\n", FILE_APPEND);
        $sql = "SELECT * FROM {$_M['table']['admin_table']}";
        $admin_list = DB::get_all($sql);

        foreach ($admin_list as $admin) {
            if ($admin['role_id']) continue;

            switch ($admin['admin_group']) {
                case 10000://超级管理员
                    $sql = "SELECT * FROM {$_M['table']['admin_roles']} WHERE code = 'root'";
                    $role = DB::get_one($sql);
                    $sql = "UPDATE {$_M['table']['admin_table']} SET role_id = '{$role['id']}', pid=0 WHERE id = '{$admin['id']}'";
                    DB::query($sql);
                    break;
                case 0://自定义管理员
                case 1://内容管理员
                case 2://SEO优化
                    $sql = "SELECT * FROM {$_M['table']['admin_roles']} WHERE code = 'sys_admin_2'";
                    $role = DB::get_one($sql);
                    $sql = "UPDATE {$_M['table']['admin_table']} SET role_id = '{$role['id']}', pid=1 WHERE id = '{$admin['id']}'";
                    DB::query($sql);

                    //自定义管理员设置独立成员权限
                    if ($admin['admin_group'] == 0) {
                        //admin_type
                        $admin_type = explode('-', $admin['admin_type']);
                        if ($admin['admin_type']) {
                            sort($admin_type);
                            foreach ($admin_type as $access) {
                                if (!$access) continue;
                                $type = substr($access, 0, 1);
                                $aid = substr($access, 1);
                                //系统模块权限
                                self::permissionsInsert(0, $admin['id'], $type, $aid, 1);

                                //菜单菜单权限
                                if ($type == 's') {
                                    $sql = "SELECT * FROM {$_M['table']['admin_menus']} WHERE sid = '{$aid}'";
                                    $menus = DB::get_all($sql);
                                    if ($menus) {
                                        foreach ($menus as $menu) {
                                            self::permissionsInsert(0, $admin['id'], 'm', $menu['aid'], 1);
                                        }
                                    }
                                }
                            }
                        }

                        //操作权限
                        $admin_op = explode('-', $admin['admin_op']);
                        if ($admin['admin_op']) {
                            $options = array('add' => '1802', 'editor' => '1803', 'del' => '1804',);
                            foreach ($admin_op as $action) {
                                if (isset($options[$action])) {
                                    self::permissionsInsert(0, $admin['id'], 's', $options[$action], 1);
                                }
                            }
                        }

                        //多语言权限
                        $langoks = explode('-', $admin['langok']);
                        if ($admin['langok']) {
                            $lang_list = self::permissionsLang();
                            foreach ($langoks as $langok) {
                                if ($langok == 'metinfo') {
                                    foreach ($lang_list as $permission) {
                                        self::permissionsInsert(0, $admin['id'], 'l', $permission['aid'], 1);
                                    }
                                    break;
                                }

                                foreach ($lang_list as $permission) {
                                    if ($langok == $permission['lang'])
                                        self::permissionsInsert(0, $admin['id'], 'l', $permission['aid'], 1);
                                }
                            }
                        }

                        //可视化权限
                        if ($admin['admin_pop']) {
                            self::permissionsInsert(0, $admin['id'], 'm', '8888', 1);
                        }
                    }

                    break;
                case 3://系统管理员
                    $sql = "SELECT * FROM {$_M['table']['admin_roles']} WHERE code = 'sys_admin_1'";
                    $role = DB::get_one($sql);
                    $sql = "UPDATE {$_M['table']['admin_table']} SET role_id = '{$role['id']}', pid=1 WHERE id = '{$admin['id']}'";
                    DB::query($sql);
                    break;
            }
        }
    }

    /**
     * @param $role_id
     * @param $uid
     * @param $type
     * @param $aid
     * @param $access
     * @return void
     */
    private function permissionsInsert($role_id = 0, $uid = 0, $type, $aid, $access = 1)
    {
        global $_M;
        if ($role_id) {
            $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']} WHERE role_id = '{$role_id}' AND type = '{$type}' AND aid = '{$aid}'";
            $has = DB::get_one($sql);
        } elseif ($uid) {
            $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']} WHERE uid = '{$uid}' AND type = '{$type}' AND aid = '{$aid}'";
            $has = DB::get_one($sql);
        }

        if (!$has) {
            $sql = "INSERT INTO {$_M['table']['admin_has_permissions']} (role_id, uid, type ,aid, access) VALUES ('{$role_id}','{$uid}','{$type}','{$aid}' ,'{$access}')";
            DB::query($sql);
//            dump($sql);
        }
        return;
    }

    /**************************************************************/
    /**
     * 获取所用系统权限
     * @return void
     */
    private function permissionsList()
    {
        $s_list = self::permissionsSys();
        $f_list = self::permissionsFunc();
        $m_list = self::permissionsMenus();
        $c_list = self::permissionsColumns();
        $a_list = self::permissionsApp();
        $l_list = self::permissionsLang();

        $redata = array();
        $redata['sys'] = $s_list;
        $redata['func'] = $f_list;
        $redata['menus'] = $m_list;
        $redata['columns'] = $c_list;
        $redata['app'] = $a_list;
        $redata['web_lang'] = $l_list;

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

        $redata = array();
        foreach ($list as $k => $v) {
            $one = array();
            $one['aid'] = $v['aid'];
            $one['type'] = 's';
            $one['name'] = $v['name'];
            $redata[] = $one;
        }
        return $redata;
    }

    /**
     * 内容栏目权限
     * @return array
     */
    private function permissionsColumns()
    {
        global $_M;
        $slq = "SELECT id,name,no_order,lang,module,foldername, bigclass AS pid FROM {$_M['table']['column']} WHERE 1=1 ORDER BY no_order ASC";
        $list = DB::get_all($slq);

        $redata = array();
        foreach ($list as $k => $v) {
            $one = array();
            $one['aid'] = $v['id'];
            $one['type'] = 'c';
            $one['info'] = $v['name'];
            $redata[] = $one;
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
        $slq = "SELECT * FROM {$_M['table']['applist']} WHERE `no`!=0";
        $list = DB::get_all($slq);

        $redata = array();
        foreach ($list as $k => $v) {
            $one = array();
            $one['aid'] = $v['no'];
            $one['type'] = 'a';
            $one['info'] = $v['info'];
            $redata[] = $one;
        }
        return $redata;
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

        $redata = array();
        foreach ($list as $k => $v) {
            $one = array();
            $one['aid'] = $v['id'];
            $one['type'] = 'l';
            $one['lang'] = $v['lang'];
            $one['info'] = $v['name'];
            $redata[] = $one;
        }
        return $redata;
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

        $redata = array();
        foreach ($list as $k => $v) {
            $one = array();
            $one['aid'] = $v['aid'];
            $one['type'] = 'f';
            $one['info'] = $v['name'];
            $redata[] = $one;
        }
        return $redata;
    }

    /**
     * @return array
     */
    public function permissionsMenus()
    {
        global $_M;
        $slq = "SELECT * FROM {$_M['table']['admin_menus']}  ORDER BY sort";
        $list = DB::get_all($slq);

        $redata = array();
        foreach ($list as $k => $v) {
            $one = array();
            $one['aid'] = $v['aid'];
            $one['sid'] = $v['sid'];
            $one['type'] = 'm';
            $one['info'] = $v['info'];
            $redata[] = $one;
        }
        return $redata;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
