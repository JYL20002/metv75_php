<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('common');

class permission_op
{
    public $admin_member;

    public function __construct()
    {
        global $_M;
    }

    public function __invoke()
    {
        return self::currentMember();
    }

    public function getMember()
    {
        return $this->admin_member;
    }

    public function currentMember()
    {
        global $_M;
        $member = admin_information();
        self::ini($member);
        self::leftNav();
        self::topNav();
        self::uisetNav();
        return $this->admin_member;
    }

    /**
     * @param $member
     * @return $this
     */
    public function ini($member)
    {
        if ($this->admin_member['id'] == $member['id']) {
            return $this;
        }
        self::member($member);
        self::memberRole();
        self::permissions();
        return $this;
    }

    /**
     * @param $member
     * @return mixed
     */
    private function member($member)
    {
        unset($member['admin_pass']);
        unset($member['cookie']);
        return $this->admin_member = $member;
    }

    protected function memberRole()
    {
        global $_M;
        $role_id = $this->admin_member['role_id'];
        $query = " SELECT * FROM {$_M['table']['admin_roles']} WHERE id = '{$role_id}' ";
        $role = DB::get_one($query);
        $this->admin_member['role'] = $role;
        return $role;
    }

    /**
     * 当前成员权限
     * @return array|null
     */
    protected function permissions()
    {
        global $_M;
        //超级管理员权限
        $role = $this->admin_member['role'];
        if ($role['code'] == 'root') {
            return self::rootPermissions();
        }

        //成员权限
        $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']}  
        WHERE `uid`='{$this->admin_member['id']}' ";
        $list = DB::get_all($sql);
        if (!$list) {
            //角色权限
            $sql = "SELECT * FROM {$_M['table']['admin_has_permissions']}  
            WHERE  `role_id`='{$this->admin_member['role']['id']}'";
            $list = DB::get_all($sql);
            $permission_type = 'role';
        }else{
            $permission_type = 'member';
        }

        $permissions = array();
        $type_arr = array('s', 'c', 'a', 'f', 'l', 'm');
        foreach ($type_arr as $type) {
            $permissions[$type] = array();
            foreach ($list as $key => $row) {
                if ($type == $row['type']) {
                    $permissions[$type][] = $row;
                    unset($list[$key]);
                }
            }
        }

        $this->admin_member['permissions'] = $permissions;
        $this->admin_member['langok'] = self::langPermissions($permissions['l']);
        $this->admin_member['permission_type'] = $permission_type; //权限类型 角色|成员
        return $permissions;
    }

    /**
     * 超级管理员权限
     * @return void
     */
    private function rootPermissions()
    {
        global $_M;
        //sys
        $slq = "SELECT * FROM {$_M['table']['admin_permissions']} WHERE `type` = 's'";
        $list = DB::get_all($slq);
        $s_list = array();
        foreach ($list as $row) {
            $s = array();
            $s['aid'] = $row['aid'];
            $s['type'] = 's';
            $s['access'] = 2;
            $f['info'] = $row['name'];
            $s_list[] = $s;
        }
        $permissions['s'] = $s_list;

        //func
        $slq = "SELECT * FROM {$_M['table']['admin_permissions']} WHERE `type` = 'f'";
        $list = DB::get_all($slq);
        $s_list = array();
        foreach ($list as $row) {
            $f = array();
            $f['aid'] = $row['aid'];
            $f['type'] = 'f';
            $f['access'] = 2;
            $f['info'] = $row['name'];
            $s_list[] = $f;
        }
        $permissions['f'] = $s_list;

        //column
        $slq = "SELECT * FROM {$_M['table']['column']}";
        $list = DB::get_all($slq);
        $c_list = array();
        foreach ($list as $row) {
            $c = array();
            $c['aid'] = $row['id'];
            $c['type'] = 'c';
            $c['access'] = 2;
            $c['info'] = $row['name'];
            $c_list[] = $c;
        }
        $permissions['c'] = $c_list;

        //app
        $slq = "SELECT * FROM {$_M['table']['applist']}";
        $list = DB::get_all($slq);
        $a_list = array();
        foreach ($list as $row) {
            $a = array();
            $a['aid'] = $row['no'];
            $a['type'] = 'a';
            $a['access'] = 2;
            $a['info'] = $row['appname'];
            $a_list[] = $a;
        }
        $permissions['a'] = $a_list;

        //lang   lang !='metinfo' 兼容老数据
        $slq = "SELECT * FROM {$_M['table']['lang']} WHERE lang !='metinfo'";
        $list = DB::get_all($slq);
        $s_list = array();
        foreach ($list as $row) {
            $l = array();
            $l['aid'] = $row['id'];
            $l['type'] = 'l';
            $l['access'] = 2;
            $l['info'] = $row['name'];
            $s_list[] = $l;
        }
        $permissions['l'] = $s_list;

        //m
        $slq = "SELECT * FROM {$_M['table']['admin_menus']} ";
        $list = DB::get_all($slq);
        $s_list = array();
        foreach ($list as $row) {
            $m = array();
            $m['aid'] = $row['aid'];
            $m['type'] = 'm';
            $m['access'] = 1;
            $m['info'] = $row['info'];
            $s_list[] = $m;
        }
        $permissions['m'] = $s_list;

        $this->admin_member['permissions'] = $permissions;
        $this->admin_member['langok'] = self::langPermissions($permissions['l']);
        return $permissions;
    }

