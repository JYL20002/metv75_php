<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

class sysfunc extends admin
{
   public function __construct()
   {
       global $_M;
       parent::__construct();
   }

    /**
     * 清除系统和模板缓存
     * @return [type] [description]
     */
    public function doclearCache()
    {
        global $_M;
        if (file_exists(PATH_WEB . 'cache')) {
            deldir(PATH_WEB . 'cache', 1);
        }

        //开启静态化后不清除模板缓存
        if (!$_M['config']['met_webhtm']){
            $template_type = 'ui';
            $inc_file = PATH_WEB . "templates/{$this->no}/metinfo.inc.php";
            if (file_exists($inc_file)) {
                require $inc_file;
                if ($template_type) {
                    deldir(PATH_WEB . 'templates/' . $_M['config']['met_skin_user'] . '/cache', 1);
                }
            }
        }

        $this->success('', $_M['word']['jsok']);
    }


    /**
     * 清除缩略图
     */
    public function doClearThumb()
    {
        global $_M;
        if (file_exists(PATH_WEB . 'upload/thumb_src')) {
            deldir(PATH_WEB . 'upload/thumb_src');
        }
        $this->success('', $_M['word']['jsok']);
    }

    /**
     * 系统检测
     * @return array
     */
    public function doSysCheck()
    {
        global $_M;
        if ($_M['config']['met_secret_key']) {
            $myapp = load::mod_class('myapp/class/myapp', 'new');
            $new_app = $myapp->checkAppUpdate();

            $data = array(
                'action' => 'checkSystemUpdate',
                'version' => $_M['config']['metcms_v'],
            );
            $result = api_curl($_M['config']['met_api'], $data);
            $res = json_decode($result, true);
            if ($res['status'] == 200) {
                $sys_new_ver = $res['data'];
            }
        }


        $redata = array(
            'new_app' => $new_app,
            'sys_new_ver' => $sys_new_ver,
        );
        $this->success($redata);

    }

    /**
     * 功能大全
     * @return array
     */
    public function doFuncList()
    {
        global $_M;
        //1508
        if (!hasPermission('s', 1508)) $this->error($_M['word']['js81']);
        $this->admin_member;
        return get_auth($this->admin_member);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>