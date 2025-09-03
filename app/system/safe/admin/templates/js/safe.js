/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */ ;
(function () {
    var that = $.extend(true, {}, admin_module)
    TEMPLOADFUN[that.hash] = ()=>{
        M.ajax({
                url: that.own_name + '&c=index&a=doGetSetup'
            },
            function (result) {
                let data = result.data
                if(data.is_root){
                    that.obj.find('.set-admindir').removeClass('hide');
                }
                Object.keys(data).map(item => {
                    if ($.inArray(item, ['met_img_rename', 'met_logs', 'met_login_code', 'met_memberlogin_code', 'disable_cssjs', 'met_auto_play_pc', 'met_auto_play_mobile','met_auto_close','met_auto_show', 'met_info_security_statement_open']) >= 0) {
                        if (data[item] > 0 && that.obj.find(`[name="${item}"]`).val() === '0') that.obj.find(`[name="${item}"]`).click()
                        return
                    }
                    if (item === 'access_type') {
                        that.obj.find(`[name="${item}"][value="${data[item]}"]`).click()
                        return
                    }
                    if (item === 'install') {
                        if (data[item] > 0) that.obj.find(`.delete-file`).show()
                        return
                    }

                    that.obj.find(`[name=${item}]`).val(data[item])
                })
                if(that.obj.find('form').attr('data-validate_order')) return;
                that.obj.metCommon()
                M.load(['form', 'formValidation'], function () {
                    var order = that.obj.find('form').attr('data-validate_order')
                    formSaveCallback(order, {
                        true_fun: function (result) {
                            setTimeout(() => {
                                result.data ? (window.location.href = result.data.url) : null
                        }, 500)
                        }
                    })
                })
            }
        )
    };
    that.obj.find('.btn-delete-install').metClickConfirmAjax({
        url:that.own_name + '&c=index&a=doDelInstallFile',
        confirm_text:`${METLANG.setsafeinstall}?`,
        ajax_fun:(result)=>{
            metAjaxFun({result:result,true_reload:1});
        }
    });
})();