<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<?php
$html_class=$body_class='h-100';
$html_class.=' met-admin';
?>
<include file="pub/header"/>
<if value="!$_M['form']['noside']">
<div class="h-100 cover d-flex">
	<div class="metadmin-sidebar h-100 transition500 bg-grey-100 pt-1">
		<div class="metadmin-logo d-flex justify-content-between align-items-center py-2 px-4" style="height: 62px;">
			<a href="#/home" title="{$word.metinfo}" class="d-block">
				<img src="{$c.met_agents_logo_index}" alt="{$word.metinfo}" class="img-fluid" style="max-height: 50px;">
			</a>
			<button type="button" class="btn btn-default border-none p-0 ml-2 text-center position-relative btn-adminsidebar-control" style="top: 2px;"><i class="fa-dedent h4 mb-0 position-relative"></i></button>
		</div>
		<!-- <hr class="my-0"> -->
		<ul class="list-unstyled mb-0 metadmin-sidebar-nav">
			<li class="transition500 first px-3 mb-2">
				<div class="d-flex justify-content-center align-items-center px-3 rounded-20" style="background: #EDEDED;">
					<a href="#/home" class="text-content">{$word.backstage}</a>
					<if value="$data['admin_pop']">
						<a href="{$url.site_admin}?lang={$_M['lang']}&n=ui_set" target="_blank" class="text-content ml-3">{$word.visualization}</a>
					</if>
					<a href="{$url.site}index.php?lang={$_M['lang']}" target="_blank" class="text-content ml-3">{$word.preview}</a>
				</div>
			</li>
			<li class="transition500 position-relative">
				<a href="javascript:;" class="d-flex justify-content-between align-items-center px-4">
					<div><i class="fa-home mr-3"></i></div>
				</a>
				<ul class="sub list-unstyled text-nowrap py-2 shadow rounded-lg">
					<li class="transition500">
						<a href="#/home" title="{$msub.name}" class="d-flex align-items-center px-4"><span>{$word.backstage}</span></a>
						<a href="{$url.site_admin}?lang={$_M['lang']}&n=ui_set" class="d-flex align-items-center px-4"><span>{$word.visualization}</span></a>
						<a href="{$url.site}index.php?lang={$_M['lang']}" target="_blank" class="d-flex align-items-center px-4"><span>{$word.preview}</span></a>
					</li>
				</ul>
			</li>
			<!-- <hr class="my-0"> -->
		</ul>
	</div>
</if>
	<div class="metadmin-rightcontent h-100 met-scrollbar position-relative media-body bg-grey-100" style="overflow-x: hidden;">
		<if value="!$_M['form']['noside']">
		<?php
        $lang_name = $_M['langlist']['web'][$_M['lang']]['name'];
		?>
		<div class="pt-2 mr-4 metadmin-head-wrapper">
			<div class="bg-grey-100 w-100 position-absolute" style="left: 0;top: 0;height: 50%;"></div>
			<header class="metadmin-head navbar bg-white px-0 rounded-20 position-relative">
				<div class="container-fluid px-4">
					<div class="metadmin-head-right d-flex align-items-center">
						<div class="dropdown" style="margin-right: 100px;">
							<a href="javascript:;" class="btn btn-outline-dark rounded-5 py-1 dropdown-toggle" data-toggle="dropdown">
								<span class="d-none d-md-inline-block">{$lang_name}</span>
							</a>
							<ul class="dropdown-menu metadmin-head-langlist">
								<list data="$_M['user']['langok']" name="$v">
									<a href="javascript:;" data-val='{$v.mark}' class='dropdown-item px-3'>{$v.name}</a>
								</list>
								<li class="px-3 py-1"><a href="#/language" class="btn btn-outline-666"><i class="fa fa-plus"></i> {$word.added} {$word.langweb}</a></li>
							</ul>
						</div>
						<if value="$c['met_agents_metmsg']">
						<a href="javascript:;" class="text-content mr-4 d-flex align-items-center" data-toggle="modal" data-target=".syspackage-modal" data-modal-url="ui_set/package" data-modal-title="授权信息" data-modal-type="centered" data-modal-footerok="0">
							<img src="{$url.public_images}admin_top_nav/authorization.svg" height="15">
							<span class="d-none d-md-inline-block ml-1">版本信息</span>
						</a>
						</if>
						<div class="dropdown">
							<a href="javascript:;" class="text-content d-flex align-items-center dropdown-toggle" type="button" data-toggle="dropdown">
								<img src="{$url.public_images}admin_top_nav/check_updates.svg" height="15">
								<span class="d-none d-md-inline-block ml-1">{$word.update_log}</span>
							</a>
							<ul class="dropdown-menu">
								<if value="$c['met_agents_metmsg']">
								<a href="{$c.help_url}" target="_blank" class='dropdown-item px-3'>{$word.help_manual}</a>
								<a href="{$c.edu_url}" target="_blank" class='dropdown-item px-3'>{$word.extension_school}</a>
								<a href="{$c.kf_url}" target="_blank" class='dropdown-item px-3'>{$word.online_work_order}</a>
								</if>
								<a href="javascript:;" class='dropdown-item px-3' data-toggle="modal" data-target=".syslicense-modal" data-modal-url="ui_set/license/?n=system&c=license&a=doLicenseList" data-modal-title="许可协议">许可协议</a>
							</ul>
						</div>
					</div>
					<div class="dropdown">
						<button type="button" class="btn btn-default bg-grey border-none rounded-pill py-1 dropdown-toggle" data-toggle="dropdown">
							<i class="metinfo-admin-icon metinfo-admin-icon-administrator"></i>
							<span>{$_M['user']['admin_name']}</span>
						</button>
						<ul class="dropdown-menu dropdown-menu-right">
							<a href="#/admin/admin_sys/?n=admin&c=index&a=doInfo" class="dropdown-item px-3">{$word.modify_information}</a>
							<a href="{$url.adminurl}n=login&c=login&a=dologinout" class="dropdown-item px-3">{$word.indexloginout}</a>
						</ul>
					</div>
				</div>
			</header>
		</div>
		</if>
		<div class="metadmin-main pr-4 mt-4 mb-3">
		</div>
		<div class="metadmin-loader"><div class="text-center d-flex align-items-center h-100"><div class="loader loader-round-circle"></div></div></div>
		<footer class="metadmin-foot px-4 my-3 text-grey text-center position-sticky" style="top: 100%;">{$data.copyright}</footer>
		<button type="button" class="btn btn-primary px-2 met-scroll-top position-fixed" hidden><span class="px-1"><i class="icon wb-chevron-up" aria-hidden="true"></i></span></button>
	</div>
<if value="!$_M['form']['noside']">
</div>
<button type="button" data-toggle="modal" class="btn-admin-common-modal" hidden></button>
</if>
<include file="pub/footer"/>