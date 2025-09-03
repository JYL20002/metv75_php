<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/base_handle');


class download_handle extends base_handle
{
    public function __construct()
    {
        global $_M;
        $this->construct('download');
    }

    /**
     * 处理list数组
     * @param  string $content 内容数组
     * @return array            处理过后数组
     */
    public function one_para_handle($content = array())
    {
        global $_M;
        $content = parent::one_para_handle($content);
//        $content['downloadurl'] = str_replace('../', $_M['url']['web_site'], $content['downloadurl']);
        $content['true_path'] =  str_replace('../', $_M['url']['web_site'], $content['downloadurl']);

        //下载权限
        if ($content['downloadaccess'] || 1) {
            if (strstr($content['downloadurl'], 'http')) {//外链资源
                $auth_code = load::sys_class('auth', 'new');
                $durl = urlencode($auth_code->encode($content['downloadurl']));
                $groupid = urlencode($auth_code->encode($content['downloadaccess']));
                $links = "{$_M['url']['entrance']}?m=include&c=access&a=dojump&lang={$_M['lang']}&url={$durl}&groupid={$groupid}";
                $downloadurl = $links;
            }else{//本地资源
                $downloadurl = "{$_M['url']['entrance']}?m=include&c=access&a=dodown&lang={$_M['lang']}&id={$content['id']}";
            }
            $content['downloadurl'] = $downloadurl;
        }
        return $content;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
