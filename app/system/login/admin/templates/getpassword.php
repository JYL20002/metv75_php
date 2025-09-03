<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
$html_class=$body_class='h-100';
$html_class.=' met-login';
$body_class.=' d-flex flex-column pb-3 pt-4 px-3';
$met_title=$word['getTip5'];
$login_logo_filemtime=filemtime(str_replace($url['site'], PATH_WEB, $data['met_agents_logo_login']));
?>
<include file="pub/header"/>
<style>
    <if value="$data['qrcode']">
    .btn-change-login-method{right: -15px;top: -30px;}
    </if>
    body{background: url('{$url.own_tem}images/login-bg.jpg') center/cover no-repeat;}
    .met-ie-tips a{color: #fff;}
    .login-card-wrapper{width: 800px;}
    .login-card{box-shadow: 0px 4px 14px 0px rgba(0,0,0,0.05);border-color: #fff !important;}
    .login-card .form-control:focus{border-color: #000;}
    .login-card input.form-control,
    .login-card textarea.form-control{
        padding-top: 12px;padding-bottom: 12px;
    }
    .login-card .btn{border-radius: 5px;}
    .login-card .btn-primary{background: linear-gradient( 90deg, #009DD8 0%, #00CFCF 100%) !important;padding-top: 10px;padding-bottom: 10px;}

    .login-card form .custom-radio,
    .login-card form .custom-checkbox{padding:15px 20px 15px 35px;}
    .login-card .custom-control-label{z-index:1;}
    .login-card .custom-control-label::before{border-radius:100% !important;}
    .login-card .custom-control-input:checked~.custom-control-label{color: #269FCB;}
    .login-card .custom-control-input:checked~.custom-control-label::before{background:#269FCB;}
    .login-card .custom-control-input~.custom-control-bd{width:100%;height:100%;left:0;top:0;border:1px solid #999;border-radius:5px;}
    .login-card .custom-control-input:checked~.custom-control-bd{border:1px solid #269FCB;background: #F2FCFF;}
</style>
<div class="media-body d-flex flex-column justify-content-center align-items-center mb-5">
    <div class="mw-100 login-card-wrapper position-relative">
        <div class="text-center mb-0 bg-danger alert hide mb-3 met-ie-tips">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">×</span>
            </button>
            你正在使用一个 <strong>过时</strong> 的浏览器。请 <a href='https://browsehappy.com/' target=_blank>升级您的浏览器</a>，以提高您的体验。
        </div>
        <div class="login-card rounded-20 bg-white border cover d-flex">
            <div class="d-flex align-items-center justify-content-center" style="width: 300px;background: linear-gradient( 155deg, #CDEAFF 1%, #EFFFFF 100%);">
                    <a href="{$data.met_agents_linkurl}" title="{$word.metinfo}" target="_blank">
                        <img src="{$data.met_agents_logo_login}?{$login_logo_filemtime}" alt="{$word.metinfo}" style="max-width: 200px;max-height: 200px;">
                    </a>
                </div>
                <div class="py-4 py-md-5 px-3 px-sm-4 px-md-5 media-body">
                    <div class="px-3">
                        <h1 class="h4 mb-4 pb-3 text-center font-weight-bold">{$word.loginforget}</h1>
                        <p class="<if value="$data['admin_name']">text-success<else/>text-dark</if>">{$data.description}</p>
                        <form action="{$data.url}&langset={$data.langset}" method="post" <if value="$data['abt_type'] eq 1 || $data['abt_type'] eq 2 || M_ACTION eq 'doCodeCheck'">data-submit-ajax="1"</if> class="met-getpassword-form">
                            <if value="($data['abt_type'] eq 1 || $data['abt_type'] eq 2) && !$data['admin_name']">
                            <!-- 输入用户名、邮箱地址、手机号码 -->
                            <input type="hidden" name="abt_type" value="{$data.abt_type}">
                            <div class="form-group mb-0">
                                <input type="text" name="admin_id" placeholder="{$data.title}" required class='form-control border-none bg-grey-100 rounded-10 h-auto'>
                            </div>
                            <elseif value="M_ACTION eq 'doCodeCheck'"/>
                            <!-- 输入验证码 -->
                            <div class="form-group mb-0">
                                <input type="text" name="code" placeholder="{$data.title}" required class='form-control border-none bg-grey-100 rounded-10 h-auto'>
                            </div>
                            <elseif value="$data['admin_name']"/>
                            <!-- 输入修改密码 -->
                            <div class="row mb-2">
                                <label class="col-form-label col-3 pr-0 text-right">{$word.password24}</label>
                                <div class="col-9 col-form-label">{$data.admin_name}</div>
                            </div>
                            <div class="row">
                                <label class="col-form-label col-3 pr-0 text-right">{$word.password25}</label>
                                <div class="col-9 form-group">
                                    <input type="password" name="password" required class='form-control border-none bg-grey-100 rounded-10 h-auto'>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-form-label col-3 pr-0 text-right">{$word.password26}</label>
                                <div class="col-9 form-group mb-0">
                                    <input type="password" name="passwordsr" required data-fv-identical="true" data-fv-identical-field="password" class='form-control border-none bg-grey-100 rounded-10 h-auto'>
                                </div>
                            </div>
                            <else/>
                            <!-- 选择验证修改密码方式 -->
                            <div class="d-flex">
                                <div class="custom-control custom-radio media-body">
                                    <input type="radio" id="abt_type1" name="abt_type" value='1' data-checked="2" class="custom-control-input" />
                                    <label class="custom-control-label pl-3" for="abt_type1">
                                        <i class="fa-mobile-phone h5 my-0 position-relative" style="top: 1px;"></i>
                                        <div>{$word.password27}</div>
                                    </label>
                                    <div class="custom-control-bd w-100 h-100 position-absolute"></div>
                                </div>
                                <div class="custom-control custom-radio media-body ml-3">
                                    <input type="radio" id="abt_type2" name="abt_type" value='2' class="custom-control-input" />
                                    <label class="custom-control-label pl-3" for="abt_type2">
                                        <i class="fa-envelope-o h5 my-0 position-relative" style="top: 1px;"></i>
                                        <div>{$word.password29}</div>
                                    </label>
                                    <div class="custom-control-bd w-100 h-100 position-absolute"></div>
                                </div>
                            </div>
                            </if>
                            <button type="submit" class="btn btn-primary px-4 btn-block btn-lg h6 mt-5 border-none">{$word.password20}</button>
                            <div class="text-center mt-4">
                                <a href="{$url.site_admin}?lang={$_M['lang']}&langset={$_M['langset']}" title="{$word.password21}" class="text-content"><i class="fa-angle-left h6 my-0 mr-1 position-relative" style="top: 1px;"></i> {$word.password21}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="metadmin-foot text-grey text-center">{$data.copyright}</footer>
<include file="pub/footer"/>