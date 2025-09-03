<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_func('admin');

class html_op
{
    public function __construct()
    {
        global $_M;
    }

    /**
     * 跳转至静态页面生成
     * @param  string $url url id
     * @param  string $column_id 顶级栏目id
     * @param  string $content_id 内容id
     */
    public function htmlGenerate($column_id = '', $content_id = '',$back_url = '')
    {
        global $_M;
        //自动更新网站地图
        if ($_M['config']['met_sitemap_auto']) {
            load::sys_class('label', 'new')->get('seo')->site_map();
        }
        //开启静态化 并自动生成
        if ($_M['config']['met_webhtm'] != 0 && $_M['config']['met_htmway'] == 0) {
            //生成静态页
            $column_label = load::sys_class('label', 'new')->get('column');
            $column_label->get_column($_M['lang']);
            $c = $column_label->get_column_id($column_id);
            $html_res = "{$_M['url']['site_admin']}index.php?lang={$_M['lang']}&n=html&c=html&a=doCreatePage&type=column&module={$c['module']}&class1={$column_id}&content={$content_id}&index=1";

            return $html_res;
        }
        return null;
    }

    /**
     * @param null $id
     * @param string $module
     */
    public function htmlDel($id = null, $module = '')
    {
        global $_M;
        $mod = load::sys_class('handle', 'new')->mod_to_file($module);
        $mod_label = load::sys_class('label', 'new')->get($mod);
        if (!$mod_label) {
            return;
        }
        $one = $mod_label->get_one_content($id);
        if (!$one || $one['links']) {
            return;
        }
        $url = $mod_label->handle->get_content_url($one, 3);
        $path = str_replace($_M['url']['web_site'], '', $url);
        $htmlFile = PATH_WEB . $path;
        $info = pathinfo($htmlFile);
        $ext = array('html', 'htm');
        if (in_array($info['extension'], $ext) && file_exists($htmlFile)) {
            delfile($htmlFile);
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
