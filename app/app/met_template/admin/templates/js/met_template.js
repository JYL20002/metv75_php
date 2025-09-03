/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
  var thats = $.extend(true, {}, admin_module)
  TEMPLOADFUNS[`met_template/other`] = function() {
    const met_template_other = $.extend(true, {}, admin_module)
    renderList(met_template_other)
  }
  TEMPLOADFUNS[`met_template/ui`] = function() {
    const met_template = $.extend(true, {}, admin_module)
    var $template_right = met_template.obj.find('.met-template-right'),
		$headtab = met_template.obj.parents('.tab-content').prev('.met-headtab');
    if ($template_right.length) {
      if($headtab.find('.met-template-right').length){
        $template_right.remove();
      }else{
        $headtab.append($template_right);
      }
    }
    getUserInfo(met_template)
    renderList(met_template)
  }
  function renderList(that) {
    const isOther = that.hash == `met_template/other`
    const list = that.obj.find('.met-template-list')
    list.html(M.component.loader({ height: '300px', class_name: 'w-100' }))
    $.ajax({
      url: that.own_name + '&c=index&a=dolist',
      type: 'POST',
      data: {
        type: isOther ? 'tag' : 'ui'
      },
      dataType: 'json',
      success: function(result) {
        const url = thats.obj.find('.met-tips').data('url')
        const templates_url = thats.obj.find('.met-tips').data('templates_url')
        const noTemplate = `<div class="alert alert-primary tips w-100 mx-3">
        <p>还没有购买模板或购买的模板绑定域名不是：${url}（注意域名不能带http://、二级目录） </p>
        <a href="${templates_url}" target="_blank"><button class="btn btn-primary">${METLANG.met_template_buytemplates}</button></a>
          </div>`
        if (result.status && !result.data) {
          list.html(noTemplate)
          return
        }
        that.data = result.data
        let html = ''
        var active_index=that.data.findIndex((value, index, array) => value.enable);
        if(active_index>=0){
          var active_tem=that.data[active_index];
          that.data.splice(active_index,1);
          that.data.unshift(active_tem);
        }
        that.data.map(item => {
          const card = `<div class="col-6 col-xl-4 col-xxl-3 mb-4">
              <div class="item position-relative border rounded-20 p-3 bg-grey-100 h-100 cover ${item.enable ? `active` : ''}" data-skin_name="${item.skin_name}">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="my-0">${item.skin_name}</h4>
                  ${item.hasOwnProperty('enable')?
                  `<div class="d-flex align-items-center">状态：
                  <input type="checkbox" data-plugin="switchery" value=" ${item.enable ? '1' : '0'} " name="met_enable" ${item.enable ? 'checked="checked" disabled="disabled"' : ''} class="met_enable" />
                  <span class="text-help ml-2">${item.enable ? '<span class="text-primary">已启用</span>' : '未启用'}</span>
                  </div>
                  `: ''}
                </div>
                <div class="img-wrapper rounded-10 my-3 cover">
                  <img src="${item.view}" width="100%">
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                    ${item.hasOwnProperty('enable') ? `<div class="tem-ver mr-3 ${item.enable ? `active-tem` : ''}">当前版本：${item.version}</div>` : ''}
                    ${item.hasOwnProperty('enable') && item.enable && item.skin_name.indexOf('ui') > -1 ? `<div class="install-data"><a  href="javascript:;" class="btn-install-data" data-toggle="modal" data-target=".met-install-modal">${METLANG.met_template_installdemo}</a></div>` : ''}
                  </div>
                  ${
                    item.hasOwnProperty('enable') && !item.enable && item.skin_name.indexOf('ui') > -1
                      ? `<button type="button" class="btn btn-light py-1 delete">
                  <i class="fa fa-trash ml-2">
                  <span>${METLANG.delete}</span></i>
                  </button>`
                      : ''
                  }
                </div>
              ${
                item.install
                  ? `<div class="overlay">
                <button class="btn btn-primary btn-install px-4">${METLANG.appinstall}</button>
              </div>`
                  : ''
              }
              ${
                item.import
                  ? `<div class="overlay">
                <button class="btn btn-primary btn-import px-4">${METLANG.setdbImportData}</button>
              </div>`
                  : ''
              }
            </div>
          </div>`;
          html = html + card
        })
        setTimeout(() => {
          list.html(html)
          installTemplate(that)
          importTemplate(that)
          deleteTemplate(that)
          enableTemplate(that)
          !isOther && checkTemplate(that)
          installData(that)
          that.obj.metCommon()
        }, 300)
      }
    })
  }
  function deleteTemplate(that) {
    that.obj
      .find('.delete')
      .off()
      .click(function() {
        const btn = $(this)
        M.load('alertify', function() {
          alertify.addHead();
          alertify
            .okBtn(METLANG.confirm)
            .cancelBtn(METLANG.cancel)
            .confirm(METLANG.met_template_delettemplatesinfo, function(ev) {
              $.ajax({
                url: that.own_name + 'c=index&a=dodelete',
                data: {
                  skin_name: btn.parents('.item').data('skin_name')
                },
                dataType: 'json',
                success: function(result) {
                  metAjaxFun({
                    result: result,
                    true_fun: function() {
                      renderList(that)
                    },
                    false_fun: function() {}
                  })
                }
              })
            })
        })
      })
  }
  function checkTemplate(that) {
    const enableList = that.data.filter(item => {
      return item.enable
    })
    if (enableList.length === 0) {
      return
    }
    $.ajax({
      url: that.own_name + 'c=index&a=docheck',
      type: 'POST',
      dataType: 'json',
      data: {
        skin_name: enableList[0].skin_name
      },
      success: function(result) {
        if (result.status) {
          that.obj.find('.active-tem').append(`<button  class="ml-2 btn-update btn btn-primary btn-xs"
          data-skin_name="${enableList[0].skin_name}"><i class="fa fa-upload mr-2"></i>${METLANG.appupgrade}</button>`)
          updateTemplate(that)
        }
      }
    })
  }
  function updateTemplate(that) {
    const active = that.obj.find('.met-template-list .active')
    that.obj.find('.btn-update').metClickConfirmAjax({
      confirm_text: '如你自行修改了模板源代码文件，且未按商业模板修改规则设置UI区块版本号，升级模板会覆盖原文件，覆盖之后无法恢复！',
      true_fun: function() {
        active.append(`<div class="overlay"><button class="btn btn-default block" style="width:85%;">${METLANG.upgrade}</button></div>`)
        $.ajax({
          url: that.own_name + 'c=index&a=doupdate',
          type: 'POST',
          dataType: 'json',
          data: {
            skin_name: $(this)[0].el.data('skin_name')
          },
          success: function(result) {
            active.find('.overlay').remove()
            metAjaxFun({
              result: result,
              true_fun: function() {
                location.reload()
              }
            })
          }
        })
      }
    })
  }
  function enableTemplate(that) {
    that.obj.find('.met_enable').on('change', function(e) {
      M.ajax(
        {
          url: that.own_name + 'c=index&a=doenable',
          data: {
            skin_name: $(this)
              .parents('.item')
              .data('skin_name'),
            enable: e.target.value ? 1 : 0
          }
        },
        function(result) {
          metAjaxFun({
            result: result,
            true_fun: function() {
              renderList(that)
              $('.met-pageset').length && $('.page-iframe').attr({ 'data-reload': 1 })
            }
          })
        }
      )
    })
  }
  function installTemplate(that) {
    that.obj.find('.btn-install').click(function() {
      const btn = $(this)
      const skin_name = btn.parents('.item').data('skin_name')
      btn.html(`<i class="fa fa-circle-o-notch fa-spin"></i> ${METLANG.installing}`).css({ width: '85%', height: 'auto' })
      btn.unbind('click')
      M.ajax(
        {
          url: that.own_name + '&c=index&a=doinstall',
          data: {
            skin_name: skin_name
          }
        },
        function(result) {
          metAjaxFun({
            result: result,
            true_fun: function() {
              btn.unbind('click')
              that.precent = Math.ceil(100 / result.data.length)
              that.now = 0
              that.btn = btn
              that.count = that.precent * result.data.length
              btn.css({ width: '85%', height: 'auto' })
              result.data.map((item, index) => {
                const data = {
                  url: that.own_name + '&c=index&a=dodownloadUI',
                  data: {
                    ui_name: item,
                    skin_name: skin_name
                  }
                }
                setTimeout(() => {
                  installUI(data, that)
                }, index * 100)
              })
            },
            false_fun: function() {
              installTemplate(that)
            }
          })
        }
      )
    })
  }
  function installData(that) {
    that.obj.find('.btn-install-data').click(function() {
      setTimeout(()=>{
        const modal = $('.met-install-modal')
        const btn = $(this)
        const skin_name = btn.parents('.item').data('skin_name')
        const body = modal.find('.modal-body')
        modal
        .find('.btn-backup')
        .off()
        .click(function() {
          var start=(progress)=>{
              var start_progress=0;
              $(this)
              .html(`<i class="fa fa-spinner fa-spin mr-2"></i>${METLANG.databacking}`)
              .attr('disabled', true)
              that.precent = 0
              let params = {
                data: {
                  skin_name: skin_name,
                  piece: 0
                },
                body: body
              }
              modal.find('button').attr('disabled',true);
              renderProgress(body, { title: METLANG.databacking });
              if(progress==2){
                back_upload_dir({callback:()=>{start_progress++;}});
                metAlert(METLANG.databacking,'',1);
              }
              M.ajax(
                {
                  url: M.url.adminurl + 'n=databack&c=index&a=dopackdata'
                },
                (result)=>{
                  start_progress++;
                  interval({
                    true_val:(time)=>{
                      return start_progress==progress;
                    },true_fun:(time)=>{
                      console.log(result)
                      $(this).html(METLANG.databackup4+METLANG.jsok);
                      continueBack(that,result, params);
                    }
                  });
                }
              );
            };
          M.load('alertify', function() {
            alertify.okBtn('备份网站数据库').confirm('请选择备份类型', ()=>{
              start(1);
            });
            setTimeout(()=>{
              $('.alertify .dialog>div').width(450).find('nav').append(`<button class="ok btn btn-primary btn-temp-databack-upload-dir">备份网站数据库及图片</button>`);
              // 点击备份upload文件夹按钮
              $('.alertify .btn-temp-databack-upload-dir').click(function(){
                start(2);
                $('.alertify').remove();
              });
            },100);
          });
        });
        modal
        .find('.btn-reset')
        .off()
        .click(function() {
          $(this)
            .html(`<i class="fa fa-spinner fa-spin mr-2"></i>${METLANG.met_template_installnewmetinfo}...`)
            .attr('disabled', true)
          that.precent = 0
          let params = {
            data: {
              skin_name: skin_name,
              piece: 0
            },
            body: body
          }
          modal.find('button').attr('disabled',true);
          renderProgress(body, { title: METLANG.download_prompt })
          downloadData(that, params)
        });
      },500);
    })
  }
  // 备份upload文件夹
  function back_upload_dir({url=M.url.adminurl + 'n=databack&c=index&a=dopackupload',callback}){
    M.ajax({
      url: url,
      success: function(result) {
        if (result.status == 0) back_upload_dir_callback(0,result.msg);
        if (result.status === 1) back_upload_dir_callback(1,result.msg,callback);
        if (result.status === 2){
          back_upload_dir({url:`${M.url.adminurl}${result.call_back}`,callback});
          metAlert(METLANG.databacking+(result.piece?'正在备份数据分卷'+result.piece:'备份整站文件中')+'...','',1);
        }
      },
      error:function(result){
        (!result.status||result.status!=200)&&back_upload_dir_callback(0,METLANG.databackup4+METLANG.jsx10);
      }
    })
  }
  // 备份upload文件夹-回调
  function back_upload_dir_callback(type,msg,callback){
    metAlert('',1);
    M.load('alertify', function() {
      type?alertify.success(msg):alertify.error(msg);
    });
    if(type && callback) callback();
  }
  function renderProgress(body, params) {
    if (params.precent) {
      body
        .find('.progress-bar')
        .text(params.precent + '%')
        .css('width', `${params.precent}%`)
    } else {
      let html = `
      <div class="p-2">
      <h4>${params.title}</h4>
      <div class="progress border">
      <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%">0%</div>
      </div>
      </div>
    `
      body.html(html)
    }
  }
  function continueBack(that,result, params) {
    if (result.status === 1) {
      that.precent = 0
      renderProgress(params.body, { title: METLANG.download_prompt })
      downloadData(that, params)
    }
    if (result.status === 2) {
      M.ajax(
        {
          url: `${M.url.adminurl}${result.call_back}`
        },
        function(result) {
          setTimeout(() => {
            that.precent = that.precent + Math.floor(Math.random() * 10 + 1)
            renderProgress(params.body, { precent: that.precent })
            continueBack(that,result, params)
          }, 800)
        }
      )
    }
  }
  function downloadData(that, params) {
    M.ajax(
      {
        url: that.own_name + '&c=index&a=dodownloadData',
        data: params.data
      },
      function(result) {
        if (result.status) {
          that.precent = that.precent + Math.floor(100 / result.data.total)
          renderProgress(params.body, { precent: that.precent })
          if (params.data.piece < result.data.total - 1) {
            params.data.piece = params.data.piece + 1
            downloadData(that, params)
          } else {
            that.precent = 0
            params.data.piece = 0
            renderProgress(params.body, { title: METLANG.being_imported })
            importData(that, params)
          }
        } else {
          M.load(['alertify'], function() {
            alertify.error(result.msg)
            setTimeout(() => {
              window.location.reload()
            }, 500)
          })
        }
      }
    )
  }
  function importData(that, params) {
    M.ajax(
      {
        url: that.own_name + '&c=index&a=doimportData',
        data: params.data
      },
      function(result) {
        metAjaxFun({
          result: result,
          true_fun: function() {
            that.precent = that.precent + Math.floor(100 / result.data.total)
            renderProgress(params.body, { precent: that.precent })
            if (params.data.piece < result.data.total - 1) {
              params.data.piece = params.data.piece + 1
              importData(that, params)
            } else {
              setTimeout(() => {
                location.reload()
              }, 500)
            }
          },
          false_fun: function() {
            setTimeout(() => {
              location.reload()
            }, 500)
          }
        })
      }
    )
  }
  function importTemplate(that) {
    that.obj.find('.btn-import').click(function() {
      const btn = $(this)
      const skin_name = btn.parents('.item').data('skin_name')
      btn.html(`<i class="fa fa-circle-o-notch fa-spin"></i> ${METLANG.being_imported}`);
      btn.unbind('click')
      // return;
      M.ajax(
        {
          url: that.own_name + '&c=index&a=doimport',
          data: {
            skin_name: skin_name
          }
        },
        function(result) {
          metAjaxFun({
            result: result,
            true_fun: function() {
              if (result.data) {
                that.precent = Math.ceil(100 / result.data.length)
                that.now = 0
                that.btn = btn
                that.count = that.precent * result.data.length
                btn.css({ width: '85%', height: 'auto' })
                result.data.map((item, index) => {
                  const data = {
                    url: that.own_name + '&c=index&a=dodownloadUI',
                    data: {
                      ui_name: item,
                      skin_name: skin_name
                    }
                  }
                  setTimeout(() => {
                    installUI(data, that)
                  }, index * 100)
                })
              } else {
                renderList(that)
              }
            },
            false_fun: function() {
              importTemplate(that)
            }
          })
        }
      )
    })
  }
  function installUI(data, that) {
    M.ajax(data, function(result) {
      metAjaxFun({
        result: result,
        true_fun: function() {
          that.now = that.now + that.precent
          that.btn.html(
            `<div>
            <p>${result.data.ui_name}下载完成</p>
              <div class="progress border">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  style="width: ${that.now}%" />
              </div>
            </div>
            `
          )
          if (that.now === that.count) {
            renderList(that)
          }
        }
      })
    })
  }
  function getUserInfo(that) {
    const tips = thats.obj.find('.met-tips')
    $.ajax({
      url: M.url.adminurl + 'n=myapp&c=index&a=doUserInfo',
      type: 'GET',
      dataType: 'json',
      success: function(result) {
        const url = tips.data('url')
        const noLoginTips = `<div class="alert alert-primary tips">
        <p>使用米拓用户中心（u.mituo.cn）账号登录即可同步已购买模板！</p>
        <span>友情提示：此处显示的模板绑定域名必须为 ${url}</span></div>`
        const LoginTips = `<div class="alert alert-primary tips">
        <p>请保持账号登录状态，以便正常获取模板升级状态并正常升级</p>
        <span>友情提示：此处显示的模板绑定域名必须为 ${url}</span></div>`
        if (result.status) {
          const user = $('.met-template-right')
          const userHtml = `<div class="d-flex user">
            <div class="user-name">${result.data.username}</div>
            <a href="https://u.mituo.cn/#/user/login" target="_blank">${METLANG.account_Settings}</a>
            <button type="button" class="btn btn-logout btn-default py-1">${METLANG.indexloginout}</button>
            </div>`
          user.html(userHtml)
          tips.html(LoginTips)
          user.find('.btn-logout').click(function() {
            $.ajax({
              url: M.url.adminurl + 'n=myapp&c=index&a=doLogout',
              type: 'GET',
              dataType: 'json',
              success: function(result) {
                metAjaxFun({
                  result: result,
                  true_fun: function() {
                    window.location.reload()
                  }
                })
              }
            })
          })
        } else {
          const user = $('.met-template-right')
          const userHtml = `<a href="#/myapp/login" onClick="setCookie('app_href_source','app/met_template/?head_tab_active=0')" class="mr-2">
          <button type="button" class="btn btn-default py-1">
          ${METLANG.loginconfirm}
          </button>
          </a>
          <a href="https://u.mituo.cn/#/user/register" target="_blank"><button class="btn btn-primary py-1">${METLANG.registration}</button></a>`
          user.html(userHtml)
          tips.html(noLoginTips)
        }
      }
    })
  }
  M.load('modal',()=>{
    M.component.modal_options['.met-install-modal']={
      modalSize:'lg',
      modalTitle:METLANG.met_template_demoinstalltitle,
      modalRefresh:'one',
      modalType:'centered',
      modalBody:`<p>${METLANG.met_template_demoinstallsel}：</p>
      <p>1.${METLANG.met_template_demoinstallt1}</p>
      <p>2.${METLANG.met_template_demoinstallt2}</p>
      <p>3.${METLANG.met_template_demoinstallt3}</p>`,
      modalOktext:'',
      modalFooter:`<div class="float-right mr-0 ml-1">
        <button type="button" class="btn btn-primary btn-backup">${METLANG.met_template_saveinstall}</button>
        <button type="button" class="btn btn-outline-primary btn-reset">${METLANG.met_template_installnewmetinfo}</button>
      </div>`,
      callback:function(key,data){

      }
    };
  })
})()
