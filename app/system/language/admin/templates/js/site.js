/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
    var that = $.extend(true, {}, admin_module)
    TEMPLOADFUN[that.hash] = ()=>{
        if (that.table){
            that.table.ajax.reload();
            return;
        }
        M.load(['table', 'alertify'], function() {
            const table = that.obj.find('#lang-table')
            table.attr({ 'data-table-ajaxurl': table.data('ajaxurl') })
            datatable_option['#lang-table'] = {
                ajax: {
                    dataSrc: function(result) {
                        that.met_index_type = result.data.met_index_type
                        that.langData = result.data.language_data
                        let newData = [],
                            select = ``,
                            sysMark = ['cn', 'en']
                        $.each(that.langData, function(index, val) {
                            let list = [
                                val.no_order,
                                `<div class="d-flex align-items-center text-wrap">${val.name}${that.met_index_type === val.mark ? `<span class="badge font-weight-normal badge-pill badge-light-danger ml-2">${M.synchronous=='cn'?'默认语言':'default language'}</span>` : ''}</div>`,
                                `<img src="${M.url.public_third}flag/${val.flag}" style="width:20px;">`,
                                `<div class="text-nowrap"><i class="wb-large-point mr-2 text-${parseInt(val.useok) ? 'success' : 'danger'}"></i>${METLANG[parseInt(val.useok)?'open':'close']}</div>`,
                                `<a href="${val.link}" target="_blank">${val.link}</a>`,
                                `<button class="btn btn-light-primary m-1 btn-edit"
                                    type="button"
                                    data-index="${index}"
                                    data-toggle="modal"
                                    data-target=".langweb-edit-modal"
                                    data-modal-url="language/edit"
                                    data-modal-size="lg"
                                    data-modal-fullheight="1"
                                    data-modal-title="${METLANG.editor}"
                                    data-modal-tablerefresh="#lang-table"
                                    data-refresh="1"
                                    data-modal-loading="1"
                                    >${METLANG.editor}
                                </button>
                                <button class="btn btn-light-success m-1 btn-search-edit"
                                    type="button"
                                    data-index="${index}"
                                    data-toggle="modal"
                                    data-target=".lang-search-modal"
                                    data-modal-url="language/search"
                                    data-modal-size="lg"
                                    data-modal-fullheight="1"
                                    data-modal-tablerefresh="#lang-table"
                                    data-modal-title="${METLANG.editor}">${METLANG.langwebeditor}
                                </button>
                                <button type="button"
                                    class="btn btn-light-danger m-1 btn-langweb-delete" data-head_title
                                    data-id="${val.id}">${METLANG.delete}
                                    </button><br>
                                    <button class="btn btn-default m-1 mt-2 btn-replace"
                                    type="button"
                                    data-index="${index}"
                                    data-toggle="modal"
                                    data-target=".langweb-replace-modal"
                                    data-modal-url="language/replace"
                                    data-modal-size="lg"
                                    data-modal-title="${METLANG.language_batchreplace_v6}"
                                    data-modal-tablerefresh="#lang-table">${METLANG.language_batchreplace_v6}
                                </button>
                                <button type="button" class="btn btn-default m-1 mt-2 btn-export-pack" data-url="${that.own_name}c=language_general&a=doExportPack&site=web&editor=${val.mark}">
                                    ${METLANG.language_outputlang_v6}
                                </button>
                                    ${sysMark.indexOf(val.mark) > -1 ? `<button
                                    type="button"
                                    class="btn btn-default m-1 mt-2 btn-sync"
                                    data-index="${index}"
                                    data-modal-footerok="0"
                                    data-modal-title="${METLANG.unitytxt_9}"
                                    >${METLANG.unitytxt_9}
                                    <!--data-toggle="modal"
                                    data-modal-url="language/sync"
                                    data-target=".langweb-sync-modal"-->
                                </button>`: ''}`,
                                // `<button class="btn btn-default btn-edit-app"
                                // type="button"
                                // data-index="${index}"
                                // data-toggle="modal"
                                // data-target=".lang-app-modal"
                                // data-modal-size="lg"
                                // data-modal-footerok="0"
                                // data-modal-fullheight="1"
                                // data-modal-title="${METLANG.edit_app_lang}"
                                // > ${METLANG.edit_app_lang}</button>`
                            ]
                            select = select + `<option value="${val.mark}">${val.name}</option>`
                            newData.push(list)
                        })
                        that.selectHtml = select
                        return newData
                    }
                }
            }

            that.obj.metDataTable(function() {
                that.table = datatable['#lang-table']
                deleteLang()
                renderAdd()
                renderEditForm()
                renderSearchForm()
                renderEditAppForm()
                renderReplaceForm()
                renderSyncForm()
            })
        })
    };
    function deleteLang() {
        that.obj.find('.btn-langweb-delete').metClickConfirmAjax({
            true_fun: function() {
                M.ajax({
                    url: that.own_name + 'c=language_web&a=doDeleteLanguage',
                    data: {
                        id: $(this)[0].el.data('id')
                    },
                    success: function(result) {
                        metAjaxFun({
                            result: result,
                            true_fun: function() {
                                alertify.confirm('是否刷新页面以隐藏被删除的语言站链接', function (ev) {
                                    window.location.reload();
                                });
                            }
                        })
                    }
                })
            }
        })
    }
    function renderAdd() {
        M.component.modal_options['.langweb-add-modal'] = {
            callback: function() {
                const modal = $('.langweb-add-modal')
                setTimeout(() => {
                    renderAddModal(modal)
                    const form = modal.find('form')
                    formSaveCallback(form.attr('data-validate_order'), {
                    true_fun: function() {
                        alertify.confirm('是否刷新页面以增加新添加的语言站链接', function (ev) {
                            window.location.reload();
                        });
                    }
                })
                modal.find('.modal-loader').addClass('hide')
                modal.find('.modal-html').removeClass('hide')
                modal.scrollTop(0)
            }, 300)
            }
        }
        function renderAddModal(modal) {
            M.ajax({
                url: that.own_name + 'n=language&c=language_general&a=doGetNationalFlag',
                success: function(result) {
                    M.load('webui-popover', function() {
                        modal.find('[name="order"]').val(parseInt(that.langData[that.langData.length - 1].no_order) + 1)
                        modal.find('[name="file"]').html(that.selectHtml)
                        modal.find('[name="copy_config"]').html(that.selectHtml)
                        modal.find('[name="theme_style"]').html(that.selectHtml)
                        // modal.find('[name="content"]').html(that.selectHtml)
                        modal
                            .find('[name="autor"]')
                            .off()
                            .change(function(e) {
                                const text = modal.find('[name="autor"] option:selected').text()
                                const value = e.target.value
                                modal.find('[name="mark"]').val(value)
                                if (value === '') {
                                    modal.find('.lang-mark').show()
                                    modal.find('[name="mark"]').attr('data-fv-field', 'mark')
                                    modal.find('[name="mark"]').attr('required', '')
                                } else {
                                    modal.find('.lang-mark').hide()
                                    modal.find('[name="mark"]').attr('data-fv-field', false)
                                    modal.find('[name="mark"]').attr('required', false)
                                    modal.find('[name="name"]').val(text)
                                    modal.find('[name="flag"]').val(value + '.gif')
                                    modal.find('.lang-flag').attr('src', `${M.url.public_third}flag/${value}.gif`)
                                }
                            })
                        rednerFlag(result, modal)
                    })
                }
            })
        }
    }
    function renderEditForm() {
        $(document).on('click', '.met-lang-web .btn-edit', function(event) {
            const index = $(this).data('index')
            that.activeData = that.langData[index]
        })
        M.component.modal_options['.langweb-edit-modal'] = {
            callback: function() {
                const modal = $('.langweb-edit-modal')
                setTimeout(() => {
                    rednerEditModal(modal)
                    const form = modal.find('form')
                    formSaveCallback(form.attr('data-validate_order'), {
                    true_fun: function() {
                        alertify.confirm('是否刷新页面', function (ev) {
                            window.location.reload();;
                        });
                    }
                })
                modal.find('.modal-loader').addClass('hide')
                modal.find('.modal-html').removeClass('hide')
                modal.scrollTop(0)
            }, 230)
            }
        }

        function rednerEditModal(modal) {
            M.ajax({
                url: that.own_name + 'n=language&c=language_general&a=doGetNationalFlag',
                success: function(result) {
                    M.load('webui-popover', function() {
                        modal.find('[name="order"]').val(that.activeData.no_order)
                        modal.find('[name="name"]').val(that.activeData.name)
                        modal.find('[name="link"]').val(that.activeData.link)
                        modal.find('[name="mark"]').val(that.activeData.mark)

                        if (that.activeData.useok > 0) {
                            modal.find('[name="useok"]').click()
                        }
                        if (that.activeData.newwindows > 0) {
                            modal.find('[name="newwindows"]').click()
                        }
                        if (that.met_index_type === that.activeData.mark) {
                            modal.find('[name="met_index_type"]').click()
                        }
                        modal.find('[name="flag"]').val(that.activeData.flag)
                        modal.find('.lang-flag').attr('src', `${M.url.public_third}flag/${that.activeData.flag}`)
                        rednerFlag(result, modal)
                    })
                }
            })
        }
    }
    function rednerFlag(result, modal) {
        let html = ''
        result.data.map(item => {
            html = html + `<img src="${item}" class="flag" style="width:16px;height:11px;" />`
        })
        modal.find('.btn-select-flag').webuiPopover({
            trigger: 'click',
            title: `${METLANG.national_flag}<span class="text-help ml-2" style="font-weight: 500;font-size: 13px;">${METLANG.national_flag_tips1}</text>`,
            content: `<div class="flag-div">${html}</div>`,
            onShow: function(e) {
                $('.flag-div .flag').click(function() {
                    const src = $(this).attr('src')
                    modal.find('.lang-flag').attr('src', src)
                    const newArr = src.split('/')
                    const mark = newArr[newArr.length - 1]
                    modal.find('[name="flag"]').val(mark)
                })
            }
        })
    }
    function search(val, modal) {
        if (!val) {
            alertify.error(`${METLANG.js41}`)
            return
        }
        let params = {
            word: val,
            editor: that.activeData.mark,
            site: 'web'
        }
        if (modal.find('[name="appno"]').val()) {
            params.appno = modal.find('[name="appno"]').val()
        }
        M.ajax(
            {
                url: that.own_name + 'c=language_general&a=doSearchParameter',
                data: params
            },
            function(result) {
                metAjaxFun({
                    result: result,
                    true_fun: function() {
                        let html
                        if (result.data) {
                            submitParameter(modal)
                            html = result.data
                                .map(item => {
                                return `<dl>
                <dt>
                  <label class="form-control-label">{$${item.name}}</label>
                </dt>
                <dd>
                  <div class="form-group clearfix">
                    <input name="${item.name}" class="form-control" value="${item.value}"/>
                  </div>
                </dd>
                </dl>`
                            })
                        .join('')
                        } else {
                            html = `<h3 class="example-title">${METLANG.no_data}</h3>`
                        }
                        modal
                            .find('.metadmin-fmbx')
                            .show()
                            .find('.tips')
                            .html(html)
                    }
                })
            }
        )
    }
    function renderSearchForm() {
        $(document).on('click', '.met-lang-web .btn-search-edit', function(event) {
            const index = $(this).data('index')
            that.activeData = that.langData[index]
        })
        M.component.modal_options['.lang-search-modal'] = {
            callback: function() {
                const modal = $('.lang-search-modal')
                const form = modal.find('form')
                formSaveCallback(form.attr('data-validate_order'), {
                    true_fun: function() {
                        alertify.confirm('是否刷新页面', function (ev) {
                            window.location.reload();
                        });
                    }
                })
                setTimeout(() => {
                    renderSearchModal(modal)
                }, 230)
            }
        }

        function renderSearchModal(modal) {
            const submit = modal.find('[data-ok]')
            const btn_search = modal.find('.btn-search')
            const hot_search = modal.find('.hot-search')
            const input = modal.find('.input')
            modal.find('[name="editor"]').val(that.activeData.mark)
            modal.find('[name="site"]').val('web')
            submit.attr('disabled', true)
            btn_search.off().click(function() {
                const val = modal.find('.input').val()
                search(val, modal)
            })
            input.off().keypress(function(e) {
                let keycode = e.keyCode ? e.keyCode : e.which
                if (keycode == '13') {
                    const val = modal.find('.input').val()
                    search(val, modal)
                }
            })
            hot_search.off().click(function() {
                const val = $(this).text()
                modal.find('.input').val(val)
                search(val, modal)
            })
        }
    }
    function submitParameter(modal) {
        const form = modal.find('form')
        const submit = modal.find('[data-ok]')
        submit.removeAttr('disabled')
        submit.off().click(function() {
            const formData = form.serializeArray()
            let values = {
                data: {}
            }
            formData.map(item => {
                if (item.name === 'editor' || item.name === 'site' || item.name === 'appno') {
                values[item.name] = item.value
                return
            }
            values['data'][item.name] = item.value
        })
            M.ajax(
                {
                    url: form.attr('action'),
                    data: values
                },
                function(result) {
                    metAjaxFun({
                        result: result,
                        true_fun: function() {
                            alertify.confirm('是否刷新页面', function (ev) {
                                window.location.reload();
                            });
                        }
                    })
                }
            )
        })
    }
    function renderEditAppForm() {
        $(document).on('click', '.met-lang-web .btn-edit-app', function(e) {
            const btn = $(e.target)
            const index = btn.data('index')
            that.activeData = that.langData[index]
            M.ajax(
                {
                    url: that.own_name + 'c=language_general&a=doGetAppList',
                    data: {
                        site: 0,
                        editor: that.activeData.mark
                    }
                },
                function(result) {
                    that.appData = result.data
                    const html = result.data ? result.data
                        .map((item, index) => {
                            return `<div class="card col-6" >
                                <div class="card-body">
                                  <h5 class="card-title">${item.appname}</h5>
                                  <p class="card-text">${METLANG.numbering}${METLANG.marks}${item.no}</p>
                                  <a href="${that.own_name}c=language_general&a=doExportPack&site=web&editor=${that.activeData.mark}&appno=${item.no}" >${METLANG.language_outputlang_v6}</a>
                                  <a
                                  href="javascript:;"
                                  data-index="${index}"
                                  data-toggle="modal"
                                  data-target=".lang-replaceApp-modal"
                                  data-modal-url="language/replace"
                                  data-modal-size="lg"
                                  data-modal-title="${METLANG.language_batchreplace_v6}"
                                  class="ml-2 btn-app-replace">${METLANG.language_batchreplace_v6}</a>
                                  <a
                                  href="javascript:;"
                                  data-index="${index}"
                                  data-toggle="modal"
                                  data-target=".lang-searchApp-modal"
                                  data-modal-url="language/search"
                                  data-modal-size="lg"
                                  data-modal-title="${METLANG.langwebeditor}"
                                  class="ml-2 btn-app-search">${METLANG.langwebeditor}</a>
                                </div>
                              </div>`
                        }):`<div class="text-center h5 mb-0 py-5">${METLANG.dataexplain1}</div>`
                    $.isArray(html) && html.join('');
                    $('.lang-app-modal .modal-body').html(html)
                }
            )
        })

        $(document).on('click', '.lang-app-modal .btn-app-search', function(e) {
            const btn = $(e.target)
            const index = btn.data('index')
            that.appActiveData = that.appData[index]
        })
        M.component.modal_options['.lang-searchApp-modal'] = {
            callback: function() {
                const modal = $('.lang-searchApp-modal')
                setTimeout(() => {
                    modal.find('[name="editor"]').val(that.activeData.mark)
                modal.find('[name="editor"]').after(`<input name="appno" hidden value="${that.appActiveData.no}"/>`)
                modal.find('[name="site"]').val('web')
            }, 230)
                modal
                    .find('.btn-search')
                    .off()
                    .click(function() {
                        const val = modal.find('.input').val()
                        search(val, modal)
                    })
                modal
                    .find('.input')
                    .off()
                    .keypress(function(e) {
                        let keycode = e.keyCode ? e.keyCode : e.which
                        if (keycode == '13') {
                            const val = modal.find('.input').val()
                            search(val, modal)
                        }
                    })
                modal
                    .find('.hot-search')
                    .off()
                    .click(function() {
                        const val = $(this).text()
                        modal.find('.input').val(val)
                        search(val, modal)
                    })
            }
        }

        $(document).on('click', '.lang-app-modal .btn-app-replace', function(e) {
            const btn = $(e.target)
            const index = btn.data('index')
            that.appActiveData = that.appData[index]
        })
        M.component.modal_options['.lang-replaceApp-modal'] = {
            callback: function() {
                const modal = $('.lang-replaceApp-modal')
                setTimeout(() => {
                    modal.find('[name="editor"]').after(`<input name="appno" hidden value="${that.appActiveData.no}"/>`)
                modal.find('[name="editor"]').val(that.activeData.mark)
                modal.find('[name="site"]').val('web')
            }, 230)
            }
        }
    }
    //同步语言
    function renderSyncForm() {
        $(document).on('click', '.met-lang-web .btn-sync', function(e) {
            const btn = $(e.target)
            const index = btn.data('index')
            that.activeData = that.langData[index]
            M.load(['alertify'], function() {

                alertify.confirm(METLANG.unitytxt_9 + "?", function (ev) {
                    metAlert(METLANG.being_synced,'',1);
                    M.ajax(
                        {
                            url: that.own_name + 'c=language_general&a=doSynLanguage',
                            data: {
                                site: 'web',
                                editor: that.activeData.mark
                            }
                        },
                        function(result) {
                            metAjaxFun({
                                result: result,
                                true_fun: function() {
                                },
                                false_fun: function() {
                                }
                            })
                            metAlert(' ');
                        }
                    )
                });
            })
        })

        $(document).on('shown.bs.modal', '.langweb-sync-modal', function(e) {
            let html = `
      <div class="p-2">
      <h4>${METLANG.being_synced}</h4>
      </div>
      ${M.component.loader({ class_name: 'w-100' })}
    `
            $('.langweb-sync-modal .modal-body').html(html)
        })
    }
    function renderReplaceForm() {
        $(document).on('click', '.met-lang-web .btn-replace', function(e) {
            const btn = $(e.target)
            const index = btn.data('index')
            that.activeData = that.langData[index]
        })
        M.component.modal_options['.langweb-replace-modal'] = {
            callback: function() {
                const modal = $('.langweb-replace-modal')
                setTimeout(() => {
                    modal.find('[name="editor"]').val(that.activeData.mark)
                    modal.find('[name="site"]').val('web')
                    const form = modal.find('form')
                    const order = form.attr('data-validate_order')
                    const btn = modal.find('[data-ok]')
                    validate[order].success(function(e, form) {
                        btn.attr('disabled', 'disabled')
                        btn.append(`<i class="fa fa-spinner fa-spin ml-2"></i>`)
                        alertify.success(METLANG.saving)
                        return false
                    }, false)
                    formSaveCallback(order, {
                        true_fun: function() {
                            btn.removeAttr('disabled')
                            btn.find('.fa').remove()
                        },
                        fasle_fun: function() {
                            btn.removeAttr('disabled')
                            btn.find('.fa').remove()
                        }
                    })
                }, 230)
            },
            modalType:'centered'
        }
    }
    $(document).on('click', '#lang-table .btn-export-pack', function (e) {
		alertify.confirm(`${METLANG.confirm}${METLANG.language_outputlang_v6}？`,  (ev)=>{
			location.href=$(this).data('url');
		});
	});
})()