    /**
     * @param $permissions
     * @return array|null
     */
    private function langPermissions($permissions_l)
    {
        global $_M;
        $list = array();
        foreach ($permissions_l as $permissions) {
            if ($permissions['access']) {
                $slq = "SELECT * FROM {$_M['table']['lang']}  WHERE id = '{$permissions['aid']}'";
                $res = DB::get_one($slq);
                if ($res) $list[] = $res;
            }
        }
        return $list;
    }

    /**
     * @return mixed
     */
    public function leftNav()
    {
        global $_M;
        $menus = array();
        foreach ($this->admin_member['permissions']['m'] as $permission) {
            if ($permission['access']) {
                $menus[] = $permission['aid'];
            }
        }

        $m_id = implode(',', $menus) ?: 0;
        $sql = "SELECT * FROM {$_M['table']['admin_menus']} WHERE aid IN ({$m_id}) AND type = 1 AND display = 1  ORDER BY sort";
        $res = DB::get_all($sql);

        $list = array();
        foreach ($res as $key => $val) {
            //1508 企业超市
            //1505 应用插件
            //1405 网站模板

            $val['menu_lang'] = get_word($val['menu_lang']);
            if (in_array($val['name'], array('feedback_system', 'message_system', 'recruitment_system'))) {
                $res = self::getClssUrl($val, $list);
                if(!$res) continue; //没有反馈栏目时候不显示上级菜单
            }
            $list[] = $val;
        }
        $tree = array();
        $tree = getTree($tree, $list, 0, 0, 'pid');
        $this->admin_member['left_nav'] = $tree;
        return $tree;
    }

    /**
     * @param $info
     * @param $list
     * @return array
     */
    private function getClssUrl($info, &$list)
    {
        global $_M;
        $arr = array('feedback_system' => 8, 'message_system' => 7, 'recruitment_system' => 6);
        $module = $arr[$info['name']];
        $_c = is_array($this->admin_member['permissions']['c']) ? arrayColumn($this->admin_member['permissions']['c'], 'aid') : array();
        $c_id = implode(',', $_c);

        $sql = "SELECT * FROM {$_M['table']['column']} WHERE id IN ({$c_id}) AND `lang` = '{$_M['lang']}' AND `module` = '{$module}'  ORDER BY no_order DESC ";
        $res = DB::get_all($sql);
        if (!$res) return false;

        $module_name = load::sys_class('handle', 'new')->mod_to_name($module);
        foreach ($res as $row) {
            $url = "manage/?module={$module_name}";
            $class = array();
            if ($row['bigclass']) {
                $url .= "&class1={$row['bigclass']}&class2={$row['id']}";
            } else {
                $url .= "&class1={$row['id']}";
            }

            $class['id'] = null;
            $class['name'] = $row['name'];
            $class['menu_lang'] = $row['name'];
            $class['info'] = $row['name'];
            $class['sort'] = $row['no_order'];
            $class['pid'] = $info['id'];
            $class['url'] = $url;
            $list[] = $class;
        }
        return true;
        //return $list;
    }

