<?php

// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

class history extends admin
{
    public $handle;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->sys_handle = load::sys_class('handle', 'new');
        $this->database = load::mod_class('history/history_database', 'new');
    }

    /**
     * 历史记录列表
     * aid 内容id
     * mod 模块编号
     * @return mixed
     */
    public function doList()
    {
        global $_M;
        $aid = $_M['form']['aid'];
        $mod = $_M['form']['mod'];
        $module = load::sys_class('handle', 'new')->file_to_mod($mod);

        $_where = " aid = '{$aid}' AND module = '{$module}' ";
        $_order = " record_time DESC";

        $this->tabledata = load::sys_class('tabledata', 'new');
        $data = $this->tabledata->getdata($this->database->table, '*', $_where, $_order);
        return $this->tabledata->rdata($data);
    }

    /**
     * 删除历史记录
     * allid 历史记录id （1,2,3）
     */
    public function doDel()
    {
        global $_M;
//        $aid = $_M['form']['aid'];
        $allid = $_M['form']['allid'];
        $allid = explode(',', $allid);
        foreach ($allid as $id) {
            $this->database->del_by_id($id);
        }

        $this->success($allid, $_M['word']['jsok']);
    }

    /**
     * 恢复数据
     * @return void
     */
    public function doRestore()
    {
        global $_M;
        $id = $_M['form']['id'];    //历史记录id

        $one = $this->database->get_list_one_by_id($id);
        if(!$one) $this->error('data error');
        unset($one['id']);

        $module_name = load::sys_class('handle','new')->mod_to_name($one['module']);
        $module_admin_obj = load::mod_class("{$module_name}/admin/{$module_name}_admin", 'new');
        $res = $module_admin_obj->update_list($one, $one['aid']);
        if($res) $this->success('',$_M['word']['jsok']);
        $this->error($_M['word']['dataerror']);
    }

    /**
     * 查看记录详情
     * id 记录ID
     */
    public function doView()
    {
        global $_M;
        $id = $_M['form']['id'];

        $one = $this->database->get_list_one_by_id($id);
        if (!$one) return array();
        $one = $this->listAnalysis($one);

        return $one;
        $this->success($one);
    }

    public function listAnalysis($list = array())
    {
        global $_M;
        $list['class1'] = $list['class1'] ?: 0;
        $list['class2'] = $list['class2'] ?: 0;
        $list['class3'] = $list['class3'] ?: 0;
        $list['class_now'] = $list['class3'] ? $list['class3'] : ($list['class2'] ? $list['class2'] : $list['class1']);
        $list['updatetime'] = date("Y-m-d H:i:s");
        $list['title'] = htmlspecialchars($list['title']);
        $list['ctitle'] = htmlspecialchars($list['ctitle']);
        $list['description'] = htmlspecialchars($list['description']);
        $list = self::imgList($list);
        return $list;
    }

    private function imgList($list)
    {
        $imgurl_all = array();
        $imgurl_all[] = $list['imgurl'];
        $displayimg = explode("|", $list['displayimg']);
        foreach ($displayimg as $val) {
            if (!$val) continue;
            $img = explode("*", $val);
            $imgurl_all[] = $img[1];
        }
        $list['imgurl_all'] = implode("|", $imgurl_all);
        return $list;
    }
}

// This program is an open source system, commercial use, please consciously to purchase commercial license.
// Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
