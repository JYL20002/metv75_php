<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class feedback extends web
{
    private $conlum_configs;

    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    public function dofeedback()
    {
        global $_M;
        if ($_M['form']['action'] == 'add') {
            $this->check_field();
            $this->add($_M['form']);
        } else {
            $data = $this->input_class();
            $this->load_config($_M['lang'], $data['id']);
            unset($data['id']);
            $this->add_array_input($data);

            //静态页权限验证
            if ($data['access'] && $_M['form']['html_filename']) {
                $groupid = load::sys_class('auth', 'new')->encode($data['access']);
                $data['access_code'] = $groupid;
            } else {
                $this->check($data['access']);
            }

            $class_config = self::getClsaaConfig($data['id']);
            if ($class_config['met_fdtable']) {
                $this->seo($class_config['met_fdtable'], $data['keywords'], $data['description']);
            } else {
                $this->seo($data['name'], $data['keywords'], $data['description']);
            }

            $this->seo_canonical($data['url']);
            $this->seo_title($data['ctitle']);
            // $this->seo_title($_M['config']['met_fdtable']);
            $this->add_input('fdtitle', $data['name']);
            if ($_M['form']['fdtitle']) {//产品询价
                $fdtitle = urlencode($_M['form']['fdtitle']);
                $this->input['url'] = $this->input['url'] . "?fdtitle={$fdtitle}";
            } else {
                load::sys_class('handle', 'new')->redirectUrl($this->input); //伪静态时动态链接跳转
            }
            $this->view('feedback', $this->input);
        }
    }

    public function add($info)
    {
        global $_M;
        $classnow = $info['id'];
        if (!$classnow) {
            okinfo('-1', $_M['word']['dataerror']);
        }

        $conlum_configs = self::getClsaaConfig($classnow);
        if (!$conlum_configs['met_fd_ok']) {
            okinfo(-1, $_M['word']['Feedback5']);
        }

        if (self::checkCaptcha($conlum_configs) && self::checkSmsToken($conlum_configs) && self::checkWords() && self::checktime($conlum_configs) && self::checkToken()) {
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

            $fromurl = $_M['form']['referer'] ?: HTTP_REFERER;
            $fdtable = $conlum_configs['met_fdtable'];
            if ($fdtable && $fdtable != $_M['form']['fdtitle']) {
                $title = "{$_M['form']['fdtitle']} - {$fdtable}";
            } else {
                $title = "{$_M['form']['fdtitle']}";
            }
            $user_info = $this->get_login_user_info();

            $info['title'] = $title;
            $info['fromurl'] = $fromurl;
            if (load::sys_class('label', 'new')->get('feedback')->insert_feedback($info['id'], $info, $user_info)) {
                $this->notice_by_emial($info, $conlum_configs);

                $this->notice_by_sms($info);
            }
            okinfo(HTTP_REFERER, $_M['word']['Feedback4']);
        }
    }

    /**
     * 获取栏目配置信息
     * @param string $class
     * @return mixed
     */
    protected function getClsaaConfig($class = '')
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

        $para_list = load::mod_class('parameter/parameter_label', 'new')->get_parameter(8);
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
     * @return bool
     */
    protected function checkTime($conlum_configs = array())
    {
        global $_M;
        $ip = getip();
        $ipok = DB::get_one("SELECT * FROM {$_M['table']['feedback']} WHERE ip='{$ip}' ORDER BY addtime DESC");
        if ($ipok) {
            $time1 = strtotime($ipok['addtime']);
        } else {
            $time1 = 0;
        }

        $submit = load::sys_class('session', 'new')->get('submit');
        $time2 = time();
        $timeok = (float)($time2 - $time1);
        $timeok2 = (float)($time2 - $submit);

        if ($timeok <= $conlum_configs['met_fd_time'] && $timeok2 <= $conlum_configs['met_fd_time']) {
            $fd_time = "{$_M['word']['Feedback1']}" . $conlum_configs['met_fd_time'] . "{$_M['word']['Feedback2']}";
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
        if (!$conlum_configs['met_fd_sms_check']) return true;

        $met_fd_sms_tell = 'para' . $conlum_configs['met_fd_sms_tell'];
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
     * @param array $info
     */
    protected function notice_by_emial($info = array(), $conlum_configs = array())
    {
        global $_M;
        $fd_para = load::mod_class('parameter/parameter_label', 'new')->get_parameter(8, $info['id']);

        $addtime = date('Y-m-d H:i:s', time());
        $body = '<table border="0" cellspacing="0" cellpadding="8">';
        $j = 0;
        foreach ($fd_para as $key => $val) {
            $j++;
            if ($j > 1) {
                $bt = '';
            }
            if ($val['type'] != 4) {
                $para = $info[$val['para']];
            } else {
                $para = "";
                for ($i = 1; $i <= $info[$val['para']]; $i++) {
                    $para1 = "para" . $val['id'];
                    $para2 = $info['$para1'];
                    $para = ($para2 != "") ? $para . $para2 . "-" : $para;
                }
                $para = substr($para, 0, -1);
            }
            $para = strip_tags($para);
            if ($val['type'] == 4) {
                $count = is_array($val['para_list']) ? count($val['para_list']) : 0;
                for ($i = 1; $i <= $count; $i++) {
                    if ($info['para' . $val['id'] . '_' . $i]) {
                        $info['para' . $val['id']] = $info['para' . $val['id'] . '_' . $i];
                    }
                }
            }
            if ($val['type'] != 5) {
                $body .=  '<tr><td class="l"><b>' . $val['name'] . ':</b></td><td class="r">' . $info['para' . $val['id']] . '</td>' . $bt . '</tr>' . "\n";
            } else {
                $para_url = str_replace('../', $_M['url']['web_site'], $info['para' . $val['id']]);
                $body .= '<tr><td class="l"><b>' . $val['name'] . ':</b></td><td class="r">' . $para_url . '</td>' . $bt . '</tr>' . "\n";
            }
        }
        $body .= '</table>';

        $body .=  "<hr><b>{$_M['word']['AddTime']}</b>:" . $addtime . "<br>";
        $body .=  "<b>{$_M['word']['SourcePage']}</b>:" . $info['fromurl'] . "<br>";
        $body .=  "<b>IP</b>:" . getip() . "<br>";

        //管理员邮件通知
        $mail = load::sys_class('jmail', 'new');
        $met_fd_type = explode('#@met@#', $conlum_configs['met_fd_type']);  //通知方式
        if (in_array(1, $met_fd_type)) {
            $met_fd_to = explode('|', $conlum_configs['met_fd_to']);
            foreach ($met_fd_to as $email) {
                $mail->send_email($email, $info['title'], $body);
            }
        }

        //用户员邮件通知
        $cvto = "para" . $conlum_configs['met_fd_email'];
        $cvto = $info[$cvto]; //用戶接收郵箱
        if ($conlum_configs['met_fd_back'] == 1 && $cvto) {
            $mail->send_email($cvto, $conlum_configs['met_fd_title'], $conlum_configs['met_fd_content']);
        }
    }

    /**
     * 通过短信通知
     * @param array $info
     */
    protected function notice_by_sms($info = array())
    {
        global $_M;
        $classnow = $_M['form']['id'];
        $conlum_configs = $this->getClsaaConfig($classnow);

        //管理员短信通知
        $met_fd_type = explode('#@met@#', $conlum_configs['met_fd_type']);
        if (in_array(2, $met_fd_type) && $conlum_configs['met_fd_admin_tel'] || 1) {
            $domain = HTTP_HOST ?: $_M['config']['met_weburl'];
            #$message="您网站[{$domain}]您收到了新的反馈[{$job_list[position]}]，请尽快登录网站后台查看";
            $message = "{$_M['word']['reMessage1']}[{$domain}]{$_M['word']['newFeedback']}[{$info['title']}]{$_M['word']['reMessage2']}";
            $met_fd_admin_tel = explode('|', $conlum_configs['met_fd_admin_tel']);
            foreach ($met_fd_admin_tel as $tel) {
                load::sys_class('sms', 'new')->sendsms($tel, $message);
            }
        }

        //用户短信通知
        $met_fd_sms_tell = 'para' . $conlum_configs['met_fd_sms_tell'];
        $user_tel = $_M['form'][$met_fd_sms_tell];
        if ($conlum_configs['met_fd_sms_back'] && $user_tel && $conlum_configs['met_fd_sms_content']) { //用户短信回复
            load::sys_class('sms', 'new')->sendsms($user_tel, $conlum_configs['met_fd_sms_content']);
        }
    }

    /*检测后台设置的字段*/
    protected function check_field()
    {
        global $_M;
        $id = $_M['form']['id'];
        $feedbackcfg = self::getClsaaConfig($id);

        $met_fd_email = $_M['form']['para' . $feedbackcfg['met_fd_email']];
        $met_fd_sms_tell = $_M['form']['para' . $feedbackcfg['met_fd_sms_tell']];
        $met_fd_class = $_M['form']['para' . $feedbackcfg['met_fd_class']];
        $met_fd_back = $feedbackcfg['met_fd_back'];

        $class123 = $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($id);
        $paralist = load::mod_class('parameter/parameter_label', 'new')->get_parameter('8', $class123['class1']['id'], $class123['class2']['id'], $class123['class3']['id']);
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
