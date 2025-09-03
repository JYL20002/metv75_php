<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

class recycle extends admin
{
    public $handle;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->handle = load::sys_class('handle', 'new');
    }

    public function dojson_list()
    {
        global $_M;
        $module = $_M['form']['module'];
        $search = $_M['form']['title'];
        $lang = $_M['lang'];

        $table = load::sys_class('tabledata', 'new'); //加载表格数据获取类
        $fields = 'id,title,class1,class2,class3,updatetime,recycle';
        $modules = array('news', 'product', 'download', 'img');
        $searchsql = $search ? $searchsql = "AND title LIKE '%{$search}%'" : $searchsql = '';
        $where = "recycle > 0 AND lang='{$lang}' {$searchsql}";
        $order = 'updatetime desc,id desc';

        if (in_array($module, $modules)) {
            $mod_num = load::sys_class('handle', 'new')->file_to_mod($module);
            $data = $table->getdata($_M['table'][$module], "{$fields},'{$mod_num}' AS mod_num ", $where, $order); //获取数据
        }else{
            $query = "SELECT {$fields},'2' AS mod_num  FROM {$_M['table']['news']} WHERE $where ";
            $query .= " UNION SELECT {$fields},'3' AS mod_num FROM {$_M['table']['product']} WHERE $where";
            $query .= " UNION SELECT {$fields},'4' AS mod_num FROM {$_M['table']['download']} WHERE $where";
            $query .= " UNION SELECT {$fields},'5' AS mod_num FROM {$_M['table']['img']} WHERE $where";
            $data = $table->getdata($_M['table'][$module], '*', $where, $order, $query); //获取数据
        }

        $query = "SELECT * FROM {$_M['table']['column']} where lang ='{$_M['lang']}'";
        $c_list = DB::get_all($query);

        foreach ($c_list as $key => $value) {
            $column_list[$value['id']] = $value;
        }

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if ($val['class3']) {
                    $column_name = $column_list[$val['class3']]['name'];
                }elseif ($val['class2']) {
                    $column_name = $column_list[$val['class2']]['name'];
                } elseif ($val['class1']) {
                    $column_name = $column_list[$val['class1']]['name'];
                }

                $list = array();
                $list['id'] = "{$val['id']}-{$val['mod_num']}";
                $list['title'] = $val['title'];
                $list['updatetime'] = $val['updatetime'];
                $list['column_name'] = $column_name;
                $list['del_url'] = "{$_M['url']['own_form']}a=dolistsave&allid={$list['id']}&submit_type=del";
                $list['recyclere_url'] = "{$_M['url']['own_form']}a=dolistsave&allid={$list['id']}&submit_type=restore";
                $rarray[] = $list;
            }
            $table->rdata($rarray); //返回数据
            die();
        } else {
            $table->rdata(''); //返回数据;
            die();
        }
    }

    /**
     * 列表操作.
     */
    public function dolistsave()
    {
        global $_M;
        $submit_type = $_M['form']['submit_type'];
        if (isset($_M['form']['allid'])) {
            $item = explode(',', $_M['form']['allid']);
            foreach ($item as $val) {
                $row = explode('-', $val);
                list($pid, $module) = $row;

                switch (strtolower($submit_type)) {
                    case "restore":
                        $res = $this->restore($pid, $module);
                        if (!$res) {
                            $this->error('还原失败, 栏目已删除');
                        }
                        break;
                    case 'del':
                        $this->delete($pid, $module);

                        $para_op = load::mod_class('parameter/parameter_op', 'new');
                        $para_op->delPlist($pid, $module);

                        $relation_op = load::mod_class('relation/relation_op', 'new');
                        $relation_op->delRelations($pid, $module);
                        break;
                    default:
                        break;
                }
            }
        }

        $this->success('',$_M['word']['jsok']);
    }

    /**
     * 从回收站删除
     * @param string $id
     * @param string $module
     */
    public function delete($id = '', $module = '')
    {
        global $_M;
        $module_name = $this->handle->mod_to_name($module);
        if ($module_name) {
            $query = "DELETE  FROM {$_M['table'][$module_name]} WHERE `id` = '{$id}' and `lang` = '{$_M['lang']}'";
            DB::get_all($query);
        }
        return;
    }

    /**
     * 从回收站恢复
     * @param string $id
     * @param string $module
     */
    public function restore($id = '', $module = '')
    {
        global $_M;
        $module_name = $this->handle->mod_to_name($module);
        if (!$module_name) {
            return false;
        }
            $query = "SELECT id,class1,class2,class3 FROM {$_M['table'][$module_name]} WHERE `id` = '{$id}' and `lang` = '{$_M['lang']}'";
            $one = DB::get_one($query);
            if (!$one) {
                return false;
            }

            extract($one);
            $classonw = $class3 ?: ($class2 ?: ($class1 ?: 0));
            if (!$classonw) {
                return false;
            }

            $query = "SELECT id FROM {$_M['table']['column']} WHERE id = '{$classonw}'";
            $c = DB::get_one($query);
            if (!$c) {
                return false;
            }

            $query = "UPDATE {$_M['table'][$module_name]} SET `recycle` = 0 WHERE `id` = '{$id}' and `lang` = '{$_M['lang']}'";
            DB::query($query);
            return true;

    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
