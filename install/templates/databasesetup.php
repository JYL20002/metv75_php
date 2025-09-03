<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
echo <<<EOT
<p>检查你的数据库设置情况，请在相应栏目仔细输入配置内容。</p>
<form method="post" class="p-4 mt-4" style="border:1px solid #D2EEF9;border-radius:10px;">
	<input name="setup" type="hidden" value="1">
	<input name="db_type" type="hidden" value="{$db_type}">
	<div class="py-2">
		<h1 class="font-weight-bold h6 mb-4">数据库信息</h1>
		<div class="form-group row align-items-center mb-4">
			<label class="col-form-label px-3">数据表前缀</label>
			<div class="media-body px-3">
				<input type="text" name="db_prefix" value="met_" required class="form-control d-inline-block">
				<span class="text-muted ml-2">例如：met_ 请不要留空，且使用“_”结尾</span>
			</div>
		</div>
		<div class="form-group row align-items-center mb-4">
			<label class="col-form-label px-3">数据库连接地址</label>
			<div class="media-body px-3">
				<input type="text" name="db_host" value="localhost" required class="form-control d-inline-block">
				<span class="text-muted ml-2">一般不需要更改，参考主机或服务器MYSQL控制面板</span>
			</div>
		</div>
		<div class="form-group row align-items-center mb-4">
			<label class="col-form-label px-3">数据库名</label>
			<div class="media-body px-3">
				<input type="text" name="db_name" required class="form-control d-inline-block">
				<span class="text-muted ml-2">例如'met'或'my_met',请确保用字母开头</span>
			</div>
		</div>
		<div class="form-group row align-items-center mb-4">
			<label class="col-form-label px-3">数据库用户名</label>
			<div class="media-body px-3">
				<input type="text" name="db_username" value="root" required class="form-control d-inline-block">
			</div>
		</div>
		<div class="form-group row align-items-center mb-4">
			<label class="col-form-label px-3">数据库密码</label>
			<div class="media-body px-3">
				<input type="password" name="db_pass" class="form-control d-inline-block">
			</div>
		</div>
		<div class="form-group row align-items-center mb-4">
			<label class="col-form-label px-3">前台语言</label>
			<div class="media-body px-3">
				<div class="custom-control custom-checkbox custom-control-inline">
					<input type="checkbox" id="cndata" name='cndata' value='yes' checked class="custom-control-input">
					<label class="custom-control-label" for="cndata">安装中文语言包</label>
					<div class="custom-control-bd w-100 h-100 position-absolute"></div>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input type="checkbox" id="endata" name='endata' value='yes' checked class="custom-control-input">
					<label class="custom-control-label" for="endata">安装英文语言包</label>
					<div class="custom-control-bd w-100 h-100 position-absolute"></div>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input type="checkbox" id="showdata" name='showdata' value='yes' checked class="custom-control-input">
					<label class="custom-control-label" for="showdata">安装官方演示数据</label>
					<div class="custom-control-bd w-100 h-100 position-absolute"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-form-label px-3">后台语言</label>
			<div class="media-body px-3">
				<div class="custom-control custom-checkbox custom-control-inline">
					<input type="checkbox" id="admin_cndata" name='admin_cndata' value='yes' checked class="custom-control-input">
					<label class="custom-control-label" for="admin_cndata">安装中文语言包</label>
					<div class="custom-control-bd w-100 h-100 position-absolute"></div>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input type="checkbox" id="admin_endata" name='admin_endata' value='yes' checked class="custom-control-input">
					<label class="custom-control-label" for="admin_endata">安装英文语言包</label>
					<div class="custom-control-bd w-100 h-100 position-absolute"></div>
				</div>
			</div>
		</div>
	</div>
	<button type="submit" id="formsubmit" onClick="formSubmit()" hidden></button>
</form>
<div class="text-right mt-4">
	<button type="submit" class="btn btn-success px-4 h6 my-0 btn-nextprocess" onClick="formsubmit.click()">保存数据库设置并继续</button>
</div>
<script language="javascript">
setTimeout(function(){
	window.formValidate=function(){
		return $('[name="db_prefix"]').val()!='' && $('[name="db_host"]').val()!='' && $('[name="db_name"]').val()!='' && $('[name="db_username"]').val()!='';
	};
	$('form input.form-control').on('change input',function(){
		formValidate()?$('.btn-nextprocess').removeClass('disabled'):$('.btn-nextprocess').addClass('disabled');
	});
	window.formSubmit=function(e){
		if($('.btn-nextprocess').hasClass('disabled')) return false;
		if(formValidate()){
			$('.btn-nextprocess').addClass('disabled').append(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		}else{
			$('.btn-nextprocess').removeClass('disabled');
		}
	};

	//演示数据联动
	$('[name="cndata"]').on('click',function() {
	    damoData();
	})
	$('[name="endata"]').on('click',function() {
	    damoData();
	})

	damoData = function() {
	  if(!$('[name="cndata"]').is(':checked') && !$('[name="endata"]').is(':checked')){
            $('[name="showdata"]').prop('checked',false);
            $('[name="showdata"]').attr("disabled","disabled");
        }else{
	        $('[name="showdata"]').removeAttr("disabled");
        }
	}
},500);
</script>
EOT;
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>