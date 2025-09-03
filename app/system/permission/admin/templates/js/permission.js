/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
    var that = $.extend(true, {}, admin_module),
        other_hash=that.hash=='permission/role_list'?'permission/member_list':'permission/role_list',
        role_list='#role-list',
        member_list='#member-list',
        permission_list={};
    // 获取所有权限数据
    function getPermission(callback){
        if($.isEmptyObject(permission_list)){
            M.ajax({
                url:that.own_name+'c=Permission&a=doSysPermissions',
                success:res=>{
                    if(res.data){
                        $.each(res.data, function (index, element) {
                            var sub_permission={},
                                permission_item='';
                            function handle(list){
                                list=list.filter((value, index, array) => {
                                    var is_sub=parseInt(value.pid);
                                    if(is_sub){
                                        if(!sub_permission[is_sub]) sub_permission[is_sub]=[];
                                        sub_permission[is_sub].push(value);
                                    }
                                    return !is_sub;
                                });
                                function handle1(list1){
                                    return list1.map((value, index, array) => {
                                        if(sub_permission[value.id]){
                                            value.sub=handle1(sub_permission[value.id]);
                                        }
                                        return value;
                                    });
                                }
                                return handle1(list);
                            }
                            if($.isArray(element)){
                                permission_item=handle(element);
                            }else{
                                permission_item=[];
                                var time=new Date().getTime();
                                $.each(element, function (index1, element1) {
                                    var p_name=index1;
                                    switch(index1){
                                        case 'left':
                                            p_name='传统后台左侧栏菜单';
                                            break;
                                        case 'top':
                                            p_name='传统后台顶部栏菜单';
                                            break;
                                        case 'ui_set':
                                            p_name='可视化界面顶部栏菜单';
                                            break;
                                    };
                                    permission_item.push({
                                        p_name,
                                        p_code:time+'-'+index1,
                                        key:index1,
                                        sub:handle(element1)
                                    });
                                });
                            }
                            var p_name='';
                            switch(index){
                                case 'app':
                                    p_name='应用插件';
                                    break;
                                case 'columns':
                                    p_name='栏目管理';
                                    break;
                                case 'func':
                                    p_name='操作权限';
                                    break;
                                case 'menus':
                                    p_name='后台菜单显示';
                                    break;
                                case 'sys':
                                    p_name='功能模块操作权限';
                                    break;
                                case 'web_lang':
                                    p_name='语言权限';
                                    break;
                            }
                            permission_list[index]={
                                p_name,
                                key:index,
                                permission:permission_item||element
                            };
                        });

                        var sub_column=permission_list.sys.permission.find((value, index, array) => value.p_code=='s_1301');
                        sub_column.sub=[
                            {
                                sub:sub_column.sub,
                                sub_class:'pl-0',
                            },
                            ...permission_list.columns.permission.map((value, index, array) => {
                                value.wrapper_class='bg-grey p-2 mt-2 rounded-lg';
                                return value;
                            })
                        ]
                        delete permission_list.columns;

                        var sub_app=permission_list.sys.permission.find((value, index, array) => value.p_code=='s_1505');
                        sub_app.sub=[
                            {
                                sub:sub_app.sub,
                                sub_class:'pl-0',
                            },
                            {
                                sub:permission_list.app.permission,
                                sub_class:'bg-grey p-2 mt-2 rounded-lg',
                            }
                        ];
                        delete permission_list.app;

                        $.each(permission_list, function (index, element) {
                            has_sub=element.permission.find((value, index, array) => value.sub);
                            has_sub && element.permission.forEach((value,index1) => {
                                if(index1) value.class='border-top pt-2 mt-2';
                            });
                        });

                        callback && callback();
                    }
                }
            });
        }else{
            callback && callback();
        }
    }
    function randerList(){
        M.component.commonList(function(thats,table_order){
            return {
                ajax:{
                    dataSrc:function(result){
                        var data=[];
                        if(result.data){
                            // 角色列表加载
                            if(thats.hash=='permission/role_list'){
                                var del_url=that.own_name+'c=Role&a=doDel&allid=&id=';
                                data=result.data.map((value, index, array) => {
                                    return [
                                        value.name,
                                        `<button type="button" class="btn btn-primary btn-edit"
                                        data-toggle="modal"
                                        data-target=".role-edit-modal"
                                        data-modal-title="${METLANG.editor}"
                                        data-modal-url="permission/role_edit/?c=Role&a=doRoleInfo&id=${value.id}"
                                        >${METLANG.editor}</button>
                                        ${M.component.btn('del',{del_url:del_url+value.id,confirm_title:'确定要删除该角色？删除之后无法再恢复。'})}`
                                    ];
                                });
                            }else{
                                // 成员列表加载
                                var del_url=that.own_name+'c=MemberAdmin&a=doDel&allid=&id=';
                                result.data=[
                                    result.data.own,
                                    ...result.data.list,
                                ];
                                data=result.data.map((value, index, array) => {
                                    return [
                                        value.admin_id,
                                        value.role?value.role.name:'',
                                        value.admin_name,
                                        value.admin_login,
                                        value.admin_modify_date,
                                        value.admin_modify_ip,
                                        index?`<button type="button" class="btn btn-primary btn-edit"
                                        data-toggle="modal"
                                        data-target=".member-edit-modal"
                                        data-modal-title="${METLANG.editor}"
                                        data-modal-url="permission/member_edit/?c=MemberAdmin&a=doMemberInfo&id=${value.id}"
                                        >${METLANG.editor}</button>
                                        ${M.component.btn('del',{del_url:del_url+value.id,confirm_title:'确定要删除该成员？删除之后无法再恢复。'})}`
                                        :`<button type="button" class="btn btn-primary btn-edit"
                                        data-toggle="modal"
                                        data-target=".member-sys-edit-modal"
                                        data-modal-type="centered"
                                        data-modal-tablerefresh="${member_list}"
                                        data-modal-title="${METLANG.modify_information}"
                                        data-modal-url="admin/admin_sys/?n=admin&c=index&a=doInfo&modal=1"
                                        >${METLANG.modify_information}</button>`
                                    ];
                                });
                            }
                        }
                        return data;
                    }
                }
            };
        });
    }
    randerList();
	TEMPLOADFUNS[other_hash]=function(){
		randerList();
	}
    // 角色、成员编辑、添加-弹框
    M.component.modal_options['.member-edit-modal'] =
    M.component.modal_options['.role-edit-modal'] = {
        modalSize:'lg',
        modalFullheight:1,
        modalHeight100:1,
        callback:function(key){
            var is_role=key=='.role-edit-modal';
            // 渲染权限列表
            getPermission(()=>{
                var role_val=JSON.parse($('.handle-json',key).val()),
                    permissions=role_val.permissions||{};
                var content_data=permission_list.sys.permission.find((value, index, array) => value.p_code=='s_1301');
                content_data.sub.forEach((value, index, array) => {
                    if(value.wrapper_class){
                        var langok=role_val.langok.find((value1) => value1.mark==value.key);
                        value.p_name=langok.name;
                    }
                });
                var time=new Date().getTime(),
                    check_html = function ({
                        list = [],
                        level = 0,
                        first_level_class = '',
                        first_level_label_style = '',
                        first_level_input_class = '',
                        name = 'permission',
                        required=0,
                        required_message=0,
                        match_parent=-1,
                    }) {
                        var has_sub=list.find((value, index, array) => value.sub);
                        return list.map((value, index, array) =>{
                            var html=value.p_name?`<div class="custom-control custom-checkbox ${value.class} ${has_sub?'':'custom-control-inline'} ${!level&&first_level_class?first_level_class:''}">
                                <input type="checkbox" id="${value.p_code}-${time}" name="${name}" value="${value.p_code}"
                                ${required&&!index&&!level?`required ${required_message?`data-fv-notempty-message="${required_message}"`:''}`:''}
                                ${permissions[value.p_code]?'checked':''}
                                class="custom-control-input checkall-item ${first_level_input_class} ${value.sub?'checkall-all':''}"/>
                                <label class="custom-control-label" for="${value.p_code}-${time}" ${!level&&first_level_label_style?`style="${first_level_label_style}"`:''}>${value.p_name}</label>
                            </div>`:'';
                            if(value.sub) html=`<div class='${value.p_name?`checkall-parents`:''} ${value.wrapper_class||''}' ${value.p_name&&match_parent>=0?`data-match_parent="${match_parent}"`:''}>
                                ${html}
                                <div class='${value.sub_class||'pl-4'}'>${check_html({
                                    ...arguments[0],
                                    list:value.sub,
                                    level:level+1,
                                })}</div>
                            </div>`;
                            return html;
                        }).join('');
                    },
                    permission_html=`<dl>
                        <dt>
                            <label class="form-control-label">${permission_list.web_lang.p_name}</label>
                        </dt>
                        <dd class="form-group clearfix" data-plugin="checkAll">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="langok_all-${time}" name="permission" value="langok-all"
                                class="custom-control-input checkall-all checkitem-web-lang"/>
                                <label class="custom-control-label" for="langok_all-${time}">${METLANG.admintips1}</label>
                            </div>
                            <div class='pl-4'>
                                ${check_html({
                                    list: permission_list.web_lang.permission,
                                    first_level_input_class: 'checkitem-web-lang',
                                    name: 'web_lang',
                                    required: 1,
                                    required_message:METLANG.admintips2
                                })}
                            </div>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label class="form-control-label">${permission_list.func.p_name}</label>
                        </dt>
                        <dd class="form-group clearfix" data-plugin="checkAll" data-match_parent="2">
                            ${check_html({list:permission_list.func.permission})}
                        </dd>
                    </dl>
                    <dl class='border-top'>
                        <dt>
                            <label class="form-control-label">${permission_list.sys.p_name}</label>
                        </dt>
                        <dd class="form-group clearfix" data-plugin="checkAll">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="sys_all-${time}" name="permission" value="sys-all"
                                class="custom-control-input checkall-all checkitem-web-lang"/>
                                <label class="custom-control-label" for="sys_all-${time}">${METLANG.adminSelectAll}</label>
                            </div>
                            <div class='border p-2 mt-2'>${check_html({
                                list:permission_list.sys.permission,
                                match_parent:2,
                                first_level_class:'h5 mb-2',
                                first_level_label_style:'line-height: 1.2;',
                            })}</div>
                        </dd>
                    </dl>
                    <dl class='border-top'>
                        <dt>
                            <label class="form-control-label">${permission_list.menus.p_name}</label>
                        </dt>
                        <dd class="form-group clearfix" data-plugin="checkAll">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="menus_all-${time}" name="permission" value="menus-all"
                                class="custom-control-input checkall-all checkitem-web-lang"/>
                                <label class="custom-control-label" for="menus_all-${time}">${METLANG.adminSelectAll}</label>
                            </div>
                            <div class='border p-2 mt-2'>${check_html({
                                list:permission_list.menus.permission,
                                match_parent:2,
                                first_level_class:'h5 mb-2',
                                first_level_label_style:'line-height: 1.2;',
                            })}</div>
                        </dd>
                    </dl>`;
                    $('.permission-list',key).html(permission_html).metCommon();
                    $('.permission-list',key).metFormAddField();
                    M.load('checkall',()=>{
                        setTimeout(()=>{
                            $('.permission-list .checkall-item:checked',key).trigger('subchange');
                        },0);
                    });
            });
            // 成员设置部分
            if(!is_role){
                M.ajax({
                    url:that.own_name+'c=MemberAdmin&a=doGetRoles',
                    success:res=>{
                        if(res.data){
                            // 渲染所属角色下拉框
                            var html=`<option value="">${METLANG.please_choose}</option>`+res.data.map((value, index, array) => `<option value="${value.id}">${value.name}</option>`),
                                $role_id=$('[name="role_id"]',key);
                            $role_id.html(html);
                            if($('[name="id"]',key).val()){
                                $role_id.val($role_id.data('checked'));
                            }else{
                                interval({
                                    true_val:function(){
                                        return $('.permission-list',key).html();
                                    },
                                    true_fun:function(){
                                        $('.permission-list .custom-control-input[type="checkbox"]',key).prop('checked',true);
                                    }
                                });
                            }
                        }
                    }
                });
            }
        }
    };
    // 角色、成员编辑、添加-保存
    $(document).on('click','.role-edit-modal .btn[data-ok],.member-edit-modal .btn[data-ok]',function(){
        var $modal=$(this).parents('.modal'),
            is_role=$modal.attr('data-key')=='.role-edit-modal';
        if($modal.find('.has-danger').length) return;
        $(this).clickLoading();
        var form_data=$modal.find('form').serializeArray(),
            new_form_data={
                permissions:{},
            };
        form_data.forEach((value, index, array) => {
            if(value.name!='permission') new_form_data[value.name]=value.value;
        });
        $modal.find('form .checkall-item').each(function (index, domEle) {
            var name=$(this).val();
            if(name.indexOf('-')<0) new_form_data.permissions[name]=$(this).prop('checked')?1:0;
        });
        M.ajax({
            url:`${that.own_name}c=${is_role?'Role':'MemberAdmin'}&a=do${new_form_data.id?'EditSave':'AddSave'}`,
            data:new_form_data,
            success:res=>{
                $(this).clickLoading('stop');
                metAjaxFun({
                    result: res,
                    true_fun: ()=>{
                        $modal.modal('hide');
                        datatable[is_role?role_list:member_list].row().draw(false);
                    },
                });
            }
        })
    });
    // 成员编辑-继承角色的权限-切换
    $(document).on('change','.member-edit-modal [name="sync_role_permissions"],.member-edit-modal [name="role_id"]',function(){
        var $modal=$('.member-edit-modal');
        if(!$modal.find('.permission-list').html()) return;
        var val=parseInt($modal.find('[name="sync_role_permissions"]').val());
        $modal.find('.permission-list .checkall-item:checked').prop('checked',false).change();
        // // 显示所属角色的权限
        if(val){
            var role_id=$modal.find('[name="role_id"]').val();
            M.ajax({
                url:that.own_name+'c=Role&a=doRoleInfo&ajax=1&id='+role_id,
                success:res=>{
                    if(res.data){
                        $.each(res.data.permissions, function (index, element) {
                            element && $modal.find(`.permission-list .checkall-item[value="${index}"]`).prop('checked',true);
                        });
                        $modal.find('.permission-list .checkall-item:checked').trigger('subchange');
                        $modal.find('.checkall-item.checkitem-web-lang').change();
                    }
                }
            });
        }
    });
})();