/**
 * 反馈、招聘系统设置
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
	M.component.modal_call_status.form_sys_set=[];
	M.load('form',function(){
		var that=$.extend(true,{}, admin_module),
			formSaveCallbackItem=function(){
				var thats=$.extend(true,{}, admin_module),
					order=thats.obj.find('[data-validate_order]').attr('data-validate_order');
				if(!M.component.modal_call_status.form_sys_set[order]){
					M.component.modal_call_status.form_sys_set[order]=1;
					formSaveCallback(order,{
			            true_fun: function(result) {
			                $($(order).parents('.content-show-item').find('.met-headtab a[data-url*="'+thats.module+'/list/"]').attr('href')).removeAttr('data-loaded');
							result.html_res && contentCreateHtml(result.html_res);
			            }
			        });
					// 监听邮箱、手机号码输入框
					thats.obj.on('input', 'form .token-input', function(e) {
						var val=$(this).val();
						if(val.indexOf('|')>=0 && val.length>1){
							var $el=$(this).parent().find('[data-plugin="tokenfield"]'),
								old_val=$el.val();
							$el.tokenfield('setTokens', `${old_val?`${old_val}|`:''}${val}`);
							$(this).val('');
						}
					});
				}
			};
		formSaveCallbackItem();
		// 表单系统设置保存后销毁表单信息列表的已加载状态，以使表单信息列表页面重新加载
		['job','feedback','message'].forEach((value, index, array) => {
			TEMPLOADFUNS[value+'/set']=formSaveCallbackItem;
		});
	});
	// 招聘设置
	$(document).on('click', '#job-set-form input[name="met_cv_emtype"]', function(event) {
		var $dl=$(this).parents('form').find('input[name="met_cv_to"]').parents('dl');
		if(parseInt($(this).val())){
			$dl.addClass('hide');
		}else{
			$dl.removeClass('hide');
		}
	});
})();