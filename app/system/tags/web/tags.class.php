<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class tags extends web
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
    }


    public function doGetTagData()
    {
        global $_M;
        $data = $this->input_class();
        $this->check($data['access']);
        $this->seo($data['name'], $data['keywords'], $data['description']);
        $this->seo_title($data['ctitle']);
        $this->add_input('searchword', urldecode($_M['form']['searchword']));
        $this->seo_canonical($data['url']);
        load::sys_class('handle', 'new')->redirectUrl($this->input); //伪静态时动态链接跳转
        $this->view('tags', $this->input);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
