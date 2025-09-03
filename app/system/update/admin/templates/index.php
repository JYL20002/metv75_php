<?php
    // MetInfo Enterprise Content Management System
    // Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
    defined('IN_MET') or exit('No permission');
?>
<div class="met-update">
    <div class="px-4 pb-2 pt-4 border rounded-10">
        <div class="font-weight-bold">{$word.program_information}</div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">{$word.checkupdate}</div>
            <div class="col-md-9">
                <div class='clearfix update'>
                    <span><i class="fa fa-refresh fa-spin mr-2"></i>{$word.jsx20}</span>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">{$word.current_version}</div>
            <div class="col-md-9">
                <div class='clearfix version'>
                    <span></span>
                </div>
            </div>
        </div>
        <if value="$c['met_agents_update']">
            <div class="row my-3">
                <div class="col-md-3 form-control-label">{$word.update_log}</div>
                <div class="col-md-9">
                    <div class='clearfix '>
                        <a class="log_url" target="_blank">{$word.View}</a>
                    </div>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-md-3 form-control-label">{$word.reserved}</div>
                <div class="col-md-9">
                    <div class='clearfix copyright'>
                        <a href="{$_M['config']['copyright_url']}" target="_blank"></a>
                    </div>
                </div>
            </div>
        </if>
        <div class="row my-3 update-met_template hide">
            <div class="col-md-3 form-control-label">{$word.appearance}</div>
            <div class="col-md-9">
                <a href="#/app/met_template" title="{$word.appearance}" onclick="$(this).parents('.update-modal').modal('hide')">{$word.checkupdate}</a>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">{$word.myapp}</div>
            <div class="col-md-9">
                <a href="#/myapp" title="{$word.myapp}" onclick="$(this).parents('.update-modal').modal('hide')">{$word.checkupdate}</a>
            </div>
        </div>
    </div>
    <div class="px-4 pb-2 pt-4 border rounded-10 mt-3">
        <div class="font-weight-bold">{$word.upfiletips38}</div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">{$word.Operating}</div>
            <div class="col-md-9">
                <div class='clearfix os'>
                    <span></span>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">Web{$word.the_server}</div>
            <div class="col-md-9">
                <div class='clearfix software'>
                    <span></span>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">PHP{$word.the_version}</div>
            <div class="col-md-9">
                <div class='clearfix php'>
                    <span></span>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-3 form-control-label">{$word.database}{$word.the_version}</div>
            <div class="col-md-9">
                <div class='clearfix mysql'>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</div>