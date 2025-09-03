/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
define(function(require, exports, module) {

	var common = require('common');
	require('third/minicolors/jquery.minicolors.min.css');
	require('third/minicolors/jquery.minicolors.min');
	exports.func = function(d){
		d.find('.ftype_color .fbox input').minicolors();
	}
});