<?php
// MetInfo Enterprise Content Management System
// Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$table_order = 'role-list';
$table_ajaxurl = $url['own_name'] . 'c=Role&a=doList';
$pagelength=1000;
?>
<div>
  <button type="button" class="btn btn-outline-666 btn-add" data-toggle="modal" data-target=".role-edit-modal" data-modal-url="permission/role_edit" data-modal-title="新增角色">
    新增角色
  </button>
</div>
<include file="pub/content_list/form_head"/>
                <th>角色名称</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php $colspan=2; ?>
            <include file="pub/content_list/table_loader"/>
        </tbody>
    </table>
</form>