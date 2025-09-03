/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
$(function(){
    // 账号安全页面
    if($('.member-profile').length){
        // 邮箱修改、解绑，手机号码修改、解绑、修改密码
        metFormvalidationLoadFun(function(){
            setTimeout(function(){
                $('.safety-modal-email-bind,.safety-modal-email-unbind,.safety-modal-tel-bind,.safety-modal-tel-unbind,.safety-modal-pass').find('form').each(function (index, domEle) {
                    var form_index=$(this).index('form'),
                        $modal=$(this).parents('.modal');
                    validate[form_index].success(function(result,form){
                        $modal.find('.btn[type="submit"]').removeClass('loading');
                        metAjaxFun({
                            result:result,
                            true_fun:function(){
                                $modal.modal('hide');
                                setTimeout(function(){
                                    location.reload();
                                },1000);
                            }
                        });
                    });
                });
            },600);
            $('.safety-modal-email-bind,.safety-modal-email-unbind,.safety-modal-tel-bind,.safety-modal-tel-unbind,.safety-modal-pass').find('form').submit(function(){
                $('.btn[type="submit"]',this).addClass('loading');
            });
        });
        // 手机绑定-获取短信验证码
        $('.safety-modal-tel-bind .phone-code').click(function(){
            var $self = $(this),
                $tel = $('.safety-modal-tel-bind input[name="tel"]'),
                $code = $('.safety-modal-tel-bind input[name="code"]');
            if($tel.val()==''||!/^1[0-9]{10}$/.test($tel.val())){
                $tel.focus();
                $.include(M['plugin']['alertify'],function(){
                    alertify.error($tel.data('fv-integer-message'));
                });
            }else if($code.val()==''){
                $code.focus();
                $.include(M['plugin']['alertify'],function(){
                    alertify.error($code.data('fv-notempty-message'));
                });
            }else{
                $.ajax({
                   url: $(this).data('url'),
                   type: 'POST',
                   data:{tel:$tel.val(),code:$code.val(),random:$('.safety-modal-tel-bind [name="random"]').val()},
                   dataType:'json',
                   success: function(result){
                        metAjaxFun({
                            result:result,
                            true_fun:function(){
                                $self.attr('disabled',true);
                                $self.html($self.data('retxt') + ' <span class="badge"></span>');
                                identifyTime($self,90);
                            }
                        });
                   }
                });
            }
        });
        // 微信绑定
        if($('.member-profile .btn-weixinadd').length){
            var user_weixin_bind_code='';
            $('.member-profile .btn-weixinadd').click(function(event) {
                var delay=0;
                $.ajax({
                    url: M.weburl+'member/basic.php?lang=cn&a=doOtherBind&type=weixin',
                    type: 'GET',
                    dataType: 'json',
                    success:function(result){
                        metAjaxFun({
                            result:result,
                            true_fun:function(){
                                $('.safety-modal-weixinadd .modal-body').html('<img src="'+result.data.img+'" width="100%"/>');
                                !user_weixin_bind_code && setTimeout(function(){
                                    var interval=setInterval(function(){
                                        if($('.safety-modal-weixinadd.in').length){
                                            delay++;
                                            delay<300?$.ajax({
                                                url: M.weburl+'member/basic.php?lang=cn&a=docheckBind&type=weixin',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: {code: user_weixin_bind_code},
                                                success:function(result1){
                                                    metAjaxFun({
                                                        result:result1
                                                    });
                                                }
                                            }):$('.safety-modal-weixinadd').modal('hide');
                                        }
                                    },2000);
                                },1000);
                                user_weixin_bind_code=result.data.code;
                            }
                        })
                    }
                });
            });
        }
        // 微信解绑
        if($('.member-profile .btn-weixinunbind').length){
            $('.member-profile .btn-weixinunbind').metClickConfirmAjax({
                confirm_text:METLANG.weixinunbind,
                url:M.weburl+'member/basic.php?lang=cn&a=doUnbind&type=weixin',
            });
        }
    }
    if($('.met-login-register').length){
        // 注册页面、找回密码-获取短信验证码
        var $btn_phone_code = $('.met-login-register button.phone-code');
        if($btn_phone_code.length){
            $btn_phone_code.click(function(){
                var $self = $(this),
                    $phone = $('.met-login-register input[name="username"]').length?$('.met-login-register input[name="username"]'):$('.met-login-register input[name="phone"]'),
                    $code = $('.met-login-register input[name="code"]');
                if($phone.val()==''){
                    $phone.focus();
                    $.include(M['plugin']['alertify'],function(){
                        alertify.error($phone.data('fv-notempty-message'));
                    });
                }else if($phone.attr('name')=='phone' && !/^1[0-9]{10}$/.test($phone.val())){
                    $phone.focus();
                    $.include(M['plugin']['alertify'],function(){
                        alertify.error($phone.data('fv-phone-message'));
                    });
                }else if($code.val()==''){
                    $code.parents('.form-group').removeClass('hide');
                    $code.focus();
                    $.include(M['plugin']['alertify'],function(){
                        alertify.error($code.data('fv-notempty-message'));
                    });
                    setTimeout(function(){
                        $('body').click(hideCodeWrapper);
                    },0);
                }else{
                    $btn_phone_code.attr('disabled',true);
                    $.ajax({
                       type: 'POST',
                       url: $(this).data('url'),
                       data:{phone:$phone.val(),code:$code.val(),random:$('.met-login-register [name="random"]').val()},
                       dataType:'json',
                       success: function(result){
                            $code.val('');
                            metAjaxFun({
                               result:result,
                               true_fun:function(){
                                    $('.code-wrapper').addClass('hide'),$('body').unbind("click",hideCodeWrapper);
                                    $self.html($self.data('retxt') + ' <span class="badge"></span>');
                                    identifyTime($self,90);
                                },
                                false_fun:function(){
                                    $btn_phone_code.removeAttr('disabled');
                                    $('.user-code-img .met-getcode').click();
                                }
                            });
                       }
                    });
                }
            });
            // 关闭添加分支弹框
            function hideCodeWrapper(e){
                !($(e.target).closest('.code-wrapper').length||$(e.target).closest('.phone-code').length) && ($('.code-wrapper').addClass('hide'),$('body').unbind("click",hideCodeWrapper));
            }
        }
    }
    // 邮箱验证
    if($('.send-email').length){
        $('.send-email').click(function(e){
            e.preventDefault();
            var $li = $(this).parent('li');
            $li.addClass('loading');
            $.ajax({
                type: 'GET',
                url: $(this).attr('href'),
                dataType:'json',
                success: function(result){
                    metAjaxFun({result:result,true_fun:function(){}});
                    $li.removeClass('loading');
                }
            });
        });
    }
    // 微信登录
    if($('.met-user-login-weixin[data-toggle="modal"]').length){
        var user_login_weixin_code='';
        $('.met-user-login-weixin[data-toggle="modal"]').click(function(event) {
            var delay=0;
            $.ajax({
                url: M.weburl+'member/login.php?a=doLoginQrcode',
                type: 'GET',
                dataType: 'json',
                success:function(result){
                    metAjaxFun({
                        result:result,
                        true_fun:function(){
                            $('.met-user-login-weixin-modal .modal-body').html('<img src="'+result.data.img+'" width="100%"/>');
                            !user_login_weixin_code && setTimeout(function(){
                                var interval=setInterval(function(){
                                    if($('.met-user-login-weixin-modal.in').length){
                                        delay++;
                                        delay<300?$.ajax({
                                            url: M.weburl+'member/login.php?a=docheckwxlogin',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {code: user_login_weixin_code},
                                            success:function(result1){
                                                metAjaxFun({
                                                    result:result1,
                                                    true_fun:function(){
                                                        setTimeout(function(){
                                                            window.location.href=result1.data.url;
                                                        },result1.msg?1000:0);
                                                    }
                                                });
                                            }
                                        }):$('.met-user-login-weixin-modal').modal('hide');
                                    }
                                },2000);
                            },1000);
                            user_login_weixin_code=result.data.code;
                        }
                    })
                }
            });
        });
    }
    // 登录注册页面
    if($('.met-form-login').length && $('.met-login-register .login-type').length){
        $('.met-form-login').append($('.met-login-register .login-type').clone(true));
        $('.met-form-login .login-type').addClass('hidden-lg-up m-t-20').removeClass('hidden-md-down flex').find('p').removeClass('m-x-20').addClass('text-xs-center m-b-10');
    }
})
// 验证码发送倒计时
function identifyTime(o,wait) {
    var $badge=o.find('span.badge'),
        countdown=setInterval(function(){
            if (wait == 0) {
                o.attr('disabled',false);
                $badge.html('');
                wait = 90;
                clearInterval(countdown);
            } else {
                wait--;
                $badge.html(wait);
            }
        },1000);
    $badge.html(wait);
}