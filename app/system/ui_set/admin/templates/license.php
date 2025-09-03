<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$license_data=$data['handle'];
?>
<table class="table table-hover">
    <tbody>
		<tr <if value="!$c['met_agents_metmsg']">class="hide"</if>>
            <td>
				<a href="https://www.metinfo.cn/metinfo/license.html" title="{$word.copyright_type_tips2}" target="_blank">{$word.copyright_type_tips2}</a>
            </td>
		</tr>
		<tr>
			<th>开源代码许可协议</th>
		</tr>
        <list data="$license_data" num="1000">
        <tr>
            <td>
                <a href="javascript:;" <if value="substr($val['name'],-1) neq '/'">data-modal-title="{$val.license_url}"</if>>{$val.name}</a>
            </td>
        </tr>
        </list>
    </tbody>
</table>