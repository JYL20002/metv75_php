<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('news/admin/news_admin');

class download_admin extends news_admin
{
    public $shop;
    public $module;

    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->module = 4;
        $this->database = load::mod_class('download/download_database', 'new');
    }

    /**
     * 增加
     */
    public function doadd()
    {
        global $_M;
        return parent::doadd();
    }

    public function doaddsave()
    {
        global $_M;
        return parent::doaddsave();
    }

    public function insert_list($list = array())
    {
        global $_M;
        return parent::insert_list($list);
    }

    /**
     *系统属性
     */
    public function dopara()
    {
        return parent::paramentList();
    }

    /*产品编辑*/
    public function doeditor()
    {
        global $_M;
        return parent::doeditor();
    }

    public function doeditorsave()
    {
        global $_M;
        return parent::doeditorsave();
    }

    public function update_list($list = array(), $id = '')
    {
        global $_M;
        return parent::update_list($list, $id);
    }

    /**
     * 分页数据
     */
    public function dojson_list()
    {
        global $_M;
        return parent::dojson_list();
    }

    function dolistsave()
    {
        global $_M;
        return parent::dolistsave();
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
