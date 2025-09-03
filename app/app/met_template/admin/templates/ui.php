<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$url = HTTP_HOST;
?>
<if value="$c['met_agents_metmsg']">
<div class="met-template-right position-absolute"></div>
</if>
<div class="content">
    <if value="$c['met_agents_metmsg']">
    <div class="met-tips" data-url="{$url}" data-templates_url="{$c.templates_url}"></div>
    </if>
    <div class="met-template-list row">
    </div>
</div>