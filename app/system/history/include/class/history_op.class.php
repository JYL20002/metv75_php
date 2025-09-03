<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class history_op
{
    public function __construct()
    {
        global $_M;
        $this->history_database = load::mod_class('history/history_database', 'new');
        $this->history_plist_database = load::mod_class('history/history_plist_database', 'new');
        $this->history_relation_database = load::mod_class('history/history_relation_database', 'new');
    }

    /**
     * @param $hid
     * @return null
     */
    public function getHistoryByid($hid)
    {
        $history = $this->history_database->get_list_one_by_id($hid);
        if (!$history) return null;
        return $history;
    }

    /**
     * 保存历史记录
     * @param array $data
     * @param string $mod
     * @return mixed
     */
    public function record($data = array(), $mod = '')
    {
        $list = self::transful($data, $mod);

        $hid = $this->history_database->insert($list);
        return $hid;
    }

    /**
     * @param $data
     * @param $mod
     * @return bool
     */
    protected function transful($data, $mod)
    {
        global $_M;
        $data['aid'] = $data['id'];
        $data['module'] = $mod;
        $data['record_time'] = date("Y-m-d H:i:s");
        unset($data['id']);
//        $mod_type = "mod{$mod}";
        return $data;
    }

    /**
     * @param $aid
     * @param $module
     * @return void
     */
    public function delHistory($aid, $module)
    {
        $this->history_database->delByAid($aid, $module);
        self::delPlistHistory($aid, $module);
        self::delRelationsHistory($aid, $module);
    }


    /**
     * @param $aid
     * @param $module
     * @param $paraid
     * @param $hid
     * @return array
     */
    public function getPlistHistory($aid,$module,$paraid,$hid)
    {
        return $this->history_plist_database->getPlistListHistory($aid, $module, $paraid, $hid);
    }

    /**
     * 参数历史记录
     * @param $aid 关联内容id
     * @param $module 模块编号
     * @param $hid 历史记录id
     * @return void
     */
    public function recordPlistHistory($aid, $module, $hid)
    {
        $parameter_list_database = load::mod_class('parameter/parameter_list_database', 'new');
        $parameter_list_database->construct($module);
        $plist = $parameter_list_database->get_by_listid($aid);
        foreach ($plist as $p) {
            unset($plist['id']);
            $p['hid'] = $hid;
            $this->history_plist_database->insert($p);
        }
    }

    /**
     * @param $aid 关联内容id
     * @param $module 模块编号
     * @return void
     */
    public function delPlistHistory($aid, $module)
    {
        $this->history_plist_database->delByAid($aid, $module);
    }


    /**
     * @param $aid
     * @param $module
     * @return mixed
     */
    public function getRelationsHistory($aid, $module ,$hid)
    {
        global $_M;
        $_where = " lang='{$_M['lang']}' AND aid = '{$aid}' AND module = '{$module}' AND hid = '{$hid}'";
        $_order = '';
        return $this->history_relation_database->table_json_list($_where, $_order);
    }


    /**
     * @param $aid
     * @param $module
     * @param $hid
     * @return void
     */
    public function recordRelationsHistory($aid, $module, $hid)
    {
        global $_M;
        $relation_op = load::mod_class('relation/relation_op', 'new');
        $relations = $relation_op->getRelations($aid, $module);

        foreach ($relations as $key => $relation) {// $relation 模块|ID
            unset($relation['id']);
            $relation['hid'] = $hid;
            $this->history_relation_database->insert($relation);
        }
    }

    /**
     * @param $aid
     * @param $module
     * @return void
     */
    public function delRelationsHistory($aid, $module)
    {
        $this->history_relation_database->delByAid($aid, $module);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>

