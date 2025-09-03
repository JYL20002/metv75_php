<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
if(isset($data['id'])){
  $handle_json=jsonencode(array(
    'permissions'=>$data['handle']['permissions'],
    'langok'=>$_M['user']['langok'],
  ));
  $role_info=$data['handle']['role'];
}else{
  $handle_json=jsonencode(array(
    'permissions'=>array(),
    'langok'=>$_M['user']['langok'],
  ));
  $role_info=array();
}
$time = time();
?>
<form method="POST" action="javascript:;">
  <input type="hidden" name="id" value="{$data.id}">
  <div class="metadmin-fmbx">
    <h3 class="example-title">{$word.baceinfo}</h3>
    <dl>
      <dt>
        <label class="form-control-label">角色名称</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="text" name="name" value="{$role_info.name}" class="form-control" required/>
      </dd>
    </dl>
    <dl>
      <dt>
        <label class="form-control-label">角色说明</label>
      </dt>
      <dd class="form-group clearfix">
        <input type="text" name="info" value="{$role_info.info}" class="form-control"/>
      </dd>
    </dl>
    <h3 class="example-title">权限</h3>
    <div class="permission-list"></div>
  </div>
</form>
<textarea class='handle-json' hidden>{$handle_json}</textarea>