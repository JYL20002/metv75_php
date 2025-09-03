/**
 * 后台登录页面
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
    var start_weixin_floop=0;
    // 切换登录方式
    $('.btn-change-login-method .item').click(function(){
        var method=$(this).data('method');
        $(this).addClass("hide").siblings().removeClass("hide");
        $('.login-method').addClass('hide');
        $(`.login-method[data-method="${method}"]`).removeClass('hide');

        if(method=='weixin' && !start_weixin_floop){
            start_weixin_floop=1;
            // 扫描绑定微信二维码后轮询
            var $qrcode_wrapper=$('.qrcode-wrapper');
            if($qrcode_wrapper.find('img').attr('src')){
                setTimeout(()=>{
                    var has_login=0,
                        login_code=$qrcode_wrapper.data('login_code');
                    interval({
                        delay:2000,
                        true_val:function(){
                            var ajax_ok=!has_login && $qrcode_wrapper.is(':visible');
                            ajax_ok && M.ajax({
                                url:`${M.url.adminurl}n=login&c=login&a=doGetWxStatus`,
                                data:{
                                    login_code,
                                    submit_type:1,
                                },
                                success:res=>{
                                    if(res.data){
                                        has_login=res.data;
                                        metAjaxFun({
                                            result: res,
                                            true_fun: ()=>{
                                                setTimeout(()=>{
                                                    var referrer=$('.met-login-form [name="referrer"]').val();
                                                    if(referrer.indexOf(M.url.admin)>=0){
                                                        var referrer_n=referrer.match(/\?n=(\w+)/)||referrer.match(/&n=(\w+)/);
                                                        if(referrer_n && referrer_n[1]!='ui_set'){
                                                            setCookie('met_auths',1,'',0);
                                                            location.href=referrer;
                                                            return;
                                                        }
                                                        if(top!=self){
                                                            top.window.location.reload();
                                                            return;
                                                        }
                                                    }
                                                    location.href=M.url.admin+'?lang='+has_login.admin_lang+'&n=ui_set';
                                                },1000);
                                            }
                                        });
                                    }
                                }
                            });
                            return has_login;
                        },
                    });
                },2000);
            }
        }
    });
})();