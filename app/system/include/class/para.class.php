<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 仅供会员模块使用
 * @param string $lang      操作的语言
 * @param string $table     模块数据表
 */

class para
{
    protected $lang;
    protected $table;

    public function __construct()
    {
        global $_M;
        $this->lang = $_M['lang'];
    }

    public function table($module = '')
    {
        global $_M;
        switch ($module) {
            case 10:
                $table = $_M['table']['user_list'];
                break;
            default :
                $table = $_M['table']['user_list'];
                break;
        }
        $this->table = $table;
        return $this->table;
    }

    public function form_para($form, $module, $class1 = '', $class2 = '', $class3 = '')
    {
        $parameters = $this->get_para_list($module, $class1, $class2, $class3);
        foreach ($parameters as $parameter) {
            if ($parameter['type'] == 7) {
                $info[$parameter['id']] = $form["info_{$parameter['id']}_1"] . '-' . $form["info_{$parameter['id']}_2"];
                if ($form["info_{$parameter['id']}_3"]) $info[$parameter['id']] .= '-' . $form["info_{$parameter['id']}_3"];
            } else {
                $info[$parameter['id']] = $form['info_' . $parameter['id']];
            }
        }
        return $info;
    }

    /**
     * 前台表单渲染
     * @param $listid
     * @param $module
     * @param int $wr_oks
     * @param int $access
     * @param string $class1
     * @param string $class2
     * @param string $class3
     */
    public function parawebtem($listid, $module, $wr_oks = 0, $access = 0, $class1 = '', $class2 = '', $class3 = '')
    {
        global $_M;
        if ($listid) {
            $para = $this->get_para($listid, $module, $class1, $class2, $class3);  //用户属性
            foreach ($para as $key => $value) {
                $para[$key] = htmlspecialchars($value);
            }
        }

        $parameters = $this->get_para_list($module, $class1, $class2, $class3);
        $paralist = $this->paraaccess($parameters, $access);

        require PATH_SYS_TEM . 'web/paratype.php';
    }

    /**
     * @param $paralist
     * @param int $access
     * @return array
     */
    public function paraaccess($paralist, $access = 0)
    {
        // 属性访问权限
        foreach ($paralist as $key => $val) {
            if ($access==$val['access'] || !$val['access']) {
                $paralist_bace[] = $val;
            }
        }
        return $paralist_bace;
    }

    /**
     * @param $listid
     * @param $module
     * @param $class1
     * @param $class2
     * @param $class3
     * @return mixed
     */
    public function get_para($listid, $module, $class1, $class2, $class3)
    {
        global $_M;
        $parameters = $this->get_para_list($module, $class1, $class2, $class3);

        $table = $this->table($module);
        foreach ($parameters as $val) {
            $sql = "SELECT * FROM {$table} WHERE listid='{$listid}' AND paraid='{$val['id']}' AND lang = '{$this->lang}'";
            $para = DB::get_one($sql);

            if ($val['type'] == 7) {
                $para7 = explode("-", $para['info']);
                $list['info_' . $val['id'] . '_1'] = $para7[0];
                $list['info_' . $val['id'] . '_2'] = $para7[1];
                if ($para7[2]) $list['info_' . $val['id'] . '_3'] = $para7[2];
            }
            if ($val['type'] == 4) {
                $para4 = explode("#@met@#", $para['info']);
                foreach ($para4 as $k => $v) {
                    $list["info_{$val['id']}_{$k}"] = $v;
                }
                continue;
            }
            $list['info_' . $val['id']] = $para['info'];
            if (!$para) {
                $infos[$val['id']] = '';
            }
        }
        if ($infos) $this->insert_para($listid, $infos, $module);

        return $list;
    }

    /**
     * @param string $module
     * @param string $class1
     * @param string $class2
     * @param string $class3
     * @return array
     */
    public function get_para_list($module = '', $class1 = '', $class2 = '', $class3 = '')
    {
        global $_M;
        //获取指点栏目熟悉
        $where = "WHERE lang='{$_M['lang']}' AND module = '{$module}' ";

        if ($class1 || $class2 || $class3) {
            $_class = '';
            if (intval($class1)) {
                $_class .= " OR ( class1 = '{$class1}' AND class2 = 0 AND class3 = 0 ) ";
            }
            if (intval($class2)) {
                $_class .= " OR ( class1 = '{$class1}' AND class2 = '{$class2}' AND class3 = 0 ) ";
            }
            if (intval($class3)) {
                $_class .= " OR ( class1 = '{$class1}' AND class2 = '{$class2}' AND class3 = '{$class3}' ) ";
            }
            $where .= " AND (class1 = 0  {$_class} )";
        }
        $query = "SELECT * FROM {$_M['table']['parameter']} {$where} ORDER BY no_order ASC, id DESC ";
        $parameters = DB::get_all($query);


        foreach ($parameters as $key => $parameter) {
            if ($parameter['options']) {
                $options = json_decode($parameter['options'], true);
                $_order = arrayColumn($options, 'order');
                array_multisort($_order, SORT_ASC, $options); //选项排序
                $parameter['list'] = $options;
            }
            $parameters[$key] = $parameter;
        }

        return $parameters;
    }

    /**
     * @param $listid
     * @param $infos
     * @param $module
     */
    public function insert_para($listid, $infos, $module)
    {
        global $_M;
        $table = $this->table($module);
        foreach ($infos as $paraid => $val) {
            if ($val) {
                $query = "INSERT INTO {$table} SET
                        listid  = '{$listid}',
                        paraid  = '{$paraid}',
                        info    = '{$val}',
                        lang    = '{$this->lang}'
                    ";
                DB::query($query);
            }
        }
    }

    /**
     * @param $listid
     * @param $infos
     * @param $module
     */
    public function update_para($listid, $infos, $module)
    {
        global $_M;
        $table = $this->table($module);
        foreach ($infos as $paraid => $val) {
            if (isset($val)) {
                $query = "SELECT * FROM {$table} WHERE listid = '{$listid}' AND paraid = '{$paraid}' AND lang = '{$this->lang}'";
                $para = DB::get_one($query);

                if (!$para) {
                    $query = "INSERT INTO {$table} SET
                        listid  = '{$listid}',
                        paraid  = '{$paraid}',
                        info    = '{$val}',
                        lang    = '{$this->lang}'
                    ";
                    DB::query($query);
                } else{
                    $query = "UPDATE {$table}
                    SET info = '{$val}'
                    WHERE listid = '{$listid}'
                    AND paraid = '{$paraid}'
                    AND lang = '{$this->lang}'";
                    DB::query($query);
                }
            }
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>