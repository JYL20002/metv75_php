<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$random = random(4, 1);
?>
<include file="sys_web/head"/>
<include file="app/style"/>
<div class="met-member met-login-register p-y-50 bg-pagebg1">
	<div class="container">
		<div class='met-form-wrapper m-x-auto'>
            <div class='p-x-20 p-y-15 border-bottom1 met-form-top flex hidden-md-down' style="justify-content:flex-end;">
                <p class="m-b-0"><a class="btn btn-info btn-sm" href="{$url.login}">{$word.relogin}</a></p>
            </div>

			<div class="flex form-list">
				<form class="met-form" method="post" action="{$url.set_pass_by_sms}">
					<input type="hidden" name="username" value="{$data['username']}" />
					<h1 class='m-t-0 m-b-30 font-size-24 text-xs-center'>{$word.getTip5}</h1>
					<div class="form-group">
						<div class="input-group input-group-icon">
							<span class="input-group-addon p-x-10"><i class="fa-shield font-size-24 blue-grey-400"></i></span>
							<input type="text" name="code" required class="form-control" placeholder="{$word.memberImgCode}" data-fv-notempty-message="{$word.js14}">
							<div class="input-group-addon p-5 user-code-img">
								<img src="{$url.entrance}?m=include&c=ajax_pin&a=dogetpin&random={$random}" title="{$word.memberTip1}" class='met-getcode' align="absmiddle" role="button">
								<input type="hidden" name="random" value="{$random}">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group input-group-icon">
							<input type="text" name="phonecode" required class="form-control" placeholder="{$word.memberImgCode}" data-fv-notempty-message="{$word.noempty}">
							<div class="input-group-addon p-0 border-none">
								<button type="button" data-url="{$url.send_sms_find_pass}" class="btn btn-success btn-squared w-full phone-code" data-retxt="{$word.rememberImgCode}">
									{$word.phonecode}
									<span class="badge"></span>
								</button>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon p-x-10"><i class="fa-lock font-size-24 blue-grey-400"></i></span>
							<input type="password" name="password" class="form-control" placeholder="{$word.newpassword}" required
								data-fv-notempty-message="{$word.noempty}"
								data-fv-identical="true"
								data-fv-identical-field="confirmpassword"
								data-fv-identical-message="{$word.passwordsame}"
								data-fv-stringlength="true"
								data-fv-stringlength-min="6"
								data-fv-stringlength-max="30"
								data-fv-stringlength-message="{$word.passwordcheck}"
							>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon p-x-10"><i class="fa-lock font-size-24 blue-grey-400"></i></span>
							<input type="password" name="confirmpassword" data-password="password" class="form-control" placeholder="{$word.renewpassword}" required
							data-fv-notempty-message="{$word.noempty}"
							data-fv-identical="true"
							data-fv-identical-field="password"
							data-fv-identical-message="{$word.passwordsame}"
							>
						</div>
					</div>
					<div class='text-xs-center m-t-30'>
						<button class="btn btn-lg btn-primary btn-squared p-x-50" type="submit">{$word.Submit}</button>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
<include file="sys_web/foot"/>