<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
if(isset($data['id'])){
  $handle_json=jsonencode(array(
    'permissions'=>$data['handle']['permissions'],
    'langok'=>$_M['user']['langok'],
  ));
  $member_info=$data['handle']['member_info'];
  if(!$member_info['admin_login_lang']) $member_info['admin_login_lang']='cn';
}else{
  $handle_json=jsonencode(array(
    'permissions'=>array(),
    'langok'=>$_M['user']['langok'],
  ));
  $member_info=array(
    'admin_login_lang'=>'cn',
    'sync_role_permissions'=>1,
  );
}
$is_add=!isset($data['id']);
$time = time();
?>
<form method="POST" action="javascript:;" autocomplete="new-password">
  <input type="hidden" name="id" value="{$data.id}">
  <div class="metadmin-fmbx">
    <h3 class="example-title">{$word.admininfo}</h3>
    <dl>
      <dt>
        <label class="form-control-label">所属角色</label>
      </dt>
      <dd class="form-group clearfix">
        <select name="role_id" class="form-control" data-checked="{$member_info.role_id}" required></select>
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.adminusername}</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="text" name="admin_id" value="{$member_info.admin_id}" class="form-control" required autocomplete="new-password"/>
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.adminpassword}</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="password" name="admin_pass" class="form-control"
            <if value="$is_add">required<else/>placeholder="{$word.empty_not_modified}"</if> autocomplete="new-password"/>
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.adminpassword1}</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="password" name="admin_pass_replay" <if value="$is_add">required</if> class="form-control"
            data-fv-notEmpty-message="{$word.formerror1}" data-fv-identical="true" data-fv-identical-field="admin_pass"
            data-fv-identical-message="{$word.formerror5}" />
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.adminname}</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="text" name="admin_name" value="{$member_info.admin_name}" class="form-control" />
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.adminmobile}</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="text" name="admin_mobile" value="{$member_info.admin_mobile}" class="form-control" />
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.admin_email}</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="text" name="admin_email" value="{$member_info.admin_email}" class="form-control" />
      </dd>
    </dl>
    <h3 class="example-title">{$word.admintips7}</h3>
    <dl>
      <dt>
        <label class="form-control-label">{$word.admin_login_lang}</label>
      </dt>
      <dd class="form-group clearfix">
        <list data="$_M['langlist']['admin']" name="$lang">
          <div class="custom-control custom-radio ">
            <input type="radio" id="radio-{$lang.lang}-{$time}" name="admin_login_lang" value="{$lang.lang}" data-checked="{$member_info.admin_login_lang}"
              class="custom-control-input"/>
            <label class="custom-control-label" for="radio-{$lang.lang}-{$time}">{$lang.name}</label>
          </div>
        </list>
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">{$word.adminPower}</label>
      </dt>
      <dd class="form-group clearfix">
        <div class="custom-control custom-checkbox ">
          <input type="checkbox" id="admin_issueok" name="admin_issueok" value="1" data-checked="{$member_info.admin_issueok}" class="custom-control-input"/>
          <label class="custom-control-label" for="admin_issueok">{$word.adminTip2}</label>
        </div>
        <div class="custom-control custom-checkbox ">
          <input type="checkbox" id="admin_check-{$time}" name="admin_check" value="1" data-checked="{$member_info.admin_check}"
            class="custom-control-input"/>
          <label class="custom-control-label" for="admin_check-{$time}">{$word.adminTip3}</label>
        </div>
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">继承角色的权限</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="checkbox" data-plugin="switchery" name="sync_role_permissions" value='{$member_info.sync_role_permissions}' hidden>
      </dd>
    </dl>
    <div class="permission-list"></div>
  </div>
</form>
<textarea class='handle-json' hidden>{$handle_json}</textarea>