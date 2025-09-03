<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
$disabled='';
echo <<<EOT
<style>
.inspect-list li div:before{
	content:'';
	width:6px;height:6px;border-radius:100%;border:1px solid #999;position:absolute;left:0;top:50%;transform:translateY(-50%);
}
</style>
<p>检查你的服务器是否支持安装MetInfo|米拓企业建站系统，请在继续安装前消除错误或警告信息。</p>
<div class="p-4 mt-4" style="border:1px solid #D2EEF9;border-radius:10px;">
	<div class="px-2">
		<h2 class="font-weight-bold" style="font-size:16px !important;">环境/函数检测结果</h2>
		<ul class="row mt-2 mb-0 list-unstyled inspect-list">

EOT;
foreach ($data as $val) {
	if(!$disabled && $val[1]=='danger') $disabled='disabled';
	$val['msg']=$val[1]!='success' && $val[3]?'<div class="text-danger">'.$val[3].'</div>':'';
	$val['icon']=$val[1]=='success'?'check':'close';
echo <<<EOT
			<li class='col-3'>
				<div class="d-flex align-items-center justify-content-between flex-wrap py-3 border-bottom position-relative pl-3">
					<span>{$val[0]}{$val['msg']}</span>
					<i class="fa-{$val['icon']} font-weight-bold h6 mb-0 text-{$val[1]} mr-2"></i>
				</div>
			</li>
EOT;
}
echo <<<EOT
		</ul>
		<div class="text-warning mt-4 px-4 py-2" style="background:#EDF9F5;border-radius:5px;" id='api-check'>检测链接API服务器中....</div>
	</div>
</div>
<div class="p-4 mt-4" style="border:1px solid #D2EEF9;border-radius:10px;">
	<div class="px-2">
		<h2 class="mb-4 font-weight-bold" style="font-size:16px !important;">文件和目录权限</h2>
		<p class="mb-2">要能正常使用MetInfo|米拓企业建站系统，需要将几个文件/目录设置为 "可写"。下面是需要设置为"可写" 的目录清单，以及建议的 CHMOD 设置。</p>
		<p>某些主机不允许你设置 CHMOD 777，要用666。先试最高的值，不行的话，再逐步降低该值。</p>
		<ul class="row mt-2 mb-0 list-unstyled inspect-list">
EOT;
foreach ($dirs as $val) {
	if(!$disabled && $val['status']=='danger') $disabled='disabled';
	$thisurl=explode('..',$val['dir']);
	$val['icon']=$val['status']=='success'?'check':'close';
	$val['msg']=$val['status']!='success'?'<div class="text-danger">'.$val['msg'].'</div>':'';
echo <<<EOT
			<li class='col-3'>
				<div class="d-flex align-items-center justify-content-between flex-wrap py-3 border-bottom position-relative pl-3">
					<span>{$thisurl[1]}{$val['msg']}</span>
					<i class="fa-{$val['icon']} font-weight-bold h6 mb-0 text-{$val['status']} mr-2"></i>
				</div>
			</li>
EOT;
}
echo <<<EOT
			<li class="col-12 border-top" style="margin-top:-1px;"></li>
		</ul>
	</div>
</div>
<div class="text-right mt-4">
	<a href="" class="btn btn-light px-4 h6 my-0">重新检查</a>
	<a href="index.php?action=db_setup&db_type={$db_type}" class="btn btn-success px-4 h6 my-0 ml-3 btn-nextprocess {$disabled}">下一步</a>
</div>
<script language="javascript">
	setTimeout(function(){
		$('.btn-nextprocess').hasClass('disabled')?$('.btn-nextprocess').attr('disabled','1'):$('.btn-nextprocess').addClass('disabled');
		$.ajax({
			url: 'index.php?action=apitest',
			type: 'POST',
			success: function(data) {
				$('#api-check').removeClass('text-warning').addClass('text-'+(data=='ok'?'success':'danger')).html(data=='ok'?'API服务器链接正常，此服务器用于下载应用和在线更新程序':'API服务器链接不正常，无法在线下载应用和更新程序 <a href="https://www.mituo.cn/qa/2471.html" target="_blank">[帮助]</a>');
				data=='ok' && !$('.btn-nextprocess').attr('disabled') && $('.btn-nextprocess').removeClass('disabled');
			}
		});
	},500);
</script>
EOT;
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
