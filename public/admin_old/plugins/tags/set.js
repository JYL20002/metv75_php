/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
define(function(require, exports, module) {

	var $ = require('jquery');
	var common = require('common');

	function tags_add(put){
		var vput = put.parent("li").parent("ul").prev('.tags_val').find("input");
		var l = vput.attr('data-label');
		var myval = $.trim(put.val());
			myval = myval.replace(l,"");
		if(myval!=''){
			put.parent("li").before('<li class="tags_list"><span>'+myval+'</span><a></a></li>');
			vput.val(function(){
				return put.val()==''?$(this).val():($(this).val()?$(this).val()+l+put.val():put.val());
			});
			put.val('');
		}
	}
	function TagsM(t){
		var d,v,l,list,vhtml,t_html;
		for(p=0;p<t.length;p++){
			d = $(t[p]);
			v = d.find('input').val();
			l = d.find('input').attr('data-label');
			list = v.split(l);
			vhtml = d.find('.fbox').html();
			t_html = '<div class="tags_box">';
			t_html+= '<div class="tags_val">'+vhtml+'</div>';
			t_html+= '<ul>';
		for(i=0;i<list.length;i++){
			if(list[i]!='')t_html+= '<li class="tags_list"><span>'+list[i]+'</span><a></a></li>';
		}
			t_html+= '<li class="tags_tj">';
			t_html+= '<span></span>';
			t_html+= '<input type="text" style="width:30px; display:none;" />';
			t_html+= '</li>';
			t_html+= '</ul>';
			t_html+= '</div>';
			t_html+= '<div class="clear"></div>';
			d.find('.fbox').html(t_html);
		}
	}

	exports.func = function(d){
		d = d.find('.ftype_tags');
		TagsM(d);
	}

	//Click the add button
	$(document).on('click',".tags_box .tags_tj span",function(){
		var put = $(this).next();
		if(put.is(':hidden')){
			put.show().focus();
		}else{
			tags_add(put);
			put.show().focus();
		}
	});
	$(document).on('focusout',".tags_box .tags_tj input",function(){
		if($(this).val()==''){
			$(this).hide();
		}else{
			tags_add($(this));
			$(this).hide();
		}
	});
	//Automatically increase the width of the input box when entering text
	$(document).on('keyup','.tags_box .tags_tj input',function(){
		var current=$(this).val().replace(/[^\u0000-\u00ff]/g,"aa").length;
		var size=current;
		var wd = size*8;
			wd = wd+25;
		$(this).css("width",wd+'px');
	});
	//Enter to increase
	$(document).on('keypress',".tags_box .tags_tj input",function(e){
		var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
		if (keyCode == 13 ){
			tags_add($(this));
			return false;
		}else{
			return true;
		}
	});
	//delete label
	$(document).on('click',".tags_box .tags_list a",function(){
		var kl = $(this).parent('li').index();
		var dm = $(this).parent("li").parent("ul").prev('.tags_val').find("input");
		var l = dm.attr('data-label');
		dm.val(function(){
			var v = $(this).val(),vn='';
				v = v.split(l);
			v=$.grep(v, function(n) {return $.trim(n).length > 0;});
			for(var i=0;i<v.length;i++){
				if(i!=kl&&v[i]!=''){
					vn += l+v[i];
				}
			}
			vn=vn.substring(1);
			return vn;
		});
		$(this).parent("li").remove();
	});
});
