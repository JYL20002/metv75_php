/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
  var that = $.extend(true, {}, admin_module);
  function fetch(refresh) {
    M.ajax(
      {
        url: that.own_name + '&c=admin_set&a=doGetUserSetup'
      },
      function(result) {
        let data = result.data
        Object.keys(data).map(item => {
          switch(item){
            case 'met_member_register':
            case 'met_member_idvalidate':
            case 'met_member_agreement':
              if (data[item] !== $(`[name="${item}"]`).val()) $(`[name="${item}"]`).click()
              break;
            case 'met_member_vecan':
            case 'met_login_box_position':
            case 'met_member_bg_range':
              $(`[name="${item}"][value="${data[item]}"]`).attr('checked', 'checked')
              return;
              break;
          }
          $(`[name=${item}]`).attr('type') == 'file' ? $(`[name=${item}]`).attr('value', data[item]) : $(`[name=${item}]`).val(data[item])
        })
        $(`[name="met_member_agreement"]`)
          .off()
          .on('change', function(e) {
            if (e.target.checked) {
              $('.met_member_agreement').removeClass('hide')
            } else {
              $('.met_member_agreement').addClass('hide')
            }
          })

        if (!refresh) {
          that.obj.metCommon()
        }
      }
    )
  }
  fetch();
  TEMPLOADFUNS[that.hash] = function() {
    fetch();
  };
})();