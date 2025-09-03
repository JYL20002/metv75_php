<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 系统模板解析类
 * Class engine
 */
 class engine
 {
     public function __construct()
     {
         global $_M;
         define("TEMP_CACHE_PATH", PATH_WEB . 'cache/templates');
         define("PATH_TEM", PATH_WEB . 'templates/' . $_M['config']['met_skin_user'] . '/');
         load::sys_class('view/met_view');
     }

     /**
      * @param $file
      * @param array $data
      * @return null
      */
     public function dodisplay($file, $data = array())
     {
         $view = new met_view();
         $view->assign('data', $data);
         $view->display($file);
         return $view->compileFile;
     }

     /**
      * @param $file
      * @param array $data
      * @return bool|string
      */
     public function dofetch($file, $data = array())
     {
         global $_M;
         $view = new met_view();
         $view->assign('data', $data);
         $content = $view->fetch($file);
         return $content;
     }
 }

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
