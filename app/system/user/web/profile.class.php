<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('user/web/class/userweb');

class profile extends userweb
{
    protected $paraclass;
    protected $paralist;
    protected $no;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->user_database = load::mod_class('user/user_database', 'new');
        $this->user_handle = load::mod_class('user/user_handle', 'new');
        $this->paraclass = load::sys_class('para', 'new');

        $this->paralist = $this->paraclass->get_para_list(10, $this->input['classnow']);
        $this->sys_group = load::mod_class('user/sys_group', 'new');
        $this->paygroup_list = $this->sys_group->get_paygroup_list_buyok();

        $this->user = $this->get_login_user_info();
    }

    /**
     * 基本信息
     * @return void
     */
    public function doindex()
    {
        global $_M;
        $_M['config']['own_order'] = 1;
        if (!$_M['user']['valid']) {
            $valid = $_M['config']['met_member_vecan'] == 1 ? 'valid_email' : 'valid_admin';
            $this->view('app/' . $valid, $this->input);
        } else {
            $_M['paralist'] = $this->paralist;
            $_M['paraclass'] = $this->paraclass;

            $paygroupnow = $this->sys_group->get_paygroup_by_id($_M['user']['groupid']);
            foreach ($this->paygroup_list as $pgroup) {
                if ($pgroup['price'] > $paygroupnow['price']) {
                    $groupshow[] = $pgroup;
                }
            }
            $this->input['groupshow'] = $groupshow;
            $this->view('app/profile_index', $this->input);
        }
    }

    /**
     * 保存会员信息
     * @return void
     */
    public function doinfosave()
    {
        global $_M;
        $infos = $this->paraclass->form_para($_M['form'], 10, $this->input['classnow']);
        $this->paraclass->update_para($_M['user']['id'], $infos, 10);
        $this->userclass->modify_head($_M['user']['id'], $_M['form']['head']);
        okinfo($_M['url']['profile'], $_M['word']['modifysuc']);
    }

    /**
     * 帐号安全
     * @return void
     */
    public function dosafety()
    {
        global $_M;
        $_M['config']['own_order'] = 2;
        if ($_M['user']['email']) {
            $_M['profile_safety']['emailtxt'] = $_M['user']['email'];
            $_M['profile_safety']['emailbut'] = $_M['word']['modify'];
            $_M['profile_safety']['emailclass'] = 'emailedit';
        } else {
            $_M['profile_safety']['emailtxt'] = $_M['word']['notbound'];
            $_M['profile_safety']['emailbut'] = $_M['word']['binding'];
            $_M['profile_safety']['emailclass'] = 'emailadd';
        }

        if ($_M['user']['tel']) {
            $_M['profile_safety']['teltxt'] = $_M['user']['tel'];
            $_M['profile_safety']['telbut'] = $_M['word']['modify'];
            $_M['profile_safety']['telclass'] = 'teledit';
        } else {
            $_M['profile_safety']['teltxt'] = $_M['word']['notbound'];
            $_M['profile_safety']['telbut'] = $_M['word']['binding'];
            $_M['profile_safety']['telclass'] = 'teladd';
        }

        if ($_M['user']['idvalid']) {
            $_M['profile_safety']['idvalitxt'] = $_M['word']['authen'];
            $_M['profile_safety']['idvalibut'] = $_M['word']['memberDetail'];
            $_M['profile_safety']['idvaliclass'] = 'idvalview';
            $_M['user']['realidinfo'] = $this->userclass->getRealIdInfo($_M['user']);
        } else {
            $_M['profile_safety']['idvalitxt'] = $_M['word']['notauthen'];
            $_M['profile_safety']['idvalibut'] = $_M['word']['binding'];
            $_M['profile_safety']['idvaliclass'] = 'idvaliadd';
        }

        if ($_M['config']['met_member_vecan'] == 1 && $_M['user']['email'] && $_M['user']['email'] == $_M['user']['username']) {
            $_M['profile_safety']['emailbut'] = $_M['word']['accnotmodify'];
            $_M['profile_safety']['disabled'] = 'disabled';
        }

        if ($_M['config']['met_weixin_open'] == 1) {
            $other_user = load::mod_class('user/web/class/other', 'new')->getOtherUserByUid($_M['user']['id'], 'weixin');
            if ($other_user['openid'] != '' && $other_user['openid'] != '#') {
                //已绑定
                $_M['profile_safety']['weixintxt'] = $_M['word']['bound'];
                $_M['profile_safety']['weixinbut'] = $_M['word']['unbind'];
                $_M['profile_safety']['weixinclass'] = 'weixinunbind';

            }else{
                //未绑定
                $_M['profile_safety']['weixintxt'] = $_M['word']['notbound'];
                $_M['profile_safety']['weixinbut'] = $_M['word']['binding'];
                $_M['profile_safety']['weixinclass'] = 'weixinadd';
            }
        }
        $this->input['user'] = $_M['user'];
        $this->view('app/profile_safety', $this->input);
    }

    /**
     * 密码修改
     * @return void
     */
    public function dopasssave()
    {
        global $_M;
        $oldpassword = $_M['form']['oldpassword'];
        $password = $_M['form']['password'];

        //社会化登陆账号
        if ($this->user['source']) {
            $res = $this->user_handle->setUserPassword($this->user, $password);
            if ($res) {
                $this->error($_M['word']['modifypasswordsuc']);
            } else {
                $this->error($_M['word']['opfail']);
            }
        }

        $oldpassword = authcode($oldpassword);
        $password = authcode($password);
        if (!empty($password) && !empty($oldpassword) && md5($oldpassword) == $this->user['password']) {
            $res = $this->user_handle->setUserPassword($this->user, $password);
            if ($res) $this->success('', $_M['word']['modifypasswordsuc']);
            $this->error($_M['word']['opfail']);
        }
        $this->error($_M['word']['lodpasswordfail']);
    }

    /**
     * 绑定邮箱
     * @return void
     */
    public function doBindEmail()
    {
        global $_M;
        $email = $_M['form']['email'] ?: '';
        if(empty($email) || !is_email($email)) $this->error($_M['word']['dataerror']);
        if ($this->user_database->hasOthers($this->user, $email)) {//邮箱已被占用
            $this->error($_M['word']['emailhave']);
        }
        $valid = load::mod_class('user/web/class/valid', 'new');
        $res = $valid->sendEmail($_M['form']['email'], 'emailbind');
        if (!$res) $this->error($_M['word']['emailfail']);
        $this->success('',$_M['word']['emailsuclink']);
    }

    /**
     * 解绑邮箱
     * @return void
     */
    public function doUnbindemail()
    {
        global $_M;
        $password = $_M['form']['password'];
        $password = authcode($password);

        if (!empty($password) && md5($password) === $this->user['password']) {
            $res = $this->user_database->setUserAttr($this->user,'email','');
            $this->success('',$_M['word']['jsok']);
        }
        $this->error($_M['word']['lodpasswordfail']);
    }

    /**
     * 邮箱绑定确认
     * @return void
     */
    public function doCheckBindeEmail()
    {
        global $_M;
        $p = $_M['form']['p'] ?: '';
        if (!$this->user) okinfo($_M['url']['login']);

        $auth = load::sys_class('auth', 'new');
        $email = $auth->decode($p);
        $email = sqlinsert($email);
        if (!$email) okinfo($_M['url']['profile_safety'], $_M['word']['emailvildtips2']);
        $res = $this->user_database->setUserAttr($this->user, 'email', $email);

        if ($res) {
            okinfo($_M['url']['profile_safety'], $_M['word']['bindingok']);
        } else {
            okinfo($_M['url']['profile_safety'], $_M['word']['opfail']);
        }
    }

    /**
     * 未激活账户重发邮件验证
     * @return void
     */
    public function dovalid_email()
    {
        global $_M;
        $valid = load::mod_class('user/web/class/valid', 'new');
        if ($valid->sendEmail($_M['user']['username'], 'register')) {
            $this->success('', $_M['word']['emailsuc']);
        } else {
            $this->error($_M['word']['emailfail']);
        }
    }

    /**
     * 发送手机验证码
     * @return void
     */
    public function doBindTelSmsCode()
    {
        global $_M;
        $tel = $_M['form']['tel'];

        $res = load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random']);
        if (!$res) $this->error($_M['word']['membercode']);

        if ($this->user_database->hasOthers($this->user, $tel)) {//手机号码已被占用
            $this->error($_M['word']['teluse']);//号码已绑定其他账号
        }

        //发送手机验证码
        $valid = load::mod_class('user/web/class/valid', 'new');
        if ($valid->smsCode($tel)) {
            $this->success('ok');
        } else {
            $this->error($_M['word']['Sendfrequent'], $valid->error);
        }
    }

    /**
     * 绑定手机
     * @return void
     */
    public function doBindTel()
    {
        global $_M;
        $tel = $_M['form']['tel'] ?: '';
        $phonecode = $_M['form']['phonecode'] ?: '';

        $valid = load::mod_class('user/web/class/valid', 'new');
        $res = $valid->checkSmsCoode($phonecode, $tel);
        if (!$res) $this->error($valid->errormsg);

        if ($this->user_database->hasOthers($this->user, $tel)) {//手机号码已被占用
            $this->error($_M['word']['teluse']);//号码已绑定其他账号
        }

        $res = $this->user_database->setUserAttr($this->user,'tel',$tel);
        if (!$res) $this->error($_M['word']['opfail']);
        $this->success('',$_M['word']['bindingok']);
    }

    /**
     * 解绑手机
     * @return void
     */
    public function doUnbindTel()
    {
        global $_M;
        $password = $_M['form']['password'];
        $password = authcode($password);

        if (!empty($password) && md5($password) === $this->user['password']) {
            $this->user_database->setUserAttr($this->user,'tel','');
            $this->success('',$_M['word']['jsok']);
        }
        $this->error($_M['word']['lodpasswordfail']);
    }



    /**
     * 用户实名认证
     */
    public function dosafety_idvalid()
    {
        global $_M;
        if (!load::sys_class('pin', 'new')->check_pin($_M['form']['code'] ,$_M['form']['random'])) {
            okinfo($_M['url']['profile_safety'], $_M['word']['membercode']);
            die();
        }
        $idvalid = load::mod_class('user/include/class/user_idvalid', 'new');
        $result = $idvalid->idvalidate();
        if (!$result) {
            okinfo($_M['url']['profile_safety'], $_M['word']['idvalidfailed']);
            die();
        }

        $info = $_M['form']['realname'] . "|" . $_M['form']['idcode'] . "|" . $_M['form']['phone'];
        if ($this->userclass->editor_uesr_idvalid($_M['user']['id'], $info)) {
            okinfo($_M['url']['profile_safety'], $_M['word']['idvalidok']);
            die();
        }
    }

    /**
     * 绑定社会化账号
     */
    public function doOtherBind()
    {
        global $_M;
        $type = $_M['form']['type'];
        switch ($type) {
            case 'weixin':
                $rand = random(16).uniqid();
                $weixin_party = load::mod_class('user/web/class/weixin_party', 'new');
                $qrcode = $weixin_party->WXBind($_M['user'],$rand);
                $error = $weixin_party->error;
                break;
            default:
                break;
        }

        if ($error) {
            $this->error($error);
        }else{
            $redata = array();
            $redata['img'] = $qrcode;
            $redata['code'] = $rand;
            $this->success($redata);
        }
    }

    /**
     * 解绑社会化账号
     */
    public function doUnbind()
    {
        global $_M;
        $type = $_M['form']['type'];
        switch ($type) {
            case 'weixin':
                $weixin_party = load::mod_class('user/web/class/weixin_party', 'new');
                $weixin_party->WXUnbind($_M['user']['id']);
                break;
            default:
                break;
        }

        $redata = array();
        $this->success($redata,$_M['word']['jsok']);
    }

    /**
     * 检测绑定状态
     */
    public function docheckBind()
    {
        global $_M;
        $code = $_M['form']['code'];
        $type = $_M['form']['type'];

        switch ($type) {
            case 'weixin':
                $weixin_party = load::mod_class('user/web/class/weixin_party', 'new');
                $other_user = $weixin_party->checkWXBind($code);
                if ($other_user) {
                    $redata = array();
                    $this->success($redata,'微信账号绑定成功');
                }
                break;
            default:
                break;
        }

        $redata = array();
        $this->error($redata);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>