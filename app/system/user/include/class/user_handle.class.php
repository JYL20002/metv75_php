<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('database');

class user_handle
{
    public $user_database;
    public $user_group;
    public $paraclass;
    public $error;

    public function __construct()
    {
        $this->user_database = load::mod_class('user/user_database', 'new');
        $this->user_group_database = load::mod_class('user/user_group_database', 'new');
        $this->user_group_handle = load::mod_class('user/user_group_handle', 'new');
        $this->paraclass = load::sys_class('para', 'new');
    }

    /**
     * @param $id
     * @return false
     */
    public function getUserInfo($id)
    {
        global $_M;
        $user = $this->user_database->getOneById($id);
        if(!$user) return false;
        unset($user['password']);

        $para_list = $this->paraclass->get_para_list(10);
        foreach ($para_list as $key => $para) {
            $query = "SELECT info FROM {$_M['table']['user_list']} WHERE listid = '{$id}' AND paraid='{$para['id']}' AND lang = '{$_M['lang']}'";
            $user_info = DB::get_one($query);
            $para_list[$key]['value'] = htmlspecialchars($user_info['info']);
        }
        $user['user_para'] = $para_list;
        return $user;
    }

    /**
     * @param $username
     * @param $password
     * @param $email
     * @param $tel
     * @param $valid
     * @param $groupid
     * @param $source
     * @return false
     */
    public function createUser($username = '', $password = '', $email = '', $tel = '',$valid = '', $groupid = '', $source = '')
    {
        global $_M;
        
        if (!self::checkUsernameStr($username)) {
            $this->error = "username invalidate";
            return false;
        }
        
        if ($this->user_database->hasAdminMember($username)||$this->user_database->getOneByUsername($username)) {
            $this->errormsg = "用户名已存在";
            $this->error = "username_already_exists";
            return false;
        }
        
        if (!self::checkPassword($password)) {
            $this->error = "password invalidate";
            return false;
        }
        //defaul
        $defaultGroup = $this->user_group_handle->defaultGroup();
        $groupid = $groupid ?: $defaultGroup['id'];
        $time = time();

        $new = array();
        $new['username'] =  $username;
        $new['email'] =  $email;
        $new['tel'] =  $tel;
        $new['groupid'] = $groupid;
        $new['valid'] =  $valid;
        $new['source'] =  $source;
        $new['login_time'] =  $time;
        $new['register_time'] =  $time;
        $new['register_ip'] = get_userip();
        $new['password'] = md5($password);
        $new['lang'] = $_M['lang'];
        $new_id = $this->user_database->insert($new);

        if (!$new_id) {
            $this->errormsg = '用户创建失败';
            $this->error = 'error_user_create_failed';
            return false;
        }

        return $new_id;
    }

    /**
     * @param $user_id
     * @param $para
     * @return mixed
     */
    public function setUsetPara($user_id, $para)
    {
        //用户属性
        return $this->paraclass->insert_para($user_id, $para, 10);
    }

    /**
     * 更改用户组
     * @param int $userid 用户id
     * @param int $group 分组编号
     * @return bool
     */
    public function setUserGroup($userid = '', $group_id = '')
    {
        global $_M;
        if (!$userid) return false;
        $user = $this->user_database->getOneById($userid);
        if (!$user) return false;
        $group = $this->user_group_database->getGroup($group_id);
        if(!$group) return false;
        return $this->user_database->setUserAttr($user, 'groupid', $group['id']);
    }

    /**
     * @param $user
     * @param $password
     * @return false|void
     */
    public function setUserPassword($user, $password)
    {
        global $_M;
        if (!self::checkPassword($password)) {
            $this->error = "password invalidate";
            return false;
        }
        $md5_password = md5($password);
        $res = $this->user_database->setUserAttr($user, 'password', $md5_password);
        return $res;
    }


    /**
     * 验证密码的有效性
     * @param string $password
     * @return bool
     */
    public function checkPassword($password = '')
    {
        global $_M;
        $len = str_length($password, 1);
        if ($len < 6 || $len > 30) {
            $this->errormsg = '密码长度需为6-30个字符';
            $this->error = 'error_password_cha';
            return false;
        }
        return true;
    }

    /**
     * 用户字符检测
     * @param string $username
     * @return bool
     */
    public function checkUsernameStr($username = '')
    {
        global $_M;
        $len = str_length($username, 1);
        if ($len < 2 || $len > 30) {
            $this->errormsg = "用户名长度需为2-30个字符";
            $this->errorno = 'error_username_cha';
            return false;
        }

        #$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
        $guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
        if ($len > 30 || $len < 2 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
            $this->errormsg = "含有非法字符";
            $this->error = 'error_username_cha';
            return false;
        }

        $arr = (explode('|', $_M['config']['met_fd_word']));
        foreach ($arr as $val) {
            if ($val != '' && strstr($username, $val)) {
                $this->errormsg = "含有非法字符";
                $this->error = 'error_username_cha';
                return false;
            }
        }
        return true;
    }



}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>