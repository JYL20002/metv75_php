<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('web');

class smscode extends web {

    public function __construct() {
        global $_M;
        parent::__construct();
        $this->verifier = load::sys_class('verifier', 'new');
    }

    public function doSmsCode()
    {
        global $_M;
        $arr = array('tel', 'smsRandom');
        foreach ($arr as $name) {
            if (!isset($_M['form'][$name]) || $_M['form'][$name] == '') {
                $this->error('参数错误');
            }
        }
        $tel = $_M['form']['tel'];
        $smsRandom = $_M['form']['smsRandom'];

        if (!load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random'])) {
            $this->error($_M['word']['membercode']);
        }

        $res = $this->verifier->smsCode($tel, $smsRandom);
        if (!$res){
            $this->error($this->verifier->error);
        }
        $this->success($res, $_M['word']['getOK']);

    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
