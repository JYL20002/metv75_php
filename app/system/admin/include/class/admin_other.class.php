<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class admin_other
{
    public $error;

    public function __construct()
    {
    }

    public function getLoginQr($admin_user = array())
    {
        global $_M;
        $rand = random(32) . uniqid();
        $weixinapi = load::mod_class('weixin/weixinapi', 'new');
        if ($admin_user['openid'] == '') {
            //首次登录
            $expires_in = time() + 62; //1分钟过期
            $query = "UPDATE {$_M['table']['admin_table']} SET access_token = '{$rand}' ,expires_in = '{$expires_in}' , other_login = -1 WHERE id = '{$admin_user['id']}' ";
            DB::query($query);
            $scene_str = 'adminregister&' . $rand;
        }else{
            //已绑定账号登录
            $expires_in = time() + 62; //1分钟过期
            $query = "UPDATE {$_M['table']['admin_table']} SET access_token = '{$rand}' ,expires_in = '{$expires_in}', other_login = 0 WHERE id = '{$admin_user['id']}' ";
            DB::query($query);
            $scene_str = 'adminlogin&' . $rand;
        }
        $qrcode = $weixinapi->QRcode($scene_str);
        if ($qrcode && !$weixinapi->error) {
            $redata = array();
            $redata['qrurl'] = $qrcode;
            $redata['token'] = $rand;
            $redata['admin_id'] = $admin_user['admin_id'];
            return $redata;
        }
        $this->error = $weixinapi->error;
        return false;
    }

    /**
     * @param array $wxuser
     * @param string $token
     */
    public function wxAdminRegister($wxuser = array(), $token = '')
    {
        global $_M;
        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE openid = '{$wxuser['openid']}'";
        $admin_user = DB::get_one($query);
        if ($admin_user) {
            //微信已绑定
            return false;
        }

        $date_now = time();
        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE access_token = '{$token}' AND openid ='' AND expires_in > $date_now AND other_login = -1";
        $admin_user = DB::get_one($query);
        if (!$admin_user) {
            //超时
            return false;
        }

        $query = "UPDATE {$_M['table']['admin_table']} SET openid = '{$wxuser['openid']}' , other_login = 1 WHERE access_token = '{$token}'";
        DB::query($query);
        return true;
    }

    /**
     * @param array $wxuser
     * @param string $token
     * @return bool
     */
    public function wxAdminLogin($wxuser = array(), $token = '')
    {
        global $_M;
        $date_now = time();
        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE access_token = '{$token}'";
        $admin_user = DB::get_one($query);
        if (!$admin_user) {
            return false;
        }

        if ($admin_user['openid'] != $wxuser['openid']) {
            $query = "UPDATE {$_M['table']['admin_table']} SET expires_in = '0' ,access_token = '' WHERE access_token = '{$token}' ";
            DB::query($query);
            return false;
        }

        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE openid = '{$wxuser['openid']}' AND  access_token = '{$token}' AND expires_in > $date_now ";
        $admin_user = DB::get_one($query);
        if ($admin_user) {
            $query = "UPDATE {$_M['table']['admin_table']} SET other_login = 1 WHERE openid = '{$wxuser['openid']}' AND access_token = '{$token}' ";
            DB::query($query);
        }
    }

    /**
     * 轮询微信登录状态
     * @param string $code
     * @return bool
     */
    public function checkWxLogin($token = '' ,$admin_id = '')
    {
        global $_M;
        if (!$token) {
            return false;
        }
        $date_now = time();

        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE  admin_id = '{$admin_id}' AND access_token = '{$token}'";
        $admin_user = DB::get_one($query);
        if (!$admin_user) {
            $this->error = '登录失败';
            return false;
        }

        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE access_token = '{$token}' AND admin_id = '{$admin_id}' AND expires_in > $date_now ";
        $admin_user = DB::get_one($query);
        if (!$admin_user) {
            $this->error = '登录超时';
            return false;
        }

        if ($admin_user['openid']) {
            if ($admin_user['other_login'] == 0) {
                return true;
            }
        }else{
            if ($admin_user['other_login'] == -1) {
                return true;
            }
        }

        $weixinapi = load::mod_class('weixin/weixinapi', 'new');
        $date = date("Y-m-d H:i:s");
        $msg = "你的账号{$admin_user['admin_id']}已登录网站后台
登录时间：$date ";
        $weixinapi->customMessage($admin_user['openid'], $msg);  //微信通知

        return $admin_user;
    }

    /**
     * 绑定微信二维码
     * @param array $admin_user
     * @return array|bool
     */
    public function getBindQr($admin_user = array())
    {
        global $_M;
        if ($admin_user['openid'] == '') {
            $rand = random(32) . uniqid();
            $expires_in = time() + 62; //1分钟过期
            $query = "UPDATE {$_M['table']['admin_table']} SET openid = '' ,access_token = '{$rand}' ,expires_in = '{$expires_in}' WHERE id = '{$admin_user['id']}' ";
            DB::query($query);

            $weixinapi = load::mod_class('weixin/weixinapi', 'new');
            $qrcode = $weixinapi->QRcode('adminbind&' . $rand);
            if ($qrcode && !$weixinapi->error) {
                $redata = array();
                $redata['qrurl'] = $qrcode;
                $redata['token'] = $rand;
                return $redata;
            }
            $this->error = $weixinapi->error;
            return false;
        }else{
            return false;
        }
    }

    /**
     * @param array $wxuser
     * @param string $code
     * @return bool
     */
    public function wxAdminBind($wxuser = array(), $code = '')
    {
        global $_M;
        if (!$code) {
            return false;
        }

        $date_now = time();
        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE access_token = '{$code}' AND openid = '' AND expires_in < $date_now";
        $admin_user = DB::get_one($query);
        if (!$admin_user) {//
            return false;
        }

        $query = "UPDATE  {$_M['table']['admin_table']} SET openid = '{$wxuser['openid']}' WHERE access_token = '{$code}' AND id = {$admin_user['id']}";
        DB::query($query);


        return true;
    }

    /**
     * @param string $token
     * @param string $admin_id
     * @return array|bool
     */
    public function checkWxBind($token = '', $admin_id = '')
    {
        global $_M;
        if (!$token || !$admin_id) {
            return false;
        }

        $date_now = time();
        $query = "SELECT * FROM  {$_M['table']['admin_table']} WHERE admin_id='{$admin_id}', access_token = '{$token}' AND openid !='' AND expires_in > $date_now";
        $admin_user = DB::get_one($query);
        if (!$admin_user) {
            return false;
        }

        if ($admin_user['openid']) {
            $weixinapi = load::mod_class('weixin/weixinapi', 'new');
            $date = date("Y-m-d H:i:s");
            $msg = "你的账号{$admin_user['admin_id']}已成功绑定微信
绑定时间：$date ";
            $weixinapi->customMessage($admin_user['openid'], $msg);  //微信通知
            return $admin_user;
        }
        return false;

    }
}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
