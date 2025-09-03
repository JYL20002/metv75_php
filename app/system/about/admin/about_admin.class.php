<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/admin/base_admin');

class about_admin extends base_admin
{
    public $moduleclass;
    public $module;
    public $database;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->module = 1;
        $this->database = load::mod_class('about/about_database', 'new');
        $this->tabledata = load::sys_class('tabledata', 'new');
    }

    /**
     * @return mixed
     */
    public function doeditor()
    {
        global $_M;
        $id = $_M['form']['id'];
        $about = $this->database->get_list_one_by_id($id);
        $redata['list'] = $about;
        return $redata;
    }

    /**
     * 修改保存页面
     * @param  array $list 插入的数组
     * @return number  插入后的数据ID
     */
    public function doeditorsave()
    {
        global $_M;
        $cid = $_M['form']['id'];
        if ($this->update_list($_M['form'], $cid)) {
            buffer::clearColumn($_M['lang']);

            if ($_M['config']['met_webhtm']) {
                $redata['html_res'] = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n=html&c=html&a=doCreatePage&type=content&module=1&class1={$cid}&index=1";
            }
            $this->success($redata, $_M['word']['jsok']);
        } else {
            $this->error($_M['word']['dataerror']);
        }
    }

    /**
     * 保存修改
     * @param  array $list 修改的数组
     * @return bool                     修改是否成功
     */
    public function update_list($list = array(), $id = '')
    {
        global $_M;
        //图片处理 缩略图 水印图
        if ($_M['config']['met_big_wate'] == 1) {
            $list['content'] = $this->concentwatermark($list['content']);
        }

        if ($this->update_list_sql($list, $id)) {
            return true;
        } else {
            $this->error[] = 'Data error';
            return false;
        }
    }

    /**
     * 栏目json
     */
    public function docolumnjson()
    {
        global $_M;
        $redata = array();
        $column_database = load::mod_class('column/column_database', 'new');
        $list = $column_database->get_column_by_module(1);
        $new_list = array();

        foreach ($list as $column) {
            $arr = array();
            if ($column['isshow'] == 1) {
                $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($column['id']);
                if ($class123) {
                    $para_class123 = '';
                    $para_class123 .= $class123['class1']['id'] ? "&class1={$class123['class1']['id']}" : '';
                    $para_class123 .= $class123['class2']['id'] ? "&class2={$class123['class2']['id']}" : '';
                    $para_class123 .= $class123['class3']['id'] ? "&class3={$class123['class3']['id']}" : '';
                }
                $mod_name = load::sys_class('handle', 'new')->mod_to_file($column['module']);
                $url = "lang={$this->lang}&n={$mod_name}&c={$mod_name}_admin&a=doeditor". $para_class123;

                $arr['id'] 			= $column['id'];
                $arr['name'] 		= $column['name'];
                $new_list[] = $arr;
            }
        }
        $redata['list']=$new_list;
        return $redata;
    }

    /**
     * 检测静态文件名称是否存在
     */
    public function doCheckFilename()
    {
        global $_M;
        $filename = $_M['form']['filename'];
        $id = $_M['form']['id'] ? $_M['form']['id'] : '';
        $redata['valid'] = true;
        if (is_string($filename)) {
            $patten = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u';
            $check_res = is_simplestr($filename,$patten);
            if ($check_res == false) {
                $redata['valid'] = false;
                $redata['message'] = $_M['word']['special_che_deny'];
            }
            $res = $this->database->get_column_by_filename($filename);
            if ($id == '' && $res) {
                $redata['valid'] = false;
                $redata['message'] = $_M['word']['jsx27'];
            }
            if ($id && $res && $res['id'] != $id) {
                $redata['valid'] = false;
                $redata['message'] = $_M['word']['jsx27'];
            }
        }
        $this->ajaxReturn($redata);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
