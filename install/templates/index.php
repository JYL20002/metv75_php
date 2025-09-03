<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
global $url_public, $siteurl, $_M;
$url_site='../';
$url_public = $url_site . 'public/';
$action = $action ? $action : 'license';
if($action=='adminsetup' && isset($_M['form']['setup']) && $_M['form']['setup']==1){
	$active['finished']='active text-white';
	$img_active['finished']='_active';
}else{
	$active[$action] = 'active text-white';
	$img_active[$action]='_active';
}
echo <<<EOT
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta name="robots" content="noindex,nofllow">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0,minimal-ui">
<meta name="format-detection" content="telephone=no">
<title>MetInfo|米拓企业建站系统v{$sys_ver}安装 - Powered by MetInfo</title>
<link href="{$url_site}favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="{$url_public}third-party/bootstrap/bootstrap-v4.3.1.min.css" rel='stylesheet' type='text/css'>
<link href="../public/fonts/metinfo-icon1/metinfo-icon1.min.css" rel='stylesheet' type='text/css'>
<style>
body{background: linear-gradient( 324deg, #F1FFFA 0%, #EAFAFF 50%, #F9F2FA 100%);min-height:100vh;}
.container {max-width: 1200px;}
.install-process li:not(:first-child) i{left:-30px;top:50%;transform:translateY(-50%);font-size: 20px;color:#DDD;}
.install-process li>div{border-radius:10px;box-shadow: 0px 9px 11px 0px rgba(102,102,102,0.1);padding-top:20px;padding-bottom:20px;}
.install-process li.active>div{background: linear-gradient( 90deg, #26B1E7 0%, #00D2EF 100%);}
.install-content{border-radius:10px;}
.btn{border-radius:5px;padding-top:13px;padding-bottom:13px;}
.btn-success{background-color:none !important;background: linear-gradient( 90deg, #009DD8 0%, #00CFCF 100%) !important;border:none !important;}
.text-success{color:#15C083 !important;}
form .form-control{width:500px;border-radius:5px;padding-top: 10px;padding-bottom: 10px;height: auto;border-color: #ccc;}
form .col-form-label {width:130px;}
form .form-group.has-required:before{
	content:'*';
	position: absolute;left: 0;top: 11px;color: red;font-size: 20px;
}
form .custom-radio,
form .custom-checkbox{padding:15px 20px 15px 35px;}
.custom-control-label{z-index:1;}
.custom-control-label::before{border-radius:100% !important;top:.15rem;}
.custom-control-input:checked~.custom-control-label::before{border:3px solid #C9F0FF !important;}
.custom-control-input:checked~.custom-control-label::after{display:none;}
.custom-control-input~.custom-control-bd{width:100%;height:100%;left:0;top:0;border:1px dashed #999;border-radius:5px;}
.custom-control-input:checked~.custom-control-bd{border:1px dashed #26B1E7;}
</style>
</head>
<body>
<div class="py-3">
	<div class="container d-flex justify-content-between align-items-center">
		<a href="https://www.metinfo.cn" title='米拓企业建站系统' target="_blank"><img src="../public/images/logo.png" alt="MetInfo|米拓企业建站系统" height="50"/></a>
		<div class="text-primary" style="font-size:18px;">MetInfo|米拓企业建站系统 {$sys_ver} <span>全新安装</span></div>
	</div>
</div>
<div class="container mt-3 mb-4">
EOT;
if($action!='guide'){
echo <<<EOT
	<ul class="list-inline text-center h6 mb-0 pb-2 text-dark d-flex justify-content-between install-process">
		<li class="list-inline-item position-relative {$active['license']}">
			<div class="px-3 px-xl-4 bg-white">
				<div class="d-flex align-items-center px-3">
					<img src="./templates/images/license{$img_active['license']}.svg" width="20" height="20" class="mr-2 mr-xl-3">阅读使用协议
				</div>
			</div>
		</li>
		<li class="list-inline-item position-relative {$active['inspect']}">
			<i class="fa-angle-right position-absolute"></i>
			<div class="px-3 px-xl-4 bg-white">
				<div class="d-flex align-items-center px-3">
					<img src="./templates/images/inspect{$img_active['inspect']}.svg" width="20" height="20" class="mr-2 mr-xl-3">系统环境检测
				</div>
			</div>
		</li>
		<li class="list-inline-item position-relative {$active['db_setup']}">
			<i class="fa-angle-right position-absolute"></i>
			<div class="px-3 px-xl-4 bg-white">
				<div class="d-flex align-items-center px-3">
					<img src="./templates/images/databasesetup{$img_active['db_setup']}.svg" width="20" height="20" class="mr-2 mr-xl-3">数据库设置
				</div>
			</div>
		</li>
		<li class="list-inline-item position-relative {$active['adminsetup']}">
			<i class="fa-angle-right position-absolute"></i>
			<div class="px-3 px-xl-4 bg-white">
				<div class="d-flex align-items-center px-3">
					<img src="./templates/images/adminsetup{$img_active['adminsetup']}.svg" width="20" height="20" class="mr-2 mr-xl-3">管理员设置
				</div>
			</div>
		</li>
		<li class="list-inline-item position-relative {$active['finished']}">
			<i class="fa-angle-right position-absolute"></i>
			<div class="px-3 px-xl-4 bg-white">
				<div class="d-flex align-items-center px-3">
					<img src="./templates/images/finished{$img_active['finished']}.svg" width="20" height="20" class="mr-2 mr-xl-3">安装完成
				</div>
			</div>
		</li>
	</ul>
EOT;
}
echo <<<EOT
	<div class="text-dark install-content bg-white p-5 mt-4">
EOT;
switch($action){
	case 'license':
		include self::template('license');
		break;
	case 'guide':
		include self::template('guide');
		break;
	case 'inspect':
		self::inspect();
		break;
	case 'db_setup':
		self::db_setup();
		break;
	case 'adminsetup':
		self::adminsetup();
		break;
	case 'finished':
		include self::template('finished');
		break;
}
echo <<<EOT
	</div>
</div>
<div class="text-center mb-3">Powered by <b><a href="https://www.metinfo.cn" target="_blank">MetInfo {$sys_ver}</a></b> &copy;2009-{$nowyear} &nbsp;<a href="https://www.mituo.cn" target="_blank">mituo.cn</a></div>
EOT;
if($action=='inspect' || $action=='adminsetup' || $action=='db_setup'){
echo <<<EOT
<script src="{$url_public}third-party/jquery/jquery.min.js"></script>
EOT;
}
echo <<<EOT
</body>
</html>
EOT;
?>