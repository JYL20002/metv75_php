/**
 * @Name：底部菜单
 * @Description：
 * @Date：2024-09-05 16:02:38
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
	var that=$.extend(true,{}, admin_module),
		menu_type=[
			{name:METLANG.parameter10,value:0},
			{name:METLANG.parameter8,value:1},
			{name:METLANG.short_message,value:2},
			{name:METLANG.mailbox,value:3},
			{name:METLANG.enterprise_qq,value:5},
			{name:METLANG.open_wechat,value:6}
		];
	M.component.commonList(function(thats,table_order){
		// 底部菜单列表加载
		return {
        	ajax:{
        		dataSrc:function(result){
					var data=[];
					if(result.data){
						var del_url=that.own_name+'c=menu_admin&a=doSaveMenu&submit_type=del&allid=';
						$.each(result.data, function(index, val) {
							var item=[
									M.component.checkall('item',val.id)+M.component.formWidget('no_order-'+val.id,val.no_order),
									M.component.formWidget('name-'+val.id,val.name,'text',1),
									M.component.formWidget({
										name:'url-'+val.id,
										value:val.url,
										type:'textarea',
										required:1,
										rows:3,
										placeholder:placeholderType(val.type),
									}),
									M.component.formWidget({
										type:'select',
										name:'type-'+val.id,
										value:val.type||'',
										data:menu_type
									}),
									M.component.formWidget({
										name:'icon-'+val.id,
										type:'icon',
										value:val.icon
									}),
									M.component.formWidget({
										name:'target-'+val.id,
										type:'select',
										value:val.target,
										data:[
											{name:METLANG.original_window,value:0},
											{name:METLANG.new_window,value:1},
										]
									}),
									M.component.formWidget('but_color-'+val.id,val.but_color,'color'),
									M.component.formWidget('text_color-'+val.id,val.text_color,'color'),
									M.component.formWidget({
										name:'enabled-'+val.id,
										type:'select',
										value:val.enabled,
										data:[
											{name:METLANG.yes,value:1},
											{name:METLANG.no,value:0},
										]
									}),
									M.component.btn('del',{del_url:del_url+val.id})
								];
							data.push(item);
						});
					}
				    return data;
		        }
        	}
    	};
	});
	// 底部菜单列表排序
	M.load('dragsort',function(){
        setTimeout(function(){
        	dragsortFun[that.obj.find('table tbody').attr('data-dragsort_order')]=function(wrapper,item){
	        	wrapper.find('tr [name^="no_order-"]').each(function(index, el) {
	    			$(this).val($(this).parents('tr').index());
	        	});
	        };
        },0);
    });
	// 添加按钮后的回调
	that.obj.find('table [table-addlist]').click(function(event) {
		var $self=$(this);
		setTimeout(function(){
			var $new_tr=$self.parents('table').find('tbody tr:last-child');
			$new_tr.find('[name^="no_order-"]').val($new_tr.index());
			$new_tr.find('[name^="type-"]').change();
		},0);
	});
	// 输入框提示字符
	function placeholderType(type){
		type=parseInt(type||0);
		if(type==0) return METLANG.parameter10;
		if([1,2].includes(type)) return METLANG.parameter8;
		return menu_type.find(value => value.value==type).name+METLANG.account;

	}
	// 链接类型切换
	that.obj.find('table').on('change','select[name^="type-"]',function(event) {
		$(this).parents('tr').find('[name^="url-"]').attr('placeholder',placeholderType($(this).val()));
	});
})();