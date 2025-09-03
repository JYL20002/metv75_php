<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$del_title=$del_title?$del_title:$word['delete_information'];
?>
<include file="pub/content_list/tfoot_first"/>
<button type="button" class="btn btn-light px-4 <if value="$del_type">btn-content-list-del</if>"
table-delete data-plugin="alertify" data-type='confirm' data-confirm-title='{$del_title}'
<if value="$del_url">data-url="{$del_url}"</if>
data-head_title="确认删除" data-head_icon="fa-trash-o"
>{$word.delete}</button>