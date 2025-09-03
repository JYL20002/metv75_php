<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

load::sys_class('admin');
/** 会员组设置 */
class admin_group extends admin
{
    public $userclass;
    public $groupclass;

    public function __construct()
    {
        parent::__construct();
        $this->groupclass = load::mod_class('user/sys_group', 'new');
        $this->user_group_database = load::mod_class('user/user_group_database', 'new');
    }

    //获取会员组
    public function doGetUserGroup()
    {
        global $_M;
        $group_list = $this->user_group_database->userGroupList();
        $redata['group_list'] = $group_list;
        $redata['payment_open'] = $_M['config']['payment_open'] ?: 0;
        $this->success($redata);
    }

    //保存分组
    public function doSaveGroup()
    {
        global $_M;
        $list = isset($_M['form']['data']) ? $_M['form']['data'] : '';
        if (!$list) $this->error();

        foreach ($list as $group) {
            if (!$group['name']) continue;
            $group['access'] = intval($group['access']);
            if ($group['access'] < 1) {
                $this->error($_M['word']['usereadinfo']);
            }

            if ($group['id']) {
                $this->update($group);
                logs::addAdminLog('membergroup', 'update', 'jsok', 'doSaveGroup');
            } else {
                $this->insert($group);
                logs::addAdminLog('membergroup', 'added', 'jsok', 'doSaveGroup');
            }
        }
        buffer::clearGroup($_M['lang']);
        $this->success('', $_M['word']['jsok']);
    }

    /**
     * @param $data
     * @return void
     */
    private function insert($data)
    {
        global $_M;
        $new_group = array();
        $new_group['name'] = $data['name'];
        $new_group['access'] = $data['access'];
        $new_group['lang'] = $_M['lang'];
        $group_id = $this->user_group_database->insert($new_group);
        if ($group_id) {
            $pay_group['recharge_price'] = $data['recharge_price']? : 0;
            $pay_group['rechargeok'] = $data['rechargeok']?  : 0;
            $pay_group['price'] = $data['price'] ?  : 0;
            $pay_group['buyok'] = $data['buyok'] ?  : 0;
            $this->user_group_database->setGroupPay($group_id, $pay_group);
        }
        return;
    }

    /**
     * @param $data
     * @return bool
     */
    private function update($data)
    {
        global $_M;
        $group = array();
        $group['id'] = $data['id'];
        $group['name'] = $data['name'];
        $group['access'] = $data['access'];
        $group['lang'] = $_M['lang'];
        $this->user_group_database->update_by_id($group);

        $pay_group['recharge_price'] = $data['recharge_price']? : 0;
        $pay_group['rechargeok'] = $data['rechargeok']?  : 0;
        $pay_group['price'] = $data['price'] ?  : 0;
        $pay_group['buyok'] = $data['buyok'] ?  : 0;
        $this->user_group_database->setGroupPay($data['id'], $pay_group);
        return true;
    }

    /**
     * 删除会员组
     * @return void
     */
    public function doDelGroup()
    {
        global $_M;
        $all_id = $_M['form']['id'] ?: '';
        if (!$all_id) $this->error();

        foreach ($all_id as $id) {
            $this->user_group_database->delGroup($id);
        }
        //写日志
        logs::addAdminLog('membergroup', 'delete', 'jsok', 'doDelGroup');
        cache::del('user', 'file');
        $this->success('', $_M['word']['jsok']);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>