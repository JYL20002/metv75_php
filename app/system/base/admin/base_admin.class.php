<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

class base_admin extends admin
{
    public $table;
    public $module;
    public $lang;
    public $met_admin;
    public $allow_class;
    public $para_op;
    public $relation_op;

    /**
     * 初始化
     */
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->lang = $_M['lang'];
        $this->module = 0;
        $this->table = '';
        $this->getAllowClass();
        $this->para_op = load::mod_class('parameter/parameter_op', 'new');
        $this->relation_op = load::mod_class('relation/relation_op', 'new');
        $this->history_op = load::mod_class('history/history_op', 'new');
        $this->html_op = load::mod_class('html/html_op', 'new');
    }

    /*******内容管理操作********/
    /**
     * 修改排序
     * @param string $id
     * @param string $no_order
     * @return bool
     */
    public function list_no_order($id = '', $no_order = '')
    {
        if ($id && is_numeric($id)) {
            $list['id'] = $id;
            $list['no_order'] = $no_order;
            return $this->database->update_by_id($list);
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 前台显示
     * @param string $id
     * @param string $display
     * @return bool
     */
    public function list_display($id = '', $display = '')
    {
        global $_M;
        if ($display == 1) {
            if(!self::verifyPermissions()){
                $this->error[] = $_M['word']['js81']; //forbidden
                return false;
            }
        }

        if ($id && is_numeric($id)) {
            $list['id'] = $id;
            $list['displaytype'] = $display;
            return $this->database->update_by_id($list);
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 置顶
     * @param string $id
     * @param string $top
     * @return bool
     */
    public function list_top($id = '', $top = '')
    {
        if ($id && is_numeric($id)) {
            $list['id'] = $id;
            $list['top_ok'] = $top;
            return $this->database->update_by_id($list);
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 推荐
     * @param string $id
     * @param string $com
     * @return bool
     */
    public function list_com($id = '', $com = '')
    {
        if ($id && is_numeric($id)) {
            $list['id'] = $id;
            $list['com_ok'] = $com;
            return $this->database->update_by_id($list);
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 删除
     * @param string $id
     * @param int $recycle
     * @return bool
     */
    public function del_list($id = '', $recycle = 1)
    {
        if ($id && is_numeric($id)) {
            if ($recycle == 1) {
                //放入回收站
                $list['id'] = $id;
                $list['recycle'] = $recycle;
                $list['updatetime'] = date('Y-m-d H:i:s', time());
                return $this->database->update_by_id($list);
            } else {
                //删除数据
                self::delPara($id);
                self::delRelations($id);
                self::delHistory($id);
                return $this->database->del_by_id($id);
            }
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 复制内容
     * @param string $id
     * @param string $class1
     * @param string $class2
     * @param string $class3
     * @return bool|mixed|number
     */
    public function list_copy($id = '', $class1 = '', $class2 = '', $class3 = '')
    {
        if ($id && is_numeric($id)) {
            $content = $this->database->get_list_one_by_id($id);
            $content['filename'] = '';
            $content['class1'] = $class1;
            $content['class2'] = $class2;
            $content['class3'] = $class3;
            $content['classother'] = '';
//            $content['updatetime'] = date("Y-m-d H:i:s");
//            $content['addtime'] = date("Y-m-d H:i:s");
            $content['title'] = str_replace("'", "\'", $content['title']);
            $content['description'] = str_replace("'", "\'", $content['description']);
            $content['keywords'] = str_replace("'", "\'", $content['keywords']);
            $content['filename'] = str_replace("'", "\'", $content['filename']);
            $content['custom_info'] = str_replace("'", "\'", $content['custom_info']);
            $content['other_info'] = str_replace("'", "\'", $content['other_info']);
            $content['content'] = str_replace('\'', '\'\'', $content['content']);
            $content['content1'] = str_replace('\'', '\'\'', $content['content1']);
            $content['content2'] = str_replace('\'', '\'\'', $content['content2']);
            $content['content3'] = str_replace('\'', '\'\'', $content['content3']);
            $content['content4'] = str_replace('\'', '\'\'', $content['content4']);
            $copyid = $this->insert_list_sql($content);

            //复制产品参数
            if (in_array($this->module, array(3, 4, 5))) {
                $this->para_copy($id, $copyid);
            }
            return $copyid;
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 复制系统属性
     * @param $id
     * @param $copyid
     */
    public function para_copy($id, $copyid)
    {
        global $_M;
        $this->plist_database = load::mod_class('parameter/parameter_list_database', 'new');
        $this->plist_database->construct($this->module);

        ##$paralist = $this->para_list->get_list($id, $this->module);//
        $paralist = $this->plist_database->get_by_listid($id);
        foreach ($paralist as $key => $paravalue) {
            $listid = $copyid;
            $paraid = $paravalue['paraid'];
            $info = $paravalue['info'];
            $imgname = $paravalue['imgname'];
            $lang = $paravalue['lang'];
            $module = $paravalue['module'];
            ##$this->para_list->insert_plist($listid, $paraid, $info, $imgname, $lang, $module);
            $this->plist_database->update_by_listid_paraid($listid, $paraid, $info, $imgname);
        }
    }

    /**
     * 多语言内容复制
     */
    public function copy_tolang($id = '', $module = '', $tolang = '', $new_class = array())
    {
        global $_M;
        if ($id && $module && $tolang && $new_class) {
            $content = $this->database->get_list_one_by_id($id);
            if ($content) {
                $content['id'] = '';
                $content['filename'] = '';
                $content['class1'] = $new_class[0];
                $content['class2'] = $new_class[1];
                $content['class3'] = $new_class[2];
                $content['classother'] = '';
//                $content['updatetime'] = date("Y-m-d H:i:s");
//                $content['addtime'] = date("Y-m-d H:i:s");
                $content['title'] = str_replace("'", "\'", $content['title']);
                $content['description'] = str_replace("'", "\'", $content['description']);
                $content['keywords'] = str_replace("'", "\'", $content['keywords']);
                $content['filename'] = str_replace("'", "\'", $content['filename']);
                $content['custom_info'] = str_replace("'", "\'", $content['custom_info']);
                $content['other_info'] = str_replace("'", "\'", $content['other_info']);
                $content['content'] = str_replace('\'', '\'\'', $content['content']);
                $content['content1'] = str_replace('\'', '\'\'', $content['content1']);
                $content['content2'] = str_replace('\'', '\'\'', $content['content2']);
                $content['content3'] = str_replace('\'', '\'\'', $content['content3']);
                $content['content4'] = str_replace('\'', '\'\'', $content['content4']);
                $content['lang'] = $tolang ? $tolang : $content['lang'];
                $new_id = $this->insert_list_sql($content);
                if ($new_id) {
                    return $new_id;
                }
            }
            $this->error[] = "Content replication failed";
            return false;
        }
    }

    /**
     * 移动内容
     * @param string $id
     * @param string $class1
     * @param string $class2
     * @param string $class3
     * @return bool
     */
    public function list_move($id = '', $class1 = '', $class2 = '', $class3 = '')
    {
        if ($id && is_numeric($id)) {
            $list['id'] = $id;
            $list['class1'] = $class1 ?: 0;
            $list['class2'] = $class2 ?: 0;
            $list['class3'] = $class3 ?: 0;
            return $this->database->update_by_id($list);
        }
        $this->error[] = 'error no id';
        return false;
    }

    /**
     * 获取系统属性列表
     * @return void
     */
    public function paramentList()
    {
        global $_M;
        $class1 = is_numeric($_M['form']['class1']) ? intval($_M['form']['class1']) : '';
        $class2 = is_numeric($_M['form']['class2']) ? intval($_M['form']['class2']) : '';
        $class3 = is_numeric($_M['form']['class3']) ? intval($_M['form']['class3']) : '';
        $listid = is_numeric($_M['form']['id']) ? intval($_M['form']['id']) : 0;
        $hid = is_numeric($_M['form']['hid']) ? intval($_M['form']['hid']) : 0;
        $paralist = $this->para_op->paratem($listid, $this->module, $class1, $class2, $class3, $hid);

        if ($_M['form']['app_type'] == 'shop') {
            require PATH_SYS_TEM . 'admin_old/paratype.php';
        } else {
            $this->success($paralist);
        }
    }

    /**
     * @param $pid
     * @param array $para
     */
    public function setPara($pid, $para = array())
    {
        if (in_array($this->module, array(2, 3, 4, 5))) {
            //更新系统属性新闻让你产品 下载 图片
            $this->para_op->insert($pid, $this->module, $para);
        }
        return;
    }

    /**
     * @param $pid
     * @param array $para
     */
    public function updatePara($pid, $para = array(), $hid = 0)
    {
        if (in_array($this->module, array(2, 3, 4, 5))) {
            if ($hid) $this->history_op->recordPlistHistory($pid, $this->module, $hid); //记录参数历史数据
            $this->para_op->update($pid, $this->module, $para);
        }
    }

    /**
     * 删除属性值
     * @param $pid
     */
    public function delPara($pid)
    {
        if (in_array($this->module, array(2, 3, 4, 5))) {
            $this->para_op->delPlist($pid, $this->module); //删除属性值
        }
    }

    /**
     * @param $pid
     * @param $relations
     * @param $hid
     * @return void
     */
    public function setRelations($pid, $relations, $hid = null)
    {
        if (in_array($this->module, array(2, 3, 4, 5))) {
            if ($hid) $this->history_op->recordRelationsHistory($pid, $this->module,$hid); //记录参数历史数据
            //更新系统属性产品 下载 图片
            $this->relation_op->setRelations($pid, $this->module, $relations);
        }
        return;
    }

    /**
     * 删除关联数据
     * @param $pid
     */
    public function delRelations($pid)
    {
        if (in_array($this->module, array(2, 3, 4, 5))) {
            $this->relation_op->delRelations($pid, $this->module);
        }
        return;
    }

    /**
     * 输出历史记录
     * @param $pid
     * @return void
     */
    public function delHistory($pid)
    {
        $this->history_op->delHistory($pid, $this->module);

    }

    /**
     * 条件内容基础数据 多模块共用
     * @return mixed
     */
    public function add()
    {
        global $_M;
        $list['class1'] = $_M['form']['class1'] ?: '';
        $list['class2'] = $_M['form']['class2'] ?: '';
        $list['class3'] = $_M['form']['class3'] ?: '';
        $class_now = $list['class3'] ?: ($list['class2'] ?: $list['class1']);
        $column = load::sys_class('label', 'new')->get('column')->get_column_id($class_now);
        $list['access'] = $column['access'];
        $list['displaytype'] = 1;
        $list['addtype'] = 1;
        $list['updatetype'] = 1;
        $list['updatetime'] = date("Y-m-d H:i:s");
        $list['publisher'] = $this->admin_member['admin_name'] ? : $this->admin_member['admin_id'];

        return $list;
    }

    /**
     * 新增内容插入数据处理
     * @param  前台提交的表单数组 $list
     * @return $pid  新增的ID 失败返回FALSE
     */
    public function insert_list($list = array())
    {
        global $_M;
        //图片处理 缩略图 水印图
        if ($list['imgurl'] == '') {
            if (preg_match('/\.\.\/upload([\w\/\_<\x{4e00}-\x{9fa5}>\-\(\)]*)\.(jpg|png|gif)/iu', $list['content'], $out)) {
                if ($out[0]) {
                    $list['imgurl'] = str_replace('watermark/', '', $out[0]);
                }
            }
        }
        if ($list['imgurl']) {
            $list = $this->form_imglist($list, $this->module);
        }

        $pid = $this->insert_list_sql($list);
        // 更新TAG标签
        load::sys_class('label', 'new')->get('tags')->updateTags($list['tag'], $this->module, $list['class1'], $pid, 1);

        if ($pid) {
            //更新系统属性
            self::setPara($pid, $list);
            self::setRelations($pid, $list['relations']);
            return $pid;
        } else {
            $this->error[] = "Data error";
            return false;
        }
    }

    /**
     * @param $list
     * @return mixed
     */
    public function insert_list_sql($list)
    {
        global $_M;
        if (!$this->check_filename($list['filename'], '')) {
            return false;
        }

//        $list['links'] = $list['links'] ? url_standard($list['links']) : "";
        $list['description'] = $list['description'] ?: $this->description($list['content']);
        if ($list['addtype'] == 2 && strtotime($list['updatetime']) < strtotime($list['addtime'])) {
            $list['updatetime'] = $list['addtime'];
        }

        $list['class1'] = $list['class1'] ?: 0;
        $list['class2'] = $list['class2'] ?: 0;
        $list['class3'] = $list['class3'] ?: 0;
        $list['description'] = $list['description'] ?htmlentities($list['description']): '';
        $list['title'] = $list['title'] ?htmlentities($list['title']): '';
        $list['ctitle'] = $list['ctitle'] ?htmlentities($list['ctitle']): '';
        $list['keywords'] = $list['keywords'] ?htmlentities($list['keywords']): '';
        $list['custom_info'] = $list['custom_info'] ?: '';
        $list['displayimg'] = $list['displayimg'] ?: '';
        $list['no_order'] = $list['no_order'] ?: 0;
        $list['displaytype'] = $list['displaytype'] ?: 0;
        $list['com_ok'] = $list['com_ok'] ? 1 : 0;
        $list['wap_ok'] = $list['wap_ok'] ? 1 : 0;
        $list['top_ok'] = $list['top_ok'] ? 1 : 0;
        $list['new_ok'] = $list['new_ok'] ? 1 : 0;
        $list['text_size'] = is_numeric($list['text_size']) ? $list['text_size'] : 0;
        $list['lang'] = $list['lang'] ? $list['lang'] : $this->lang;
        $list['issue'] = $this->admin_member['admin_id'];
        //发布信息需要审核才能正常显示
        $list['displaytype'] = self::verifyPermissions() ? $list['displaytype'] : 0;
        return $this->database->insert($list);
    }

    /**
     * 保存修改
     * @param  array $list 修改的数组
     * @return bool  修改是否成功
     */
    public function update_list($list = array(), $id = '')
    {
        global $_M;
        //水印图
        if ($list['imgurl'] == '') {
            if (preg_match('/\.\.\/upload([\w\/\_<\x{4e00}-\x{9fa5}>\-\(\)]*)\.(jpg|jpeg|png|gif)/iu', $list['content'], $out)) {
                if ($out[0]) {
                    $list['imgurl'] = str_replace('watermark/', '', $out[0]);
                }
            }
        }

        //图片处理 缩略图 水印图
        $list = $this->form_imglist($list, $this->module);
        // 更新TAG标签
        load::sys_class('label', 'new')->get('tags')->updateTags($list['tag'], $this->module, $list['class1'], $id);
        //发布信息需要审核才能正常显示
        $list['displaytype'] = self::verifyPermissions() ? $list['displaytype'] : 0;

        //历史记录
        $history_data = $this->database->get_list_one_by_id($id);
        if ($this->update_list_sql($list, $id)) {
            $hid = $this->history_op->record($history_data, $this->module);
            self::updatePara($id, $list, $hid);
            self::setRelations($id, $list['relations'] ,$hid);
            return true;
        } else {
            $this->error[] = 'Data error';
            return false;
        }
    }

    /**
     * 保存修改sql
     * @param array $list
     * @param string $id
     * @return bool
     */
    public function update_list_sql($list = array(), $id = '')
    {
        global $_M;
        if (!$this->check_filename($list['filename'], $id)) {
            return false;
        }

        if (strtotime($list['addtime_l']) > time()) {
            $addtime = date("Y-m-d H:m:s");
        } else {
            $addtime = $list['addtime_l'];
        }

        $list['id'] = $id;
        if ($list['description']){
            $list['description'] = htmlentities($list['description']);
        }else{
            if(!$id){
                $this->description($list['content']);
            }
        }
        $list['title'] = $list['title'] ?htmlentities($list['title']): '';
        $list['ctitle'] = $list['ctitle'] ?htmlentities($list['ctitle']): '';
        $list['keywords'] = $list['keywords'] ?htmlentities($list['keywords']): '';
        $list['custom_info'] = $list['custom_info'] ?: '';
        $list['displayimg'] = $list['displayimg'] ?: '';
        $list['no_order'] = $list['no_order'] ?: 0;
        $list['displaytype'] = $list['displaytype'] ?: 0;
        $list['com_ok'] = $list['com_ok'] ? 1 : 0;
        $list['wap_ok'] = $list['wap_ok'] ? 1 : 0;
        $list['top_ok'] = $list['top_ok'] ? 1 : 0;
        $list['new_ok'] = $list['new_ok'] ? 1 : 0;
        $list['addtime'] = $list['addtype'] == 2 ? $list['addtime'] : $addtime;
        $list['updatetime'] = $list['updatetype'] == 2 ? $list['updatetime'] : date("Y-m-d H:m:s");

//        $list['links'] = $list['links'] ? url_standard($list['links']) : "";
        return $this->database->update_by_id($list);
    }

    /**
     * 内容审核后发布
     * @return void
     */
    private function verifyPermissions()
    {
        //发布信息需要审核才能正常显示
        if ($this->admin_member['role']['code'] == 'root') return true;
        if ($this->admin_member['admin_check']) return false;
        return true;
    }

    /*******内容管理操作********/

    /**
     * 栏目列表
     * @return array
     */
    public function docolumnjson()
    {
        global $_M;
        $list = self::_columnjson();
        if ($_M['form']['noajax']) {
            return $list;
        }
        $redata = array();
        $redata['citylist'] = $list['columnlist'];
        return $this->ajaxReturn($redata);
    }

    public function _columnjson()
    {
        $list = self::column_json($this->module/*, $_M['form']['type']*/);
        $list = array(
            'columnlist' => $list['citylist'],
            'columnlist_json' => jsonencode($list['citylist'])

        );
        return $list;
    }

    /**
     * 模块栏目列表
     * @param string $module
     * @param string $type
     * @param string $id
     * @return array
     */
    public function column_json($module = '', $type = '', $id = '')
    {
        global $_M;
        if ($type == 1) {
            $array = $this->column(3, $module, $id);
        } else {
            $array = $this->column(3, $module);
        }

        $metinfo = array();
        $i = 0;
        if ($type != 1) {
            $metinfo['citylist'][$i]['p']['name'] = "{$_M['word']['columnselect1']}";
            $metinfo['citylist'][$i]['p']['value'] = '';
        }
        if ($type == 1) {
            $this->handle = load::sys_class('handle', 'new');
        }
        foreach ($array['class1'] as $key => $val) { //一级级栏目
            if ($val['module'] == $module) {
                $i++;
                $metinfo['citylist'][$i]['p']['name'] = $val['name'];
                if ($type == 1) {
                    $metinfo['citylist'][$i]['p']['value'] = $this->handle->mod_to_file($val['module']) . '|';
                }
                $metinfo['citylist'][$i]['p']['value'] .= $val['id'];

                //if (count($array['class2'][$val['id']])) { //二级栏目
                if ($array['class2'][$val['id']]) { //二级栏目
                    $k = 0;
                    if ($type != 1) {
                        $metinfo['citylist'][$i]['c'][$k]['n']['name'] = "{$_M['word']['modClass2']}";
                        $metinfo['citylist'][$i]['c'][$k]['n']['value'] = '';
                        $k++;
                    }
                    foreach ($array['class2'][$val['id']] as $key => $val2) {
                        $metinfo['citylist'][$i]['c'][$k]['n']['name'] = $val2['name'];
                        $metinfo['citylist'][$i]['c'][$k]['n']['value'] = $val2['id'];

                        //if (count($array['class3'][$val2['id']])) { //三级栏目
                        if ($array['class3'][$val2['id']]) { //三级栏目
                            $j = 0;
                            if ($type != 1) {
                                $metinfo['citylist'][$i]['c'][$k]['a'][0]['s']['name'] = "{$_M['word']['modClass3']}";
                                $metinfo['citylist'][$i]['c'][$k]['a'][0]['s']['value'] = '';
                                $j++;
                            }
                            foreach ($array['class3'][$val2['id']] as $key => $val3) {
                                $metinfo['citylist'][$i]['c'][$k]['a'][$j]['s']['name'] = $val3['name'];
                                $metinfo['citylist'][$i]['c'][$k]['a'][$j]['s']['value'] = $val3['id'];
                                $j++;
                            }
                        }
                        $k++;
                    }
                }
            }
        }

        return $metinfo;
        ##$this->ajaxReturn($array);
    }

    /**
     * 提取描述文字
     * @param  string $content 描述文字
     * @return array  提取后的描述文字
     */
    public function description($content)
    {
        global $_M;
        $desc = strip_tags($content);
        $arr = array("\n", "\r", "\t", "&nbsp;");
        $desc = str_replace($arr, '', $desc);
        $desc = str_replace("&nbsp;", ' ', $desc);
        $desc = mb_substr($desc, 0, 200, 'utf-8');
        return $desc;
    }

    /**
     * ajax检测静态文件是否重名//base
     */
    public function docheck_filename()
    {
        global $_M;
        $redata = array();
        if (is_numeric($_M['form']['filename'])) {
            $errorno = $this->errorno == 'error_filename_cha' ? $_M['word']['js74'] : $_M['word']['admin_tag_setting10'];
            $redata['valid'] = false;
            $redata['message'] = $errorno;
            $this->ajaxReturn($redata);
        }

        if (!$this->check_filename($_M['form']['filename'], $_M['form']['id'])) {
            $errorno = $this->errorno == 'error_filename_cha' ? $_M['word']['js74'] : $_M['word']['js73'];
            $redata['valid'] = false;
            $redata['message'] = $errorno;
            $this->ajaxReturn($redata);
        } else {
            $redata['valid'] = true;
            $redata['message'] = $_M['word']['js75'];
            $this->ajaxReturn($redata);
        }
    }

    /**
     * 静态页面名称验证
     * @param  string $filename select的name名称
     * @param  string $id 选中的权限字段
     * @return array    配置数组
     */
    public function check_filename($filename, $id)
    {
        global $_M;
        if ($filename != '') {
            if (!preg_match("/^[a-zA-Z0-9_^\x80-\xff]+$/", $filename) || is_numeric($filename)) {
                $this->errorno = 'error_filename_cha';
                $this->error = 'error_filename_cha';
                return false;
            }
        }

        if ($filename) {
            $filenames = $this->database->get_list_by_filename($filename);
            if ($filenames) {
                $count = count($filenames);
                if ($count > 1 || ($filenames[0]['id'] != $id && $filenames[0]['id'])) {
                    $this->errorno = 'error_filename_exist';
                    $this->error = 'error_filename_exist';
                    return false;
                }
            }
        }
        return true;
    }

    public function displayimg_check($img = '')
    {
        $imgs = stringto_array($img, '*', '|');
        $str = '';
        foreach ($imgs as $val) {
            if ($val[1]) {
                $str .= "{$val[0]}*{$val[1]}*{$val[2]}|";//增加展示图片尺寸值{$val[2]}（新模板框架v2）
            }
        }
        $str = trim($str, '|');
        return $str;
    }

    /**
     * 信息列表URL
     * @param  string $p 信息数组
     * @param  string $module 模块类型
     * @return array               url
     */
    public function url($p, $module)
    {
        global $_M;
        $met_class = $this->column(2, $module);
        $classnow = $p['class3'] ?: ($p['class2'] ?: $p['class1']);
        $url = "{$_M['url']['site']}{$met_class[$classnow]['foldername']}/";
        switch ($module) {
            case 2:
                $url .= "shownews.php?lang={$_M['lang']}&id={$p['id']}";
                break;
            case 3:
                $url .= "showproduct.php?lang={$_M['lang']}&id={$p['id']}";
                break;
            case 4:
                $url .= "showdownload.php?lang={$_M['lang']}&id={$p['id']}";
                break;
            case 5:
                $url .= "showimg.php?lang={$_M['lang']}&id={$p['id']}";
                break;
        }
        return $url;
    }

    /**
     * 列表页面排序
     * @param  string $type
     * @param  string $module 模块
     * @return array  栏目数组
     */
    public function list_order($type = '', $table = '')
    {
        $ps = $table ? $table . '.' : '';
        switch ($type) {
            case '0':
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}updatetime desc,id desc";
                break;
            case '1':
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}updatetime desc,id desc";
                break;
            case '2':
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}addtime desc,id desc";
                break;
            case '3':
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}hits desc,id desc";
                break;
            case '4':
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}id desc";
                break;
            case '5':
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}id asc";
                break;
            default :
                $list_order = "{$ps}top_ok desc,{$ps}com_ok desc,{$ps}no_order desc,{$ps}updatetime desc";
                break;
        }
        if ($this->module == 6) {
            $list_order = str_replace("{$ps}com_ok desc,", '', $list_order);
        }
        return $list_order;
    }

    /**
     * 获取栏目
     * @param  string $type 1；按模块生成;2：按栏目生成
     * @param  string $module 模块
     * @return array  栏目数组
     */
    public function column($type = 1, $module = '', $id = 0)
    {
//        if (!$this->met_column && $type != 3) {
//            $this->met_column = column_sorting(1);
//        }

        switch ($type) {
            case 1:
                $newarray = column_sorting(1);
                if ($module) {
                    ksort($newarray[$module]);
                }
                return $newarray;
                break;
            case 2:
                $array = column_sorting(1);
                $newarray = array();
                foreach ($array[$module]['class1'] as $val) {
                    $newarray[$val['id']] = $val;
                }
                foreach ($array[$module]['class2'] as $val) {
                    $newarray[$val['id']] = $val;
                }
                foreach ($array[$module]['class3'] as $val) {
                    $newarray[$val['id']] = $val;
                }
                return $newarray;
                break;
            case 3:
                $array = column_sorting(2);
                $newarray = array();
                foreach ($array['class1'] as $key => $val) {
                    if ($val['module'] == $module) {
                        if ($id && $val['id'] != $id) {
                            continue;
                        }
                        $newarray['class1'][] = $val;
                    }
                }
                foreach ($array['class2'] as $key => $val) {
                    foreach ($val as $val2) {
                        if ($val2['module'] == $module) {
                            if ($val2['releclass']) {
                                $newarray['class1'][] = $val2;
                                //if (count($array['class3'][$val2['id']])) {
                                if ($array['class3'][$val2['id']]) {
                                    $newarray['class2'][$val2['id']] = $array['class3'][$val2['id']];
                                }
                            } else {
                                $newarray['class2'][$val2['bigclass']][] = $val2;
                            }
                        }
                    }
                }
                foreach ($array['class3'] as $key => $val) {
                    foreach ($val as $key1 => $val3) {
                        # code...
                        if (!$val3['releclass']) {
                            $newarray['class3'][$key][$key1] = $val3;
                        }
                    }
                }
                return $newarray;
                break;
        }
    }

    /**
     * 缩略图生成
     * @param  string $filePath 原图路径
     * @return array                缩略图路径
     */
    public function thumbimg($filePath = '', $module = '')
    {
        global $_M;
        $thumb = load::sys_class('thumb', 'new');
        $thumb->list_module($module);
        $ret = $thumb->createthumb($filePath);
        return $ret['path'];
    }

    /**
     * 大图水印
     * @param  string $filePath 原图路径
     * @return array                大图水印路径
     */
    public function waterbigimg($filePath)
    {
        global $_M;
        $watermark = load::sys_class('watermark', 'new');
        $watermark->set_system_bigimg();
        $ret = $watermark->create($filePath);
        return $ret['path'];
    }

    /**
     * 缩略图水印
     * @param  string $filePath 缩略图路径
     * @return array                缩略图水印路径
     */
    public function waterthumbimg($filePath)
    {
        global $_M;
        $watermark = load::sys_class('watermark', 'new');
        $watermark->set_system_thumb();
        $ret = $watermark->create($filePath);
        return $ret['path'];
    }

    /**
     * 处理图片
     * @param  string $list 数据数组
     * @return array  处理完的图片后的数据数组
     */
    public function form_imglist($list = array(), $module = '')
    {
        global $_M;
        $imglist = explode("|", $list['imgurl']);
        $imgsizes = explode("|", $list['imgsizes']);//增加图片尺寸变量（新模板框架v2）
        $i = 0;
        $j = 0;
        $flag = 0;
        $displayimg = '';


        foreach ($imglist as $key => $val) {
            if ($val != '' || $j == 1) $i++;
            $img = str_replace('watermark/', '', $val);
            if ($_M['config']['met_big_wate'] == 1) {
                $new_imgurl = $this->waterbigimg($img);
            } else {
                $new_imgurl = $img;
            }

            if (!$flag && $val != '') {
                if (strstr($val, '../')) {
                    $img = str_replace('watermark/', '', $val);
                    $img_name = basename($val);

                    if ($_M['config']['met_big_wate'] == 1) {
                        $imgurls = '';
                        $imgurl = str_replace($img_name, 'watermark/' . $img_name, $img);
                    } else {
                        $imgurls = '';
                        $imgurl = $img;
                    }
                    $flag = 1;
                }

                if (!$imgurl) {
                    $imgurls = '';
                    $imgurl = $val;
                }
            }

            if (!strstr($val, '../')) {//外部图片
                $new_imgurl = $val;
            }
            $list['imgsize'] = $imgsizes[0];//增加图片尺寸值（新模板框架v2）
            $list['title'] = str_replace(array('|', '*'), '-', $list['title']);//分割夫替换
            $lt = $list['title'] . '*' . $new_imgurl . '*' . $imgsizes[$key];
            if ($key === 0) {
                $lt = '';
            }
            $displayimg .= $lt . '|';
            $list['displayimg'] = trim($displayimg, '|');
        }

        //详情缩略图
        if ($_M['config']['met_big_wate'] == 1) {
            $list['content'] = $this->concentwatermark($list['content']);
            if ($list['content1']) $list['content1'] = $this->concentwatermark($list['content1']);
            if ($list['content2']) $list['content2'] = $this->concentwatermark($list['content2']);
            if ($list['content3']) $list['content3'] = $this->concentwatermark($list['content3']);
            if ($list['content4']) $list['content4'] = $this->concentwatermark($list['content4']);
        } else {
            $list['content'] = str_replace('watermark/', '', $list['content']);
            if ($list['content1']) $list['content1'] = str_replace('watermark/', '', $list['content1']);
            if ($list['content2']) $list['content2'] = str_replace('watermark/', '', $list['content2']);
            if ($list['content3']) $list['content3'] = str_replace('watermark/', '', $list['content3']);
            if ($list['content4']) $list['content4'] = str_replace('watermark/', '', $list['content4']);
        }

        $list['displayimg'] = trim($list['displayimg'], '|');
        $list['imgurls'] = $imgurls;
        $list['imgurl'] = $imgurl;
        return $list;
    }

    /**
     * 处理内容中的图片
     * @param  string $str 内容html
     * @return array  处理完的图片后的html
     */
    public function concentwatermark($str)
    {
        if (preg_match_all('/<img.*?src=\\\\"(.*?)\\\\".*?>/i', $str, $out)) {
            foreach ($out[1] as $key => $val) {
                $imgurl = explode("upload/", $val);
                if ($imgurl[1]) {
                    $list['imgurl_now'] = 'upload/' . $imgurl[1];
                    $list['imgurl_original'] = 'upload/' . str_replace('watermark/', '', $imgurl[1]);
                    if (file_exists(PATH_WEB . $list['imgurl_original'])) $imgurls[] = $list;
                }
            }

            foreach ($imgurls as $key => $val) {
                $watermarkurl = str_replace('../', '', $this->waterbigimg($val['imgurl_original']));
                $str = str_replace($val['imgurl_now'], $watermarkurl, $str);
            }
        }
        return $str;
    }

    /**
     * 栏目选择
     * @param $module
     * @param string $choice
     * @return string
     */
    public function class_option($module = '', $choice = '')
    {
        global $_M;
        $column = $this->column(3, $module);

        $checked = "0-0-0" == $choice ? 1 : '';
        $row1 = array(
            'name' => $_M['word']['allcategory'],
            'val' => "0-0-0",
            'selected' => $checked
        );

        $res = array($row1);
        foreach ($column['class1'] as $val) {
            $checked = "{$val['id']}-0-0" == $choice ? 1 : '';
            $res[] = array(
                'name' => $val['name'],
                'val' => "{$val['id']}-0-0",
                'selected' => $checked,
                'classtype' => $val['classtype'],
            );
            foreach ($column['class2'][$val['id']] as $val2) {
                $checked = "{$val2['bigclass']}-{$val2['id']}-0" == $choice ? 1 : '';
                $res[] = array(
                    'name' => ' - ' . $val2['name'],
                    'val' => "{$val2['bigclass']}-{$val2['id']}-0",
                    'selected' => $checked,
                    'classtype' => $val2['classtype'],
                );
                foreach ($column['class3'][$val2['id']] as $val3) {
                    $checked = "{$val2['bigclass']}-{$val3['bigclass']}-{$val3['id']}" == $choice ? 1 : '';
                    $res[] = array(
                        'name' => ' - - ' . $val3['name'],
                        'val' => "{$val2['bigclass']}-{$val3['bigclass']}-{$val3['id']}",
                        'selected' => $checked,
                        'classtype' => $val3['classtype'],
                    );
                }
            }
        }
        return $res;
    }

    /**
     * 栏目权限列表
     */
    public function getAllowClass()
    {
        $column_list = load::mod_class('column/column_op', 'new')->lv_class();
        $id_list = array();
        $id_list['class1'] = arrayColumn($column_list['class1'], 'id');
        $id_list['class2'] = arrayColumn($column_list['class2'], 'id');
        $id_list['class2'][] = 0;
        $id_list['class3'] = arrayColumn($column_list['class3'], 'id');
        $id_list['class3'][] = 0;
        return $this->allow_class = $id_list;
    }

    /**
     * 获取列表数据
     * @param  array $where 条件
     * @param  array $order 排序
     * @return bool  json数组
     */
    public function pageList($where = '', $order = '')
    {
        global $_M;
        if (in_array($this->module, array(2, 3, 4, 5))) {
            $where = "lang='{$_M['lang']}' AND (recycle = '0' or recycle = '-1') {$where}";
        } else {
            $where = "lang='{$_M['lang']}' {$where}";
        }
        $data = $this->database->table_json_list($where, $order);
        return $data;
    }

    /**
     * 返回json数据
     * @param  array $data 条件
     */
    public function json_return($data)
    {
        $this->database->tabledata->rdata($data);
    }

    /**
     * 处理内容数据
     * @param array $list
     * @return array
     */
    public function listAnalysis($list = array())
    {
        global $_M;
        $list['class1'] = $list['class1'] != '' ? $list['class1'] : 0;
        $list['class2'] = $list['class2'] != '' ? $list['class2'] : 0;
        $list['class3'] = $list['class3'] != '' ? $list['class3'] : 0;
        $list['class_now'] = $list['class3'] ?: ($list['class2'] ?: $list['class1']);
        $list['addtype'] = strtotime($list['addtime']) > time() ? 2 : 1;
        if ($_M['config']['met_webhtm']) $list['addtype'] = 1;
        if(!$list['updatetime']) $list['updatetime'] = date("Y-m-d H:i:s");
        $list['updatetype'] = 1;
        return $list;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>