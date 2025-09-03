<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class mituoMember
{
    public $power;

    public function __construct()
    {
        global $_M;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function login($data)
    {
        global $_M;
        $data['action'] = 'login';
        $result = api_curl($_M['config']['met_api'], $data);
        $res = json_decode($result, true);

        if ($res['status'] == 200 || $res['code'] == 0) {
            $user_key = $res['data']['user_key'];
            if ($user_key) {
                $query = "UPDATE {$_M['table']['config']} SET value = '{$user_key}' WHERE name = 'met_secret_key'";
                DB::query($query);
            }

            return $res;
        } else {
            error($res['msg']);
        }
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        global $_M;
        if (!$_M['config']['met_secret_key']) {
            error('用户未登录');
        }
        $data['action'] = 'userInfo';
        $result = api_curl($_M['config']['met_api'], $data);
        $res = json_decode($result, true);
        if ($res['status'] == 200) {
            return $res;
        } else {
            error($res['msg']);
        }
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getUserInfo($type = '')
    {
        global $_M;
        if (!$_M['config']['met_secret_key']) {
            error('用户未登录');
        }
        $data['action'] = 'userInfo';
        if ($type == 'logout') {
            $data['logout'] = 1;
        }
        $result = api_curl($_M['config']['met_api'], $data);
        $res = json_decode($result, true);
        if ($res['status'] == 200 ? $type == 'logout' : 1) {
            $query = "UPDATE {$_M['table']['config']} SET value = '' WHERE name = 'met_secret_key'";
            DB::query($query);
        }
        if ($res['status'] == 200) {
            return $res;
        } else {
            error($res['msg']);
        }
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
