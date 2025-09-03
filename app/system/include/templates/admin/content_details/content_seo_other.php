<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<h3 class='example-title'>
	<span class='d-inline-block' style="width:155px;">{$word.parameter}</span>
	<button type="button" class="btn btn-outline-primary btn-paraset" data-toggle="modal" data-target=".content-para-manage-modal" data-modal-title="{$word.parmanage}" data-modal-url="parameter/list/?c=parameter_admin&a=doparaset&module={$data.n}&class1={$data.list.class1}&&class2={$data.list.class2}&class3={$data.list.class3}&id={$data.list.id}" data-modal-size="xl" data-modal-fullheight="1" data-modal-oktext="" data-modal-notext="{$word.close}">{$word.parmanage}</button>
	<button type="button" class="btn btn-outline-primary btn-content-para-refresh ml-2">
		<i class="fa-refresh mr-1"></i>
		{$word.refresh}
	</button>
	<span class="text-help font-weight-normal font-size-14">{$word.relation_tips}</span>
</h3>
<div class="content-details-paralist" data-url="{$url.own_name}c={$data.n}_admin&a=dopara&id={$data.list.id}&hid={$data.hid}&class1={$data.list.class1}&class2={$data.list.class2}&class3={$data.list.class3}"></div>
<h3 class='example-title'>
    <span class='d-inline-block' style="width:155px;">{$word.relation_data}</span>
    <button type="button" class="btn btn-outline-666 btn-relation" data-toggle="modal" data-target=".content-relation-manage-modal" data-modal-title="{$word.selected}{$word.content}" data-modal-url="relation/list/?content_id={$data.list.id}&module={$data.n}" data-modal-size="xl" data-modal-fullheight="1" data-modal-oktext="" data-modal-notext="{$word.close}">{$word.relation_data_add}</button>
    <span class="text-help font-weight-normal font-size-14">{$word.relation_tips}</span>
</h3>
<div class="content-details-relationlist met-scrollbar scrollbar-grey" data-info="{$data.n}|{$data.list.id}" data-url="{$url.adminurl}n=relation&c=relation_admin&a=doGetRelations&content_id={$data.list.id}&hid={$data.hid}&module={$data.n}">
    <div class="metadmin-loader py-5"><div class="text-center d-flex align-items-center"><div class="loader loader-round-circle"></div></div></div>
</div>
<include file="pub/content_details/editor"/>
<include file="pub/content_details/seo"/>
<include file="pub/content_details/other"/>