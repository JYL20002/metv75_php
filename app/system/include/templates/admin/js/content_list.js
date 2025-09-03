/**
 * 内容列表功能
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
	// 产品、新闻、图片、下载模块列表加载
	M.component.contentList=function(){
		// 内容列表-拖拽排序
		M.load('dragsort',function(){
			interval({
                true_val:(time)=>{
                    return dragsortFun;
                },true_fun:(time)=>{
                    if(!dragsortFun['#content-sort-list']) dragsortFun['#content-sort-list']=function(wrapper){
						var max=0,min=0;
						wrapper.find('tr [name*="no_order-"]').each(function(index, el) {
							var val=$(this).val();
							if(val>max) max=val;
							if(val<max) min=val;
							setTimeout(()=>{
								$(this).val(Math.max(max-index,min));
							},100);
						});
					};
                }
            });
		});
		M.component.commonList(function(that,table_order){
			M.load('form',function(){
				formSaveCallback(that.obj.find('form').attr('data-validate_order'),{
					true_fun: function(result) {
						result.html_res && contentCreateHtml(result.html_res);
					}
				});
			});
			// 内容列表删除-点击底部删除勾选按钮
			that.obj.on('click','table.dataTable tfoot .btn-content-list-del',function(){
				setTimeout(()=>{
					const allid=$(this).parents('form').find('[name="allid"]').val()||'',
						class1=$(this).parents('form').find('[name="class1"]').val()||'',
						class2=$(this).parents('form').find('[name="class2"]').val()||'',
						class3=$(this).parents('form').find('[name="class3"]').val()||'';
					$(this).attr('data-url',$(this).parents('form').attr('action')+`&class1=${class1}&class2=${class2}&class3=${class3}&submit_type=del&allid=${allid}`);
				},0);
			});
			// 内容列表删除-弹框参数
			that.obj.on('click','table.dataTable tfoot .btn-content-list-del,table.dataTable tbody [table-delete]',function(){
				setTimeout(()=>{
					$('.alertify .dialog.confirm>div').find('nav .ok').html(METLANG.thoroughlyDeleting).removeClass('btn-primary').addClass('btn-light-danger border-trans').after(`<button class="ok btn btn-primary btn-del-content-recycle" data-url="${$(this).attr('data-url')}&recycle=1">${METLANG.putIntoRecycle}</button>`);
					setTimeout(() => {
						$('.alertify .dialog nav .ok').blur();
					}, 100);
				},100);
			});
			// 列表内容放入回收站
			$(document).on('click','.alertify .btn-del-content-recycle',function(){
			    var url=$(this).attr('data-url');
			    that.obj.find('form').removeAttr('data-submited');
			    M.ajax({
			        url,
			        success:res=>{
			            that.obj.find('table.dataTable').tabelAjaxFun(res);
			        }
			    });
			    $('.alertify').remove();
			});
			var edit_dataurl=`${that.module}/edit/?c=${that.module}_admin&a=doeditor&id=`,
				content_history_dataurl=`history/list/?mod=${that.module}&id=`;
			return {
				ajax:{
	        		dataSrc:function(result){
						var data=[];
						if(result.data){
							$.each(result.data, function(index, val) {
								var item=[],
									status='';
								if(parseInt(val.displaytype)<0) status+='<span class="badge text-grey font-weight-normal m-1 border">草稿</span>';
								if(parseInt(val.com_ok)) status+='<span class="badge font-weight-normal m-1 badge-success">'+METLANG.recom+'</span>';
								if(parseInt(val.top_ok)) status+='<span class="badge font-weight-normal m-1 badge-info">'+METLANG.top+'</span>';
								if(!parseInt(val.displaytype)) status+='<span class="badge font-weight-normal m-1 badge-secondary">'+METLANG.displaytype2+'</span>';
								if(parseInt(val.addtype)) status+='<span class="badge font-weight-normal m-1 badge-secondary">'+METLANG.timedrelease+'</span>';
								item.push(M.component.checkall('item',val.id));
								if(that.module=='img'||that.module=='product'){
									item.push('<a href="'+val.url+'" target="_blank" class="media align-items-center"><img src="'+val.imgurl+'" width="100" class="mr-2"/><div class="media-body">'+val.title+'</div></a>');
								}else{
									item.push('<a href="'+val.url+'" target="_blank">'+val.title+'</a>');
								}
								item.push(val.hits);
								item.push(val.updatetime);
								item.push(status);
								item.push(M.component.formWidget('no_order-'+val.id,val.no_order,'number',1,0,'text-center'));
								item.push(`<button type="button" class="btn px-2 btn-primary my-1 mr-1" data-toggle="modal" data-target=".${that.module}-details-modal" data-modal-title="${METLANG.editor}" data-modal-size="xl" data-modal-url="${edit_dataurl+val.id}" data-modal-fullheight="1">${METLANG.editor}</button>
								<button type="button" class="btn px-2 btn-default my-1 mr-1" data-toggle="modal" data-target=".content-history-modal" data-modal-size="xl" data-modal-url="${content_history_dataurl+val.id}" data-modal-fullheight="1">历史记录</button>
								${M.component.btn('del',{del_url:val.del_url,other_class:'my-1 px-2'})}`);
								data.push(item);
							});
						}
					    return data;
			        }
	        	},
	        	ordering:true,
	        	order:[],
	        	columnDefs:[
	        		{targets:[0,1,4,5,6],orderable:false}
	        	]
			};
		});
	};
	var content_show_item='.content-show .content-show-item';
	// 内容列表-栏目选择后刷新添加按钮地址
	$(document).on('change', content_show_item+' .column-select[data-plugin="select-linkage"] select', function(event) {
		var $parent=$(this).parents('.column-select'),
			class1=$parent.find('[name="class1"]').val()||'',
			class2=$parent.find('[name="class2"]').val()||'',
			class3=$parent.find('[name="class3"]').val()||'',
			$btn_content_list_add=$(this).parents('.content-show-item').find('.btn-content-list-add'),
			url=$btn_content_list_add.attr('data-modal-url').split('class1=')[0]+'class1='+class1+'&class2='+class2+'&class3='+class3;
		$btn_content_list_add.attr({'data-modal-url':url});
	});
	// 模块列表复制下拉列表更新
	$(document).on('click', '.content-show .content-show-item .contentlist-copy-langlist a[data-val]', function(event) {
		var $contentlist_copy=$(this).parents('table').find('.contentlist-copy'),
			tolang=$(this).data('val');
		M.ajax({
			url:M.url.admin+'?n=manage&index&a=doGetLangColumn',
			data:{tolang:tolang,module:$(this).parents('table').find('[name="module"]').val()}
		},function(result){
			var html='',
				columnList=function(json,p_key,c_key,level,pre_value){
					var pre_value=pre_value||'';
					level++;
					$.each(json, function(index, val) {
						if(val[p_key].value!=""){
							var value=pre_value?(pre_value+'-'+val[p_key].value):val[p_key].value;
							if(val[c_key]){
								html+='<div class="dropdown dropright dropdown-submenu">'
										+'<a href="javascript:;" class="dropdown-item px-3 dropdown-toggle" onClick="dropdownMenuPosition(this)" data-toggle="dropdown">'+val[p_key].name+'</a>'
										+'<div class="dropdown-menu">';
								columnList(val[c_key],level==1?'n':'s',level==1?'a':'ss',level,value);
								html+='</div></div>';
							}else{
								html+='<a href="javascript:;" class="dropdown-item px-3" table-delete data-submit_type="copy_tolang" data-val="'+value+'">'+val[p_key].name+'</a>';
							}
						}
					});
				};
			columnList(result.citylist,'p','c',0);
			$contentlist_copy.html(html).parents('.dropup').removeClass('hide').find('>.dropdown-toggle').click();
			$contentlist_copy.parents('table').find('[name="tolang"]').val(tolang);
		});
	});
	// 选择复制栏目列表失焦后隐藏
	$(document).on('blur', content_show_item+' table .contentlist-copy-list a,'+content_show_item+' table .contentlist-copy-list button', function(event) {
		var $list=$(this).parents('.contentlist-copy-list').find('.contentlist-copy');
		setTimeout(function(){
			if($list.is(':hidden')) $list.parent().addClass('hide');
		},100);
	});
	// 内容列表-移动、复制
	$(document).on('click', content_show_item+' table .contentlist-move a[data-val],'+content_show_item+' table .contentlist-copy a[data-val]', function(event) {
		var $form=$(this).parents('form'),
			value=$(this).data('val');
		if(!$form.find('[type="hidden"][name="columnid"]').length) $form.append(M.component.formWidget('columnid',''));
		$form.find('[type="hidden"][name="columnid"]').val(value);
	});
	// 内容详情/添加弹框-内容加载完后的回调
	M.component.modal_call_status.content_list=[];
	M.component.modal_options['.about-details-modal']=
	M.component.modal_options['.news-details-modal']=
	M.component.modal_options['.product-details-modal']=
	M.component.modal_options['.img-details-modal']=
	M.component.modal_options['.download-details-modal']=
	M.component.modal_options['.job-position-details-modal']=
	M.component.modal_options['.news-add-modal']=
	M.component.modal_options['.product-add-modal']=
	M.component.modal_options['.img-add-modal']=
	M.component.modal_options['.download-add-modal']=
	M.component.modal_options['.job-add-modal']={
		modalOtherclass:'content-details-modal',
		modalFullheight: 1,
		modalHeight100: 1,
		modalFooterclass:'text-right',
		modalFooter:`<button type="button" class="btn btn-outline-primary mr-2 btn-save-draft">保存为草稿</button>`,
		modalOktext:`发布`,
		before:(key)=>{
			var $btn_ok=$('.btn[data-ok]',key);
			if(!['.about-details-modal'].includes(key) && !$btn_ok.hasClass('btn-publish')) $btn_ok.removeClass('btn-primary').addClass('btn-success btn-publish');
		},
		callback:function(key,data,refresh){
			var $form=$('.modal-body form',key),
				validate_order=$form.attr('data-validate_order');
			$form.find('.btn-content-para-refresh').click();
			$form.find('.content-details-relationlist').length && getRelationlist($form.find('.content-details-relationlist'));
			productTab($form);
			if(!M.component.modal_call_status.content_list[validate_order]){
				M.component.modal_call_status.content_list[validate_order]=1;
				// 弹框内表单提交后的回调
				M.load('form',function(){
					formSaveCallback(validate_order,{
				        true_fun: function(result) {
				        	// 静态页面更新
				        	result.html_res && contentCreateHtml(result.html_res);
				        	if(key=='.about-details-modal') return;
				        	// 添加内容后跳转到对应内容列表
				        	var $this_form=$(key+' .modal-body form'),
								module=$this_form.attr('data-validate_order').split('-')[0].substr(1),
								class1=parseInt($this_form.find('[name="class1"]').val()),
								class2=parseInt($this_form.find('[name="class2"]').val())||'',
								class3=parseInt($this_form.find('[name="class3"]').val())||'';
							var info=module+'|'+class1+'|'+class2+'|'+class3,
								data_path=module=='job'?module+'/'+class1+'|'+class2+'|'+class3:module+'/list',
								$content_show_item=$((M.is_admin?'.metadmin-content[data-page="manage"]':'.pageset-nav-modal .nav-modal-item[data-path="manage"]')+':visible .content-show-item[data-path="'+data_path+'"]'),
								$column_select=$content_show_item.find('.column-select')
								table_select_class1=parseInt($column_select.find('[name="class1"]').val())||'',
								table_select_class2=parseInt($column_select.find('[name="class2"]').val())||'',
								table_select_class3=parseInt($column_select.find('[name="class3"]').val())||'';
							// 传统后台已存在显示的目标模块、可视化内容管理页面打开的情况下已存在显示的目标模块
							if((M.is_admin || $('.pageset-nav-modal.show').length) && $content_show_item.length){
								var reload=(table_select_class1!=''?table_select_class1!=class1:0) || (table_select_class2!=''?table_select_class2!=class2:0) || (table_select_class3!=''?table_select_class3!=class3:0);
								!reload && (reload=key.indexOf('add')>0);
								if(reload){// 目标模块所选栏目和当前栏目不一致时，列表更新到目标栏目第一页
									if($('#content-view-column').is(':visible')){// 按栏目显示的模式
										$('#content-view-column .column-view-list li a[data-info="'+info+'"]').click();
									}else{// 按模块显示的模式，只切换栏目id
										$content_show_item.tabelsearchReset(function(){
											$column_select.find('[name="class1"]').val()!=class1 && $column_select.find('[name="class1"]').val(class1).change();
											$column_select.find('[name="class2"]').val()!=class2 && $column_select.find('[name="class2"]').val(class2).change();
										});
										$column_select.find('[name="class3"]').val()!=class3 && $column_select.find('[name="class3"]').val(class3).change();
									}
	        					}else{// 目标模块所选栏目和当前栏目一致时，列表更新当前页
	        						var $table = $content_show_item.find('.dataTable:eq(0)'),
	        							datatable_order=$table.attr('data-datatable_order');
                                    $table.length && datatable[datatable_order].row().draw(false);
	        					}
	        				}
							if($('.content-history-modal').length && $('.content-history-modal').is(':visible')) datatable['#content-history-list'].row().draw(false);
				        }
				    });
				});
				// 内容详情/弹框-保存为草稿
				$('.btn-save-draft',key).click(function(){
					$('.modal-body form',key).find('[name="displaytype"]').val(-1).prop('checked',true);
					$('.btn[data-ok]',key).click();
				});
			}
		}
	};
	// 内容详情/添加弹框-关闭后
	$(document).on('hidden.bs.modal','.content-details-modal',function(){
		$('.modal-body .modal-html',this).html('');
	});
	// 静态页面生成弹框
	M.component.modal_options['.html-update-modal']={
		modalTitle: METLANG.indexhtm,
		modalBody:'<div class="html-loading"></div>',
		modalRefresh:0,
		modalFullheight: 1,
		modalHeight100: 1,
		modalFooterok:0,
		modalStyle:'z-index:1703;'
	};
	// 内容详情页-参数刷新
	$(document).on('click', '.btn-content-para-refresh', function(event) {
		var $self=$(this),
			$paralist=$(this).parents('form').find('.content-details-paralist'),
			para_type={
				1:'text',
				2:'select',
				3:'textarea',
				4:'checkbox',
				5:'file',
				6:'radio',
				10:'text'
			};
		$(this).attr({disabled:''}).find('i').addClass('fa-spin');
		M.ajax({
			url: $paralist.attr('data-url')
		},function(result){
			metAjaxFun({result:result,true_fun:function(){
				var html='';
				$.each(result.data, function(index, val) {
					val.value=val.value||'';
					val.type=parseInt(val.type);
					var name='para-'+val.id,
						option={
							type:para_type[val.type],
							name:name,
							value:val.value,
							label:val.name,
							dl:1
						};
					if($.inArray(val.type, [2,4,6])>=0){
						option.data=[];
						val.list && $.each(val.list, function(index1, val1) {
							option.data.push({
								name:val1.value,
								value:val1.id
							});
							val.type==6 && option.value=='' && (option.value=val1.id);
						});
					}
					val.type==5 && (option.accept='file');
					html+=M.component.formWidget(option);
				});
				if(!html) html='<dl><dt>'+METLANG.csvnodata+'</dt></dl>';
				$paralist.html(html).metCommon();
				$self.removeAttr('disabled').find('i').removeClass('fa-spin');
			}});
		});
	});
	// 内容详情页-自动获取添加时间
	$(document).on('click', 'form [type="radio"][name="addtype"]', function(event) {
		var $addtime=$(this).parents('.form-group').find('[name="addtime"]');
		parseInt($(this).val())==2?$addtime.focus():$addtime.val('');
	})
    // 内容详情页-栏目改变后，参数也改变
    $(document).on('change','.modal .content-details-column select',function(){
    	var $form=$(this).parents('form').eq(0);
    		$paralist=$form.find('.content-details-paralist'),
    		$column=$(this).parents('.content-details-column');
		// 产品栏目数据更新
		if($(this).parents('.product-details-modal,.product-add-modal').length){
			var classother='';
	    	$.each($(this).val(), function(index, val) {
	    		!index && (classother+='|');
	    		classother+='-'+val+'-|';
	    		if(!index){
	    			val=val.split('-');
	    			$column.find('[name="class1"]').val(val[0]);
	    			$column.find('[name="class2"]').val(val[1]);
	    			$column.find('[name="class3"]').val(val[2]);
	    		}
	    	});
	    	$(this).val().length==1 && (classother='');
	    	$column.find('[name="classother"]').val(classother);
	    	// 更新选项卡显示
	    	productTab($form);
		}
		// 更新参数
		if($paralist.length){
			var class1=class2=class3='',
				paralist_url=$paralist.attr('data-url').split('&class1')[0];
	    	setTimeout(function(){
				class1=parseInt($column.find('[name="class1"]').val())||'';
				class2=parseInt($column.find('[name="class2"]').val())||'';
				class3=parseInt($column.find('[name="class3"]').val())||'';
				$paralist.attr({'data-url':paralist_url+'&class1='+class1+'&class2='+class2+'&class3='+class3});
				$form.find('.btn-content-para-refresh').click();
				// 更新选项卡设置按钮地址
				var $btn_product_details_tabset=$form.find('button[data-target=".product-details-tabset-modal"]');
				if($btn_product_details_tabset.length){
					var classnow=class3?class3:(class2?class2:class1);
					$btn_product_details_tabset.attr({'data-modal-url':$btn_product_details_tabset.attr('data-modal-url').split('&classnow=')[0]+'&classnow='+classnow});
				}
	    	},0);
		}
    });
    // 产品详情页tab选项卡信息更新
    function productTab(form){
    	var $navtab=form.find('.product-details-navtab'),
    		$content=form.find('.product-details-content');
		if(!$navtab.length) return;
    	M.ajax({
			url: $navtab.data('url'),
			data: {
				class1: form.find('[name="class1"]').val(),
				class2: form.find('[name="class2"]').val(),
				class3: form.find('[name="class3"]').val()
			}
		},function(result){
			result.tab_name=result.tab_name.split('|');
			result.tab_num=parseInt(result.tab_num);
			$navtab.find('a').hide().each(function(index, el) {
				$(this).html(result.tab_name[index]);
			});
			$navtab.find('a:lt('+result.tab_num+')').show();
			$content.find('.tab-pane').removeClass('show active').parent().removeClass('hide');
			$navtab.find('a:eq(0)').click();
			$content.find('.tab-pane:eq(0)').addClass('show active');
		});
    }
    // 产品详情页选项卡设置框回调
    M.component.modal_options['.product-details-tabset-modal']={
		callback:function(key){
			M.load('form',function(){
				setTimeout(function(){
					formSaveCallback($(key+' .modal-body form').attr('data-validate_order'),{
				        true_fun: function(result) {
				        	productTab($('.product-details-modal,.product-add-modal').find('form'));
			        	}
		        	});
	        	},100);
        	});
		}
	};
	// 内容详情页-获取关联内容
	function getRelationlist(obj){
		M.ajax({
			url: obj.attr('data-url')
		},function(result){
			metAjaxFun({result:result,true_fun:function(){
				var list=[];
				result.data.length && result.data.map(item=>{
					list.push({
						id:parseInt(item.relation_id),
						module:parseInt(item.relation_module),
						relation_class:parseInt(item.relation_class),
						relation_class_name:item.relation_class_name,
						title:item.content.title,
						url:item.content.url
					});
				});
				obj.html(`<dl><dd></dd></dl><textarea name="relations" hidden>${JSON.stringify(list)}</textarea>`);
				renderRelationlist(obj);
			}});
		});
	};
	// 内容详情页-渲染关联内容列表
	function renderRelationlist(obj){
		var list=JSON.parse(obj.find('textarea').val()),
			new_list={},
			html='';
		$.each(list, function(index, val) {
			if(!new_list[val.relation_class]){
				new_list[val.relation_class]={
					relation_class_name:val.relation_class_name,
					module:val.module,
					list:[]
				};
			}
			new_list[val.relation_class].list.push(val);
		});
		$.each(new_list, function(index, val) {
			html+=`<li class="parents border-top pt-3">
				<h3 class="h6">${val.relation_class_name}</h3>
				<ul>`;
			$.each(val.list, function(index1, val1) {
				html+=`<li class="border-top p-2 d-flex align-items-center">
					<a href="${val1.url}" target="_blank" class="media-body pr-3">${val1.title}</a>
					<div><a href="javascript:;" class="text-content h6 mb-0 p-2 relation-del" data-info="${val1.id}|${val1.module}"><i class="fa-close"></i></a></div>
				</li>`;
			});
			html+=`</ul></li>`;
		});
		html=html?`<ul class="">${html}</ul>`:METLANG.csvnodata;
		obj.find('dd')[0].innerHTML=html;
		obj.scrollTop(0);
	}
	$(document).on('render','.content-details-relationlist',function(){
		renderRelationlist($(this));
	});
	// 内容详情页-关联内容列表-取消关联
	$(document).on('click','.content-details-relationlist .relation-del',function(){
		var $parents=$(this).parents('.content-details-relationlist'),
			$self=$(this);
		M.load('alertify',()=>{
			alertify.confirm('确定要取消关联吗？', function (ev) {
				var list=JSON.parse($parents.find('textarea').val()),
					info=$self.data('info').split('|'),
					del_index=0;
				info[0]=parseInt(info[0]);
				info[1]=parseInt(info[1]);
				list.map((item,index)=>{
					if(parseInt(item.id)==info[0] && parseInt(item.module)==info[1]){
						del_index=index;
						return false;
					}
				});
				list.splice(del_index,1);
				$parents.find('textarea').val(JSON.stringify(list));
				$self.parents('li').eq(0).remove();
			});
		});
	});
	// 历史记录弹框
	var $last_script=$('body>script').eq($('body>script').length-1);
	M.component.modal_options['.content-history-modal']={
		modalTitle: '历史记录',
		modalFullheight: 1,
		modalHeight100: 1,
		modalOktext:'',
		modalNotext:METLANG.close,
		modalStyle:'z-index:1701;',
		callback:function(key){
			if(!$last_script.next('.content-history-modal').length) $last_script.after($(key));
		}
	};
	// 历史记录列表
	TEMPLOADFUNS['history/list'] = function() {
		var thats=$.extend(true,{}, admin_module),
			table = thats.obj.find('#content-history-list'),
			module=thats.obj.find('[name="mod"]').val(),
			aid=thats.obj.find('[name="aid"]').val(),
			view_url=`history/view/?n=history&c=history&a=doView&mod=${module}&aid=${aid}&id=`,
			restore_url=`${module}/${module=='job'?'position_':''}edit/?c=${module}_admin&a=doeditor&id=${aid}&hid=`,
			del_url=`${M.url.adminurl}n=history&c=history&a=doDel&aid=${aid}&allid=`;
		table.attr({ 'data-table-ajaxurl': table.data('ajaxurl') })
		datatable_option['#content-history-list'] = {
			ajax: {
				dataSrc: function(result) {
					var data=[];
					if(result.data){
                        data=result.data.map((val,index)=>{
							return [
                                M.component.checkall('item',val.id),
                                val[module=='job'?'position':'title'],
                                val.record_time,
                                `<button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target=".content-history-view-modal" data-modal-url="${view_url+val.id}">${METLANG.View}</button>
								<button type="button" class="btn btn-primary my-1 mr-1" data-toggle="modal" data-target=".${module}${module=='job'?'-position':''}-details-modal" data-modal-title="历史记录恢复" data-modal-size="xl" data-modal-url="${restore_url+val.id}" data-modal-fullheight="1">恢复</button>
								${M.component.btn('del',{del_url:del_url+val.id,other_class:'del_item'})}`
                            ];
						});
					}
				    return data;
				}
			}
		}
		thats.obj.metDataTable();
    };
	// 历史记录详情
	M.component.modal_options['.content-history-view-modal']={
		modalTitle: '历史记录详情',
		modalSize:'xl',
		modalFullheight: 1,
		modalHeight100: 1,
		modalOktext:'',
		modalNotext:METLANG.close,
		modalStyle:'z-index:1701;',
		callback:function(key){
		}
	};
})();
// 下载模块文件上传后文件大小值更新
function downloadFilesize(obj){
	var $file=obj.parents('.file-input').find('.file-preview-thumbnails .file-preview-frame:last-child .file-preview-view');
	obj.parents('form').find('[name="filesize"]').val($file.attr('data-size')||'');
}
// 生成静态页
function contentCreateHtml(url){
	$('.btn-admin-common-modal,.btn-pageset-common-modal').attr({'data-target':'.html-update-modal'}).click();
	setTimeout(function(){
		var $modal_body=$('.html-update-modal .modal-body'),
			$html_loading = $modal_body.find(".html-loading"),
			handle=(other_url=url)=>{
				$html_loading.html(
					`<div class="html-list"></div><p style="font-size:16px;" class="createing mb-0">${METLANG.indexhtm}${METLANG.ing}...</p>`
				);
				var $html_list = $html_loading.find('.html-list');
				M.ajax({
						url: other_url
					},
					function (result) {
						var modal_body_h = $modal_body.outerHeight(),
							loop_load=()=>{
								setTimeout(()=>{
									M.ajax({
										url:result.data.check_url,
										success:(res)=>{
											if(res.status && (res.suc_num+res.err_num)>=$html_list.find('p').length){
												let html = res.suc.map((item,index)=>{
														return `<p style="color:green">(${index+1}/${result.data.total}) <a href="${M.weburl}${item.filename}" target="_blank">${item.filename}</a> ${METLANG.physicalgenok}</p>`;
													}).join('')+res.err.map((item,index)=>{
														return `<p style="color:red">(${res.suc_num+index+1}/${result.data.total}) ${item.filename} ${METLANG.html_createfail_v6}</p>`;
													}).join('');
												$html_list.html(html);
												if(res.status==1){
													$html_loading.find(".createing").html(`${METLANG.static_page_success}
													<br><span class='text-success'>${METLANG.physicalgenok}${res.suc_num}${METLANG.page}</span>
													<br><span class='text-danger'>${METLANG.html_createfail_v6}${res.err_num}${METLANG.page}${res.err_num?` <button type="button" class='btn btn-primary html-link-reset-fail'>重新生成</button>`:''}</span>`);
													res.err_num && $html_loading.find('.html-link-reset-fail').click(function () {
														handle(result.data.retry_url);
													});
												}else{
													loop_load();
												}
												var scrolltop = $html_list.outerHeight() - modal_body_h+120;
												scrolltop && $modal_body.scrollTop(scrolltop);
											}
										}
									});
								},1000);
							}
						M.ajax({
							url:result.data.callback_url,
							no_error_msg:1
						});
						loop_load();
					}
				);
			};
		handle();
	},0);
}