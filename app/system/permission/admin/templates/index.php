<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$head_tab_active=isset($data['head_tab_active'])?$data['head_tab_active']:0;
$head_tab_ajax=1;
$head_tab=array(
    array('title'=>'成员','url'=>'permission/member_list'),
    array('title'=>'角色','url'=>'permission/role_list'),
);
?>
<include file="pub/head_tab"/>