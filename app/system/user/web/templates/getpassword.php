<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data['page_title']=$_M['word']['getTip5'].$data['page_title'];
?>
<include file="sys_web/head"/>
<include file="app/style"/>
<div class="met-member met-login-register p-y-50 bg-pagebg1">
	<div class="container ">
		<div class='met-form-wrapper m-x-auto'>
			<if value="$c['met_qq_open']||$c['met_weixin_open']||$c['met_weibo_open']||$c['met_google_open']||$c['met_facebook_open']">
			<div class='p-x-20 p-y-15 border-bottom1 met-form-top flex hidden-md-down' style="justify-content:flex-end;">
				<div class="login-type flex hidden-sm-down">
					<p class='m-x-20 blue-grey-500'>{$word.otherlogin}</p>
					<ul class="login-type-list ulstyle flex">
						<!--QQ登录-->
						<if value="$c['met_qq_open']">
						<li class='m-x-10'><a title="QQ{$word.login}" href="{$url.login_other}&type=qq"><i class="fa fa-qq font-size-30"></i></a></li>
						</if>
						<!--微信登录-->
						<if value="$c['met_weixin_open'] && !(!is_weixin_client() && is_mobile_client())">
						<li class='m-x-10'><a <if value="is_weixin_client()">href="{$url.login_other}&type=weixin"<else/>href="javascript:;" data-toggle="modal" data-target=".met-user-login-weixin-modal"</if> class="met-user-login-weixin"><i class="fa fa-weixin light-green-600 font-size-30"></i></a></li>
						</if>
						<!--微博登录-->
						<if value="$c['met_weibo_open']">
						<li class='m-x-10'><a href="{$url.login_other}&type=weibo"><i class="fa fa-weibo red-600 font-size-30"></i></a></li>
						</if>
						<!--Google-->
						<if value="$c['met_google_open']">
						<li class='m-x-10'><a href="{$url.login_other}&type=google"><i class="fa fa-google-plus-official red-600 font-size-30"></i></a></li>
						</if>
						<!--Google-->
						<if value="$c['met_facebook_open']">
						<li class='m-x-10'><a href="{$url.login_other}&type=facebook"><i class="fa fa-facebook-official blue-600 font-size-30"></i></a></li>
						</if>
					</ul>
				</div>
			</div>
			</if>
			<div class="flex form-list">
				<form class="met-form" method="post" action="{$url.getpassword}">
					<h1 class='m-t-0 m-b-30 font-size-24 text-xs-center'>{$word.getTip5}</h1>
					<div class="form-group">
						<input type="text" name="username" class="form-control" placeholder="{$word.getpasswordtips}" required data-fv-notempty-message="{$word.noempty}">
					</div>
					<?php
					if($_M['config']['met_memberlogin_code']){
						$random = random(4, 1);
					?>
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
					<?php } ?>
					<div class='text-xs-center m-t-30'>
						<button class="btn btn-lg btn-primary btn-squared p-x-50" type="submit">{$word.next}</button>
					</div>
				</form>
				<div class="met-form met-login-register-right flex">
					<div class='w-full text-xs-center met-form-login'>
						<h2 class='h1 m-t-0 m-b-30 font-size-24'>{$word.memberLogin}</h2>
						<p>{$word.acchave}</p>
						<a class="btn btn-lg btn-info btn-squared p-x-50" href="{$url.login}">{$word.relogin}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<include file="sys_web/foot"/>