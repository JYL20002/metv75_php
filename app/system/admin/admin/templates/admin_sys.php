<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$member_info=$data['handle'];
?>
<form method="POST" action="{$url.own_name}c=index&a=doSaveInfo" class="member-sys-edit-form" data-submit-ajax="1" autocomplete="new-password">
    <div class="metadmin-fmbx">
        <if value="!isset($data['modal'])">
        <h3 class="example-title">{$word.modify_information}</h3>
        </if>
        <dl>
            <dt>
                <label class="form-control-label">{$word.adminusername}</label>
            </dt>
            <dd class="form-group clearfix">
                <input type="text" name="admin_id" value="{$member_info.admin_id}" disabled class="form-control" required autocomplete="new-password"/>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class="form-control-label">{$word.adminpassword}</label>
            </dt>
            <dd class="form-group clearfix">
                <input type="password" name="admin_pass" class="form-control" placeholder="{$word.empty_not_modified}" autocomplete="new-password"/>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class="form-control-label">{$word.adminpassword1}</label>
            </dt>
            <dd class="form-group clearfix">
                <input type="password" name="admin_pass_replay" class="form-control"
                    data-fv-notEmpty-message="{$word.formerror1}" data-fv-identical="true" data-fv-identical-field="admin_pass"
                    data-fv-identical-message="{$word.formerror5}" />
            </dd>
        </dl>
        <dl>
            <dt>
                <label class="form-control-label">{$word.adminname}</label>
            </dt>
            <dd class="form-group clearfix">
                <input type="text" name="admin_name" value="{$member_info.admin_name}" class="form-control" />
            </dd>
        </dl>
        <dl>
            <dt>
                <label class="form-control-label">{$word.adminmobile}</label>
            </dt>
            <dd class="form-group clearfix">
                <input type="text" name="admin_mobile" value="{$member_info.admin_mobile}" class="form-control" />
            </dd>
        </dl>
        <dl>
            <dt>
                <label class="form-control-label">{$word.admin_email}</label>
            </dt>
            <dd class="form-group clearfix">
                <input type="text" name="admin_email" value="{$member_info.admin_email}" class="form-control" />
            </dd>
        </dl>
        <hr>
        <dl>
            <dt>
                <label class="form-control-label">绑定微信[openid]</label>
            </dt>
            <dd>
                <div class="form-group clearfix">
                    <input type="text" name="weixin_nickname" value="{$member_info.met_weixin_openid}" class="form-control" disabled/>
                </div>
                <if value="!$member_info['met_weixin_install']">
                <div class="text-help">需配合【微信公众号管理应用】插件方可设置<if value="$c['met_agents_metmsg']">，请到<a href="./#/myapp/?head_tab_active=2" target="_blank">应用插件</a>页面搜索插件安装</if>。</div>
                </if>
                <if value="!$member_info['met_weixin_config']">
                <div class="text-help">请先到<a href="./#/app/met_weixin/?c=index&a=doindex&head_tab_active=0" target="_blank">【微信公众号管理应用】</a>插件进行设置</div>
                </if>
                <if value="$member_info['met_weixin_openid']">
                <button type="button" class="btn btn-primary weixin-unbind">解绑微信</button>
                </if>
                <if value="$member_info['qrcode']">
                    <img src="{$member_info['qrcode']}" width="150px;" class="qrcode border">
                    <p class="mb-0">扫描上方二维码进行绑定，绑定成功后可通过微信扫码登录网站的管理后台</p>
                </if>
            </dd>
        </dl>
        <if value="!isset($data['modal'])">
        <?php $submit_dt=1; ?>
        <include file="pub/content_details/submit"/>
        </if>
    </div>
</form>