<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * 系统标签类
 */

class language_database
{

    /** 复制数据表内语言数据
     * @param $mark string 语言标识
     * @param $autor string 选择语言
     * @param $local_lang string 同步本地语言
     * @param $copy_config string 复制基本设置语言
     * @param $theme_style string 网站主题风格
     * @param $content_lang string 复制基本内容的语言种类（cn）
     */
    public function copyconfig($mark = '', $autor = '', $local_lang = '', $copy_config = '', $theme_style = '', $content_lang = '')
    {
        global $_M;
        $result = 0;
        if ($autor) {
            $lang = $autor;
        } else {
            $lang = $mark;
        }

        if ($local_lang) {
            $query = "SELECT * FROM {$_M['table']['language']} WHERE site='0' AND lang='{$local_lang}'";
            $languages = DB::get_all($query);
            foreach ($languages as $key => $val) {
                $val['value'] = str_replace("'", "''", $val['value']);
                $val['value'] = str_replace("\\", "\\\\", $val['value']);
                $val['lang'] = $lang;
                unset($val['id']);
                $sql = get_sql($val);
                $query = "INSERT INTO {$_M['table']['language']} SET {$sql}";
                DB::query($query);
            }
            $result = 1;
        }

        //复制配置表中相关语言数据
        if ($copy_config) {
            $query = "SELECT name,value,columnid,flashid FROM {$_M['table']['config']} WHERE lang='{$copy_config}' AND columnid= 0 AND flashid= 0";
        } else {
            //默认复制中文配置数据
            $query = "SELECT name,value,columnid,flashid FROM {$_M['table']['config']} WHERE lang='cn' AND columnid= 0 AND flashid= 0";
        }
        $configs = DB::get_all($query);
        foreach ($configs as $key => $val) {
            $val['value'] = str_replace("'", "''", $val['value']);
            $val['value'] = str_replace("\\", "\\\\", $val['value']);
            if ($copy_config) {
                $query = "INSERT INTO {$_M['table']['config']} SET name='{$val['name']}',value='{$val['value']}',columnid='{$val['columnid']}',flashid='{$val['flashid']}',lang='{$lang}'";
                DB::query($query);
                //手机版数据
                if ($val['flashid'] == 10000 || $val['flashid'] == 10001) {
                    $query = "INSERT INTO {$_M['table']['config']} SET name='{$val['name']}',value='{$val['value']}',mobile_value='{$val['mobile_value']}',columnid='{$val['columnid']}',flashid='{$val['flashid']}',lang='{$lang}'";
                    DB::query($query);
                }
            } else {
                $query = "INSERT INTO {$_M['table']['config']} SET name='{$val['name']}',value='',columnid='{$val['columnid']}',flashid='{$val['flashid']}',lang='{$lang}'";
                DB::query($query);
            }
        }
        $copy_table = array('app_config', 'ifmember_left', 'other_info', 'online', 'pay_config');
        foreach ($copy_table as $value) {
            self::copy_lang($value, $copy_config, $lang);
        }

        //复制栏目内容
        if ($content_lang) {
            $column_label = load::mod_class('column/column_label', 'new');
            $columnlist = $column_label->get_column_by_classtype($content_lang);
            $column_allids = arrayColumn($columnlist, 'id');

            //重置本地源语言栏目数据
//            load::sys_class('label', 'new')->get('column')->get_column($local_lang);
            load::sys_class('label', 'new')->get('column')->get_column($content_lang);
            //复制栏目数据
            foreach ($columnlist as $key => $val) {
//                load::mod_class('column/column_op', 'new')->copy_column($val['id'], $local_lang, $lang, 1, $column_allids);
                load::mod_class('column/column_op', 'new')->copy_column($val['id'], $content_lang, $lang, 1, $column_allids);
            }

            //复制banner内容
            $query = "SELECT * FROM {$_M['table']['flash']} WHERE (module ='metinfo' OR module=',10001,') AND lang ='{$content_lang}'";
            $flash_data = DB::get_all($query);
            foreach ($flash_data as $key => $val) {
                $val['lang'] = $lang;
                unset($val['id']);
                DB::insert($_M['table']['flash'], $val);
            }
        }

        //复制Ui内容
        if ($theme_style) {
            $query = "SELECT value FROM {$_M['table']['config']} WHERE name='met_skin_user' AND lang ='{$theme_style}'";
            $config_ui = DB::get_one($query);
            $query = "SELECT * FROM {$_M['table']['ui_config']} WHERE skin_name ='{$config_ui['value']}' AND lang ='{$theme_style}'";
            $ui_list = DB::get_all($query);
            if ($ui_list) {
                foreach ($ui_list as $key => $val) {
                    $val['lang'] = $lang;
                    unset($val['id']);
                    $sql = get_sql($val);
                    $query = "INSERT INTO {$_M['table']['ui_config']} SET {$sql}";
                    DB::query($query);
                }
            } else {
                // 6.1修改复制标签模板的配置
                $skin_name = $config_ui['value'];
                $from_lang = $theme_style;
                load::mod_class('ui_set/class/config_tem.class.php');
                $tem = new config_tem($skin_name, $from_lang);

                $tem->copy_tempates($skin_name, $from_lang, $lang);
            }
        }

        return $result;
    }

    function link_error($str)
    {
        switch ($str) {
            case 'Timeout' :
                return -6;
                break;
            case 'NO File' :
                return -5;
                break;
            case 'Please update' :
                return -4;
                break;
            case 'No Permissions' :
                return -3;
                break;
            case 'No filepower' :
                return -2;
                break;
            case 'nohost' :
                return -1;
                break;
            Default;
                return 1;
                break;
        }
    }

    function curl_post($post, $timeout = 30)
    {
        global $_M;
        if (get_extension_funcs('curl') && function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_close')) {
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, 'http://app.metinfo.cn/file/lang/lang.php');
            curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($curlHandle, CURLOPT_REFERER, $_M['config']['met_weburl']);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post);
            $result = curl_exec($curlHandle);

            curl_close($curlHandle);
            if (substr($result, 0, 7) == 'metinfo') {
                return substr($result, 7);
            } else {
                return $result;
            }

        }
    }

    /**
     * 复制内容到其他语言
     * @DateTime 2018-07-18
     * @param    [type]     $table     表名，不要加前缀
     * @param    [type]     $from_lang 从哪个语言复制
     * @param    [type]     $to_lang   复制到哪个语言
     */
    public function copy_lang($table, $from_lang, $to_lang)
    {
        global $_M;
        if (!isset($_M['table'][$table])) {
            return;
        }
        $query = "SELECT * FROM {$_M['table'][$table]} WHERE lang = '{$from_lang}'";
        $from = DB::get_all($query);
        foreach ($from as $new) {
            unset($new['id']);
            $new['lang'] = $to_lang;
            $sql = get_sql($new);
            $query = "INSERT INTO {$_M['table'][$table]} SET {$sql}";
            DB::query($query);
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
