<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data['module']='content-history';
$table_order=$data['module'].'-list';
$form_action=$url['adminurl'] . 'n=history&c=history&a=doDel&aid='.$data['id'];
$table_ajaxurl=$url['adminurl'] . 'n=history&c=history&a=doList&mod='.$data['mod'].'&aid='.$data['id'];
$foot_save_no=1;
?>
<include file="pub/content_list/form_head"/>
				<include file="pub/content_list/checkall_all"/>
				<th><if value="$data['mod'] eq 'job'">{$word.jobposition}<else/>{$word.title}</if></th>
				<th data-table-columnclass="text-center">{$word.updatetime}</th>
				<th width="150">{$word.operate}</th>
			</tr>
		</thead>
		<tbody>
			<?php $colspan=4; ?>
			<include file="pub/content_list/table_loader"/>
		</tbody>
		<?php $colspan--; ?>
		<include file="pub/content_list/tfoot_common"/>
				</th>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="mod" value="{$data.mod}">
	<input type="hidden" name="aid" value="{$data.id}">
</form>
</div>