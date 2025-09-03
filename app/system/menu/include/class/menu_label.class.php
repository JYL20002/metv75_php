<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 友情链接标签类
 */

class menu_label
{

    public $lang;
    public $handle;
    public $database;

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->lang = $_M['lang'];
        $this->handle = load::mod_class('menu/menu_handle', 'new');
        $this->database = load::mod_class('menu/menu_database', 'new');
    }

    /**
     * 菜单列表
     * @param string $type
     * @return mixed
     */
    public function get_list($type = '')
    {
        global $_M;
        $redata =  $this->handle->para_handle(
            $this->database->get_menu_list($type, 1)
        );
        foreach ($redata as $key => $value) {
            switch ($value['type']) {
                case 1:
                    if(!strstr($value['url'],'tel:')){
                        $redata[$key]['url'] = "tel:{$value['url']}";
                    }
                    break;
                case 2:
                    if(!strstr($value['url'],'sms:')){
                        $redata[$key]['url'] = "sms:{$value['url']}";
                    }
                    break;
                case 3:
                    if(!strstr($value['url'],'mailto:')){
                        $redata[$key]['url'] = "mailto:{$value['url']}";
                    }
                    break;
                case 4:
                case 5://企业QQ
                    if(!strstr($value['url'],'http')){
                        $qq_url1 = 'https://wpa.qq.com/msgrd?v=3&uin=';
                        $qq_url2 = '&site=qq&menu=yes';
                        if (!met_useragent('desktop') && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
                            $qq_url1 = 'mqq://im/chat?chat_type=wpa&uin=';
                            $qq_url2 = '&version=1&src_type=web';
                        }
                        $redata[$key]['url'] = "{$qq_url1}{$value['url']}{$qq_url2}";
                    }
                    break;
                case 6:
                    if(!strstr($value['url'],'weixin://')){
                        $value['url']=str_replace('../','',$value['url']);
                        $redata[$key]['url'] = "weixin://{$value['url']}";
                    }
                    break;
            }
        }
        return $redata;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
