<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class job extends web
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->database = load::mod_class('job/job_database', 'new');
    }

    /**
     * 获取栏目配置信息
     * @param string $class
     * @return mixed
     */
    public function getClsaaConfig($class = '')
    {
        global $_M;
        if (!$this->conlum_configs) {
            $config_op = load::mod_class('config/config_op', 'new');
            $config = $config_op->getColumnConfArry($class);
            $this->conlum_configs = $config;
            return $config;
        } else {
            return $this->conlum_configs;
        }
    }

    public function dojob()
    {
        global $_M;
        if ($this->listpage('job') == 'list') {
            $_M['config']['met_job_list'] = $this->input['list_length'] ? $this->input['list_length'] : $_M['config']['met_job_list'];
            load::sys_class('handle', 'new')->redirectUrl($this->input); //伪静态时动态链接跳转

            $this->view('job', $this->input);
        } else {
            $this->doshowjob();
        }
    }

    public function doshowjob()
    {
        global $_M;
        $this->showpage('job');
        load::sys_class('handle', 'new')->redirectUrl($this->input); //伪静态时动态链接跳转
        $this->view('showjob', $this->input);
    }

    public function docvjob()
    {
        global $_M;
        $this->doshowjob();
        return;
    }

    /****************************/
    /**
     * 保存提交简历
     */
    public function dosave()
    {
        global $_M;
        $this->check_field();
        $info = $_M['form'];
        $jobid = $_M['form']['jobid'];
        $classnow = $_M['form']['id'];

        $conlum_configs = $this->getClsaaConfig($classnow);

        if (self::checkCaptcha($conlum_configs) && self::checkSmsToken($conlum_configs) && self::checkWords() && self::checkTime($conlum_configs) && self::checkToken()) {
            foreach ($_FILES as $key => $value) {
                if ($value['tmp_name']) {
                    $this->upfile = load::sys_class('upfile', 'new');
                    $ret = $this->upfile->upload($key); //上传文件
                    if ($ret['path'] != '') {
                        $info[$key] = $ret['path'];
                    } else {
                        okinfo('-1', "{$_M['word']['opfailed']} [{$ret['error']}]");
                    }
                }
            }
        }

        $user_info = $this->get_login_user_info();
        if (load::sys_class('label', 'new')->get('job')->insert_cv($jobid, $info, $user_info)) {
            //邮件通知
            $this->notice_by_emial($info);
            //短信通知
            $this->notice_by_sms($info);
        }
        okinfo(HTTP_REFERER, $_M['word']['success']);
    }

    /**
     * 字段关键词过滤
     * @return bool
     */
    protected function checkWords()
    {
        global $_M;
        $met_fd_word = DB::get_one("SELECT * FROM {$_M['table']['config']} WHERE lang ='{$_M['form']['lang']}' AND name= 'met_fd_word' AND columnid = 0");
        $met_fd_word_arr = explode("|", $met_fd_word['value']);
        if ($met_fd_word['value'] == '') {
            return true;
        }

        $para_list = load::mod_class('parameter/parameter_label', 'new')->get_parameter(6);
        $content = '';
        foreach ($para_list as $key => $val) {
            $para = "para" . $val['id'];
            $content = $content . "-" . $_M['form'][$para];
        }

        foreach ($met_fd_word_arr as $key => $word) {
            if ($word == '') {
                continue;
            }

            if (strstr($content, $word)) {
                $msg = "{$_M['word']['Feedback3']} [" . $word . "] ";
                okinfo('-1', $msg);
                die();
            }
        }
        return true;
    }

    /**
     * 表单提交时间检测
     * @param array $conlum_configs
     * @return bool
     */
    protected function checkTime($conlum_configs = array())
    {
        global $_M;
        $ip = getip();
        $ipok = DB::get_one("SELECT * FROM {$_M['table']['cv']} WHERE ip='{$ip}' ORDER BY addtime DESC");
        if ($ipok) {
            $time1 = strtotime($ipok['addtime']);
        } else {
            $time1 = 0;
        }
        $submit = load::sys_class('session', 'new')->get('submit');
        $time2 = time();
        $timeok = (float)($time2 - $time1);
        $timeok2 = (float)($time2 - $submit);
        if ($timeok <= $conlum_configs['met_cv_time'] && $timeok2 <= $conlum_configs['met_cv_time']) {
            $fd_time = "{$_M['word']['Feedback1']}" . $conlum_configs['met_cv_time'] . "{$_M['word']['Feedback2']}";
            okinfo('-1', $fd_time);
        } else {
            load::sys_class('session', 'new')->set('submit', time());
            return true;
        }
    }

    /**
     * csrf token check
     * @param array $conlum_configs
     * @return bool
     */
    protected function checkToken($conlum_configs = array())
    {
        //return true;
        global $_M;
        if ($_M['config']['met_webhtm'])  return true;

        $_key = $_M['form']['form_key'];
        $_token = $_M['form']['token_'];
        $session = load::sys_class('session', 'new');
        $s_token = $session->get($_key);

        if (!$_token || $s_token != $_token) {
            okinfo('-1', 'errno 403');
        }
        $session->del($_key);
        return true;
    }

    /**
     * 图形验证码
     * @param array $conlum_configs
     * @return bool
     */
    protected function checkCaptcha($conlum_configs = array())
    {
        global $_M;
        if ($_M['config']['met_memberlogin_code']) {
            if (!load::sys_class('pin', 'new')->check_pin($_M['form']['code'], $_M['form']['random'])) {
                okinfo(-1, $_M['word']['membercode']);
            }
        } elseif ($_M['config']['met_captcha_open']) {
            //图形验证插件
            $checkCode = load::app_class('met_captcha/include/captcha', 'new')->checkCode($_REQUEST['Ticket'], $_REQUEST['Randst']);
            if (!$checkCode) {
                okinfo(-1, $_M['word']['membercode']);
            }
        }
        return true;
    }

    /**
     * 手机验证码
     * @param array $conlum_configs
     * @return bool
     */
    protected function checkSmsToken($conlum_configs = array())
    {
        global $_M;
        if (!$conlum_configs['met_cv_sms_check']) return true;

        $met_fd_sms_tell = 'para' . $conlum_configs['met_cv_sms_tell'];
        $tel = $_M['form'][$met_fd_sms_tell];
        $smsRandom = $_M['form']['smsRandom'];
        $smsTicket = $_M['form']['smsTicket'];
        $verifier = load::sys_class('verifier', 'new');
        $res = $verifier->checkSmsCode($smsTicket, $tel, $smsRandom);
        if (!$res) {
            $msg = $verifier->error ?: 'errno 403';
            okinfo('-1', $msg);
        }
        return true;
    }

    /**
     * 通过邮箱通知
     * @param $info
     */
    public function notice_by_emial($info)
    {
        global $_M;
        $classnow = $_M['form']['id'];
        $job_list = $this->database->get_list_one_by_id($_M['form']['jobid']);
        $conlum_configs = $this->getClsaaConfig($classnow);

        $body = '<style type="text/css">' . "\n";
        $body .= 'table.metinfo_cv{ width:500px; border:1px solid #999; margin:10px auto; color:#555; font-size:12px; line-height:1.8;}' . "\n";
        $body .= 'table.metinfo_cv td.title{ background:#999; font-size:14px; text-align:center; padding:2px 5px; font-weight:bold; color:#fff;}' . "\n";
        $body .= 'table.metinfo_cv td.l{ width:20%; background:#f4f4f4; text-align:right; padding:2px 5px; font-weight:bold;}' . "\n";
        $body .= 'table.metinfo_cv td.r{ background:#fff; text-align:left; padding:2px 5px; }' . "\n";
        $body .= 'table.metinfo_cv td.pc{ text-align:right; width:25%; padding:0px;}' . "\n";
        $body .= 'table.metinfo_cv td.pc img{ border:1px solid #999; padding:1px; margin:3px;}' . "\n";
        $body .= 'table.metinfo_cv td.footer{ text-align:center; padding:0px; font-size:11px; color:#666; background:#f4f4f4; border-top:1px dotted #999;}' . "\n";
        $body .= 'table.metinfo_cv td.footer a{  color:#666; }' . "\n";
        $body .= '</style>' . "\n";
        $body .= '<table cellspacing="1" cellpadding="2" class="metinfo_cv">' . "\n";
        $body .= '<tr><td class="title" colspan="3">' . $title = "{$_M['word']['member_cv']} {$job_list['position']}" . '</td></tr>' . "\n";
        $j = 0;

        $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($info['id']);
        $cv_para = load::mod_class('parameter/parameter_label', 'new')->get_parameter(6, $class123['class1']['id'], $class123['class2']['id'], $class123['class3']['id']);
        $usename = $_M['form']['para' . $cv_para[0]['id']];

        if ($conlum_configs['met_cv_image']) {
            foreach ($cv_para as $key => $val) {
                if ($val['id'] == $conlum_configs['met_cv_image']) {
                    $imgurl = $info['para' . $val['id']];
                    break;
                }
            }
            $imgurl = str_replace("../", $_M['url']['web_site'], $imgurl);
        }
        ###$bt = $imgurl[1] != '' ? '<td class="pc" rowspan="5">' . '<img src="' . $_M['config']['met_weburl'] . $imgurl[1] . '" width="140" height="160" /></td>' : '';
        $bt = $imgurl != '' ? '<td class="pc" rowspan="5">' . '<img src="' . $imgurl . '" width="140" height="160" /></td>' : '';

        foreach ($cv_para as $key => $val) {
            $j++;
            if ($j > 1) $bt = '';
            if ($val['type'] != 4) {
                $para = $_M['form'][$val['para']];
            } else {
                $para = "";
                $para_arr = array();
                $para_total = json_decode($val['options'], true);
                $para_total = is_array($para_total) ? count($para_total) : 0;
                for ($i = 1; $i <= $para_total; $i++) {

                    $para_key = "para" . $val['id'] . '_' . $i;
                    $para_arr[] = $_M['form'][$para_key];
                }
                /*if ($para_arr) {
                    $_M['form']['para' . $val['id']] = implode(',', $para_arr);
                } else {
                    $_M['form']['para' . $val['id']] = '';
                }*/
            }
            $para = strip_tags($para);

            if ($val['type'] != 5) {
                $body = $body . '<tr><td class="l">' . $val['name'] . '</td><td class="r">' . $_M['form']['para' . $val['id']] . '</td>' . $bt . '</tr>' . "\n";
            } else {
                $para_url = str_replace("../", $_M['url']['web_site'], $info['para' . $val['id']]);
                if ($conlum_configs['met_cv_image'] != $val['id']) {
                    $para = explode('../', $para);
                    $para = $para[1] != "" ? "<a href=" . $_M['config']['met_weburl'] . $para[1] . " trage='_blank' style='color:#f00;' >" . $_M['word']['Download'] . "</a>" : $_M['word']['Emptyno'];
                    $body = $body . '<tr><td class="l">' . $val['name'] . '</td><td class="r">' . $para_url . '</td>' . $bt . '</tr>' . "\n";
                } else {
                    $body = $body . '<tr><td class="l">' . $val['name'] . '</td><td class="r">' . $para_url . '</td>' . $bt . '</tr>' . "\n";
                }
            }
        }
        $body .= '</table>';

        $title = "[{$job_list['position']}]-{$usename}({$_M['url']['web_site']})";
        $mail = load::sys_class('jmail', 'new');
        $met_cv_to = $conlum_configs['met_cv_emtype'] ? ($job_list['email'] != '' ? $job_list['email'] : $conlum_configs['met_cv_to']) : $conlum_configs['met_cv_to']; //统一邮箱接收 OR 单独职位邮箱接收
        //管理员邮件通知
        $met_cv_type = explode('#@met@#', $conlum_configs['met_cv_type']);
        if (in_array('1', $met_cv_type)) {
            $met_cv_to = explode('|', $met_cv_to);
            foreach ($met_cv_to as $email) {
                $mail->send_email($email, $title, $body);
            }
        }

        //用户邮件通知
        $cvto = "para" . $conlum_configs['met_cv_email'];
        $cvto = $_M['form'][$cvto];
        if ($conlum_configs['met_cv_back'] == 1 && $cvto) {
            $mail->send_email($cvto, $conlum_configs['met_cv_title'], $conlum_configs['met_cv_content']);
        }
    }

    /**
     * 通过短信通知
     */
    public function notice_by_sms()
    {
        global $_M;
        $classnow = $_M['form']['id'];
        $conlum_configs = $this->getClsaaConfig($classnow);

        //管理员短信通知
        $met_cv_type = explode('#@met@#', $conlum_configs['met_cv_type']);
        if (in_array('2', $met_cv_type) && $conlum_configs['met_cv_job_tel']) {
            $job_list = DB::get_one("SELECT * FROM {$_M['table']['job']} WHERE id='{$_M['form']['jobid']}'");
            $domain = HTTP_HOST ?: $_M['config']['met_weburl'];
            $title = "{$job_list['position']}";
            #$message="您网站[{$domain}]收到了新的简历[{$job_list['position']}]，请尽快登录网站后台查看";
            $message = "{$_M['word']['reMessage1']}[{$domain}]{$_M['word']['jobPrompt']}[{$title}]{$_M['word']['reMessage2']}";
            $met_cv_job_tel = explode('|', $conlum_configs['met_cv_job_tel']);
            foreach ($met_cv_job_tel as $tel) {
                load::sys_class('sms', 'new')->sendsms($tel, $message);
            }
        }

        //用户短信通知
        $met_cv_sms_tell = 'para' . $conlum_configs['met_cv_sms_tell'];
        $user_tel = $_M['form'][$met_cv_sms_tell];
        if ($conlum_configs['met_cv_sms_back'] && $user_tel && $conlum_configs['met_cv_sms_content']) { //用户短信回复
            load::sys_class('sms', 'new')->sendsms($user_tel, $conlum_configs['met_cv_sms_content']);
        }
    }

    /**
     * 检测后台设置的字段
     */
    function check_field()
    {
        global $_M;
        $id = $id = $_M['form']['id'];
        $class123 = $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($id);
        $paralist = load::mod_class('parameter/parameter_label', 'new')->get_parameter('6', $class123['class1']['id'], $class123['class2']['id'], $class123['class3']['id']);
        foreach ($paralist as $key => $val) {
            $para[$val['id']] = $val;
        }

        $paraarr = array();
        $form = array_merge($_M['form'], $_FILES);
        foreach (array_keys($form) as $vale) {
            if (strstr($vale, 'para')) {
                if (strstr($vale, '_')) {
                    $arr = explode('_', $vale);
                    $paraarr[] = str_replace('para', '', $arr[0]);
                } else {
                    $paraarr[] = str_replace('para', '', $vale);
                }
            }
        }

        //必填属性验证
        foreach (array_keys($para) as $val) {
            if ($para[$val]['wr_ok'] == 1 /*&& in_array($val, $paraarr)*/) {
                if ($para[$val]['type'] == 5) {
                    if ($_FILES['para' . $val]['name'] == '' || !$_FILES['para' . $val]['size']) {
                        $info = "【{$para[$val]['name']}】" . $_M['word']['noempty'];
                        okinfo('-1', $info);
                    }
                } else {
                    if ($_M['form']['para' . $val] == '') {
                        $info = "【{$para[$val]['name']}】" . $_M['word']['noempty'];
                        okinfo('-1', $info);
                    }
                }
            }
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
