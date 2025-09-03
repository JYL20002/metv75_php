<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
echo <<<EOT
<style>
.guide-list .item{border-radius:10px;}
.ribbon{position:absolute;top:-3px;left:-3px;width:150px;height:150px;text-align:center;background-color:transparent}.ribbon-inner{position:absolute;top:16px;left:0;display:inline-block;height:30px;padding-right:20px;padding-left:20px;line-height:30px;color:#fff;white-space:nowrap;background-color:#526069}.ribbon-inner .icon{font-size:16px}.ribbon-lg .ribbon-inner{height:38px;font-size:1.286rem;line-height:38px}.ribbon-sm .ribbon-inner{height:26px;font-size:.858rem;line-height:26px}.ribbon-xs .ribbon-inner{height:22px;font-size:.858rem;line-height:22px}.ribbon-vertical .ribbon-inner{top:0;left:16px;width:30px;height:60px;padding:15px 0}.ribbon-vertical.ribbon-xs .ribbon-inner{width:22px;height:50px}.ribbon-vertical.ribbon-sm .ribbon-inner{width:26px;height:55px}.ribbon-vertical.ribbon-lg .ribbon-inner{width:38px;height:70px}.ribbon-reverse{right:-3px;left:auto}.ribbon-reverse .ribbon-inner{right:0;left:auto}.ribbon-reverse.ribbon-vertical .ribbon-inner{right:16px}.ribbon-bookmark .ribbon-inner{-webkit-box-shadow:none;box-shadow:none}.ribbon-bookmark .ribbon-inner:before{position:absolute;top:0;left:100%;display:block;width:0;height:0;content:"";border:15px solid #526069;border-right:10px solid transparent}.ribbon-bookmark.ribbon-vertical .ribbon-inner:before{top:100%;left:0;margin-top:-15px;border-right:15px solid #526069;border-bottom:10px solid transparent}.ribbon-bookmark.ribbon-vertical.ribbon-xs .ribbon-inner:before{margin-top:-11px}.ribbon-bookmark.ribbon-vertical.ribbon-sm .ribbon-inner:before{margin-top:-13px}.ribbon-bookmark.ribbon-vertical.ribbon-lg .ribbon-inner:before{margin-top:-19px}.ribbon-bookmark.ribbon-reverse .ribbon-inner:before{right:100%;left:auto;border-right:15px solid #526069;border-left:10px solid transparent}.ribbon-bookmark.ribbon-reverse.ribbon-vertical .ribbon-inner:before{right:auto;left:0;border-right-color:#526069;border-bottom-color:transparent;border-left:15px solid #526069}.ribbon-bookmark.ribbon-xs .ribbon-inner:before{border-width:11px}.ribbon-bookmark.ribbon-sm .ribbon-inner:before{border-width:13px}.ribbon-bookmark.ribbon-lg .ribbon-inner:before{border-width:19px}.ribbon-badge{top:-2px;left:-2px;overflow:hidden}.ribbon-badge .ribbon-inner{left:-40px;width:100%;-webkit-transform:rotate(-45deg);-ms-transform:rotate(-45deg);-o-transform:rotate(-45deg);transform:rotate(-45deg)}.ribbon-badge.ribbon-reverse{right:-2px;left:auto}.ribbon-badge.ribbon-reverse .ribbon-inner{right:-40px;left:auto;-webkit-transform:rotate(45deg);-ms-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg)}.ribbon-badge.ribbon-bottom{top:auto;bottom:-2px}.ribbon-badge.ribbon-bottom .ribbon-inner{top:auto;bottom:16px;-webkit-transform:rotate(45deg);-ms-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg)}.ribbon-badge.ribbon-bottom.ribbon-reverse .ribbon-inner{-webkit-transform:rotate(-45deg);-ms-transform:rotate(-45deg);-o-transform:rotate(-45deg);transform:rotate(-45deg)}.ribbon-corner{top:0;left:0;overflow:hidden}.ribbon-corner .ribbon-inner{top:0;left:0;width:40px;height:35px;padding:0;line-height:35px;background-color:transparent}.ribbon-corner .ribbon-inner:before{position:absolute;top:0;left:0;width:0;height:0;content:"";border:30px solid transparent;border-top-color:#526069;border-left-color:#526069}.ribbon-corner.ribbon-reverse{right:0;left:auto}.ribbon-corner.ribbon-reverse .ribbon-inner{right:0;left:auto}.ribbon-corner.ribbon-reverse .ribbon-inner:before{right:0;left:auto;border-right-color:#526069;border-left-color:transparent}.ribbon-corner.ribbon-bottom{top:auto;bottom:0}.ribbon-corner.ribbon-bottom .ribbon-inner{top:auto;bottom:0}.ribbon-corner.ribbon-bottom .ribbon-inner:before{top:auto;bottom:0;border-top-color:transparent;border-bottom-color:#526069}.ribbon-corner.ribbon-xs .ribbon-inner{width:28px;height:26px;line-height:26px}.ribbon-corner.ribbon-xs .ribbon-inner:before{border-width:22px}.ribbon-corner.ribbon-xs .ribbon-inner>.icon{font-size:.858rem}.ribbon-corner.ribbon-sm .ribbon-inner{width:34px;height:32px;line-height:32px}.ribbon-corner.ribbon-sm .ribbon-inner:before{border-width:26px}.ribbon-corner.ribbon-sm .ribbon-inner>.icon{font-size:.858rem}.ribbon-corner.ribbon-lg .ribbon-inner{width:46px;height:44px;line-height:44px}.ribbon-corner.ribbon-lg .ribbon-inner:before{border-width:36px}.ribbon-corner.ribbon-lg .ribbon-inner>.icon{font-size:1.286rem}.ribbon-clip{left:-14px}.ribbon-clip .ribbon-inner{padding-left:23px;border-radius:0 5px 5px 0}.ribbon-clip .ribbon-inner:after{position:absolute;bottom:-14px;left:0;width:0;height:0;content:"";border:7px solid transparent;border-top-color:#37474f;border-right-color:#37474f}.ribbon-clip.ribbon-reverse{right:-14px;left:auto}.ribbon-clip.ribbon-reverse .ribbon-inner{padding-right:23px;padding-left:15px;border-radius:5px 0 0 5px}.ribbon-clip.ribbon-reverse .ribbon-inner:after{right:0;left:auto;border-right-color:transparent;border-left-color:#37474f}.ribbon-clip.ribbon-bottom{top:auto;bottom:-3px}.ribbon-clip.ribbon-bottom .ribbon-inner{top:auto;bottom:16px}.ribbon-clip.ribbon-bottom .ribbon-inner:after{top:-14px;bottom:auto;border-top-color:transparent;border-bottom-color:#37474f}.ribbon-danger .ribbon-inner{background-color:#f93213}.ribbon-danger.ribbon-bookmark .ribbon-inner:before{border-color:#f93213;border-right-color:transparent}.ribbon-danger.ribbon-bookmark.ribbon-reverse .ribbon-inner:before{border-right-color:#f93213;border-left-color:transparent}.ribbon-danger.ribbon-bookmark.ribbon-vertical .ribbon-inner:before{border-right-color:#f93213;border-bottom-color:transparent}.ribbon-danger.ribbon-bookmark.ribbon-vertical.ribbon-reverse .ribbon-inner:before{border-right-color:#f93213;border-bottom-color:transparent;border-left-color:#f93213}.ribbon-danger.ribbon-corner .ribbon-inner{background-color:transparent}.ribbon-danger.ribbon-corner .ribbon-inner:before{border-top-color:#f93213;border-left-color:#f93213}.ribbon-danger.ribbon-corner.ribbon-reverse .ribbon-inner:before{border-right-color:#f93213;border-left-color:transparent}.ribbon-danger.ribbon-corner.ribbon-bottom .ribbon-inner:before{border-top-color:transparent;border-bottom-color:#f93213}.ribbon-danger .ribbon-inner:after{border-top-color:#e9595b;border-right-color:#e9595b}.ribbon-danger.ribbon-reverse .ribbon-inner:after{border-right-color:transparent;border-left-color:#e9595b}.ribbon-danger.ribbon-bottom .ribbon-inner:after{border-top-color:transparent;border-bottom-color:#e9595b}
</style>
<h1 class="text-center h4">欢迎使用米拓企业建站系统</h1>
<div class="pt-1 guide-list">
	<!--MySQL-->
	<div class="item mt-4 p-4 position-relative" style="background: #F0FDFD;overflow: hidden;">
		<div class="ribbon ribbon-xs ribbon-badge ribbon-danger">
			<span class="ribbon-inner" style="top: 15px;left: -47px;">推荐</span>
		</div>
		<div class="p-3 d-flex justify-content-between align-items-center position-relative">
			<div class="text-primary" style="font-size:18px;">使用MySQL数据库，高效稳定！<br>安装过程可自行设置管理员账号密码</div>
			<a href="index.php?action=inspect&db_type=mysql" class="btn btn-success px-4 h6 my-0"><span class="px-2">一键安装</span></a>
		</div>
	</div>
	<!--SQLite-->
	<div class="item mt-4 p-4" style="background:#F0FDFD;">
		<div class="p-3 d-flex justify-content-between align-items-center">
			<div class="text-primary" style="font-size:18px;">使用SQLite数据库<br>无需安装，简单方便！</div>
			<div class="text-muted">
				<div class="d-flex align-items-center mb-1">
					<div>网站后台地址：</div>
					<div>{$siteurl}admin/</div>
				</div>
				<div class="d-flex align-items-center mb-1">
					<div>后台登录账号：</div>
					<div>admin</div>
				</div>
				<div class="d-flex align-items-center mb-1">
					<div>后台登录密码：</div>
					<div>admin</div>
				</div>
			</div>
			<a href="index.php?action=skipInstall" class="btn btn-success px-4 h6 my-0"><span class="px-2">直接使用</span></a>
		</div>
	</div>
	<!--DMSQL-->
	<div class="item mt-4 p-4" style="background:#F4FDF0;">
		<div class="p-3 d-flex justify-content-between align-items-center">
			<div style="font-size:18px;color:#00B645;">达梦数据库，<b>国产信创</b><br>安装过程可自行设置管理员账号密码</div>
			<a href="index.php?action=inspect&db_type=dmsql" class="btn btn-primary px-4 h6 my-0" style="border:none;background: linear-gradient( 90deg, #1CDB46 0%, #3EE7AA 100%) !important;"><span class="px-2">一键安装</span></a>
		</div>
	</div>
</div>
EOT;
?>