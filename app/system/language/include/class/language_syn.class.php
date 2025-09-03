<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class language_syn
{
    public $error;

    /**
     * @param $post
     * @param $mark     //同步语言标识
     * @param $lang_type //语言类型 前台|后台
     * @return false|void
     */
    public function synLang($post, $mark, $lang_type)
    {
        global $_M;
        $cache_path = PATH_CACHE . "lang_{$lang_type}_{$mark}.log";
        $res = json_decode(api_curl($_M['config']['met_api'], $post), true);
        if ($res['status'] != 200 || !$res['data']) return false;

        file_put_contents($cache_path, $res['data']);
        $lang_list = explode("\n", $res['data']);

        foreach ($lang_list as $row) {
            if (strpos("#",$row) === 0) continue;
            list($name, $value) = explode('=',$row);
            if(!$name || !$value || !is_simplestr($name)) continue;

            $value = addslashes($value);

            $sql = "SELECT id FROM {$_M['table']['language']} WHERE name='{$name}' AND site='{$lang_type}' AND lang='{$mark}'";
            $result = DB::get_one($sql);
            if ($result) {
                $query = "UPDATE {$_M['table']['language']} SET value='{$value}' WHERE name='{$name}' AND site='{$lang_type}' AND lang='{$mark}'";
            } else {
                $query = "INSERT INTO {$_M['table']['language']} SET `name`='{$name}',`value`='{$value}',site='{$lang_type}',lang='{$mark}'";
            }
            DB::query($query);
        }
        return true;
    }

    /**
     * @return array
     */
    public function langOptions()
    {
        $list = array();
        $list[] = array('lang' => 'cn', 'name' => '中文(简体中文)', 'info' => '中文(简体中文)');
        $list[] = array('lang' => 'zh', 'name' => '中文(繁體中文)', 'info' => '中文(繁體中文)');
        $list[] = array('lang' => 'en', 'name' => 'English', 'info' => '英语');
        $list[] = array('lang' => 'fr', 'name' => 'Français', 'info' => '法语');
        $list[] = array('lang' => 'ru', 'name' => 'русский', 'info' => '俄语');
        $list[] = array('lang' => 'pt', 'name' => 'Español', 'info' => '西班牙语');
        $list[] = array('lang' => 'es', 'name' => 'Português', 'info' => '葡萄牙语');
        $list[] = array('lang' => 'ar', 'name' => 'العربية', 'info' => '阿拉伯语');
        $list[] = array('lang' => 'fa', 'name' => 'فارسی', 'info' => '波斯语');
        $list[] = array('lang' => 'de', 'name' => 'Deutsch', 'info' => '德语');
        $list[] = array('lang' => 'jp', 'name' => '日本語', 'info' => '日语');
        $list[] = array('lang' => 'ko', 'name' => '한국어', 'info' => '韩语');
        $list[] = array('lang' => 'vi', 'name' => 'Việt', 'info' => '越南语');
        $list[] = array('lang' => 'th', 'name' => 'ไทย', 'info' => '泰语');
        $list[] = array('lang' => 'it', 'name' => 'Italiano', 'info' => '意大利语');
        $list[] = array('lang' => 'sr', 'name' => 'srpski', 'info' => '塞尔维亚语');
        $list[] = array('lang' => 'hu', 'name' => 'magyar', 'info' => '匈牙利语');
        $list[] = array('lang' => 'bg', 'name' => 'Български', 'info' => '保加利亚语');
        $list[] = array('lang' => 'nl', 'name' => 'Nederlands', 'info' => '荷兰语');
        $list[] = array('lang' => 'da', 'name' => 'dansk', 'info' => '丹麦语');
        $list[] = array('lang' => 'sv', 'name' => 'Svenska', 'info' => '瑞典语');
        $list[] = array('lang' => 'pl', 'name' => 'Polski', 'info' => '波兰语');
        $list[] = array('lang' => 'ro', 'name' => 'Română', 'info' => '罗马尼亚语');
        $list[] = array('lang' => 'el', 'name' => 'Ελληνικά', 'info' => '希腊语');
        $list[] = array('lang' => 'fi', 'name' => 'suomi', 'info' => '芬兰语');
        $list[] = array('lang' => 'uk', 'name' => 'українська', 'info' => '乌克兰语');
        $list[] = array('lang' => 'in', 'name' => 'Indonesia', 'info' => '印尼语');
        $list[] = array('lang' => 'hr', 'name' => 'hrvatski', 'info' => '克罗地亚语');
        $list[] = array('lang' => 'cs', 'name' => 'Čeština', 'info' => '捷克语');
        $list[] = array('lang' => 'ms', 'name' => 'Melayu', 'info' => '马来语');

        return $list;
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
