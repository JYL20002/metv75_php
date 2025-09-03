/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
define(function(require, exports, module) {

	var $ = require('jquery');
	var common = require('common');
	require('third/time/jquery.datetimepicker.min.css');
	require('third/time/jquery.datetimepicker');
	exports.func = function(d){
		d = d.find('.ftype_day .fbox input');
		d.each(function(){
			$(this).datetimepicker({
				lang:'ch',
				timepicker:$(this).attr("data-day-type")==2?true:false,
				format:$(this).attr("data-day-type")==2?'Y-m-d H:i:s':'Y-m-d'
			});
		});
	}
});