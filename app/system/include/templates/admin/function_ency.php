<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data['mod_num']=array(1,2,3,4,5,6,7,8,10,11,12);
if(!$data['handle']) die;
?>
<style>
.function-ency-list .list .item a:not(:hover){color: #333;}
</style>
<div class="function-ency-list text-center">
    <p class="text-danger text-left">{$word.function_ency1}</p>
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 font-weight-bold d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency1.svg" alt="" width="20" height="20" class="mr-2">{$word.websiteSet}</div>
        <if value="$data['handle']['content']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/manage" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.indexcontent}</a>
        </div>
        </if>
        <if value="$data['handle']['column']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/column" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.columumanage}</a>
        </div>
        </if>
        <if value="$data['handle']['basic_info']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/webset" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.basicInfoSet}</a>
        </div>
        </if>
        <if value="$data['handle']['language']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/language" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.multilingual}</a>
        </div>
        </if>
        <if value="$data['handle']['basic_info']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/safe/?head_tab_active=1" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.mailSetting}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/webset/?head_tab_active=1" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.thirdCode}</a>
        </div>
        </if>
        <if value="$data['handle']['watermark_thumbnail']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/imgmanage" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.watermarkThumbnail}</a>
        </div>
        </if>
        <if value="$data['handle']['online']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/online" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.customers}</a>
        </div>
        </if>
        <if value="$data['handle']['banner']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/banner" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.indexflash}</a>
        </div>
        </if>
        <if value="$data['handle']['content']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/recycle" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.recycleBin}</a>
        </div>
        </if>
        <if value="$data['handle']['mobile_menu']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/menu" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.the_menu}</a>
        </div>
        </if>
    </div>
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 font-weight-bold d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency2.svg" alt="" width="20" height="20" class="mr-2">{$word.securityTools}</div>
        <if value="$data['handle']['checkupdate']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/update" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.checkupdate}</a>
        </div>
        </if>
        <if value="$data['handle']['safe']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/safe" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.safety_efficiency}</a>
        </div>
        </if>
        <if value="$data['handle']['databack']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/databack/?head_tab_active=0" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.data_processing}</a>
        </div>
        </if>
        <if value="$data['handle']['clear_cache']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.own_form}n=ui_set&c=index&a=doclear_cache" title="{$word.clearCache}" class="d-block clear-cache">{$word.clearCache}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.own_form}n=ui_set&c=index&a=doClearThumb" title="{$word.clearThumb}" class="d-block clear-cache">{$word.clearThumb}</a>
        </div>
        </if>
        <if value="$data['handle']['environmental_test']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="javascript:;" class="d-block" data-toggle="modal" data-target=".system-check-env-modal" data-modal-size="lg" data-modal-url="system/envmt_check/?c=patch&a=docheckEnv" data-modal-fullheight="1" data-modal-title="{$word.environmental_test}" data-modal-oktext="" data-modal-notext="{$word.close}">{$word.environmental_test}</a>
        </div>
        </if>
        <if value="$data['handle']['safe']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/safe/?head_tab_active=2" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.database_switch}</a>
        </div>
        </if>
        <if value="$data['handle']['safe']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/safe/?head_tab_active=3" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.operation_log}</a>
        </div>
        </if>
    </div>
    <if value="$data['handle']['seo']">
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 font-weight-bold d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency3.svg" alt="" width="20" height="20" class="mr-2">{$word.searchEngineOptimization}</div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.seoSetting}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo/?head_tab_active=1" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.pseudostatic}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo/?head_tab_active=2" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.indexhtmset}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo/?head_tab_active=3" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.anchor_text}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo/?head_tab_active=4" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.htmsitemap}</a>
        </div>
        <if value="$data['handle']['link']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo/?head_tab_active=5" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.indexlink}</a>
        </div>
        </if>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/seo/?head_tab_active=6" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.tag}</a>
        </div>
    </div>
    </if>
    <if value="$data['handle']['user']">
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 font-weight-bold d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency4.svg" alt="" width="20" height="20" class="mr-2">{$word.indexuser}</div>
        <if value="$data['handle']['web_user']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/user" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.memberist}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/user/?head_tab_active=1" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.membergroup}</a>
        </div>
         <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/user/?head_tab_active=2" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.memberattribute}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/user/?head_tab_active=3" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.memberfunc}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/user/?head_tab_active=4" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.thirdPartyLogin}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/user/?head_tab_active=5" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.mailcontentsetting}</a>
        </div>
        </if>
        <if value="$data['handle']['admin_user']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/admin/user" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.metadmin}</a>
        </div>
        </if>
    </div>
    </if>
    <if value="$data['handle']['column']">
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency5.svg" alt="" width="20" height="20" class="mr-1"><span class="font-weight-bold">{$word.systemModule}</span><span class="text-danger ml-2">{$word.function_ency2}</span></div>
        <list data="$data['mod_num']" name="$v">
        <?php $mod_name=$word['mod'.$v]; ?>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/column" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$mod_name}</a>
        </div>
        </list>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/column" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.modout}</a>
        </div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/column" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.tag}</a>
        </div>
    </div>
    </if>
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 font-weight-bold d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency6.svg" alt="" width="20" height="20" class="mr-2">{$word.appAndPlugin}</div>
        <if value="$data['handle']['myapp']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/myapp" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.myapp}</a>
        </div>
        </if>
        <if value="$data['handle']['site_template']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/app/met_template" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.appearance}</a>
        </div>
        </if>
        <if value="$data['handle']['met_agents_sms']">
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/app/met_sms" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.sms_function}</a>
        </div>
        </if>
    </div>
    <if value="$data['handle']['partner']">
    <div class="list row mx-0 mb-3 px-2 pt-3 pb-2 border rounded-10">
        <div class="item px-2 col-12 text-left mb-2 font-weight-bold d-flex align-items-center"><img src="{$url.public_images}function_ency/function_ency7.svg" alt="" width="20" height="20" class="mr-2">{$word.cooperation_platform}</div>
        <div class="item px-1 col-6 col-sm-4 col-md-3 col-lg-2 my-1">
            <a href="{$url.site_admin}#/partner" target="_blank" class="d-block rounded-10 bg-grey-100 py-2">{$word.cooperation_platform}</a>
        </div>
    </div>
    </if>
</div>