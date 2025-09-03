<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

/** 管理员设置 */
class index extends admin
{
    public $database;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->database = load::mod_class('admin/admin_database', 'new');
    }

    /**
     *
     * @return mixed
     */
    public function doInfo()
    {
        $info = $this->admin_member;
        unset($info['admin_pass']);
        unset($info['cookie']);
        unset($info['permissions']);
        unset($info['left_nav']);
        unset($info['top_nav']);
        unset($info['uiset_nav']);
        unset($info['langok']);
        $info = load::mod_class('weixin/weixinapi', 'new')->getAdminBindInfo($info);
        return $info;
    }

    public function doGetOpenid()
    {
        global $_M;
        $info = $this->admin_member;
        
        $this->success($info['openid']);
    }
    /**
     * 保存系统管理员设置信息
     * @return void
     */
    public function doSaveInfo()
    {
        global $_M;
        $form = $_M['form'];

        $data = array();
        $arr = array('admin_email', 'admin_mobile', 'admin_name', 'admin_pass');
        foreach ($arr as $name) {
            $data[$name] = !empty($form[$name]) ? $form[$name] : '';
        }

        //字段检测
        self::checkField($data);

        $admin = $this->admin_member;
        if (!empty($data['admin_email'])) {
            $has = $this->database->hasAnother($admin, $data['admin_email']);
            if ($has) $this->error($_M['word']['admin_email_error']);
        }

        if (!empty($data['admin_mobile'])) {
            $has = $this->database->hasAnother($admin, $data['admin_mobile']);
            if ($has) $this->error($_M['word']['admin_mobile_error']);
        }

        //修改密码
        if (!empty($data['admin_pass'])) {
            $data['admin_pass'] = md5($data['admin_pass']);
        }else{
            unset($data['admin_pass']);
        }

        $data['id'] = $this->admin_member['id'];
        $update_result = $this->database->update_by_id($data);
        if (!$update_result) {
            logs::addAdminLog('metadmin', 'adminpassTitle', 'dataerror', 'doSaveInfo');
            $this->error($_M['word']['dataerror']);
        }
        logs::addAdminLog('metadmin', 'adminpassTitle', 'jsok', 'doSaveInfo');
        $this->success('', $_M['word']['jsok']);
    }

    /**
     * 检测提交字段
     */
    protected function checkField($data = array())
    {
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
        return true;
    }

    /**
     * 解绑微信
     * @return void
     */
    public function doUnbind()
    {
        global $_M;
        $admin = $this->admin_member;
        $sql = "UPDATE {$_M['table']['admin_table']} SET openid = '' WHERE id ='{$admin['id']}'";
        DB::query($sql);
        $this->success('','解绑成功');
    }
    
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
