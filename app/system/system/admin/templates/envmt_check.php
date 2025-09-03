<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data=array_merge($data,$data['handle']);
unset($data['handle']);
?>
<style>
.inspect-list li div:before{
	content:'';
	width:6px;height:6px;border-radius:100%;border:1px solid #999;position:absolute;left:0;top:50%;transform:translateY(-50%);
}
</style>
<p>{$word.system_check1}</p>
<div class="border rounded-10 p-3">
    <div class="h6 font-weight-bold mb-3"><strong>{$word.system_check2}</strong></div>
    <ul class="row mb-0 inspect-list">
        <list data="$data['data']">
        <?php
        if(!$disabled && $val[1]=='danger') $disabled='disabled';
        $val['msg']=$val[1]!='success' && $val[3]?'<div class="text-danger">'.$val[3].'</div>':'';
        $val['icon']=$val[1]=='success'?'check':'close';
        ?>
        <li class='col-4'>
            <div class="d-flex align-items-center justify-content-between flex-wrap py-3 border-bottom position-relative pl-3">
                <span>{$val[0]}{$val['msg']}</span>
                <i class="fa-{$val['icon']} font-weight-bold h6 mb-0 text-{$val[1]} mr-2"></i>
            </div>
        </li>
        </list>
    </ul>
</div>
<div class="border rounded-10 p-3 mt-3">
    <div class="h6 font-weight-bold mb-3"><strong>{$word.system_check3}</strong></div>
    <p class="mb-2">{$word.system_check4}</p>
    <p>{$word.system_check5}</p>
    <ul class="row mb-0 inspect-list">
        <list data="$data['dirs']">
        <?php
        if(!$disabled && $val['status']=='danger') $disabled='disabled';
        $thisurl=explode('..',$val['dir']);
        $val['icon']=$val['status']=='success'?'check':'close';
        $val['msg']=$val['status']!='success'?'<div class="text-danger">'.$val['msg'].'</div>':'';
        ?>
        <li class='col-4'>
            <div class="d-flex align-items-center justify-content-between flex-wrap py-3 border-bottom position-relative pl-3">
                <span>{$thisurl[1]}{$val['msg']}</span>
                <i class="fa-{$val['icon']} font-weight-bold h6 mb-0 text-{$val['status']} mr-2"></i>
            </div>
        </li>
        </list>
    </ul>
</div>