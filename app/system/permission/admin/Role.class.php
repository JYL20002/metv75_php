<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

    /**
     *
     */
class Role extends admin
{
    public $database;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->permissions_handle = load::mod_class('permission/permissions_handle', 'new');
        $this->roles_database = load::mod_class('permission/roles_database', 'new');
        $this->hasPermissions_database = load::mod_class('permission/hasPermissions_database', 'new');

        if ($this->admin_member['role']['code'] != 'root') {
            $this->error($_M['word']['js81']);
        };
    }

    /**
     * 角色列表
     * @return void
     */
    public function dolist()
    {
        global $_M;
        $list = $this->roles_database->roleList();
        $this->success($list);
    }

    /**
     * 角色信息
     * @return void
     */
    public function doRoleInfo()
    {
        global $_M;
        $role_id = $_M['form']['id'];
        $role = $this->roles_database->getOneById($role_id);
        if (!$role) $this->error($_M['word']['dataerror'], 'not exists');
        if (self::isRoot($role)) $this->error($_M['word']['dataerror'], 'not exists'); //超级管理员不能编辑删除

        $redata = array();
        $redata['role'] = $role;
        $redata['permissions'] = $this->hasPermissions_database->rolePermissions($role['id']);
        if($_M['form']['ajax']){
            $this->success($redata);
        }else{
            return $redata;
        }
    }

    /**
     * 新增角色
     * @return void
     */
    public function doAddSave()
    {
        global $_M;
        $form = $_M['form'];
        //必填字段
        $required = array('name', 'permissions');
        foreach ($required as $name) {
            if (empty($form[$name])) $this->error($_M['word']['dataerror'], "{$name} field is required ");
        }

        //接收参数
        $paras = array('name','info');
        $data = array();
        foreach ($paras as $name) {
            $data[$name] = !empty($form[$name]) ? $form[$name] : '';
        }

        $r_name = sqlinsert($data['name']);
        if(empty($r_name)) $this->error("角色名称不能为空");
        if ($this->roles_database->getOneByName($r_name)) $this->error('角色名已存在');

        $data['code'] = 'admin';//自定义管理员
        $new_id = $this->roles_database->insert($data);
        if ($new_id && is_numeric($new_id)) {
            //更新角色权限
            self::setRolePermissions($new_id, $form['permissions']);

            $this->success($_M['word']['jsok']);
        }
        $this->error($_M['word']['dataerror']);
    }

    /**
     * 编辑角色
     * @return void
     */
    public function doEditSave()
    {
        global $_M;
        $form = $_M['form'];
        //必填字段
        $required = array('id', 'name', 'permissions');
        foreach ($required as $name) {
            if (empty($form[$name])) $this->error($_M['word']['dataerror'], "{$name} field is required ");
        }

        //接收参数
        $data = array();
        $paras = array('id','name','info');
        foreach ($paras as $name) {
            $data[$name] = !empty($form[$name]) ? $form[$name] : '';
        }

        if(empty($data['name'])) $this->error("角色名称不能为空");

        $role = $this->roles_database->getOneById($form['id']);
        if (!$role) $this->error($_M['word']['dataerror'], 'Not exists');
        if (self::isRoot($role)) $this->error($_M['word']['dataerror'], 'No access'); //超级管理员不能编辑删除
        if($this->roles_database->hasOther($role,$data['name'])) $this->error('角色名已存在');

        $res = $this->roles_database->update_by_id($data);
        //更新角色权限
        self::setRolePermissions($role['id'], $form['permissions']);

        $res ? $this->success('',$_M['word']['jsok']) : $this->error($_M['word']['dataerror']);
    }

    /**
     * 删除角色
     * @return void
     */
    public function doDel()
    {
        global $_M;
        $form = $_M['form'];
        if (!$form['id']) $this->error($_M['word']['dataerror'], "id field is required ");

        $role = $this->roles_database->getOneById($form['id']);
        if (!$role) $this->error($_M['word']['dataerror'], 'role not exists');
        if (self::isSysRole($role)) $this->error('系统角色不可删除', 'system role can not delete'); //系统角色不可删除

        $res = $this->roles_database->delRole($role);
        if (!$res) {
            $msg = $this->roles_database->msg;
            $this->error($msg);
        }
        $this->success($_M['word']['jsok']);
    }

    /**
     * 系统角色
     * @param $role
     * @return bool
     */
    private function isSysRole($role)
    {
        global $_M;
        if (in_array(trim($role['code']), array('root', 'sys_admin_1','sys_admin_2'))) return true;
        return false;
    }

    /**
     * @param $role
     * @return bool
     */
    private function isRoot($role)
    {
        if (in_array(trim($role['code']), array('root'))) return true;
        return false;
    }

    /**
     * 更新角色权限
     * @param $role_id
     * @param $permissions_list
     * @return void
     */
    private function setRolePermissions($role_id, $permissions_list = array())
    {
        if(!$permissions_list) return false;
        foreach ($permissions_list as $key => $val) {
            if (!is_simplestr($key)) continue;
            list($type, $aid) = explode('_', $key);
            $this->hasPermissions_database->setPermissions($role_id, 0, strtolower($type), $aid, $val);
        }

    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
