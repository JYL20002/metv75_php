/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */ ;
(function () {
  var that = $.extend(true, {}, admin_module);
  TEMPLOADFUN[that.hash] = () => {
    M.ajax({
      url: that.own_name + '&c=email&a=doGetEmail',
      success: function (result) {
        let data = result.data
        Object.keys(data).map(item => {
          if (item === 'met_fd_way') {
            $(`#radio_${data[item]}`).attr('checked', 'checked')
            return
          }
          $(`[name=${item}]`).val(data[item])
        })
      }
    });
  };
  // 点击【发送测试】按钮
  $(document).on('click','.met-email-set-form .btn-test-email',function(){
    metAlert(`${METLANG.upfiletips16}${M.synchronous=='cn'?'中':'ing'}...`,'',1);
    interval({
      true_val:(time)=>{
          return !$(this).attr('disabled');
      },true_fun:(time)=>{
        metAlert(' ');
      }
    });
  });
  // 切换【发送方式】
  $(document).on('change','.met-email-set-form input[type="radio"][name="met_fd_way"]',function(){
    $(this).parents('form').find('[name="met_fd_port"]').val($(this).val()=='ssl'?465:25)
  });
})()