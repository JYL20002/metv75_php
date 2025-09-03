<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data = array_merge($data, $data['handle']);
unset($data['handle']);
$met_news_api = "https://www.metinfo.cn/metv5news.php?action=json&listnum=6";
$metinfo_website = "https://www.metinfo.cn/";
?>
<input type="hidden" name="admin_folder_safe" value="{$data.admin_folder_safe}" data-toggle="modal" data-target=".admin-folder-safe-modal">
<div class="row text-center site-summarize-list">
	<list data="$data['summarize']" name="$v">
	<?php
	$url='#/manage/?view_type=module&module='.$v['_index'];
	$v['_index']=='job' && $url.='&head_tab_active=1';
	?>
	<div class="item px-3" style="width: 20%;max-width: 20%;">
		<a href="{$url}" class="d-flex justify-content-center align-items-center py-4 text-content">
			<div class="d-flex align-items-center flex-column">
				<div class="img d-flex justify-content-center align-items-center bg-white rounded-circle">
					<img src="../app/system/index/admin/templates/images/{$v._index}.svg" width="30" height="30">
				</div>
				<span class="my-1 text-dark">{$v.total}</span>
				<div class="my-0 font-size-18">{$v.name}</div>
			</div>
		</a>
	</div>
	</list>
</div>
<if value="$data['home_app_ok']">
<div class="mt-4 pt-3">
	<div class="d-flex justify-content-between align-items-center">
		<h3 class="font-size-18 font-weight-bold mb-0 float-left">{$word.recom}</h3>
		<a href="{$c.market_url}" target="_blank" class="text-content">{$word.columnmore} <i class="fa fa-angle-right"></i></a>
	</div>
	<ul class="home-app-list row list-unstyled mb-0 mt-2">
		<?php $loader_class='w-100'; ?>
		<include file="pub/loader"/>
	</ul>
</div>
</if>
<if value="$data['home_app_ok']">
<div class="mt-4">
	<div class="d-flex justify-content-between align-items-center">
		<h3 class="font-size-18 font-weight-bold mb-0 float-left">MetInfo {$word.upfiletips37}</h3>
		<a href="{$metinfo_website}" target="_blank" class="text-content">{$word.columnmore} <i class="fa fa-angle-right"></i></a>
	</div>
	<div class="home-news-list mt-3" data-url="{$met_news_api}">
		<include file="pub/loader"/>
	</div>
</div>
</if>