<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

    defined('IN_MET') or exit('No permission');

    load::sys_func('file.func.php');
    load::sys_class('common');

    /**
     * 上传文件类
     * @param string $savepath		路径,为上传文件夹（upload）下的路径
     * @param string $format		允许上传文件后缀,如zip|jpg|txt,用竖线隔开,设置的格式不能超过网站设置中的格式
     * @param string $maxsize		限制上传文件大小,单位是M,设置的大小不能超过网站设置中的大小
     * @param string $is_rename		是否重命名,1：重命名，0：不重命名
     * @param string $ext			后缀
     * 以上路径变量都必须是绝对路径，如果不使用类的set方法
     */
    class upfile extends common
    {
        public $error;
        public $savepath;
        public $format;
        public $maxsize;
        public $is_rename;
        protected $ext;
        protected $not_allowed;

        public function __construct()
        {
            parent::__construct();
            global $_M;
            $query = "SELECT * FROM {$_M['table']['language']} WHERE lang='{$_M['lang']}' AND site=1 ";
            $result = DB::get_all($query);
            foreach ($result as $val) {
                $_M['word'][$val['name']] = trim($val['value']);
            }

            //上传文件大小限制
            $upload_max_filesize = intval(ini_get('upload_max_filesize'));
            $post_max_size = intval(ini_get('post_max_size'));
            $memory_limit = intval(ini_get('memory_limit'));
            $php_upload_max_size = min($upload_max_filesize, $post_max_size, $memory_limit);
            $max_size = $php_upload_max_size ?: 8;
            $this->maxsize = $max_size * 1048576;
            $this->not_allowed = array('php', 'aspx', 'asp', 'jsp', 'js', 'asa', 'web', 'config', 'htaccess');

            $this->set_upfile();
        }

        /**s
         * @param int $erron
         * @return mixed
         */
        protected function getErrorInfo($erron = 0)
        {
            global $_M;
            $errors = array(
                0 => $_M['word']['upfileOver4'],
                1 => $_M['word']['upfileOver'],
                2 => $_M['word']['upfileOver1'],
                3 => $_M['word']['upfileOver2'],
                4 => $_M['word']['upfileOver3'],
                6 => $_M['word']['upfileOver5'],
                7 => $_M['word']['upfileOver5']
            );

            if (isset($errors[$erron])) {
                return $errors[$erron];
            }else{
                return $errors[0];
            }
        }

        /**
         * 设置字段
         */
        public function set($name = null, $value = null)
        {
            global $_M;
            if ($value === null) return false;
            if ($name === null) return false;

            switch ($name) {
                case 'savepath':
                    $this->savepath = path_standard(PATH_WEB . 'upload/' . $value);
                    break;
                case 'format':
                    $this->format = explode('|', strtolower($value));
                    break;
                case 'maxsize':
                    if (is_numeric($value)) {
                        $maxsize = min($value * 1048576, $this->maxsize);
                        $this->maxsize = min($_M['config']['met_file_maxsize'] * 1048576, $maxsize);
                    } else {
                        $this->maxsize = min($_M['config']['met_file_maxsize'] * 1048576, $this->maxsize);
                    }
                    break;
                case 'is_rename':
                    $this->is_rename = $value;
                    break;
            }
        }

        /**
         * 设置上传文件模式
         */
        public function set_upfile()
        {
            global $_M;
            $this->set('savepath', 'file');
            $this->set('format', $_M['config']['met_file_format']);
            $this->set('maxsize', $_M['config']['met_file_maxsize'] * 1048576);
            $this->set('is_rename', $_M['config']['met_img_rename']);
        }

        /**
         * 设置上传图片模式
         */
        public function set_upimg()
        {
            global $_M;
            $this->set('savepath', date('Ym'));
            $this->set('format', $_M['config']['met_file_format']);
            $this->set('maxsize', $_M['config']['met_file_maxsize'] * 1048576);
            $this->set('is_rename', $_M['config']['met_img_rename']);
        }

        /**
         * 设置上传备份文件模式
         */
        public function set_upsql()
        {
            global $_M;
            $this->set('savepath', 'sql');
            $this->set('format', "sql|zip");
            $this->set('maxsize', 5 * 1048576);
            $this->set('is_rename', 0);
            $this->set('is_overwrite', 1);
        }

        /**
         * 上传方法
         * @param null $field_name 上传控件的name字段值
         * @return array
         */
        public function upload($field_name = null)
        {
            global $_M;
            if ($field_name) {
                $filear = $_FILES[$field_name];
            }else{
                foreach ($_FILES as $key => $val) {
                    $filear = $_FILES[$key];
                    break;
                }
            }
            if (!$filear) {
                return self::_error('error');
            }

            //是否能正常上传
            if (!is_array($filear)) $filear['error'] = 4;
            if ($filear['error'] != 0) {
                $error = self::getErrorInfo($filear['error']);
                $error_info[] = $error;
                return self::_error($error);
            }

            //空间超容 有些虚拟主机不支持此函数
            if (!self::checkSpace($filear)) {
                return self::_error($this->error);
            }

            //目录不可写
            if (!self::checkUploadWritable()) {
                return self::_error($this->error);
            }

            //文件大小是否正确
            if (!self::checkFileSize($filear)) {
                return self::_error($this->error);
            }

            //文件后缀是否为合法后缀
            $this->getExt($filear["name"]); //获取允许的后缀
            $res = $this->checkImgExt($filear);  //图片检测
            if (!$res) return self::_error($this->error);
            $res = $this->checkOtherExt($filear); //PDF和svg检测
            if (!$res) return self::_error($this->error);
            $res = $this->checkExt();
            if (!$res) return self::_error($this->error);

            //新建保存文件
            $res = $this->checkDir();
            if (!$res) return self::_error($this->error);

            //文件名重命名
            $this->set_savename($filear["name"], $this->is_rename);

            //复制文件
            $upfileok = 0;
            $file_tmp = $filear["tmp_name"];
            $file_name = $this->savepath . $this->savename;
            if (stristr(PHP_OS, "WIN")) {
                $file_name = @iconv("utf-8", "GBK", $file_name);
            }

            if (function_exists("move_uploaded_file")) {
                if (move_uploaded_file($file_tmp, $file_name)) {
                    $upfileok = true;
                } else if (copy($file_tmp, $file_name)) {
                    $upfileok = true;
                }
            } elseif (copy($file_tmp, $file_name)) {
                $upfileok = true;
            }

            if (!$upfileok) {
                if (is_writable($this->savepath)) {
                    $_M['word']['upfileOver4'] = $_M['word']['upfileOver5'];
                }
                $error = self::getErrorInfo($filear['error']);
                $error_info[] = $error;
                return self::_error($error);
            } else {
                if (stripos($filear['tmp_name'], PATH_WEB) === false) {
                    @unlink($filear['tmp_name']); //Delete temporary files
                }
            }

            load::plugin('doqiniu_upload', 0, array('savename' => str_replace(PATH_WEB, '', $this->savepath) . $this->savename, 'localfile' => $file_name));

            $back = '../' . str_replace(PATH_WEB, '', $this->savepath) . $this->savename;
            return self::_success($back, $filear["size"]);
        }

        /**
         * 批量上传文件
         * @param string $form
         * @return mixed
         */
        public function uploadarr($field_name = '')
        {
            if ($field_name) {
                $filear = $_FILES[$field_name];
            }else{
                foreach ($_FILES as $key => $val) {
                    $filear = $_FILES[$key];
                    break;
                }
            }
            if (!$filear) {
                return self::_error('error');
            }

            $length = count($filear['name']);
            for ($i = 0; $i < $length; $i++) {
                $file['name'] = $filear['name'][$i];
                $file['type'] = $filear['type'][$i];
                $file['tmp_name'] = $filear['tmp_name'][$i];
                $file['error'] = $filear['error'][$i];
                $file['size'] = $filear['size'][$i];
                $res[$i] = $this->uploadcustom($file);
            }
            return $res;
        }

        /**
         * @param string $filear
         * @return array|mixed
         */
        public function uploadcustom($filear = '')
        {
            global $_M;
            //是否能正常上传
            if (!is_array($filear)) $filear['error'] = 4;
            if ($filear['error'] != 0) {
                $error = self::getErrorInfo($filear['error']);
                $error_info[] = $error;
                return self::_error($error);
            }

            //是否能正常上传
            if (!is_array($filear)) $filear['error'] = 4;
            if ($filear['error'] != 0) {
                $error = self::getErrorInfo($filear['error']);
                $error_info[] = $error;
                return self::_error($error);
            }

            //空间超容 有些虚拟主机不支持此函数
            if (!self::checkSpace($filear)) {
                return self::_error($this->error);
            }

            //目录不可写
            if (!self::checkUploadWritable()) {
                return self::_error($this->error);
            }

            //文件大小是否正确
            if (!self::checkFileSize($filear)) {
                return self::_error($this->error);
            }

            //文件后缀是否为合法后缀
            $this->getExt($filear["name"]); //获取允许的后缀
            $res = $this->checkImgExt($filear);
            if (!$res) return self::_error($this->error);
            $res = $this->checkExt();
            if (!$res) return self::_error($this->error);


            //新建保存文件
            $res = $this->checkDir();
            if (!$res) return self::_error($this->error);

            //文件名重命名
            $this->set_savename($filear["name"], $this->is_rename);

            //复制文件
            $upfileok = false;
            $file_tmp = $filear["tmp_name"];
            $file_name = $this->savepath . $this->savename;
            if (stristr(PHP_OS, "WIN")) {
                $file_name = @iconv("utf-8", "GBK", $file_name);
            }
            if (function_exists("move_uploaded_file")) {
                if (move_uploaded_file($file_tmp, $file_name)) {
                    $upfileok = true;
                } else if (copy($file_tmp, $file_name)) {
                    $upfileok = true;
                }
            } elseif (copy($file_tmp, $file_name)) {
                $upfileok = true;
            }
            if (!$upfileok) {
                $error = self::getErrorInfo($filear['error']);
                $error_info[] = $error;
                return self::_error($error);
            } else {
                if (stripos($filear['tmp_name'], PATH_WEB) === false) {
                    @unlink($filear['tmp_name']); //Delete temporary files
                }
            }

            load::plugin('doqiniu_upload', 0, array('savename' => str_replace(PATH_WEB, '', $this->savepath) . $this->savename, 'localfile' => $file_name));

            $back = '../' . str_replace(PATH_WEB, '', $this->savepath) . $this->savename;
            return self::_success($back, $filear['size']);
        }

        /**
         * @param $filename
         * @return string|void
         */
        protected function getExt($filename)
        {
            if ($filename == "") return false;

            $ext = '';
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (preg_match("/^[0-9a-zA-Z]+$/u", $extension)) {
                $ext = strtolower($extension);
            }

            return $this->ext = $ext;
        }

        /**
         * 是否重命名
         * @param $filename
         * @param $is_rename 是否重命名，0或1
         * @return mixed|string
         */
        protected function set_savename($filename, $is_rename)
        {
            if ($is_rename) {//重命名
                srand((double)microtime() * 1000000);
                $rnd = rand(100, 999);
                $f_name = date('U') + $rnd;
                $f_name = $f_name . "." . $this->ext;
            } else {//不重命名
                $name_arr = explode('.', $filename);
                $ext = array_pop($name_arr);
                $str = implode('_', $name_arr);
                $f_name = "{$str}.{$ext}";
                $f_name = str_replace(array(":", "*", "?", "|", "/", "\\", "\"", "<", ">", "——", " "), '_', $f_name);

                if (stristr(PHP_OS, "WIN")) {
                    if (!preg_match('/^[<0-9a-zA-Z\x{4e00}-\x{9fa5}-_<>().\s]+$/u', $f_name) && version_compare(phpversion(),'5.4','<')) {
                        $this->set_savename($filename, 1);
                    }
                    $filename_temp = @iconv("utf-8", "GBK", $f_name);
                } else {
                    $filename_temp = $f_name;
                }

                $i = 0;
                $savename_temp = str_replace('.' . $this->ext, '', $filename_temp);
                while (file_exists($this->savepath . $filename_temp)) {
                    $i++;
                    $filename_temp = $savename_temp . '(' . $i . ')' . '.' . $this->ext;
                }
                if ($i != 0) {
                    $f_name = str_replace('.' . $this->ext, '', $f_name) . '(' . $i . ')' . '.' . $this->ext;
                }
            }
            return $this->savename = $f_name;
        }

        /**
         * @return bool
         */
        protected function checkExt()
        {
            global $_M;
            if (in_array($this->ext, $this->not_allowed)) {
                $this->error = $this->ext . $_M['word']['upfileTip3'];
                return false;
            }

            if (!$this->format || !in_array($this->ext, $this->format)) {
                $this->error = $this->ext . $_M['word']['upfileTip3'];
                return false;
            }

            return true;
        }

        /**
         * @param $filear
         * @return bool
         */
        protected function checkImgExt($filear)
        {
            global $_M;
            if (!getimagesize($filear['tmp_name']) && in_array($this->ext, array('png', 'jpg', 'gif', 'bmp', 'jpeg'))) {
                // 假图片不允许上传
                $this->error = $_M['word']['upfileTip3'];
                return false;
            }
            return true;
        }

        /**
         * @param $filear
         * @return bool
         */
        protected function checkOtherExt($filear)
        {
            global $_M;
            if (in_array($this->ext, array('pdf','svg'))) {
                $content = file_get_contents($filear['tmp_name']);
                $content = strtolower($content);
                $res = strpos($content, "<script");
                if ($res !== false) {
                    $this->error = $this->ext." content error";
                    return false;
                }
            }
            return true;
        }

        /**
         * @param $filear
         * @return bool
         */
        protected function checkSpace($filear)
        {
            if (function_exists('disk_free_space')) {
                if (disk_free_space(__DIR__) != FALSE && disk_free_space(__DIR__) != 'NULL') {
                    if (disk_free_space(__DIR__) < $filear["size"]) {
                        $this->error = "out of disk space";
                        return false;
                    }
                }
            }
            return true;
        }

        protected function checkUploadWritable()
        {
            if (!is_writable(PATH_WEB . "upload")) {
                $this->error = "directory ['" . PATH_WEB . "upload'] can not write";
                return false;
            }
            return true;
        }

        protected function checkDir()
        {
            global $_M;
            if (stripos($this->savepath, PATH_WEB . 'upload/') !== 0) {
                $this->error = $_M['word']['upfileFail2'];
                return false;
            }

            if (strstr($this->savepath, './')) {
                $this->error = $_M['word']['upfileTip3'];
                return false;
            }
            if (!makedir($this->savepath)) {
                $this->error = $_M['word']['upfileFail2'];
                return false;
            }
            return true;
        }

        /**
         * @param $filear
         * @return bool
         */
        protected function checkFileSize($filear)
        {
            global $_M;
            if ($filear["size"] > $this->maxsize) {
                $md = byte_format($this->maxsize, 0, 'MB');
                $this->error = "{$_M['word']['upfileFile']} \"" . $filear["name"] . "\" {$_M['word']['upfileMax']} {$md} {$_M['word']['upfileTip1']}";
                return false;
            }
            return true;
        }

        /**
         * @param $error
         * @return array
         */
        protected function _error($error)
        {
            $redata = array();
            $redata['error'] = $error;
            $redata['msg'] = $error;
            return $redata;
        }

        /**
         * @param string $path
         * @param string $size
         * @return array
         */
        protected function _success($path = '', $size = '')
        {
            $redata = array();
            $redata['size'] = $size;
            $redata['path'] = $path;
            return $redata;
        }
    }

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>