<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
if($data['handle']){
	$data=array_merge($data,$data['handle']);
	unset($data['handle']);
}
$table_order='bottommenu-list';
$colspan=10;
?>
<div class='alert alert-primary'>{$word.admin_menu1}</div>
<div class="metadmin-content-min bg-white p-4">
<form method="POST" action="{$url.own_name}c=menu_admin&a=doSaveMenu" data-submit-ajax='1'>
	<table class="table table-hover dataTable w-100 {$table_order}" id="{$table_order}" data-ajaxurl="{$url.own_name}c=menu_admin&a=doGetList" data-table-pageLength="1000" data-plugin="checkAll" data-datatable_order="#{$table_order}">
		<thead>
			<tr>
				<include file="pub/content_list/checkall_all"/>
				<th width="150">{$word.button_text}</th>
				<th>{$word.parameter10}/{$word.account}</th>
				<th width="80" data-table-columnclass="text-center">{$word.linkType}</th>
				<th width="100" data-table-columnclass="text-center">{$word.onlineimg}</th>
				<th width="50" data-table-columnclass="text-center">{$word.open_mode}</th>
				<th width="100" data-table-columnclass="text-center" class="text-wrap">{$word.button_color}</th>
				<th width="100" data-table-columnclass="text-center" class="text-wrap">{$word.text_color}</th>
				<th width="50" data-table-columnclass="text-center">{$word.skinusenow}</th>
				<th>{$word.operate}</th>
			</tr>
		</thead>
		<tbody data-plugin="dragsort" data-dragsort_order=".{$table_order}">
			<include file="pub/content_list/table_loader"/>
		</tbody>
		<?php
		$colspan--;
		$table_newid=1;
		?>
		<include file="pub/content_list/tfoot_common"/>
					<include file="pub/content_list/btn_table_add"/>
					<textarea table-addlist-data hidden>
						<tr>
							<td class="text-center">
								<div class="custom-control custom-checkbox">
									<input class="checkall-item custom-control-input" type="checkbox" name="id">
									<label class="custom-control-label"></label>
								</div>
								<input type="hidden" name="no_order">
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="name" required class="form-control">
								</div>
							</td>
							<td>
								<textarea name="url" rows="3" class="form-control">&lt;/textarea&gt;
							</td>
							<td class="text-center">
								<select name="type" class="form-control w-auto d-inline-block">
									<option value='0'>{$word.parameter10}</option>
									<option value='1'>{$word.parameter8}</option>
									<option value='2'>{$word.short_message}</option>
									<option value='3'>{$word.mailbox}</option>
									<option value='5'>{$word.enterprise_qq}</option>
									<option value='6'>{$word.open_wechat}</option>
								</select>
							</td>
							<td class="text-center">
								<input type="hidden" name="icon" data-plugin="iconset" data-btn_size="sm" data-icon_class="mb-1 mr-0" class="form-control">
							</td>
							<td class="text-center">
								<select name="target" class="form-control w-auto d-inline-block">
									<option value="0">{$word.original_window}</option>
									<option value="1">{$word.new_window}</option>
								</select>
							</td>
							<td class="text-center">
								<input type="text" name="but_color" data-plugin='minicolors' class="form-control">
							</td>
							<td class="text-center">
								<input type="text" name="text_color" data-plugin='minicolors' class="form-control">
							</td>
							<td class="text-center">
								<select name="enabled" class="form-control w-auto d-inline-block">
									<option value="1">{$word.yes}</option>
									<option value="0">{$word.no}</option>
								</select>
							</td>
							<td></td>
						</tr>
					</textarea>
				</th>
			</tr>
		</tfoot>
	</table>
</form>
</div>