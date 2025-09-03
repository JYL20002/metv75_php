<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class column_style
{
    /**
     * config_ui constructor.
     * @param string $no 模板编号
     * @param string $lang 语言
     */
    public function __construct()
    {
        global $_M;
        $this->skin_path = PATH_WEB . "templates/{$_M['config']['met_skin_user']}";
        $this->modules = array(1, 2, 3, 4, 5, 6);
        $this->template_type = array(
            '1'=> 'show',
            '2'=> 'news',
            '3'=> 'product',
            '4'=> 'download',
            '5'=> 'img',
            '6'=> 'job',
        );
    }

    /**
     * @param null $modue
     * @return array
     */
    public function getStyleList($module = null)
    {
        global $_M;
        $redata = array();
        $redata[] = array('sort'=>0,'name' => '默认风格', 'val' => '');
        if (!in_array($module, $this->modules)) return $redata;

        $template_type = $this->template_type[$module];
        $templates = scandir($this->skin_path);
        foreach ($templates as $key => $template) {
            $pattern = "/^{$template_type}(_\d+)+\.php$/";
            $res = preg_match($pattern, $template, $matche);
            if ($res) {
                $pathinfo = pathinfo($matche[0]);
                $sort = str_replace('_','',$matche[1]);
                $redata[] = array('sort'=>$sort, 'name' => "风格{$sort}", 'val' => $pathinfo['filename']);
            }
        }
        $sort = array_column($redata, 'sort');
        array_multisort($sort, SORT_ASC, $redata);
        return $redata;
    }

    /**
     * @param string $module
     * @param string $template_name
     * @return string
     */
    public function getStyleTemp($module = '',$template_name = '')
    {
        if (!in_array($module, $this->modules)) return $template_name;
        $default = $this->template_type[$module] ?: '';
        if (!$template_name) return $default;
        if(!is_simplestr($template_name)) return false;

        $path = $this->skin_path . "/{$template_name}.php";
        if (!file_exists($path)) return $default;
        return $template_name;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
