<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$html_class=$body_class='h-100';
$html_class.=' met-login';
$body_class.=' d-flex flex-column pb-3 pt-4 px-3';
$met_title=$word['logintitle'].'-'.$word['metinfo'];
$login_logo_filemtime=filemtime(str_replace($url['site'], PATH_WEB, $data['met_agents_logo_login']));
$metinfo_css_filemtime = filemtime(PATH_PUBLIC_ADMIN.'css/metinfo.css');
$favicon_filemtime = filemtime(PATH_WEB."favicon.ico");
$synchronous=$_M['langlist']['admin'][$_M['langset']]['synchronous'];
// dump($data);
?>
<!DOCTYPE HTML>
<html class="{$html_class}">
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta name="robots" content="noindex,nofllow">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0,minimal-ui">
<meta name="format-detection" content="telephone=no">
<title>{$met_title}</title>
<meta name="generator" content="MetInfo V{$c.metcms_v}" data-variable="{$url.site}|{$_M['lang']}|{$synchronous}|{$c.met_skin_user}||||">
<link href="{$url.site}favicon.ico?{$favicon_filemtime}" rel="shortcut icon" type="image/x-icon">
<link href="{$url.public_third}bootstrap/bootstrap-v4.3.1.min.css" rel='stylesheet' type='text/css'>
<link href="{$url.public_admin}css/metinfo.css?{$metinfo_css_filemtime}" rel='stylesheet' type='text/css'>
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
</style>
</head>
<body class="{$body_class}">
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
                    <h1 class="h4 mb-4 pb-3 text-center font-weight-bold">{$word.loginadmin}</h1>
                    <if value="$data['qrcode']">
                        <view class="row d-flex justify-content-end mb-4 btn-change-login-method">
                            <a href="javascript:;" data-method="weixin" class="item d-flex align-items-center">
                            微信扫码登录<i class="fa-qrcode h4 my-0 ml-2"></i>
                            </a>
                            <a href="javascript:;" data-method="username" class="item d-flex align-items-center hide" role="button">
                            账号密码登录<i class="fa-desktop h4 my-0 ml-2"></i>
                            </a>
                        </view>
                    </if>
                    <form action="{$url.own_form}a=dologin" class="met-login-form login-method" data-method="username" data-submit-ajax="1">
                        <input type="hidden" name="referrer" value="{$data.referrer}">
                        <if value="$c['met_admin_type_ok']">
                            <div class="">
                                <div class="col-form-label pt-0 h6 my-0">{$word.loginlanguage}</div>
                                <div class="form-group mb-4">
                                    <select name="langset" data-checked="{$data.langset}" class="form-control border-none bg-grey-100 rounded-10 h-auto" onchange="javascript:location.href=M.url.admin+'?langset='+this.value">
                                        <list data="$data['met_langadmin']" name="$v">
                                            <option value="{$v.mark}">{$v.name}</option>
                                        </list>
                                    </select>
                                </div>
                            </div>
                        </if>
                        <div class="">
                            <div class="col-form-label pt-0 h6 my-0">{$word.loginusename}</div>
                            <div class="form-group mb-4">
                                <input type="text" name="login_name" data-safety required class="form-control border-none bg-grey-100 rounded-10 h-auto">
                            </div>
                        </div>
                        <div class="">
                            <div class="col-form-label pt-0 h6 my-0">{$word.loginpassword}</div>
                            <div class="form-group mb-4">
                                <input type="password" name="login_pass" data-safety required class="form-control border-none bg-grey-100 rounded-10 h-auto">
                            </div>
                        </div>
                        <!--图形验证码-->
                        <div class="form-group captcha <if value="!$data['captcha']">hide</if>">
                            <div class="col-form-label pt-0 h6 my-0">{$word.logincode}</div>
                            <div class="d-flex">
                                <input name='code' type='text' class='form-control border-none bg-grey-100 rounded-10 h-auto media-body mr-2' placeholder='{$word.logincode}' required>
                                <div>
                                    <img <if value="$data['captcha']">src<else/>data-src</if>='{$url.entrance}?m=include&c=ajax_pin&a=dogetpin&random={$data.random}' title='{$word.memberTip1}' align='absmiddle' role='button' width="150" class="h-100 rounded-10 <if value="$data['captcha']">met-getcode</if>">
                                    <input type="hidden" name="random" value="{$data.random}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 btn-block btn-lg h6 mt-5 border-none">{$word.loginconfirm}</button>
                        <div class="mt-4 text-center">
                            <a href="{$url.get_pass}" class="text-content">{$word.loginforget}</a>
                        </div>
                    </form>
                    <if value="$data['qrcode']">
                    <div class="qrcode-wrapper hide text-center login-method" data-method="weixin" data-login_code="{$data.login_code}">
                        <view>
                            <p class="h4 mt-0 mb-3">微信扫码登录</p>
                            <img src="{$data.qrcode}" width="200" class="border">
                            <div class="mt-2 text-grey">打开微信，扫一扫登录</div>
                        </view>
                    </div>
                    </if>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="metadmin-foot text-grey text-center">{$data.copyright}</footer>
<?php
$basic_js_time = filemtime(PATH_PUBLIC_THIRD.'admin/basic.js');
$metinfo_js_time = filemtime(PATH_PUBLIC_ADMIN.'js/metinfo.js');
$lang_json_admin_js_time = filemtime(PATH_WEB.'cache/lang_json_admin_'.$_M['lang'].'.js');
?>
</body>
<script>window.MET={$data.met_para};</script>
<script src="{$url.public_third}admin/basic.js?{$basic_js_time}"></script>
<script src="{$url.public_admin}js/metinfo.js?{$metinfo_js_time}"></script>
<script src="{$url.site}cache/lang_json_admin_{$_M['langset']}.js?{$lang_json_admin_js_time}"></script>
<if value="$data['qrcode']">
<?php $login_js_time = filemtime(PATH_SYS.'login/admin/templates/js/login.js'); ?>
<script src="{$url.own_tem}js/login.js?{$login_js_time}"></script>
</if>
<script>(function(){M.is_ie&&$('.met-ie-tips').removeClass('hide');})();</script>
<!--插件代码-->
{$_M['html_plugin']['foot_script_admin']}
</html>