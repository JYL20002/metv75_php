<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon p-x-10"><i class="fa-envelope font-size-24 blue-grey-400"></i></span>
		<input type="email" name="username" required class="form-control" placeholder="{$word.memberemail}" autocomplete="new-password"
		data-fv-notempty-message="{$word.noempty}"

		data-fv-remote="true"
		data-fv-remote-url="{$url.register_userok}"
		data-fv-remote-message="{$word.emailhave}"

		data-fv-emailaddress="true"
		data-fv-emailaddress-message="{$word.emailcheck}"
		/>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon p-x-10"><i class="fa-lock font-size-24 blue-grey-400"></i></span>
		<input type="password" name="password" required class="form-control" placeholder="{$word.password}" autocomplete="new-password"
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
	<span class="text-help m-b-0">{$word.passwordcheck}</span>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon p-x-10"><i class="fa-lock font-size-24 blue-grey-400"></i></span>
		<input type="password" name="confirmpassword" required data-password="password" class="form-control" placeholder="{$word.renewpassword}" autocomplete="new-password"
		data-fv-notempty-message="{$word.noempty}"

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
<?php
if($data['captcha']){
    ?>
    <div class="form-group">
        <div class="input-group input-group-icon">
            <span class="input-group-addon p-x-10"><i class="fa-shield font-size-24 blue-grey-400"></i></span>
            <input type="text" name="code" required class="form-control" placeholder="{$word.memberImgCode}" data-fv-notempty-message="{$word.noempty}">
            <div class="input-group-addon p-5 user-code-img">
                <img src="{$url.entrance}?m=include&c=ajax_pin&a=dogetpin&random={$data.random}" title="{$word.memberTip1}" class='met-getcode' align="absmiddle" role="button">
                <input type="hidden" name="random" value="{$data.random}">
            </div>
        </div>
    </div>
<?php } ?>