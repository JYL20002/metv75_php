<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$random = random(4, 1);
?>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon p-x-10"><i class="fa-mobile font-size-24 blue-grey-400"></i></span>
		<input type="text" name="phone" required class="form-control" placeholder="{$word.telnum}" autocomplete="new-password"
		data-fv-phone="true"
		data-fv-phone-message="{$word.telok}"
		data-fv-notempty-message="{$word.telnum}{$word.noempty}"
		/>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon p-x-10"><i class="fa-lock font-size-24 blue-grey-400"></i></span>
		<input type="password" name="password" required class="form-control" placeholder="{$word.password}" autocomplete="new-password"
		data-fv-notempty-message="{$word.password}{$word.noempty}"

		data-fv-identical="true"
		data-fv-identical-field="confirmpassword"
		data-fv-identical-message="{$word.passwordsame}"

		data-fv-stringlength="true"
		data-fv-stringlength-min="6"
		data-fv-stringlength-max="30"
		data-fv-stringlength-message="{$word.passwordcheck}"
		>
	</div>
	<span class="text-help m-b-0">{$word.passwordcheck}</span>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon p-x-10"><i class="fa-lock font-size-24 blue-grey-400"></i></span>
		<input type="password" name="confirmpassword" required data-password="password" class="form-control" placeholder="{$word.renewpassword}" autocomplete="new-password"
		data-fv-notempty-message="{$word.password}{$word.noempty}"

		data-fv-identical="true"
		data-fv-identical-field="password"
		data-fv-identical-message="{$word.passwordsame}"

		data-fv-stringlength="true"
		data-fv-stringlength-min="6"
		data-fv-stringlength-max="30"
		data-fv-stringlength-message="{$word.passwordcheck}"
		>
	</div>
	<span class="text-help m-b-0">{$word.passwordcheck}</span>
</div>
<div class="phonecode-wrapper">
	<div class="form-group hide code-wrapper bg-white p-15 m-b-10 radius10 border-type1">
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
			<input type="text" name="phonecode" required class="form-control" placeholder="{$word.memberbasicCell}{$word.memberImgCode}" data-fv-notempty-message="{$word.noempty}">
			<div class="input-group-addon p-0 border-none">
				<button type="button" data-url="{$url.valid_phone}" class="btn btn-primary btn-squared w-full phone-code" data-retxt="{$word.resend}">
					{$word.phonecode}
					<span class="badge"></span>
				</button>
			</div>
		</div>
	</div>
</div>