<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('database');

class history_database extends database
{
    public function __construct()
    {
        global $_M;
        $this->construct($_M['table']['history']);
    }

    public function table_para()
    {
        return 'id|aid|module|title|ctitle|keywords|description|content|content1|content2|content3|content4|contentinfo|contentinfo1|contentinfo2|contentinfo3|contentinfo4|class1|class2|class3|no_order|wap_ok|img_ok|imgurl|imgurls|video|com_ok|issue|hits|updatetime|addtime|access|top_ok|filename|lang|recycle|displayimg|tag|links|publisher|text_size|text_color|other_info|custom_info|imgsize|downloadurl|filesize|position|count|place|deal|useful_life|email|displaytype|record_time';

        
//        $sql = "show COLUMNS FROM {$this->table}";
//        $res = DB::get_all($sql);
//        $Field = arrayColumn($res, 'Field');
//        return implode('|', $Field);
        }

    /**
     * @param string $listid
     * @return int|mixed
     */
    public function delByAid($aid = '', $module = 0)
    {
        global $_M;
        $query = "DELETE FROM {$this->table} WHERE aid='{$aid}' AND module = '{$module}'";

        return DB::query($query);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
