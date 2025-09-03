<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * parameter标签类
 */

class parameter_label
{
    public $lang;

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->parameter_database = load::mod_class('parameter/parameter_database', 'new');
    }

    /**
     * @param string $module
     * @param string $class1
     * @param string $class2
     * @param string $class3
     * @param null $type
     * @return mixed
     */
    public function get_parameter($module = '', $class1 = '', $class2 = '', $class3 = '', $type = null)
    {
        return $this->parameter_database->get_parameter($module, $class1, $class2, $class3, $type);
    }

    /**
     * 获取字段提交表单，前台留言，反馈，招聘模块使用
     * @param string $module
     * @param array $parameter
     * @param array $column_config
     * @param null $simplify
     * @return mixed
     */
    public function build_parameter_form($module = '', $parameter = array(), $simplify = true)
    {
        global $_M;
        $userclass = load::sys_class('user', 'new');

        $parameter_list = array();
        foreach ($parameter as $key => $row) {
            $list = $this->parameter_database->get_para_values($module, $row['id']);
            $row['para_list'] = $list;
            //属性权限检测
            $power = $userclass->check_power($row['access']);
            if ($power > 0) {
                $parameter_list[] = $row;
            }
        }
        $paras = load::mod_class('parameter/parameter_handle', 'new')->para_handle_formation($module, $parameter_list, $simplify);
        return $paras;
    }

    /**
     * @param $module  模块编号
     * @param $id      内容id
     * @param $class1
     * @param $class2
     * @param $class3
     * @param int $type
     * @return array
     */
    public function get_parameter_contents($module, $id, $class1, $class2, $class3, $type = 0, $one = array())
    {
        global $_M;
        $parameter = $this->parameter_database->get_parameter($module, $class1, $class2, $class3);
        $list = $this->parameter_database->get_list($id, $module);
        $userclass = load::sys_class('user', 'new');

        $relist = array();
        foreach ($parameter as $key => $val) {
            if ($type && $val['type'] != $type) {
                continue;
            }

            if (!$type && $val['type'] == 10) {
                continue;
            }

            //参数权限控制
            if ($_M['config']['access_type'] == 2) {
                $power = $userclass->check_power($val['access']);
                if ($power < 0) {
                    continue;
                }
            }

            if (
                ($val['class1'] == 0) ||
                ($val['class1'] == $class1 && $val['class2'] == 0) ||
                ($val['class1'] == $class1 && $val['class2'] == $class2 && $val['class3'] == 0) ||
                ($val['class1'] == $class1 && $val['class2'] == $class2 && $val['class3'] == $class3)
            ) {
                if ($val['type'] == 5) {//附件
                    if ($list[$val['id']]['info']) {
                        $url = load::sys_class('handle', 'new')->url_transform($list[$val['id']]['info']);
                        $value = "<a target='_blank' href='{$url}'>{$_M['word']['downloadtext1']}</a>";
                    } else {
                        $value = '';
                    }
                } elseif (in_array($val['type'], array(2, 4, 6))) {//单选、多选、下拉
                    $value = '';
                    $info = $list[$val['id']]['info'];
                    $para_ids = array();
                    if (strstr($info, ',')) {
                        $para_ids = explode(',', $info);
                    } else {
                        $para_ids[] = $info;
                    }
                    foreach ($para_ids as $para_id) {
                        $para_value = $this->parameter_database->get_parameter_value_by_id($para_id);
                        if ($para_value) {
                            $value .= "," . $para_value;
                        }
                    }
                    $value = trim($value, ',');
                } else {
                    $value = $list[$val['id']]['info'];
                }

                $value = trim($value, ',');
                $para = array();
                $para['id'] = $val['id'];
                $para['name'] = $val['name'];
                if ($val['type'] == 10) {
                    $para['value'] = $val['access'] ? ($value != '' ? $userclass->check_power_link($value, $val['access']) : '') : $value;
                } else {
                    $para['value'] = $val['access'] ? ($value != '' ? $userclass->check_power_script($value, $val['access']) : '') : $value;
                }

                $relist[] = $para;
            }
        }

        if ($type == 10) {
            $inquiry = self::inquiry($one);
            if ($inquiry) {
                $relist[] = $inquiry;
            }
        }

        return $relist;
    }

    /**
     * 在线询价
     * @param array $one
     * @return array
     */
    protected function inquiry($one = array())
    {
        global $_M;
        $feedback = load::mod_class('feedback/feedback_database', 'new');
        //在线询价
        $inquiry = $feedback->get_inquiry();
        if ($inquiry) {
            $fd_column = load::mod_class('column/column_database', 'new')->get_column_by_id($inquiry['columnid']);
            $one_title = isset($one['title']) ? urlencode($one['title']) : '';

            $para = array();
            $para['id'] = 0;
            $para['name'] = $_M['word']['feedbackinquiry'];
            $para['value'] = $_M['url']['web_site'] . "{$fd_column['foldername']}/index.php?fdtitle={$one_title}&lang={$_M['lang']}";
            //$relist[] = $para;
        }
        return $para;
    }

    /**
     * 获取字段搜索sql语句
     * @param  string $module 模块类型
     * @param  string /array  $info    被搜索信息
     * @return string                 sql语句
     */
    public function get_search_list_sql($module, $precision, $info)
    {
        global $_M;
        if (!is_array($info)) {
            if ($precision) {
                $sql = "SELECT listid FROM {$_M['table']['plist']} WHERE info = '{$info}'";
            } else {
                $mod = load::sys_class('handle', 'new')->file_to_mod($module);
                $sql = "SELECT listid FROM {$_M['table']['plist']} WHERE info like '%{$info}%' AND module = '{$mod}'";
            }
        } else {
            $listid = $list = array();
            $para_num = 0;
            foreach ($info as $key => $val) {
                if (!$val['info']) {
                    continue;
                }
                $p_query = "SELECT id,type,name FROM {$_M['table']['parameter']} WHERE id='{$val['id']}'";
                $parameter = DB::get_one($p_query);
                if ($parameter['type'] == 4) {
                    $query = "SELECT listid FROM {$_M['table']['plist']} WHERE paraid='{$val['id']}' AND (info = '{$val['info']}' OR info LIKE '%,{$val['info']}' OR info LIKE '%,{$val['info']},%' OR info LIKE '{$val['info']},%')";
                } else {
                    $query = "SELECT listid FROM {$_M['table']['plist']} WHERE paraid='{$val['id']}' AND info = '{$val['info']}'";
                }
                $para_num++;

                $res = DB::get_all($query);
                foreach ($res as $v) {
                    array_push($listid, $v['listid']);
                }
            }

            if ($para_num == 0) {
                return 'all';
            } else {
                $listid = array_count_values($listid);
                foreach ($listid as $key => $val) {
                    if ($val >= $para_num) {
                        $list[] = $key;
                    }
                }
                if (is_array($list)) {
                    return implode(',', $list);
                }
                return '';
            }
        }

        return $sql;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
