<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_func('file');
/**
 * 栏目标签类
 */

class column_op
{

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        $this->lang = $_M['lang'];
    }

    /**
     * 对当前管理员有权限操作的栏目信息进行整理
     * @param string $lang
     * @return mixed
     */
    public function get_sorting_by_lv($lang = '')
    {
        global $_M;
        $information = load::mod_class('column/column_database', 'new')->get_all_column_by_lang($lang);
        foreach ($information as $key => $val) {
            //为外部栏目
            if ($val['module'] == 0 || 1) {
                if (!hasPermission('c', $val['id'])) continue;

                $classtype = 'class' . $val['classtype'];
                if ($val['classtype'] > 1) {
                    $sorting[$classtype][$val['bigclass']][$key] = $val;
                }else{
                    $sorting[$classtype][$key] = $val;
                }
            }
        }
        return $sorting;
    }

    /**
     * @param $lang
     * @return array
     */
    public function lv_class($lang = '')
    {
        $column_label = load::sys_class('label', 'new')->get('column');
        $column_list = load::mod_class('column/column_database', 'new')->get_all_column_by_lang($lang);
        $list = array();
        foreach ($column_list as $key => $val) {
            if (!hasPermission('c', $val['id'], 1)) continue;

            if ($val['classtype'] == 1) {
                $list['class1'][$val['id']] = $val;
            }elseif ($val['classtype'] == 2) {
                if ($val['releclass']) {
                    $list['class1'][$val['id']] = $val;
                }else{
                    $list['class2'][$val['id']] = $val;
                }
            }elseif ($val['classtype'] == 3) {
                $bigclass3 = $column_label->get_parent_column($val['id']);
                if ($bigclass3['releclass']) {
                    $list['class2'][$val['id']] = $val;
                }else{
                    $list['class3'][$val['id']] = $val;
                }
            }
        }

        return $list;
    }


    /**
     * @param $lang
     * @return array
     */
    public function __lv_class($lang = '')
    {
        $column_list = load::mod_class('column/column_database', 'new')->get_all_column_by_lang($lang);

        $list = array();
        foreach ($column_list as $key => $val) {
            $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($val['id']);

            if ($class123['class1']) {
                if (!hasPermission('c', $class123['class1']['id'], 1)) continue;
                $list['class1'][$class123['class1']['id']] = $class123['class1'];
             }

            if ($class123['class2']) {
                if (!hasPermission('c', $class123['class2']['id'], 1)) continue;
                $list['class2'][$class123['class2']['id']] = $class123['class2'];
            }

            if ($class123['class3']) {
                if (!hasPermission('c', $class123['class3']['id'], 1)) continue;
                $list['class3'][$class123['class3']['id']] = $class123['class3'];
            }
        }

        return $list;
    }

    /**
     * @param bool $power
     * @param string $lang
     * @return mixed
     */
    public function get_sorting_by_module($power = true, $lang = '')
    {
        global $_M;
        $information = load::mod_class('column/column_database', 'new')->get_all_column_by_lang($lang);
        foreach ($information as $key => $val) {
            if ($power) {
                $class123 = load::sys_class('label', 'new')->get('column')->get_class123_no_reclass($val['id']);
                if (!hasPermission('c' , $class123['class1']['id'])) continue;
            }

            if ($val['releclass'] != 0 && in_array($val['module'], array(1,2,3,4,5,6))) {
                $sorting[$val['module']]['class1'][$key] = $information[$key];
                $column_classtype[] = $val['id'];
            } else {
                if ($val['classtype'] == 1) {
                    $sorting[$val['module']]['class1'][$key] = $information[$key];
                }
                if ($val['classtype'] == 2) {
                    $sorting[$val['module']]['class2'][$key] = $information[$key];
                }
            }
        }
        foreach ($information as $key => $val) {
            $i = 0;
            if ($val['classtype'] == 3) {
                foreach ($column_classtype as $key1 => $val1) {
                    if ($val['bigclass'] == $val1) {
                        $i = 1;
                    }
                }
                if ($i == 1) {
                    $sorting[$val['module']]['class2'][$key] = $information[$key];
                } else {
                    $sorting[$val['module']]['class3'][$key] = $information[$key];
                }
            }
        }
        return $sorting;
    }

    /**
     * @param $id 要复制的栏目ID
     * @param $local_lang 被复制的语言
     * @param $to_lang 复制到的目标语言
     * @param $is_contents 是否复制内容
     * @param $allids 所有需要复制的栏目ID（判断子栏目复制与否）
     * @return bool
     */
    public function copy_column($id = '', $local_lang ='', $to_lang = '', $is_contents = 0, $allids = array())
    {
        global $_M;
        if (!$id || !$allids) {
            return false;
        }

        $c = load::sys_class('label', 'new')->get('column')->get_column_id($id);
        if (!$to_lang) {
            $to_lang = $c['lang'];
        }
        if ($c['classtype'] != 1) return false;
        $class1 = $this->in_column($c['id'], 0, $local_lang, $to_lang);

        if ($class1) {
            if ($is_contents == 1) {
                //复制模块全局栏目属性
                $module = load::sys_class('handle', 'new')->mod_to_file($c['module']);
                $para_class0 = load::mod_class('parameter/parameter_op', 'new')->copy_parameter($module, 0, 0, 0, $to_lang);

                $para_class1 = $this->copy_para($id, $c['module'], $c['module'], $class1, 0, 0, $to_lang);
                $this->copy_content($id, $c['module'], $c['module'], $class1, 0, 0, $to_lang, (array)$para_class0 + (array)$para_class1);
            }

            $son_class2 = load::sys_class('label', 'new')->get('column')->get_column_son($c['id']);
            foreach ($son_class2 as $val2) {
                if (in_array($val2['id'], $allids)) {
                    $val_class2 = $this->in_column($val2['id'], $class1, $local_lang, $to_lang);
                    if ($is_contents) {
                        $para_class2 = $this->copy_para($val2['id'], $c['module'], $val2['module'], $class1, $val_class2, 0, $to_lang);
                        $this->copy_content($val2['id'], $c['module'], $val2['module'], $class1, $val_class2, 0, $to_lang, (array)$para_class0 + (array)$para_class1 + (array)$para_class2);
                    }

                    $val_son_class3 = load::sys_class('label', 'new')->get('column')->get_column_son($val2['id']);
                    foreach ($val_son_class3 as $val3) {
                        if (in_array($val3['id'], $allids)) {
                            $val_class3 = $this->in_column($val3['id'], $val_class2, $local_lang, $to_lang);
                            if ($is_contents) {
                                $para_class3 = $this->copy_para($val3['id'], $c['module'], $val3['module'], $class1, $val_class2, $val_class3, $to_lang);
                                $this->copy_content($val3['id'], $c['module'], $val3['module'], $class1, $val_class2, $val_class3, $to_lang, (array)$para_class0 + (array)$para_class1 + (array)$para_class2 + (array)$para_class3);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * 插入栏目；
     */
    public function in_column($id = '', $bigclass = '', $local_lang = '', $tolang = '')
    {
        $infos = load::mod_class('column/column_database', 'new')->get_column_by_id($id, $local_lang);
        if (!$bigclass) $bigclass = 0;
        if ($infos['id']) {
            $classnow = $infos['id'];
            unset($infos['id']);
            $infos['bigclass'] = $bigclass;
            if ($infos['releclass']) {
                $infos['bigclass'] = $bigclass;
            }
            $infos['filename'] = '';
            $infos['lang'] = $tolang;
            $toclass = load::mod_class('column/column_database', 'new')->insert($infos);
            if ($infos['module'] == 6 || $infos['module'] == 7 || $infos['module'] == 8) {
                load::mod_class('config/config_op', 'new')->copy_column_config($classnow, $toclass, $tolang);
            }
            return $toclass;
        } else {
            return false;
        }
    }

    /**
     * 复制栏目下内容；
     */
    public function copy_content($column_id, $class1_module, $classnow_module, $toclass1, $toclass2, $toclass3, $to_lang, $paras = array())
    {
        if (in_array($classnow_module, array(2, 3, 4, 5, 6))) {
            if ($class1_module != $classnow_module) {
                $toclass1 = $toclass2;
                $toclass2 = $toclass3;
                $toclass3 = 0;
            }
            $name = load::sys_class('handle', 'new')->mod_to_file($classnow_module);
            $mod_op = load::mod_class("{$name}/{$name}_op", 'new');
            if (method_exists($mod_op, 'list_copy')) {
                $mod_op->list_copy($column_id, $toclass1, $toclass2, $toclass3, $to_lang, $paras);
            }
        }
        return true;
    }

    /**
     * 复制栏目下属性
     * @param $column_id
     * @param $class1_module
     * @param $classnow_module
     * @param $toclass1
     * @param $toclass2
     * @param $toclass3
     * @param $to_lang
     * @param $is_contents
     * @return void
     */
    public function copy_para($column_id, $class1_module, $classnow_module, $toclass1, $toclass2, $toclass3, $to_lang)
    {
        if ($class1_module != $classnow_module) {//关联栏目
            $toclass1 = $toclass2;
            $toclass2 = $toclass3;
            $toclass3 = 0;
        }
        if ($classnow_module >= 2 && $classnow_module <= 5) {
            //新闻 产品 图片 下载
            $paras = load::mod_class('parameter/parameter_op', 'new')->copy_parameter($column_id, $toclass1, $toclass2, $toclass3, $to_lang);
        } else if ($classnow_module == 6) {
            //招聘
            $paras = load::mod_class('parameter/parameter_op', 'new')->copy_parameter($column_id, $toclass1, $toclass2, $toclass3, $to_lang);
        } else if ($classnow_module == 7 || $classnow_module == 8) {
            //留言 反馈
            $paras = load::mod_class('parameter/parameter_op', 'new')->copy_parameter($column_id, $toclass1, $toclass2, $toclass3, $to_lang);
        }
        return $paras;
    }

    /**
     * 恢复栏目文件
     * @param int $force
     */
    public function do_recover_column_files($force = false)
    {
        global $_M;
        $query = "SELECT `foldername`,`module`,`id` FROM {$_M['table']['column']}";
        $columnarr = DB::get_all($query);

        $modulenum = Array(0, 1, 2, 3, 4, 5, 8, 0);
        foreach ($columnarr as $row) {
            if (in_array($row['module'], $modulenum)) {
                $exists = file_exists(PATH_WEB . "{$row['foldername']}/index.php");
                if (!$exists || $force) {
                    $this->_setColumn($row['foldername'], $row['module'], $row['id'], $force);
                }
            }
        }
    }

    /**
     * @param $address
     * @param $newfile
     * @param int $force
     * @return bool|int
     */
    public function makeFolder($address, $newfile, $force = 0)
    {
        $oldcont = "<?php\n";
        $oldcont .= "# MetInfo Enterprise Content Management System \n";
        $oldcont .= "# Copyright (C) MetInfo Co.,Ltd (http://www.mituo.cn). All rights reserved. \n";
        $oldcont .= "require_once '{$address}';\n";
        $oldcont .= "# This program is an open source system, commercial use, please consciously to purchase commercial license.\n";
        $oldcont .= "# Copyright (C) MetInfo Co., Ltd. (http://www.mituo.cn). All rights reserved.\n";
        $oldcont .= "?>";
        $filename = str_replace(PATH_WEB, '', $newfile);
        $filename = preg_replace("/\/\w+\.php/", '', $filename);
        if ((!file_exists($newfile) && !$this->checkFolder($filename)) || $force) {
            makefile($newfile);
            return file_put_contents($newfile, $oldcont);
        }
    }

    /**
     * 是否是系统模块
     * @param string $filename
     * @return bool
     */
    public function checkFolder($filename = '')
    {
        $sys_folders = load::sys_class('handle', 'new')->sys_folders();
        if (in_array($filename, $sys_folders)) {
            return true;
        }
        return false;
    }

    /**
     * @param $foldername
     * @param $module
     * @param $id
     * @param int $type
     * @return bool
     */
    public function _setColumn($foldername, $module, $id, $type = 0)
    {
        global $_M;
        if (!$foldername) return false;
        switch ($module) {
            case 1:
                $indexaddress = "../about/index.php";
                $newfile = PATH_WEB . $foldername . "/show.php";
                $address = "../about/show.php";
                $this->makeFolder($address, $newfile, $type);
                break;
            case 2:
                $indexaddress = "../news/index.php";
                $newfile = PATH_WEB . $foldername . "/news.php";
                $address = "../news/news.php";
                $this->makeFolder($address, $newfile, $type);
                $newfile = PATH_WEB . $foldername . "/shownews.php";
                $address = "../news/shownews.php";
                $this->makeFolder($address, $newfile, $type);
                break;
            case 3:
                $indexaddress = "../product/index.php";
                $newfile = PATH_WEB . $foldername . "/product.php";
                $address = "../product/product.php";
                $this->makeFolder($address, $newfile, $type);
                $newfile = PATH_WEB . $foldername . "/showproduct.php";
                $address = "../product/showproduct.php";
                $this->makeFolder($address, $newfile, $type);
                break;
            case 4:
                $indexaddress = "../download/index.php";
                $newfile = PATH_WEB . $foldername . "/download.php";
                $address = "../download/download.php";
                $this->makeFolder($address, $newfile, $type);
                $newfile = PATH_WEB . $foldername . "/showdownload.php";
                $address = "../download/showdownload.php";
                $this->makeFolder($address, $newfile, $type);
                break;
            case 5:
                $indexaddress = "../img/index.php";
                $newfile = PATH_WEB . $foldername . "/img.php";
                $address = "../img/img.php";
                $this->makeFolder($address, $newfile, $type);
                $newfile = PATH_WEB . $foldername . "/showimg.php";
                $address = "../img/showimg.php";
                $this->makeFolder($address, $newfile, $type);
                break;
            case 8:
                $indexaddress = "../feedback/index.php";
                $newfile = PATH_WEB . $foldername . "/feedback.php";
                $address = "../feedback/feedback.php";
                $this->makeFolder($address, $newfile, $type);
                break;
        }
        $this->makeFolder($indexaddress, PATH_WEB . $foldername . '/index.php', $type);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.;
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