    /**
     * 顶部菜单
     * @return array
     */
    public function topNav()
    {
        global $_M;
        $menus = array();
        foreach ($this->admin_member['permissions']['m'] as $permission) {
            if ($permission['access']) {
                $menus[] = $permission['aid'];
            }
        }

        $m_id = implode(',', $menus) ?: 0;
        $sql = "SELECT * FROM {$_M['table']['admin_menus']} WHERE aid IN ({$m_id}) AND type = 2 AND  display = 1 ORDER BY sort";
        $list = DB::get_all($sql);


        $redata = array();
        foreach ($list as $key => $val) {
            //1104 检测更新
            if (!$_M['config']['met_agents_update'] && $val['sid']=='1104') {
                continue;
            }

            $val['menu_lang'] = get_word($val['menu_lang']);
            $redata[] = $val;
        }

        $this->admin_member['top_nav'] = $redata;
        return $redata;
    }

    /**
     * 可视化菜单
     * @return array
     */
    public function uisetNav()
    {
        global $_M;
        $menus = array();
        foreach ($this->admin_member['permissions']['m'] as $permission) {
            if ($permission['access']) {
                $menus[] = $permission['aid'];
            }
        }

        $m_id = implode(',', $menus) ?: 0;
        $sql = "SELECT * FROM {$_M['table']['admin_menus']} WHERE aid IN ({$m_id}) AND type = 3 AND  display = 1 ORDER BY pid, sort ";
        $list = DB::get_all($sql);

        $redata = array();
        foreach ($list as $key => $val) {
            //1104 检测更新
            if (!$_M['config']['met_agents_update'] && $val['sid']=='1104') {
                continue;
            }
            //1508 企业超市
            //1405 网站模板
            //1505 应用插件

            $val['menu_lang'] = get_word($val['menu_lang']);
            $redata[] = $val;
        }
        $this->admin_member['uiset_nav'] = $redata;
        return $redata;
    }

    /**
     * 成员权限检测
     * @param string $type
     * @param $aid
     * @param $access
     * @return bool
     */
    public function hasPermission($type, $aid, $access = 1)
    {
        $role = $this->admin_member['role'];
        if ($role['code'] === 'root') return true;

        $permissions = $this->admin_member['permissions'][$type] ?: array();
        $aid_list = arrayColumn($permissions, 'aid');
        $find_key = array_search($aid, $aid_list);
        if (!isset($permissions[$find_key])) return false;
        $permission = $permissions[$find_key];
        if (intval($access) <= intval($permission['access'])) {
            return true;
        }
        return false;
    }

    /**
     * 获取成员权限
     * @param $type s;c;a;m;l
     * @param $aid
     * @param int $access 0,1,2
     */
    public function getPermissions($type, $access = 1)
    {
        $permissions = $this->admin_member['permissions'][$type] ?: array();
        if (!$permissions) return null;

        $redata = array();
        foreach ($permissions as $permission) {
            if (intval($access) <= intval($permission['access'])) {
                $redata[] = $permission;
            }
        }
        return $redata;
    }

    /**
     * @param $type
     * @param $aid
     * @return void
     */
    public function setPermissions($type, $aid, $access = 1)
    {
        $access = $access ? intval($access) : 0;
        $roles_database = load::mod_class('permission/roles_database', 'new');
        $roles = $roles_database->roleList();
        $hasPermissions_database = load::mod_class('permission/hasPermissions_database', 'new');
        foreach ($roles as $role) {
            $hasPermissions_database->setPermissions($role['id'], 0, $type, $aid, $access);
        }

        //设置独立成员权限
        $member_admin = self::currentMember();
        if ($member_admin['permission_type'] == 'member') {
            $hasPermissions_database->setPermissions(0, $member_admin['id'], $type, $aid, $access);
        }
    }

}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
