/**
 * 留言模块
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
	// 留言列表
	M.component.commonList(function(that,table_order){
		M.load('form',function(){
			formSaveCallback(that.obj.find('form').attr('data-validate_order'),{
				true_fun: function(result) {
					result.html_res && contentCreateHtml(result.html_res);
				}
			});
		});
		var edit_dataurl=that.module+'/edit/?c='+that.module+'_admin&a=doview&class1='+that.data.class1+'&id=';
        return {
        	ajax:{
        		dataSrc:function(result){
					var data=[];
					result.data && $.each(result.data, function(index, val) {
						val.readok=parseInt(val.readok);
						val.checkok=parseInt(val.checkok);
						var item=[
								M.component.checkall('item',val.id),
								val.customerid,
								val.name,
								val.tel,
								'<span class="badge font-weight-normal py-2 px-4 badge-pill badge-light-'+(val.readok?'success':'danger')+'">'+METLANG[val.readok?'read':'unread']+'</span>',
								val.email,
								val.checkok?METLANG.yes:METLANG.no,
								val.addtime,
								val.access.name,
								'<button type="button" class="btn px-2 btn-light-primary mr-1" data-toggle="modal" data-target=".'+that.module+'-details-modal" data-modal-title="'+METLANG.View+'" data-modal-size="lg" data-modal-url="'+edit_dataurl+val.id+'" data-modal-fullheight="1" data-modal-tablerefresh="'+table_order+'" data-modal-tablerefresh-type="'+(val.readok?0:1)+'">'+METLANG.View+'</button>'
								+M.component.btn('del',{del_url:val.del_url,other_class:'px-2'})
							];
						data.push(item);
					});
				    return data;
		        }
        	}
        };
    });
})();