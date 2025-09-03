<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('handle');

class parameter_handle extends handle
{

    /**
     * @param string $module 模块
     * @param array $para 属性列表
     * @param array $column_config 栏目设置
     * @param bool $simplify 表单样式（简洁/详细）
     * @return array
     */
    public function para_handle_formation($module = '',$para = array(), $simplify = true)
    {
        global $_M;
        $tag_id = uniqid();

        foreach ($para as $key => $val) {
            if ($val['type_class'] != 'ftype_input ftype_code') {
                $val['para'] = "para{$val['id']}";// 兼容v5模板
                $val['dataname'] = "para{$val['id']}";
                $val['placeholder'] = $val['description'] ? $val['description'] : $val['name'];
                $val['type_html'] = '';
                // $val['simplify'] = 0;

                if ($simplify && in_array($val['type'], array(1, 2, 3, 5, 8, 9))) {
                    // $val['simplify'] = 1;
                } else {//下拉|单选|多选|附件
                    $val['type_html'] = "<label class='control-label'>{$val['name']}</label>";
                }

                $fv_type = '';
                $wr_ok = $val['wr_ok'] ? "data-fv-notempty=\"true\" data-fv-message=\"{$_M['word']['Empty']}\"{$fv_type}" : '';

                //更具类型渲染表单
                switch ($val['type']) {
                    case 1:
                        $val['type_html'] .= "<input name='{$val['dataname']}' class='form-control' type='text' placeholder='{$val['placeholder']}' {$wr_ok} />";
                        break;
                    case 2:
                        if ($val['related']) {
                            $val['para_list'] = self::related_product($val['related']);
                        }
                        if ($val['productlist']) {
                            $val['type_html'] .= "<select name='{$val['dataname']}' class='form-control' {$wr_ok}>";
                            $val['type_html'] .= "<option value=''>{$val['placeholder']}</option>";
                            if (is_numeric($val['productlist'])) {
                                $product = load::sys_class('label', 'new')->get('product')->get_module_list($val['productlist']);
                            } else {
                                $product = load::sys_class('label', 'new')->get('product')->get_module_list();
                            }
                            foreach ($product as $pv) {
                                if ($_M['form']['title'] == $pv['title']) {
                                    $selected = "selected='selected'";
                                } else {
                                    $selected = "";
                                }
                                $val['type_html'] .= "<option value='" . $pv['title'] . "' {$selected}>" . $pv['title'] . "</option>";
                            }
                            $val['type_html'] .= "</select>";
                        } else {
                            $val['type_html'] .= "<select name='{$val['dataname']}' class='form-control' {$wr_ok}>";
                            $val['type_html'] .= "<option value=''>{$val['placeholder']}</option>";
                            foreach ($val['para_list'] as $key => $pv) {
                                if (is_array($pv)) {
                                    if ($_M['form']['fdtitle'] == urldecode($pv['value'])) {
                                        $product_selected = "selected=selected";
                                    } else {
                                        $product_selected = '';
                                    }
                                    $val['type_html'] .= "<option value='" . $pv['value'] . "' {$product_selected}>" . $pv['value'] . "</option>";
                                } else {
                                    $val['type_html'] .= "<option value='" . $pv . "'>" . $pv . "</option>";
                                }
                            }
                            $val['type_html'] .= "</select>";
                        }
                        break;
                    case 3:
                        $val['type_html'] .= "<textarea name='{$val['dataname']}' class='form-control' {$wr_ok} placeholder='{$val['placeholder']}' rows='5'></textarea>";
                        break;
                    case 4:
                        if ($val['related']) {
                            $val['para_list'] = self::related_product($val['related']);
                        }
                        $i = 0;
                        foreach ($val['para_list'] as $key => $pv) {
                            $i++;
                            $pv['required'] = $key ? '' : $wr_ok . '';
                            if (is_array($pv)) {
                                if ($_M['form']['fdtitle'] == urldecode($pv['value'])) {
                                    $product_selected = "checked=checked";
                                } else {
                                    $product_selected = '';
                                }
                                $val['type_html'] .= "
 								<div class=\"checkbox-custom checkbox-primary\">
 									<input
 										name=\"para{$val['id']}\"
 										type=\"checkbox\"
 										value=\"{$pv['value']}\"
 										id=\"para{$val['id']}_{$i}_{$tag_id}\"
 										{$product_selected}
 										{$pv['required']}
 										data-delimiter=',';
 									>
 									<label for=\"para{$val['id']}_{$i}_{$tag_id}\">{$pv['value']}</label>
 								</div>";
                            } else {
                                $val['type_html'] .= "
 								<div class=\"checkbox-custom checkbox-primary\">
 									<input
 										name=\"para{$val['id']}\"
 										type=\"checkbox\"
 										value='{$pv}'
 										id=\"para{$val['id']}_{$i}_{$tag_id}\"
 									>
 									<label for=\"para{$val['id']}_{$i}_{$tag_id}\">{$pv}</label>
 								</div>";
                            }

                        }
                        break;
                    case 5:
                        $val['type_html'] .= "
 						<div class='input-group input-group-file'>
 							<label class='input-group-btn'>
 								<span class='btn btn-primary btn-file'>
 									<i class='icon wb-upload'></i>
 									<input type='file' name='{$val['dataname']}' multiple class='invisible'>
 								</span>
 							</label>
 							<input type='text' name='{$val['dataname']}' class='form-control' {$wr_ok} placeholder='{$val['placeholder']}' readonly>
 						</div>";
                        break;
                    case 6:
                        if ($val['related']) {
                            $val['para_list'] = self::related_product($val['related']);
                        }
                        $i = 0;
                        foreach ($val['para_list'] as $pv) {
                            $i++;
                            $checked = $i == 1 ? 'checked' : '';
                            if (is_array($pv)) {
                                if ($_M['form']['fdtitle'] == urldecode($pv['value'])) {
                                    $product_selected = "checked=checked";
                                } else {
                                    $product_selected = $checked;
                                }
                                $val['type_html'] .= "
 								<div class=\"radio-custom radio-primary\">
 									<input
 										name=\"para{$val['id']}\"
 										type=\"radio\"
 										{$product_selected}
 										value=\"{$pv['value']}\"
 										id=\"para{$val['id']}_{$i}_{$tag_id}\"
 									>
 									<label for=\"para{$val['id']}_{$i}_{$tag_id}\">{$pv['value']}</label>
 								</div>";
                            } else {
                                $val['type_html'] .= "
 								<div class=\"radio-custom radio-primary\">
 									<input
 										name=\"para{$val['id']}\"
 										type=\"radio\"
 										{$checked}
 										value='{$pv}'
 										id=\"para{$val['id']}_{$i}_{$tag_id}\"
 									>
 									<label for=\"para{$val['id']}_{$i}_{$tag_id}\">{$pv}</label>
 								</div>";
                            }

                        }
                        break;
                    case 8;
                        //电话
                        $fv_tel = " data-fv-phone='true' data-fv-phone-message='{$val['name']}{$_M['word']['formaterror']}'";
                        $val['type_html'] .= "<input name='{$val['dataname']}' class='form-control' type='tel' placeholder='{$val['placeholder']}' {$fv_tel} {$wr_ok} />";
                        break;
                    case 9;
                        //邮箱
                        $fv_email = " data-fv-emailAddress='true' data-fv-emailaddress-message='{$val['name']}{$_M['word']['formaterror']}'";
                        $val['type_html'] .= "<input name='{$val['dataname']}' class='form-control' type='email' placeholder='{$val['placeholder']}' {$fv_email} {$wr_ok} />";
                        break;
                }
                $required = $val['wr_ok'] ? 'required' : '';
                $val['type_html'] = "<div class='form-group {$required}' data-name=\"{$val['name']}\" data-description=\"{$val['description']}\">{$val['type_html']}</div>";
                $list[] = $val;
            }
        }

        //前台验证码
        if ($_M['config']['met_memberlogin_code']) {    //系统图形验证码
            $memberlogin_code['type_html'] = "<div class='form-group'>";
            if (!$simplify) {
                $memberlogin_code['type_html'] .= "<label class='control-label'>{$_M['word']['memberImgCode']}</label>";
            }
            $memberlogin_code['type_html'] .= "<div class='input-group input-group-icon'>
					<input name='code' type='text' class='form-control input-codeimg' placeholder='{$_M['word']['memberImgCode']}' required data-fv-message='{$_M['word']['Empty']}'>
					<span class='input-group-addon p-5'>
                        <img data-src='{$_M['url']['entrance']}?m=include&c=ajax_pin&a=dogetpin' class='met-getcode' title='{$_M['word']['memberTip1']}' align='absmiddle' role='button' hidden>
                        <input name='random' type='hidden'>
					</span>
				</div>
			</div>";
            $memberlogin_code['type'] = 100;
            $list[] = $memberlogin_code;
        }

        // 信息安全声明
        if ($_M['config']['met_info_security_statement_open']) {
            $info_security_statement_json = array(
                'title' => $_M['config']['met_info_security_statement_modal_title'],
                'content' => $_M['config']['met_info_security_statement_content'],
            );
            $info_security_statement_json = json_encode($info_security_statement_json, JSON_UNESCAPED_UNICODE);
            $info_security_statement['type_html'] = "<div class='form-group'>";
            if (!$simplify) {
                $info_security_statement['type_html'] .= "<label class='control-label'></label>";
            }
            $info_security_statement_title=$_M['config']['met_info_security_statement_title'];
            if(strstr($info_security_statement_title,'《')){
                $info_security_statement_title = preg_replace_callback('/《(.*?)》/mu', function($match){
					return "<a href='javascript:;' data-toggle='modal' data-target='.info-security-statement-modal' class='text-danger'>{$match[0]}</a>";
				}, $info_security_statement_title);
            }else{
                $info_security_statement_title="<a href='javascript:;' data-toggle='modal' data-target='.info-security-statement-modal' class='text-danger'>{$info_security_statement_title}</a>";
            }
            $info_security_statement['type_html'] .= "<div class='checkbox-custom checkbox-primary m-y-0'>
                    <input type='checkbox' name='info_security_statement_agreement' value='1' id='info_security_statement' required data-fv-notEmpty-message='{$_M['word']['user_agreement_tips3']}'>
                    <label for='info_security_statement'>
                        {$info_security_statement_title}
                    </label>
                </div>
                <script>
                (function(){
                    var this_interval=setInterval(function(){
                            if(typeof M!='undefined'){
                                M.info_security_statement={$info_security_statement_json};
                                clearInterval(this_interval);
                            }
                        },200);
                })();
                </script>
            </div>";
            $info_security_statement['type'] = 100;
            $list[] = $info_security_statement;
        }

        // 提交按钮
        $submit['type_html'] = "<div class='form-group m-b-0'>
            <button type='submit' class='btn btn-primary btn-squared" . ($module != '6' ? ' btn-block' : '') . ($module == '8' ? ' btn-lg' : '') . "'>{$_M['word'][$module=='7'?'SubmitInfo':'Submit']}</button>"
            . ($module == '6' ? "<button type='button' class='btn btn-default btn-squared m-l-5' data-dismiss='modal'>{$_M['word']['cancel']}</button>" : '')
            . "</div>";
        $submit['type'] = 100;
        $list[] = $submit;
        return $list;
    }

    public function related_product($related)
    {
        global $_M;
        $product_database = load::mod_class('product/product_database', 'new');
        list($class1, $class2, $class3) = explode('-', $related);
        $product = $product_database->get_list_by_class123($class1, $class2, $class3);
        foreach ($product as $key => $p) {
            $product[$key]['id'] = $p['id'];
            $product[$key]['value'] = $p['title'];
        }
        return $product;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>