<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class config_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['config']);
    }

    /**
     * @param string $name
     * @param string $lang
     * @return bool
     */
    public function get_value($name = '', $lang = '')
    {
        global $_M;
        if (!$lang) {
            $lang = $_M['lang'];
        }

        $query = "SELECT * FROM {$_M['table']['config']} WHERE name = '{$name}' AND lang = '{$lang}'";
        $config = DB::get_one($query);
        if ($config) {
            return $config['value'];
        } else {
            return false;
        }
    }

    /**
     * @param string $id
     * @return array|void
     */
    public function get_value_by_columnid($id = '')
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['config']} WHERE columnid = '{$id}'";
        return $config = DB::get_all($query);
    }

    /**
     * 通过栏目和名称取数据
     * @param string $columnid
     * @param string $name
     * @return mixed
     */
    public function get_value_by_classid($columnid = '', $name = '')
    {
        global $_M;
        $query = "SELECT * FROM {$_M['table']['config']} WHERE columnid = '{$columnid}' AND name = '{$name}'";
        $config = DB::get_one($query);
        if ($config) {
            return $config['value'];
        }
    }

    /**
     * @param string $columnid
     * @param string $name
     * @param string $value
     * @param null $lang
     * @return bool|int|mixed
     */
    public function update_by_classid($columnid = '', $name = '', $value = '', $lang = null)
    {
        global $_M;
        if (!$columnid || !$name) return false;
        $set_lang = $lang ?: $_M['lang'];
        $query = "SELECT * FROM  {$_M['table']['config']} WHERE `columnid` = '{$columnid}' AND `name` = '{$name}' AND `lang` = '{$set_lang}'";
        $one = DB::get_one($query);
        if ($one) {
            $query = "UPDATE  {$_M['table']['config']} SET `value`='{$value}' WHERE `columnid` = '{$columnid}' AND `name` = '{$name}' AND `lang` = '{$set_lang}'";
            $res = DB::query($query);
        }else{
            $query = "INSERT INTO {$_M['table']['config']} (`columnid` ,`name` ,`value`, `lang`) VALUES ('{$columnid}', '{$name}', '{$value}', '{$set_lang}')";
            $res = DB::query($query);
        }
        return $res;
    }

    /**
     * @param string $id
     * @return int|mixed
     */
    public function del_value_by_columnid($id = '')
    {
        global $_M;
        $query = "DELETE FROM {$_M['table']['config']} WHERE columnid = '{$id}' ";
        return DB::query($query);
    }

    /**
     * @param string $id
     * @return int|mixed
     */
    public function del_value_by_flashid($id = '')
    {
        global $_M;
        $query = "DELETE FROM {$_M['table']['config']} WHERE flashid = '{$id}' ";
        return DB::query($query);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
