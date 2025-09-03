<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

class files_op
{
    public function __construct()
    {
        global $_M;
        $this->database = load::mod_class('files/files_database', 'new');
    }

    public function uploadLog($info = array())
    {
        global $_M;
        $true_path = path_absolute($info['path']);
        $extension = strtolower(pathinfo($info['path'], PATHINFO_EXTENSION));
        $folder = strtolower(pathinfo($info['path'], PATHINFO_DIRNAME));
        $folder = str_replace('../upload/', '', $folder);

        $save_data = array();
        $save_data['path'] = $info['path'];
//        $save_data['size'] = byte_format($info['size'], 0, 'KB');
        $save_data['size'] = $info['size'];
        $save_data['md5'] = md5_file($true_path);
        $save_data['extension'] = $extension;
        $save_data['create_at'] = date('Y-m-d H:i:s', filectime($true_path));
        $save_data['publisher'] = '';
        $save_data['type'] = self::fileType($extension);
        $save_data['folder'] = $folder;

        $res = $this->database->insert($save_data);
        return $res;
    }

    /**
     * @param $extension
     * @return string
     */
    private function fileType($extension = '')
    {
        $img_ext = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'icon');
        $video_ext = array('mp4', 'mov', 'ogg', 'avi', 'wmv', '3gp', 'flv', 'f4v');

        if (in_array($extension, $img_ext)) return 'img';
        if (in_array($extension, $video_ext)) return 'video';
        return '';
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>

