<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$other_login=$c['met_qq_open']||$c['met_weixin_open']||$c['met_weibo_open']||$c['met_google_open']||$c['met_facebook_open'];
?>
<include file="sys_web/head"/>
<include file="app/style"/>
<div class="met-member register-index met-login-register p-y-50 bg-pagebg1">
	<div class="container">
		<div class='met-form-wrapper m-x-auto'>
			<if value="$other_login">
			<div class='p-x-20 p-y-15 border-bottom1 met-form-top flex hidden-sm-down' style="justify-content:space-between;align-items-center;">
				<p class="m-b-0">{$word.acchave}<a class="btn btn-info btn-sm" href="{$url.login}">{$word.relogin}</a></p>
				<div class="login-type flex">
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
			<div class="form-list">
				<p class="text-xs-right m-t-15 p-r-15 <if value="$other_login"> hidden-md-up</if>" style="margin-bottom:-10px;">{$word.acchave}<a class="btn btn-info btn-sm" href="{$url.login}">{$word.relogin}</a></p>
				<form class="met-form-register met-form" method="post" action="{$url.register_save}" autocomplete="new-password">
					<h1 class='m-t-0 m-b-30 font-size-24 text-xs-center'>{$word.memberReg}</h1>
					<?php
					switch ($c['met_member_vecan']) {
						case 1:
					?>
					<include file="app/register_email"/>
					<?php
							break;
						case 3:
					?>
					<include file="app/register_phone"/>
					<?php
							break;
						default:
					?>
					<include file="app/register_ord"/>
					<?php
							break;
					}
					?>
					<if value="count($_M['paralist'])">
					<div class="form-group m-y-30 text-muted met-more">
						<hr />
						<span class='bg-white inline-block p-x-10'>{$word.memberMoreInfo}</span>
					</div>
					<div class="met-form-register-collapse">
						<div>
							<?php $_M['paraclass']->parawebtem(0,10,0,0,$data['class1'],$data['class2'],$data['class3']); ?>
						</div>
					</div>
					</if>
					<if value="$c['met_member_agreement']">
					<div class="form-group">
						<div class="checkbox-custom checkbox-primary m-y-0">
							<input type="checkbox" name="met_member_agreement" value="1" id="met_member_agreement" required data-fv-notEmpty-message="{$word.user_agreement_tips3}">
							<label for="met_member_agreement" class='blue-grey-600'>{$word.user_agreement_tips1}<a href="javascript:;" data-toggle="modal" data-target=".met-user-agreement-modal">《{$word.user_agreement}》</a>{$word.user_agreement_tips2}</label>
						</div>
					</div>
					</if>
					<div class='text-xs-center m-t-30'>
						<button class="btn btn-lg btn-primary btn-squared p-x-50" type="submit" <if value="$c['met_member_agreement']">disabled</if>>{$word.memberRegister}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<if value="$c['met_member_agreement']">
<div class="modal fade modal-primary met-user-agreement-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">{$word.user_agreement}</h4>
            </div>
            <div class="modal-body font-size-16">
				{$c.met_member_agreement_content}
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-default" data-dismiss="modal">{$word.close}</button>
            </div>
        </div>
    </div>
</div>
</if>
<include file="sys_web/foot"/>