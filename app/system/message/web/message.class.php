<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class message extends web
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    public function doMessage()
    {
        global $_M;
        if ($_M['form']['action'] == 'add') {
            $this->check_field();
            $this->add($_M['form']);
        } else {
            $data = $this->input_class();
            $_M['config']['met_message_list'] = $this->input['list_length'];
            unset($data['id']);
            $this->add_array_input($data);

            //静态页权限验证
            if ($data['access'] && $_M['form']['html_filename']) {
                $groupid = load::sys_class('auth', 'new')->encode($data['access']);
                $data['access_code'] = $groupid;
            } else {
                $this->check($data['access']);
            }

            load::sys_class('handle', 'new')->redirectUrl($this->input); //伪静态时动态链接跳转

            $this->seo_canonical($data['url']);
            $this->seo($data['name'], $data['keywords'], $data['description']);
            $this->seo_title($data['ctitle']);
            $this->add_input('list', 1);
            $this->view('message_index', $this->input);
        }
    }

    /**
     * 添加留言记录
     * @param $info
     */
    public function add($info)
    {
        global $_M;
        $classnow = $info['id'];
        if (!$classnow) {
            okinfo('-1', $_M['word']['dataerror']);
        }
        $conlum_configs = self::getClsaaConfig($classnow);

        $met_msg_ok = $conlum_configs['met_msg_ok'];
        if (!$met_msg_ok) {
            okinfo('-1', "{$_M['word']['MessageInfo5']}");
        }

        if (self::checkCaptcha($conlum_configs) && self::checkSmsToken($conlum_configs) && self::checkWords() && self::checktime($conlum_configs) && self::checkToken()) {
            foreach ($_FILES as $key => $value) {
                if ($value['tmp_name']) {
                    $this->upfile = load::sys_class('upfile', 'new');
                    $ret = $this->upfile->upload($key);//上传文件
                    if ($ret['path'] != '') {
                        $info[$key] = $ret['path'];
                    } else {
                        okinfo('javascript:history.back();', "{$_M['word']['opfailed']} [{$ret['error']}]");
                    }
                }
            }
            $user_info = $this->get_login_user_info();
            $addtime = date('Y-m-d H:i:s', time());
            $paralist = load::mod_class('parameter/parameter_label', 'new')->get_parameter(7);
            foreach ($paralist as $key => $value) {
                $list[$value['id']] = $value['name'];
                $imgname = $value['id'] . 'imgname';
                $info[$imgname] = $value['name'];
            }
            if ($insert_id = load::sys_class('label', 'new')->get('message')->insert_message($info, $user_info, $addtime)) {
                $this->notice_by_emial($insert_id, $conlum_configs);
                $this->notice_by_sms($insert_id, $conlum_configs);
            }
            okinfo(HTTP_REFERER, $_M['word']['MessageInfo2']);
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

        $para_list = load::mod_class('parameter/parameter_label', 'new')->get_parameter(7);
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
        $ipok = DB::get_one("SELECT * FROM {$_M['table']['message']} WHERE ip='{$ip}' ORDER BY addtime DESC");
        if ($ipok) {
            $time1 = strtotime($ipok['addtime']);
        } else {
            $time1 = 0;
        }
        $submit = load::sys_class('session', 'new')->get('submit');
        $time2 = time();
        $timeok = (float)($time2 - $time1);
        $timeok2 = (float)($time2 - $submit);

        if ($timeok <= $conlum_configs['met_msg_time'] && $timeok2 <= $conlum_configs['met_msg_time']) {
            $fd_time = "{$_M['word']['Feedback1']}" . $conlum_configs['met_msg_time'] . "{$_M['word']['Feedback2']}";
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
        if (!$conlum_configs['met_msg_sms_check']) return true;

        $met_fd_sms_tell = 'para' . $conlum_configs['met_msg_sms_field'];
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
     * @param string $insert_id
     * @param array $conlum_configs
     */
    public function notice_by_emial($insert_id = '', $conlum_configs = array())
    {
        global $_M;
        $query = "select * from {$_M['table']['mlist']} where lang='{$_M['form']['lang']}' and module='7' and listid={$insert_id} order by id";
        $email_list = DB::get_all($query);

        $mail = load::sys_class('jmail', 'new');
        //管理员留言通知
        $addtime = date('Y-m-d H:i:s', time());
        $fromurl = $_M['form']['referer'] ? $_M['form']['referer'] : HTTP_REFERER;
        $body = '';
        $body = $body . "<b>{$_M['word']['AddTime']}</b>:" . $addtime . "<br>";
        $body = $body . "<b>{$_M['word']['SourcePage']}</b>:" . $fromurl . "<br>";
        foreach ($email_list as $val) {
            $para = load::mod_class('parameter/parameter_database', 'new')->get_parameter_by_id($val['paraid']);
            if ($para['type'] == 5) {
                $val['info'] = str_replace('../', $_M['url']['web_site'], $val['info']);
            }
            $body .= "<b>{$val['imgname']}</b>:{$val['info']}<br />";
        }
        $met_msg_type = explode('#@met@#', $conlum_configs['met_msg_type']);
        $met_msg_to = $conlum_configs['met_msg_to'];

        $pname = "para" . $conlum_configs['met_msg_name_field'];
        $msg_user_name = $_M['form'][$pname];
        $title = $msg_user_name . "{$_M['word']['message_mailtext_v6']}";

        if (in_array(1, $met_msg_type) && $met_msg_to) {
            $met_msg_to = explode('|', $met_msg_to);
            foreach ($met_msg_to as $email) {
                $mail->send_email($email, $title, $body);
            }
        }

        //用户邮件通知
        $met_msg_back = $conlum_configs['met_msg_back'];
        $met_msg_title = $conlum_configs['met_msg_title'];
        $met_msg_content = $conlum_configs['met_msg_content'];
        $email_field = "para" . $conlum_configs['met_msg_email_field'];
        $user_email = $_M['form'][$email_field];
        if ($met_msg_back == 1 && $user_email != "") {
            $mail->send_email($user_email, $met_msg_title, $met_msg_content);
        }
    }

    /**
     * 通过短信通知
     * @param string $insert_id
     * @param array $conlum_configs
     */
    public function notice_by_sms($insert_id = '', $conlum_configs = array())
    {
        global $_M;
        //管理员短信通知
        $met_msg_admin_tel = $conlum_configs['met_msg_admin_tel'];
        $met_msg_type = explode('#@met@#', $conlum_configs['met_msg_type']);

        $pname = "para" . $conlum_configs['met_msg_name_field'];
        $msg_user_name = $_M['form'][$pname];
        if ($msg_user_name) {
            $title = $msg_user_name . " - {$_M['word']['MessageInfo1']}";
        } else {
            $title = $_M['word']['MessageInfo1'];
        }

        if (in_array(2, $met_msg_type) && $met_msg_admin_tel) {
            $domain = HTTP_HOST ?: $_M['config']['met_weburl'];
            #$message="您网站[{$domain}]收到了新的留言[{$job_list[position]}]，请尽快登录网站后台查看";
            $message = "{$_M['word']['reMessage1']}[{$domain}]{$_M['word']['messagePrompt']}[{$title}]{$_M['word']['reMessage2']}";
            $met_msg_admin_tel = explode('|', $met_msg_admin_tel);
            foreach ($met_msg_admin_tel as $tel) {
                load::sys_class('sms', 'new')->sendsms($tel, $message);
            }
        }

        //用户短信回复
        $met_msg_sms_back = $conlum_configs['met_msg_sms_back'];
        $met_msg_sms_content = $conlum_configs['met_msg_sms_content'];
        $met_msg_sms_field = 'para' . $conlum_configs['met_msg_sms_field'];
        $user_tel = $_M['form'][$met_msg_sms_field];
        if ($user_tel && $met_msg_sms_back && $met_msg_sms_content) {
            load::sys_class('sms', 'new')->sendsms($user_tel, $met_msg_sms_content);
        }
    }

    /**
     * 检测后台设置的字段
     */
    public function check_field()
    {
        global $_M;
        $id = $_M['form']['id'];
        $messagecfg = self::getClsaaConfig($id);
        $met_msg_name_field = $_M['form']['para' . $messagecfg['met_msg_name_field']];      //$met_msg_name_field  姓名
        $met_msg_content_field = $_M['form']['para' . $messagecfg['met_msg_content_field']];//$met_msg_content_field 留言内容
        $met_msg_email_field = $_M['form']['para' . $messagecfg['met_msg_email_field']];    //$met_msg_email_field  邮箱
        $met_msg_name_field = $_M['form']['para' . $messagecfg['met_msg_name_field']];      // $met_msg_name_field   电话

        $class123 = $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($id);
        $paralist = load::mod_class('parameter/parameter_label', 'new')->get_parameter('7', $class123['class1']['id'], $class123['class2']['id'], $class123['class3']['id']);
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
