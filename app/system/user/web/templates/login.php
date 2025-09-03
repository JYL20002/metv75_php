<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<include file="sys_web/head"/>
<include file="app/style"/>
<div class="met-member login-index met-login-register p-y-50 bg-pagebg1">
    <div class="container">
        <div class='met-form-wrapper {$data.login_position}'>
            <if value="$c['met_qq_open']||$c['met_weixin_open']||$c['met_weibo_open']||$c['met_google_open']||$c['met_facebook_open']">
                <div class='p-x-20 p-y-15 border-bottom1 met-form-top flex hidden-md-down'>
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
            <div class="flex form-list">
                <form method="post" action="{$url.login_check}" class='met-form met-form-login'>
                    <input type="hidden" name="gourl" value="{$data.gourl}" />
                    <h1 class='text-xs-center m-t-0 m-b-30 font-size-24'>{$word.memberLogin}</h1>
                    <div class="form-group">
                        <div class="input-group input-group-icon">
							<span class="input-group-addon p-x-10">
								<i class="fa-user font-size-24 blue-grey-400" aria-hidden="true"></i>
							</span>
                            <input type="text" name="username" class="form-control" placeholder="{$word.logintips}" data-safety required data-fv-notempty-message="{$word.noempty}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group input-group-icon">
							<span class="input-group-addon p-x-10">
								<i class="fa-lock font-size-24 blue-grey-400" aria-hidden="true"></i>
							</span>
                            <input type="password" name="password" class="form-control" placeholder="{$word.password}" data-safety required data-fv-notempty-message="{$word.noempty}">
                        </div>
                    </div>

                    <!--图形验证码-->
                    <?php
                    if($data['captcha']){
                        ?>
                        <div class="form-group">
                            <div class="input-group input-group-icon">
                                <input type="text" name="code" class="form-control" placeholder="{$word.memberImgCode}" required data-fv-notempty-message="{$word.noempty}">
                                <div class="input-group-addon p-5 user-code-img">
                                    <img src="{$url.entrance}?m=include&c=ajax_pin&a=dogetpin&random={$data.random}" title="{$word.memberTip1}" class='met-getcode' align="absmiddle" role="button">
                                    <input type="hidden" name="random" value="{$data.random}">
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!--/图形验证码-->

                    <div class='text-xs-center m-t-30'>
                        <button type="submit" class="btn btn-lg btn-primary btn-squared p-x-50">{$word.memberGo}</button>
                    </div>
                    <div class="login_link text-xs-center m-t-15"><a href="{$url.getpassword}">{$word.memberForget}</a></div>
                </form>
                <if value="$c['met_member_register']">
                    <div class="met-form met-login-register-right flex">
                        <div class='w-full text-xs-center'>
                            <h2 class='h1 m-t-0 m-b-30 font-size-24'>{$word.memberReg}</h2>
                            <p>{$word.logintips1}</p>
                            <a class="btn btn-lg btn-info btn-squared p-x-50" href="{$url.register}">{$word.memberRegister}</a>
                        </div>
                    </div>
                </if>
            </div>
        </div>
    </div>
</div>
<if value="$c['met_weixin_open'] eq 1">
    <div class="modal fade modal-primary met-user-login-weixin-modal">
        <div class="modal-dialog modal-center modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">微信扫码登录</h4>
                </div>
                <div class="modal-body text-xs-center">
                    <div class="h-250 vertical-align">
                        <div class="loader vertical-align-middle loader-round-circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</if>
<include file="sys_web/foot"/>