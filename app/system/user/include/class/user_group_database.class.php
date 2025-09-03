<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class user_group_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['user_group']);
    }

    public function table_para()
    {
        return 'id|name|access|lang';
    }

    /**
     * @return array|null
     */
    public function userGroupList()
    {
        global $_M;
        $query = "SELECT * FROM {$this->table} WHERE lang='{$_M['lang']}' ORDER BY access ASC";
        $list = DB::get_all($query);

        foreach ($list as $key => $value) {
            $query = "SELECT price,buyok,recharge_price,rechargeok FROM  {$_M['table']['user_group_pay']} WHERE groupid='{$value['id']}' AND lang = '{$_M['lang']}'";
            $payment = DB::get_one($query);

            if (!$payment) {
                $payment = array(
                    'price' => '0',
                    'buyok' => '0',
                    'recharge_price' => '0',
                    'rechargeok' => '0',
                );
            }
            $list[$key]['payment'] = $payment;
        }

        return $list;

//        $query = "SELECT t1.*,t2.groupid,t2.price,t2.recharge_price,t2.buyok,t2.rechargeok  FROM {$this->table} AS t1 LEFT JOIN {$_M['table']['user_group_pay']} AS t2 ON t1.id=t2.groupid  WHERE t1.lang='{$_M['lang']}' order by t1.access ASC";
//        return $list;
    }

    /**
     * @param $groupid
     * @return array|null
     */
    public function getGroup($groupid)
    {
        global $_M;
        $query = "SELECT * FROM {$this->table} WHERE id='{$groupid}' AND lang='{$_M['lang']}'";
        $list = DB::get_one($query);
        return $list;
    }

    public function groupList()
    {
        global $_M;
        $query = "SELECT * FROM {$this->table} WHERE lang='{$_M['lang']}' order by access ASC";
        $list = DB::get_all($query);
        return $list;
    }

    /**
     * @param $id
     * @return int|mixed
     */
    public function delGroup($id)
    {
        global $_M;
        $query = "DELETE FROM {$this->table} WHERE id='{$id}'  ";
        DB::query($query);
        $query = "DELETE FROM {$_M['table']['user_group_pay']} WHERE groupid='{$id}' ";
        DB::query($query);
        return true;
    }

    /**
     * @param $group_id
     * @param $data
     * @return void
     */
    public function setGroupPay($group_id, $data)
    {
        global $_M;
        $sql = "SELECT id FROM {$_M['table']['user_group_pay']} WHERE groupid = '{$group_id}' AND lang = '{$_M['lang']}'";
        $has = DB::get_one($sql);

        if ($has) {
            $query = "UPDATE {$_M['table']['user_group_pay']} SET 
                    price = '{$data['price']}' ,
                    buyok = '{$data['buyok']}',
                    recharge_price = '{$data['recharge_price']}',
                    rechargeok = '{$data['rechargeok']}'
                    WHERE groupid = '{$group_id}' AND 
                    lang = '{$_M['lang']}'";
            $res = DB::query($query);
        } else {
            $query = "INSERT INTO {$_M['table']['user_group_pay']} SET 
                    groupid = '{$group_id}' ,
                    price = '{$data['price']}' ,
                    buyok = '{$data['buyok']}',
                    recharge_price = '{$data['recharge_price']}',
                    rechargeok = '{$data['rechargeok']}',
                    lang = '{$_M['lang']}'";
            $res = DB::query($query);
        }
        return $res;
    }

}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
