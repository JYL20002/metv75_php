/**
 * 图标设置弹框
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 */
(function(){
    M.load('modal',function(){
        // 弹框参数
        M.component.modal_options['.met-icon-modal']={
            modalTitle:`<i class="metinfo-admin-icon metinfo-admin-icon-manage mr-1"></i> ${METLANG.column_choosicon_v6||'选择图标'}`,
            modalSize:'xl',
            modalUrl:'#pub/icon_list',
            modalFullheight:1,
            modalHeight100:1,
            modalRefresh:'one',
            callback:function(key){
            }
        };
    });
    // 图标按钮初始化
    $.fn.metIconSet=function(){
        $(this).each(function(index, el) {
            var value=$(this).val(),
                icon_class=$(this).data('icon_class')||'';
            $(this).after('<div class="d-inline-block met-icon-view position-relative h5 mb-0 px-1 mr-2 '+icon_class+(value?'':' hide')+'"><button type="button" class="btn btn-default py-1 position-absolute btn-icon-del"><i class="wb-trash"></i></button><i class="'+value+'" icon-value></i></div>'
                +'<div class="btn-group">'
                    +'<button type="button" class="btn btn-primary py-1 btn-icon-set" data-toggle="modal" data-target=".met-icon-modal">'+(METLANG.column_choosicon_v6||'选择图标')+'</button>'
                +'</div>');
        });
    }
    // 图标选择框-初始化
    $(document).on('click', '.btn-icon-set', function(event) {
        var $self=$(this);
        interval({
            true_val:(time)=>{
                return $('.met-icon-modal .modal-body .nav').length;
            },true_fun:(time)=>{
                var $icon_modal=$('.met-icon-modal'),
                    $form_group=$self.parents('.btn-group').parent(),
                    iconset_val=$form_group.find('input[type="hidden"][name][data-plugin="iconset"]').val(),
                    icon_type='';
                if(!$self.attr('id')) $self.attr({id:'iconset-'+new Date().getTime()});
                $icon_modal.find('.modal-footer button[data-ok]').attr({'data-id':$self.attr('id')});
                $icon_modal.find('.icon-detail .icondemo-wrap').removeClass('active');
                if(iconset_val){
                    var icon_prev=iconset_val.split('-')[0]+'-';
                    iconset_val=iconset_val.replace(icon_prev, '');
                    var $icon_active=$icon_modal.find(`.icon-detail .icondemo-wrap[data-name="${iconset_val}"][data-prev="${icon_prev}"]`).eq(0);
                    if($icon_active.length) icon_type=$icon_active.parents('.icon-detail').attr('data-type');
                    $icon_active.addClass('active');
                    setTimeout(()=>{
                        var st=$icon_active.offset().top-$icon_modal.find('.modal-body').outerHeight()+100;
                        if(st>0) $icon_modal.find('.modal-body').scrollTop(st);
                    },200);
                }
                $icon_modal.find(`.nav .nav-link[href=".icon-detail${icon_type?`[data-type='${icon_type}']`:``}"]`).click();
            }
        });
    });
    // 图标设置框-选择类型
    $(document).on('click', '.met-icon-modal .nav .nav-link', function(event) {
        if(!$(this).parent().index()){
            setTimeout(()=>{
                $('.met-icon-modal .tab-pane').addClass('active show');
            },300);
        }else{
            $('.met-icon-modal .tab-pane').removeClass('active show');
            setTimeout(() => {
                $(`.met-icon-modal ${$('.met-icon-modal .nav .nav-link.active').attr('href')}`).addClass('active show');
            }, 100);
        }
    });
    $(document).on('click', '.met-icon-modal .icon-detail .icondemo-wrap', function(event) {
        $('.met-icon-modal .icon-detail .icondemo-wrap').removeClass('active');
        $(this).addClass('active');
    });
    // 图标设置框-保存
    $(document).on('click', '.met-icon-modal .modal-footer button[data-ok]', function(event) {
        var $icon_modal=$('.met-icon-modal'),
            $icon_active=$icon_modal.find('.icon-detail .icondemo-wrap.active'),
            $self=$(this);
        M.load('alertify', function() {
            if($icon_active.length){
                var icon_active=$icon_active.data('prev')+$icon_active.data('name'),
                    $form_group=$('#'+$self.attr('data-id')).parents('.btn-group').parent();
                $icon_modal.modal('hide');
                (!$self.attr('data-obj') || $self.attr('data-obj').indexOf('pageset-iconset-')<0) && alertify.success(METLANG.jsok||'操作成功');
                if($form_group.length){
                    $form_group.find('input[type="hidden"][name][data-plugin="iconset"]').val(icon_active).trigger('change');
                    $form_group.find('.met-icon-view').removeClass('hide').find('i[icon-value]').attr({class:icon_active});
                }
            }else{
                alertify.error(METLANG.column_choosicon_v6||'请选择图标');
            }
        });
    });
    // 图标设置框-删除
    $(document).on('click', '.btn-icon-del', function(event) {
        var $form_group=$(this).parents('.met-icon-view').parent();
        if($form_group.find('input[type="hidden"][name][data-plugin="iconset"]').val()){
            $form_group.find('input[type="hidden"][name][data-plugin="iconset"]').val('').trigger('change');
            $form_group.find('.met-icon-view').addClass('hide').find('i[icon-value]').attr({class:''});
        }
    });
    $(document).on('hidden.bs.modal', '.met-icon-modal', function(event) {
        $('.modal-footer button[data-ok]',this).removeAttr('data-obj');
    });
})();