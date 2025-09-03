<?php
    # MetInfo Enterprise Content Management System
    # Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

    defined('IN_MET') or exit('No permission');
    load::sys_class('admin');

    class memberAdmin extends admin
    {
        public $database;

        public function __construct()
        {
            parent::__construct();
            $this->adminTable_database = load::mod_class('permission/adminTable_database', 'new');
            $this->roles_database = load::mod_class('permission/roles_database', 'new');
            $this->permission_op = load::mod_class('permission/permission_op', 'new');
            $this->hasPermissions_database = load::mod_class('permission/hasPermissions_database', 'new');
        }

        /**
         * 角色列表
         * @return void
         */
        public function doGetRoles()
        {
            global $_M;
            $list = $this->roles_database->roleList();
            $this->success($list);
        }

        /**
         * 成员列表
         * @return void
         */
        public function doList()
        {
            global $_M;
            $keyword = $_M['form']['keyword'];

            $own = $this->admin_member;
            unset($own['admin_pass']);
            unset($own['cookie']);
            $data = $this->adminTable_database->getMemberList($own);

            $list = array();
            foreach ($data as $key => $one) {
                $one['role'] = $this->roles_database->getOneById($one['role_id']);

                if (!empty($keyword)) {
                    if ($one['admin_id'] == $keyword) {
                        $list[] = $one;
                    }
                } else {
                    $list[] = $one;
                }
            }

            $redata = array();
            $redata['own'] = $own;
            $redata['list'] = $list;
            $this->success($redata);
        }

        /**
         * 成员信息
         * @return void
         */
        public function doMemberInfo()
        {
            global $_M;
            $uid = $_M['form']['id'];
            $member = $this->adminTable_database->getOneByid($uid);
            if (!$member) $this->error($_M['word']['dataerror']);
            unset($member['admin_pass']);

            //设置
            $has_MP = $this->hasPermissions_database->memberPermissions($member['id']);//成员权限
            $member['sync_role_permissions'] = $has_MP ? 0 : 1;//同步角色权限设置

            //权限
            $permissions = self::getUserPermissions($member);

            $redata = array();
            $redata['member_info'] = $member;
            $redata['permissions'] = $permissions;

            return $redata;
//        $this->success($redata);
        }

        /**
         * @param $member
         * @return mixed
         */
        private function getUserPermissions($member)
        {
            global $_M;
            $dmin_member = $this->permission_op->ini($member)->getMember();
            $member_permissions = $this->hasPermissions_database->memberPermissions($dmin_member['id']);
            if ($member_permissions) {//独立用户权限
                return $member_permissions;
            }

            //角色权限
            $role_permissions = $this->hasPermissions_database->rolePermissions($dmin_member['role']['id']);
            return $role_permissions;
        }

        /**
         * 添加成员
         * @return void
         */
        public function doAddSave()
        {
            global $_M;
            $form = $_M['form'];
            //接收参数
            $paras = array(
                'admin_id', 'admin_pass', 'admin_name', 'admin_mobile', 'admin_email',
                'role_id',          //角色id
                'admin_login_lang', //后台默认语言
                'admin_check',      //发布信息需要审核才能正常显示
                'admin_issueok',    //只允许查看自己发表的信息
                'sync_role_permissions',//同步角色权限
                'permissions'       //权限列表 array
            );
            $data = array();
            foreach ($paras as $name) {
                $data[$name] = !is_null($form[$name]) ? $form[$name] : '';
            }

            //必填字段
            $required = array('admin_id', 'admin_pass', 'role_id', 'permissions');
            foreach ($required as $name) {
                if (empty($data[$name])) $this->error($_M['word']['dataerror'], "{$name} field is required ");
            }

            //管理员admin_id
            $has = $this->adminTable_database->checkUniqueMember($data['admin_id']);
            if ($has) $this->error($_M['word']['js78']);

            //管理员邮箱
            if (!empty($data['admin_email'])) {
                $has = $this->adminTable_database->checkUniqueMember($data['admin_email']);
                if ($has) $this->error($_M['word']['admin_email_error']);
            }

            //管理员手机
            if (!empty($data['admin_mobile'])) {
                $has = $this->adminTable_database->checkUniqueMember($data['admin_mobile']);
                if ($has) $this->error($_M['word']['admin_mobile_error']);
            }

            //
            $data = self::analysis($data);
            $new_id = $this->adminTable_database->insert($data);
            if ($new_id) {
                if ($data['sync_role_permissions']) {
                    //同步角色权限
                    self::syncRolePermissions($new_id);
                } else {
                    //独立设置设置管理员权限
                    self::setMemberPermissions($new_id, $form['permissions']);
                }

                logs::addAdminLog('memberAdmin', 'add', 'jsok', M_ACTION);
                $this->success('', $_M['word']['jsok']);
            } else {
                logs::addAdminLog('memberAdmin', 'add', 'dataerror', M_ACTION);
                $this->success('', $_M['word']['jsok']);
            }
        }

        /**
         * 编辑成员
         * @return void
         */
        public function doEditSave()
        {
            global $_M;
            $form = $_M['form'];

            //接收参数
            $paras = array(
                'id', 'admin_pass', 'admin_name', 'admin_mobile', 'admin_email',
                'role_id',//角色id
                'admin_login_lang',//后台默认语言
                'admin_check',//发布信息需要审核才能正常显示
                'admin_issueok',//只允许查看自己发表的信息
                'sync_role_permissions',//同步角色权限
                'permissions'//权限列表 array
            );
            $data = array();
            foreach ($paras as $name) {
                $data[$name] = !is_null($form[$name]) ? $form[$name] : '';
            }

            //必填字段
            $required = array('id', 'role_id', 'permissions');
            foreach ($required as $name) {
                if (empty($data[$name])) $this->error($_M['word']['dataerror'], "{$name} field is required ");
            }

            $member = $this->adminTable_database->getOneByid($data['id']);
            if (!$member) $this->error($_M['word']['dataerror']);
            //判断编辑权限
            if (!self::inSubMember($member)) $this->error($_M['word']['js81']);

            //管理员邮箱
            if (!empty($data['admin_email'])) {
                $has = $this->adminTable_database->checkUniqueMember($data['admin_email'], $member['id']);
                if ($has) $this->error($_M['word']['admin_email_error']);
            }
            //管理员手机
            if (!empty($data['admin_mobile'])) {
                $has = $this->adminTable_database->checkUniqueMember($data['admin_mobile'], $member['id']);
                if ($has) $this->error($_M['word']['admin_mobile_error']);
            }

            //
            $data = self::analysis($data);
            $res = $this->adminTable_database->update_by_id($data);
            if ($res) {
                if ($data['sync_role_permissions']) {
                    //同步角色权限
                    self::syncRolePermissions($member['id']);
                } else {
                    //独立设置设置管理员权限
                    self::setMemberPermissions($member['id'], $form['permissions']);
                }

                logs::addAdminLog('memberAdmin', 'edit', 'jsok', M_ACTION);
                $this->success('', $_M['word']['jsok']);
            } else {
                logs::addAdminLog('memberAdmin', 'edit', 'dataerror', M_ACTION);
                $this->success('', $_M['word']['jsok']);
            }

        }

        /**
         * 删除成员
         * @return void
         */
        public function doDel()
        {
            global $_M;
            $data = $_M['form'];

            //必填字段
            $required = array('id');
            foreach ($required as $name) {
                if (empty($data[$name])) $this->error($_M['word']['dataerror'], "{$name} field is required ");
            }

            $member = $this->adminTable_database->getOneByid($data['id']);
            if (!$member) $this->error($_M['word']['dataerror']);
            //判断编辑权限
            if (!self::inSubMember($member)) $this->error($_M['word']['js81']);

            $this->adminTable_database->deleteMemberById($member['id'], $this->admin_member['id']);
            self::syncRolePermissions($member['id']);

            logs::addAdminLog('memberAdmin', 'delete', 'jsok', M_ACTION);
            $this->success('', $_M['word']['jsok']);
        }


        /**
         * @param $data
         * @return array|mixed
         */
        private function analysis($data = array())
        {
            global $_M;
            //修改密码
            if (empty($data['admin_pass'])) {
                unset($data['admin_pass']);
            } else {
                $data['admin_pass'] = md5($data['admin_pass']);    //新密码
            }
            $data['pid'] = $this->admin_member['id'];

            $role = $this->roles_database->getOneById($data['role_id']);
            if (!$role || $role['code'] == 'root') {
                $this->error('设置角色不可用', '角色ID错误');
            }

            self::checkField($data);

            return $data;
        }

        /**
         * 更新角色权限
         * @param $member_id
         * @param $permissions_list
         * @return void
         */
        private function setMemberPermissions($member_id, $permissions_list = array())
        {
            foreach ($permissions_list as $key => $val) {
                if (!is_simplestr($key)) continue;
                list($type, $aid) = explode('_', $key);
                $this->hasPermissions_database->setPermissions(0, $member_id, strtolower($type), $aid, $val);
            }
        }

        /**
         * 同步角色权限
         * @param $member
         * @return void
         */
        private function syncRolePermissions($member_id)
        {
            $this->hasPermissions_database->syncRolePermissions($member_id);
        }

        /**
         * @param $member
         * @return bool
         */
        private function inSubMember($member)
        {
            //判断编辑权限
            $member_own = admin_information();
            $sub_members = $this->adminTable_database->getMemberList($member_own);
            $sub_ids = arrayColumn($sub_members, 'id');

            if (in_array($member['id'], $sub_ids)) return true;
            return false;
        }

        /**
         * 检测提交字段
         * @param $data
         * @return void
         */
        private function checkField($data = array())
        {
            if ($data['admin_id']) {
                if (!is_simplestr($data['admin_id'], '/^[\x{4e00}-\x{9fa5}a-z-A-Z_0-9\-#@]+$/u')) {
                    $this->error('用户名格式错误，仅支持中文、字母、数字');
                }
            }

            if ($data['admin_name']) {
                if (!is_simplestr($data['admin_name'], '/^[\x{4e00}-\x{9fa5}a-z-A-Z_0-9\-#@]+$/u')) {
                    $this->error('姓名格式错误，仅支持中文、字母、数字');
                }
            }

            if ($data['admin_mobile']) {
                if (!is_simplestr($data['admin_mobile'], '/^\d+$/')) {
                    $this->error('手机格式错误');
                }
            }

            if ($data['admin_email']) {
                if (!is_email($data['admin_email'])) {
                    $this->error('邮箱格式有误');
                }
            }
            return;
        }
    }

    # This program is an open source system, commercial use, please consciously to purchase commercial license.
    # Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
