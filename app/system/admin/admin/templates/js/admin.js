/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
    var that = $.extend(true, {}, admin_module)
    $(document).on('click','.member-sys-edit-form .weixin-unbind',function(){
        M.load('alertify',()=>{
            alertify.confirm('是否取消绑定？',  (ev)=>{
                M.ajax({
                    url:`${that.own_name}c=index&a=doUnbind`,
                    success:(result)=>{
                        metAjaxFun({
                            result: result,
                            true_fun: ()=>{
                                $(this).addClass('hide');
                                $(`.member-sys-edit-form [name="weixin_nickname"]`).val('');
                            }
                        });
                    }
                })
            });
        });
    });
    TEMPLOADFUN[that.hash]=function(){
		M.load('form',function(){
            var $form=$('.member-sys-edit-form');
            formSaveCallback($form.attr('data-validate_order'), {
                true_fun: function(result) {
                    if ($form.find(`[name="admin_pass"]`).val()) setTimeout(() => {
                        window.location.href = M.url.admin + '?n=login&c=login&a=dologinout'
                    }, 1000);
                }
            });
            // 扫描绑定微信二维码后轮询
            if($form.find('.qrcode').length && $form.find('.qrcode').attr('src')){
                setTimeout(()=>{
                    var has_login=0;
                    interval({
                        delay:2000,
                        true_val:function(){
                            var ajax_ok=has_login || !$form.length || !$form.is(':visible');
                            !ajax_ok && M.ajax({
                                url:`${that.own_name}c=index&a=doGetOpenid`,
                                success:res=>{
                                    if(res.data){
                                        has_login=1;
                                        M.load('alertify',()=>{
                                            alertify.success('绑定成功');
                                        });
                                    }
                                }
                            });
                            return ajax_ok;
                        },
                        true_fun:function(){
                            has_login && window.location.reload();
                        }
                    });
                },2000);
            }
        });
	}
})()
