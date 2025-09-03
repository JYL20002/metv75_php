<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$checkbox_time=time();
$table_addlist='#column-list';
?>
<div class="alert alert-primary">{$word.noorderinfo}</div>
<div class="metadmin-content-min p-4 bg-white">
	<div class="clearfix">
		<button type="button" class="btn btn-outline-666 px-4 btn-add-column" data-toggle="modal" data-target=".column-add-modal">{$word.add}</button>
		<button type="button" class="btn btn-light float-right btn-show-allsubcolumn2"><span>{$word.open_allchildcolumn_v6}</span><i class="fa-angle-down"></i></button>
	</div>
	<form method="POST" action="{$url.own_name}c=index&a=dolistsave" data-submit-ajax='1'>
		<table class="table table-hover dataTable w-100 mt-3" id="column-list" data-ajaxurl="{$url.own_name}c=index&a=doGetColumnList" data-table-sdom='t' data-plugin="checkAll" data-datatable_order="#column-list">
			<thead>
				<tr>
					<include file="pub/content_list/checkall_all"/>
					<th data-table-columnclass="text-center" width="30">{$word.sort}</th>
					<th data-table-columnclass="td-column-name">
						<div class="d-flex justify-content-between align-items-center">
							{$word.columnname}<a href="javascript:;" class="btn-show-allsubcolumn px-2 text-dark"><i class="fa-angle-down"></i></a>
						</div>
					</th>
					<th data-table-columnclass="text-center" width="170">{$word.columnnav}</th>
					<th data-table-columnclass="text-center" width="100">{$word.columnmodule}</th>
					<th data-table-columnclass="text-center" width="120">{$word.columndocument}</th>
					<th width="160">{$word.operate}</th>
				</tr>
			</thead>
			<tbody>
				<?php $colspan=7; ?>
				<include file="pub/content_list/table_loader"/>
			</tbody>
			<?php
			$colspan--;
			$del_title=$word['delete_information'].$word['jsx39'];
			?>
			<include file="pub/content_list/tfoot_common"/>
						<button type="button" class="btn btn-outline-666 px-4" data-toggle="modal" data-target=".column-add-modal">{$word.add}</button>
						<div class="float-right">
							<div class="custom-control custom-checkbox checkbox-inline mr-2">
								<input type="checkbox" id="is_contents-{$checkbox_time}" name='is_contents' value='1' class="custom-control-input"/>
								<label class="custom-control-label pl-1 font-weight-normal" for="is_contents-{$checkbox_time}">{$word.copyotherlang2}</label>
							</div>
							<select name="to_lang" class="form-control d-inline-block w-a">
								<option value="">{$word.copyotherlang1}</option>
								<list data="$_M['user']['langok']" name="$v">
								<if value="$_M['lang'] neq $v['mark']">
								<option value="{$v.mark}">{$v.name}</option>
								</if>
								</list>
							</select>
							<button type="submit" class="btn btn-outline-primary" data-submit_type="copy">{$word.Copy}</button>
						</div>
					</th>
				</tr>
			</tfoot>
		</table>
	</form>
</div>