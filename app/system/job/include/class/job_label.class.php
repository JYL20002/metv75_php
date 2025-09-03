<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_label');

class job_label  extends base_label
{
    public function __construct()
    {
        global $_M;
        $this->construct('job', 6, $_M['config']['met_job_list']);
    }

    /**
     * 获取简历表单字段
     * @param string $id
     */
    public function get_module_form($id = '')
    {
        global $_M;
        if (!$id || !is_numeric($id)) return '';
        $data = $this->get_one_list_contents($id, 1, 0);    //获取职位信息
        $classnow = $data['class3'] ? $data['class3'] : ($data['class2'] ? $data['class2'] : $data['class1']);
        $parameter_label = load::mod_class('parameter/parameter_label', 'new');
        $parameter = $parameter_label->get_parameter($this->module, $data['class1'], $data['class2'], $data['class3']);

        $return['para'] = $parameter_label->build_parameter_form($this->module, $parameter);
        $return['config']['url'] = load::mod_class('job/job_handle', 'new')->module_form_url($classnow);
        $return['config']['url'] .= '?id=' . $classnow;
        $return['config']['classnow'] .= $classnow;
        $return['config']['lang']['cvtitle'] = $_M['word']['cvtitle'];
        $return['config']['lang']['title'] = '';
        return $return;
    }

    /**
     * 获取简历字段表单
     * @return array         简历表单数组
     */
    public function get_module_form_html($id = '')
    {
        global $_M;
        if (!$id || !is_numeric($id)) return '';
        $data = $this->get_one_list_contents($id, 1, 0);    //获取职位信息
        $classnow = $data['class3'] ? $data['class3'] : ($data['class2'] ? $data['class2'] : $data['class1']);
        $config_op = load::mod_class('config/config_op', 'new');
        $column_config = $config_op->getColumnConfArry($classnow);

        //csrf_token
        $_token = random('20');
        $_key = "job_{$id}_" . random(8);
        load::sys_class('session', 'new')->set($_key, $_token);

        $job = $this->get_module_form($id);
        $str = '';
        $str .= <<<EOT
		<form method='POST' class="met-form met-form-validation"  enctype="multipart/form-data" action='{$job['config']['url']}'>
		<input type="hidden" name="id" value="{$job['config']['classnow']}">
		<input type="hidden" name="lang" value="{$_M['lang']}">
		<input type="hidden" name="jobid" value="{$id}">
		<input type='hidden' name='token_' value='{$_token}' />
		<input type='hidden' name='form_key' value='{$_key}' />
EOT;
        foreach ($job['para'] as $key => $val) {
            $str .= <<<EOT
		{$val['type_html']}
EOT;
            //手机字段验证
            if (
                $column_config['met_cv_sms_tell']
                && $column_config['met_cv_sms_check']
                && $val['id'] == $column_config['met_cv_sms_tell']
            ) {
                $tel_field = $val['dataname'];
                $smsRandom = "sms_{$id}_";
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
     * @param $jobid
     * @param $paras
     * @param string $user_info
     * @return bool
     */
    public function insert_cv($jobid, $paras = array(), $user_info = '')
    {
        global $_M;
        if (!$jobid) {
            return false;
        }
        $data['jobid'] = $jobid;
        $data['ip'] = getip() ?: IP;
        $data['customerid'] = $user_info['username'] ?: '';
        $data['addtime'] = date('Y-m-d H:i:s', time());
        $data['lang'] = $_M['form']['lang'];
        $jid = load::mod_class('job/jobcv_database', 'new')->insert($data);
        if ($jid) {
            if (load::mod_class('parameter/parameter_op', 'new')->insert($jid, $this->module, $paras)) {
                return true;
            }
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
