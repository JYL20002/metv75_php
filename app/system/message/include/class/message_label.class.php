<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_label');

class message_label extends base_label
{
    public function __construct()
    {
        global $_M;
        $this->construct('message', 7, $_M['config']['met_message_list']);
    }

    public function get_list_page($id = '', $page = '', $para = 1)
    {
        global $_M;
        $message_list = parent::get_list_page($id, $page, $para);
        $config_op = load::mod_class('config/config_op', 'new');
        $column_config = $config_op->getColumnConfArry($id);

        $data = array();
        foreach ($message_list as $message) {

            $list = load::mod_class('parameter/parameter_database', 'new')->get_list($message['id'], 7);
            foreach ($list as $key => $val) {
                if ($val['paraid'] == $column_config['met_msg_name_field']) {
                    $message['name'] = $val['info'];
                }
                if ($val['paraid'] == $column_config['met_msg_content_field']) {
                    $message['content'] = $val['info'];
                }
            }

            //没有留言回复内容时默认调用邮件回复内容
            $message['useinfo'] = $message['useinfo'] ? $message['useinfo'] : $column_config['met_msg_content'];

            $data[] = $message;
        }
        return $data ?: array();
    }

    /**
     * 留言表单参数
     * @param string $cid
     * @return mixed
     */
    public function get_module_form($cid = '')
    {
        global $_M;
        $parameter_label = load::mod_class('parameter/parameter_label', 'new');
        $parameter = $parameter_label->get_parameter($this->module, $cid);

        $return['para'] = $parameter_label->build_parameter_form($this->module, $parameter);
        $return['config']['url'] = load::mod_class('message/message_handle', 'new')->module_form_url($cid);
        $return['config']['url'] .= '&id=' . $cid;
        $return['config']['lang']['submit'] = $_M['word']['SubmitInfo'];
        $return['config']['lang']['title'] = '';
        return $return;
    }

    /**
     * 获取简历字段表单
     * @param string $cid
     * @return string
     */
    public function get_module_form_html($cid = '')
    {
        global $_M;
        $class = load::sys_class('label', 'new')->get('column')->get_column_id($cid);
        if ($class['module'] != 7) {
            return '';
        }

        $config_op = load::mod_class('config/config_op', 'new');
        $column_config = $config_op->getColumnConfArry($cid);

        //csrf_token
        $_token = random('20');
        $_key = "msg_{$cid}_" . random(8);
        load::sys_class('session', 'new')->set($_key, $_token);

        $message = $this->get_module_form($cid);
        $str = '';
        $str .= <<<EOT
        <form method='POST' class="met-form met-form-validation"  enctype="multipart/form-data" action='{$message['config']['url']}'>
        <input type='hidden' name='lang' value='{$_M['lang']}' />
        <input type='hidden' name='token_' value='{$_token}' />
		<input type='hidden' name='form_key' value='{$_key}' />
EOT;
        foreach ($message['para'] as $key => $val) {
            $str .= <<<EOT
		{$val['type_html']}
EOT;

            //手机字段验证
            if (
                $column_config['met_msg_sms_field']
                && $column_config['met_msg_sms_check']
                && $val['id'] == $column_config['met_msg_sms_field']
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
     * @param $paras
     * @param $customerid
     * @param $addtime
     * @param $ip
     * @return bool
     */
    public function insert_message($paras = array(), $user_info = array())
    {
        global $_M;
        if (!$paras) {
            return false;
        }
        $date = date('Y-m-d H:i:s', time());

        $data['ip'] = getip() ?: IP;
        $data['customerid'] = $user_info['username'] ?: '';
        $data['addtime'] = $date;
        $data['lang'] = $_M['form']['lang'];

        $mid = load::mod_class('message/message_database', 'new')->insert($data);
        if ($mid) {
            if (load::mod_class('parameter/parameter_op', 'new')->insert($mid, $this->module, $paras)) {
                return $mid;
            }
        }

        return false;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
