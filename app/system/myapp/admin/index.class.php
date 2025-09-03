<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

class index extends admin
{
    public function __construct()
    {
        global $_M;
        $this->myApp = load::mod_class('myapp/class/myapp', 'new');
        $this->mituoUser = load::mod_class('myapp/class/mituoMember', 'new');

        parent::__construct();
    }

    public function doIndex()
    {
        global $_M;
        $app_list = $this->myApp->listApp();
        jsoncallback(array('data'=>$app_list));
    }


    public function doAppList()
    {
        global $_M;
        $type = $_M['form']['type'] ?: 'free';
        $app_list =  $this->myApp->typeList($type);
        jsoncallback(array('data'=>$app_list));
    }

    /**
     * @return void
     */
    public function doAction()
    {
        global $_M;
        $data = array();
        $data['name'] = $_M['form']['m_name'];
        $data['appno'] = $_M['form']['no'];
        $handle = $_M['form']['handle'];

        $action = array('install', 'uninstall', 'update');
        if (!in_array($handle, $action)) $this->error('data error');

        if (in_array($handle, array('install', 'update'))) { //安装权限
            if (!hasPermission('s', 1800)) $this->error('您没有应用安装权限');
        }
        if ($handle == 'uninstall') { //卸载权限
            if (!hasPermission('s', 1801)) $this->error('您没有应用卸载权限');
        }

        $res = $this->myApp->$handle($data);
        if ($res) {
            $this->success(array(), $_M['word']['jsok']);
        } else {
            $this->error($_M['word']['opfailed']);
        }
    }

    /**
     * 米拓账号登录
     */
    public function doLogin()
    {
        global $_M;
        $data = array();
        //$data['username'] = $_M['form']['username'];
        //$data['password'] = $_M['form']['password'];
        $data['username'] = isset($_M['form']['username']) ? authcode($_M['form']['username'], "DECODE") : '';
        $data['password'] = isset($_M['form']['password']) ? authcode($_M['form']['password'], "DECODE") : '';
        $res = $this->mituoUser->login($data);
        if ($res) {
            //写日志
            logs::addAdminLog('myapp', 'loginconfirm', 'jsok', 'doLogin');
            if ($res['status'] == 200) {
                $this->success($res['data'], $res['msg']);
            }
            $this->error($res['msg']);
        } else {
            //写日志
            logs::addAdminLog('myapp', 'loginconfirm', 'opfailed', 'doLogin');
            $this->error($_M['word']['opfailed']);
        }
    }

    public function doLogout()
    {
        global $_M;
        $res = $this->mituoUser->getUserInfo('logout');
        if ($res) {
            //写日志
            logs::addAdminLog('myapp', 'indexloginout', 'jsok', 'doLogout');
            $this->success($res['data'], $res['msg']);
        } else {
            //写日志
            logs::addAdminLog('myapp', 'indexloginout', 'opfailed', 'doLogout');
            $this->error($_M['word']['opfailed']);
        }
    }

    public function doUserInfo()
    {
        global $_M;
        $res = $this->mituoUser->getUserInfo();
        if ($res) {
            $this->success($res['data'], $res['msg']);
        } else {
            $this->error($_M['word']['opfailed']);
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
