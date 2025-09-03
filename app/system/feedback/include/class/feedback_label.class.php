<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_label');

/**
 * 反馈标签类
 */
class feedback_label extends base_label
{

    public function __construct()
    {
        global $_M;
        $this->construct('feedback', 8, 8);
    }

    /**
     * @param $cid 反馈栏目id
     * @return mixed
     */
    public function get_module_form($cid)
    {
        global $_M;
        $parameter_label = load::mod_class('parameter/parameter_label', 'new');
        $parameter = $parameter_label->get_parameter($this->module, $cid);
        $return['para'] = $parameter_label->build_parameter_form($this->module, $parameter);
        $return['config']['url'] = load::mod_class('feedback/feedback_handle', 'new')->module_form_url($cid);
        $return['config']['lang']['title'] = '';
        return $return;
    }

    /**
     * @param string $id
     * @param string $fdtitle
     * @return string
     */
    public function get_module_form_html($cid = '', $fdtitle = '')
    {
        global $_M;
        $class = load::sys_class('label', 'new')->get('column')->get_column_id($cid);
        if ($class['module'] != 8) {
            return '';
        }

        if ($_M['form']['fdtitle']) {
            $fdtitle = urlencode($_M['form']['fdtitle']);
        }

        $config_op = load::mod_class('config/config_op', 'new');
        $column_config = $config_op->getColumnConfArry($cid);

        //csrf_token
        $_token = random('20');
        $_key = "fd_{$cid}_" . random(8);
        load::sys_class('session', 'new')->set($_key, $_token);

        $feedback = $this->get_module_form($cid);
        $referer = $_M['form']['fdtitle'] ? HTTP_REFERER : '';
        $str = '';
        $str .= <<<EOT
		<form method='POST' class="met-form met-form-validation" enctype="multipart/form-data" action='{$feedback['config']['url']}'>
		<input type='hidden' name='id' value="{$cid}" />
		<input type='hidden' name='lang' value="{$_M['lang']}" />
		<input type='hidden' name='fdtitle' value='{$fdtitle}' />
		<input type='hidden' name='referer' value='{$referer}' />
		<input type='hidden' name='token_' value='{$_token}' />
		<input type='hidden' name='form_key' value='{$_key}' />
EOT;
        foreach ($feedback['para'] as $key => $val) {
            $str .= <<<EOT
		{$val['type_html']}
EOT;
            //手机字段验证
            if (
                $column_config['met_fd_sms_tell']
                && $column_config['met_fd_sms_check']
                && $val['id'] == $column_config['met_fd_sms_tell']
            ) {
                $tel_field = $val['dataname'];
                $smsRandom = "sms_{$cid}_";
                $sms_url = "{$_M['url']['entrance']}?m=include&c=smscode&a=doSmsCode&lang={$_M['lang']}";
                $sms_code_div = "<div class='form-group'>
                    <label class='control-label'>{$_M['word']['memberbasicCell']}{$_M['word']['memberImgCode']}</label>
                    <div class='input-group input-group-icon'>
                        <input type='text' name='smsTicket' required class='form-control' placeholder='{$_M['word']['memberbasicCell']}{$_M['word']['memberImgCode']}' data-fv-notempty-message='{$_M['word']['noempty']}'>
                        <div class='input-group-addon p-0 border-none'>
                            <button type='button' data-url='{$sms_url}' data-tel_field='{$tel_field}' class='btn btn-primary btn-squared w-full btn-phone-code' data-retxt='{$_M['word']['resend']}'>
                                {$_M['word']['phonecode']}
                                <span class='badge'></span>
                            </button>
                        </div>
                    </div>
                    <input type='hidden' name='smsRandom' data-value='{$smsRandom}'/>
                </div>";

                $str .= $sms_code_div;
            }
        }
        $str .= "</form>";

        return $str;
    }

    /**
     * @param $id
     * @param array $paras
     * @param string $user_info
     * @return bool
     */
    public function insert_feedback($id, $paras = array(), $user_info = '')
    {
        global $_M;
        if (!$id) {
            return false;
        }
        $date = date('Y-m-d H:i:s', time());

        $data['class1'] = $id;
        $data['fdtitle'] = "{$paras['title']} ({$date})";
        $data['fromurl'] = $paras['fromurl'];
        $data['ip'] = getip() ?: IP;
        $data['customerid'] = $user_info['username'] ?: '';
        $data['lang'] = $paras['lang'];
        $data['addtime'] = $date;
        $fid = load::mod_class('feedback/feedback_database', 'new')->insert($data);
        if ($fid) {
            if (load::mod_class('parameter/parameter_op', 'new')->insert($fid, $this->module, $paras)) {
                return true;
            }
        }
    }

    public function get_module_list($id = '', $rows = '', $type = '', $order = '', $para = 0)
    {
        return;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
