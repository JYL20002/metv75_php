<?php
// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$table_order = 'member-list';
$table_ajaxurl = $url['own_name'] . 'c=MemberAdmin&a=doList';
$pagelength=1000;
?>
<div>
  <button type="button" class="btn btn-outline-666 btn-add" data-toggle="modal" data-target=".member-edit-modal" data-modal-url="permission/member_edit" data-modal-title="新增成员">
    新增成员
  </button>
  <div class="input-group w-a float-right">
    <input type="search" name="keyword" placeholder="{$word.adminusername}" class="form-control" data-table-search="#{$table_order}" />
    <div class="input-group-append">
      <div class="input-group-text btn bg-none px-2"><i class="input-search-icon fa-search" aria-hidden="true"></i></div>
    </div>
  </div>
</div>
<include file="pub/content_list/form_head"/>
              <th data-table-columnclass="text-center">{$word.adminusername}</th>
              <th data-table-columnclass="text-center">{$word.admintips5}</th>
              <th data-table-columnclass="text-center">{$word.adminname}</th>
              <th data-table-columnclass="text-center">{$word.adminLoginNum}</th>
              <th data-table-columnclass="text-center">{$word.adminLastLogin}</th>
              <th data-table-columnclass="text-center">{$word.adminLastIP}</th>
              <th>{$word.operate}</th>
            </tr>
        </thead>
        <tbody>
            <?php $colspan=7; ?>
            <include file="pub/content_list/table_loader"/>
        </tbody>
    </table>
</form>