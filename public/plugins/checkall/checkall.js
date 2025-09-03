/**
 * checkAll 全选，反选
 * 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved.
 * @param  {[obj]} checkBtn   [全选obj]
 * @param  {[obj]} sub        [子复选框obj]
 * @param  {[obj]} contraryCheckBtn [取消全选obj]
 */
(function($){
	$.fn.checkAll=function(checkBtn,sub,contraryCheckBtn){
		$(this).each(function (index, domEle) {
			var $self=$(this),
                is_match_parent=typeof $(this).data('match_parent')!='undefined'?$(this).data('match_parent'):1;
			// 添加各级全选的key，便于获取对应的全选对象
			$(this).attr('data-checkall_id',0).checkAllInit(checkBtn,sub);

			//全选（点击全选框时，控制子复选框的选中状态）
			$(this).on('change', checkBtn, function(event) {
				var checked=$(this).prop("checked"),
					$parents=$(this).parents('.checkall-parents').length?$(this).parents('.checkall-parents').eq(0):$self,
					checkall_id=$(this).attr('data-checkall_id'),
					$checked=$parents.find(sub).filter(function(){
						return ((typeof $(this).attr('data-checkitem_id')=='undefined')&&(typeof $(this).attr('data-checkall_id')=='undefined'))?1:$(this).attr('data-checkitem_id')==checkall_id;
					});
				$checked.prop("checked",checked);
				$parents.find(checkBtn).filter(function(){
					return $(this).attr('data-checkall_id')==checkall_id;
				}).prop("checked",checked);
				// 子复选框带全选框的继续触发其全选事件
				$self.find('.checkall-parents').length && $checked.filter(function(){
					return $(this).hasClass('checkall-all');
				}).change();
			});
			//子触发父（点击子复选框时，父全选框也要有对应的改变）
			is_match_parent && $(this).on('change subchange', sub, function () {
				var $parents=$(this).parents('.checkall-parents').length?$(this).parents('.checkall-parents').eq($(this).hasClass('checkall-all')?1:0):'';
				if(!$parents.length) $parents=$self;
				//把父全选框和子复选框的状态进行匹配
				//1、遍历所有的子复选框
				var match_parent=typeof $parents.data('match_parent')!='undefined'?$parents.data('match_parent'):is_match_parent,
					isChecked = match_parent==1?true:false,
					checkall_id=$parents.attr('data-checkall_id');
				$parents.find(sub).filter(function(){
					return ((typeof $(this).attr('data-checkitem_id')=='undefined')&&(typeof $(this).attr('data-checkall_id')=='undefined'))?1:$(this).attr('data-checkitem_id')==checkall_id;
				}).each(function () {
					if(this.checked==(match_parent==1?false:true)) isChecked = match_parent==1?false:true;
				})
				// 2、改变父复选框的状态
				$parents.find(checkBtn).filter(function(){
					return $(this).attr('data-checkall_id')==checkall_id && (this.checked!=isChecked);
				}).prop("checked",isChecked).each(function(){// 触发父全选框的子触发父事件
					if($(this).hasClass('checkall-item')) $(this).trigger('subchange');
				});
			});
			//功能：点击反选按钮时，把子复选框进行反选，同时，还需要把父子复选框进行匹配
			// $(this).on('click', contraryCheckBtn,function () {
			//     //复选框反选
			//     $self.find(sub).each(function () {
			//         this.checked = !this.checked;
			//         $(this).change();
			//     })
			//     // 父子进行匹配
			//     match();
			// });
		})
	};
    $.fn.checkAllInit=function(checkBtn='.checkall-all',sub='.checkall-item:not([disabled])'){
        var $self=$(this);
        $('.checkall-parents',this).each(function(index,val){
            $(this).attr('data-checkall_id',index+1);
        });
        $(checkBtn,this).each(function(index,val){
            var $parents=$(this).parents('.checkall-parents').length?$(this).parents('.checkall-parents').eq(0):$self,
                checkall_id=$parents.attr('data-checkall_id');
            $(this).attr('data-checkall_id',checkall_id);
        });
        $(sub,this).each(function(index,val){
            var $parents=$(this).parents('.checkall-parents').length?$(this).parents('.checkall-parents').eq($(this).hasClass('checkall-all')?1:0):'';
			if(!$parents.length) $parents=$self;
            var checkitem_id=$parents.attr('data-checkall_id');
            $(this).attr('data-checkitem_id',checkitem_id);
        });
    };
})(jQuery);