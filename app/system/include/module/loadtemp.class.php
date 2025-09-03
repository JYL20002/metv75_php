<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('common');
load::sys_class('admin');

class loadtemp extends admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取模板内容字符
     * @param $file
     * @param $data
     * @return mixed
     */
    public function doviewHtml()
    {
        global $_M;
        $path = $_M['form']['path'];
        $data = $_M['form']['data'];

    //权限判断
        $paths = explode('/', $path);
        $m_type = $paths[0] != 'apps' ? 'system' : 'app';
        $m_name = $data['n'] ?: $paths[1];
        $m_class = $data['c'] ?: '';
        $m_action = $data['a'] ?: 'doindex';
        $res = self::checkMemberAuth($m_type, $m_name, $m_class, $m_action);
        if(!$res) $this->error('您没有此操作权限请联系管理员');

        //模板路径判断
        if (!$path) $this->error('error');
        $path = str_replace("\\", '/', $path);
        if (strstr($path, '../')) die();
        $path_info = pathinfo($path);
        if (isset($path_info['extension']) && strtolower($path_info['extension']) != 'php')die();
        $path_arr = explode('/', $path);

        // 加载方法，获取数据传到模板文件
        if ($path_arr[0] == 'apps' && end($path_arr) == 'index') {
            !$data['c'] && $data['c'] = 'index';
            !$data['a'] && $data['a'] = 'doindex';
        }

        if ($data['c'] && $data['a']) {
            $data['n'] = $data['n'] ?: $path_arr[1];
            $class_file = $data['n'] . '/admin/' . $data['c'] . '.class.php';
            $dir = file_exists(PATH_SYS . $class_file) ? PATH_SYS : PATH_ALL_APP;
            $class_path = $dir . $class_file;
            if (file_exists($class_path)) {
                $path_arr[0] == 'apps' && $_M['m_name'] = $data['n'];
                $_GET = array_merge($_GET, $data);
                $_GET['noajax'] = 1;
                unset($_POST);
                $data_class = load::module($dir . $data['n'] . '/admin/', $data['c'], 'new');
                if (method_exists($data_class, $data['a'])) {
                    $data['handle'] = call_user_func(array($data_class, $data['a']));
                } else {
                    $data['handle'] = array();
                }
                $_M['form']['noajax'] = 0;
                $_M['form']['path'] = $path;
            }
        }

        // 模板文件所需参数
        if ($path_arr[0] == 'sys' || $path_arr[0] == 'apps') {
            $_M['url']['own'] = $_M['url']['site'] . 'app/' . ($path_arr[0] == 'app' ? 'app' : 'system') . '/' . $path_arr[1] . '/admin/';
            $_M['url']['own_tem'] = $_M['url']['own'] . 'templates/';
            $_M['url']['own_name'] = $_M['url']['site_admin'] . '?n=' . $path_arr[1] . '&';
        }
        // 加载模板文件
        $view = load::sys_class('engine', 'new');
        $html = $view->dofetch($path, $data, false);
        // 应用css、js状态
        if ($path_arr[0] == 'apps') {
            $own_tem = PATH_WEB . 'app/app/' . $path_arr[1] . '/admin/templates/';
            $own_css = file_exists($own_tem . 'css/' . $path_arr[1] . '.css') ? 1 : 0;
            $own_js = file_exists($own_tem . 'js/' . $path_arr[1] . '.js') ? 1 : 0;
            $html .= "\n" . '<input type="hidden" name="app_file_status" value="' . $own_css . '|' . $own_js . '"/>';
        }
        $redata = array(
            'status' => 1,
            'html' => $html
        );
        $this->ajaxReturn($redata);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>