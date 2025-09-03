<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data['page_title']=$_M['config']['modifyaccemail'].$data['page_title'];
?>
<include file="sys_web/head"/>
<include file="app/style"/>
<div class="met-member p-y-50 bg-pagebg1">
	<div class="container">
		<form class="met-form p-30 bg-white" method="post" action="{$_M['url']['emailedit']}">
			<input type="hidden" name="p" value="{$_M['form']['p']}" />
			<h1 class="m-t-0 m-b-20 font-size-24 text-xs-center">{$_M['word']['modifyaccemail']}</h1>
		  	<h4 class='m-t-0 font-size-18'>{$_M['word']['emailnow']}{$_M['user']['email']}</h4>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon p-x-10"><i class="fa-envelope font-size-24 blue-grey-400"></i></span>
					<input type="email" name="email" class="form-control" placeholder="{$_M['word']['newemail']}" required
					data-fv-notempty-message="{$_M['word']['noempty']}"
                    data-fv-emailAddress-message="{$_M['word']['emailvildtips3']}"
					>
				</div>
			</div>
			<button class="btn btn-lg btn-primary btn-squared btn-block" type="submit">{$_M['word']['Submit']}</button>
		</form>
	</div>
</div>
<include file="sys_web/foot"/>