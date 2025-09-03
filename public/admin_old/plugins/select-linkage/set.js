/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
define(function(require, exports, module) {

	var $ = require('jquery');
	var common = require('common');
		require('third/select-linkage/jquery.cityselect');
	exports.func = function(d){
		d = d.find('.ftype_select-linkage .fbox');
		d.each(function(){
			var p = $(this).find(".prov").attr("data-checked"),
				c = $(this).find(".city").attr("data-checked"),
				s = $(this).find(".dist").attr("data-checked");
				p = p?p:'';
				c = c?c:undefined;
				s = s?s:undefined;
			var url = $(this).attr('data-selectdburl')?$(this).attr('data-selectdburl'):siteurl+"public/third-party/select-linkage/citydata.min.json";
			$(this).citySelect({url:url,prov:p, city:c, dist:s, nodata:"none"});
		});
	}
});