<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::mod_class('base/admin/base_admin');

class product_admin extends base_admin
{
    public $moduleclass;
    public $shop_open;
    public $shop;
    public $module;
    public $specification_admin;

    /**
     * product_admin constructor.
     */
    function __construct()
    {
        global $_M;
        parent::__construct();
        $this->module = 3;
        $this->database = load::mod_class('product/product_database', 'new');
        self::shopIni();
    }

    protected function shopIni()
    {
        global $_M;
        $this->shop_open = false;
        $shop_applist = DB::get_one("SELECT * FROM {$_M['table']['applist']} WHERE `no`='10043'");
        if (!$shop_applist) return false;

        $shop_appfile = file_exists(PATH_ALL_APP . 'shop');
        if (!$shop_appfile) return false;

        $_open = isset($_M['config']['shopv2_goods_set']) ? $_M['config']['shopv2_goods_set'] : $_M['config']['shopv2_open'];
        if (!$_open) return false;

        $this->specification_admin = load::app_class('shop/admin/specification_admin', 'new');
        $this->shop = load::plugin('doproduct_plugin_class', '99');
        if($this->shop) $this->shop_open = true;
        return true;
    }

    public function dopara()
    {
        return parent::paramentList();
    }

    public function docolumnjson()
    {
        return parent::docolumnjson();
    }

    /**
     * 商城模式产品管理
     */
    function doindex()
    {
        global $_M;
        $column = $this->column(3, $this->module);
        $list['class1'] = $_M['form']['class1'] ? $_M['form']['class1'] : '';
        $list['class2'] = $_M['form']['class2'] ? $_M['form']['class2'] : '';
        $list['class3'] = $_M['form']['class3'] ? $_M['form']['class3'] : '';

        if ($this->shop_open) {
            $tmpname = $this->shop->get_tmpname('product_shop_index');
            require $tmpname;
        } else {
            //$error = $_M['word']['app_shopv2_open_shop'] ? $_M['word']['app_shopv2_open_shop'] : '请前往商城设置开启商城模块';
            $str = "请前往<a href='".$_M['url']['adminurl']."n=shop&c=set&a=doindex' target='_top'> [设置] </a>开启 [商品设置] 选项";
            exit($str);
        }
    }

    /**
     * 获取栏目信息
     */
    public function doGetColumnSeting()
    {
        global $_M;
        $class1 = is_numeric($_M['form']['class1']) ? $_M['form']['class1'] : '';
        $class2 = is_numeric($_M['form']['class2']) ? $_M['form']['class2'] : '';
        $class3 = is_numeric($_M['form']['class3']) ? $_M['form']['class3'] : '';

        $redata = self::_GetColumnSeting($class1, $class2, $class3);
        $this->ajaxReturn($redata);
    }

    /**
     * @param int $class1
     * @param int $class2
     * @param int $class3
     * @return mixed
     */
    public function _GetColumnSeting($class1 = 0, $class2 = 0, $class3 = 0)
    {
        global $_M;
        $classnow = $class3 ? $class3 : ($class2 ? $class2 : $class1);

        $class = load::mod_class('column/column_label', 'new')->get_column_id($classnow);
        $c123 = load::mod_class('column/column_label', 'new')->get_class123_no_reclass($classnow);

        $c_lev = $class['classtype'];

        //三级栏目
        if ($c_lev == 3) {
            //tab_num
            $tab_num = $c123['class3']['tab_num'] ? $c123['class3']['tab_num'] : ($c123['class2']['tab_num'] ? $c123['class2']['tab_num'] : ($c123['class1']['tab_num'] ? $c123['class1']['tab_num'] : 3));

            //tab_name
            if ($c123['class3']['tab_name'] && trim($c123['class3']['tab_name'], "|")) {
                $tab_name = explode("|", $c123['class3']['tab_name']);
            } else {
                if ($c123['class2']['tab_name'] && trim($c123['class2']['tab_name'], "|")) {
                    $tab_name = explode("|", $c123['class2']['tab_name']);
                } else {
                    if ($c123['class1']['tab_name'] && trim($c123['class1']['tab_name'], "|")) {
                        $tab_name = explode("|", $c123['class1']['tab_name']);
                    } else {
                        $tab_name = array(
                            $_M['config']['met_productTabname'],
                            $_M['config']['met_productTabname_1'],
                            $_M['config']['met_productTabname_2'],
                            $_M['config']['met_productTabname_3'],
                            $_M['config']['met_productTabname_4']
                        );
                    }
                }
            }
        }

        //二级栏目将
        if ($c_lev == 2) {
            //tab_num
            $tab_num = $c123['class2']['tab_num'] ? $c123['class2']['tab_num'] : ($c123['class1']['tab_num'] ? $c123['class1']['tab_num'] : 3);

            //tab_name
            if ($c123['class2']['tab_name'] && trim($c123['class2']['tab_name'], "|")) {
                $tab_name = explode("|", $c123['class2']['tab_name']);
            } else {
                if ($c123['class1']['tab_name'] && trim($c123['class1']['tab_name'], "|")) {
                    $tab_name = explode("|", $c123['class1']['tab_name']);
                } else {
                    $tab_name = array(
                        $_M['config']['met_productTabname'],
                        $_M['config']['met_productTabname_1'],
                        $_M['config']['met_productTabname_2'],
                        $_M['config']['met_productTabname_3'],
                        $_M['config']['met_productTabname_4']
                    );
                }
            }
        }

        //一级栏目
        if ($c_lev == 1) {
            //tab_num
            $tab_num = $c123['class1']['tab_num'] ? $c123['class1']['tab_num'] : 3;

            //tab_name
            if ($c123['class1']['tab_name'] && trim($c123['class1']['tab_name'], "|")) {
                $tab_name = explode("|", $c123['class1']['tab_name']);
            } else {
                $tab_name = array(
                    $_M['config']['met_productTabname'],
                    $_M['config']['met_productTabname_1'],
                    $_M['config']['met_productTabname_2'],
                    $_M['config']['met_productTabname_3'],
                    $_M['config']['met_productTabname_4']
                );
            }
        }

        $redata['tab_name'] = "{$tab_name[0]}|{$tab_name[1]}|{$tab_name[2]}|{$tab_name[3]}|{$tab_name[4]}";
        $redata['tab_num'] = $tab_num;

        return $redata;
    }

    /**
     * 产品增加
     * @return array
     */
    public function doadd()
    {
        global $_M;
        $redata = array();
        $list = $this->add();
        $list['class1'] = $_M['form']['class1'] ? $_M['form']['class1'] : 0;
        $list['class2'] = $_M['form']['class2'] ? $_M['form']['class2'] : 0;
        $list['class3'] = $_M['form']['class3'] ? $_M['form']['class3'] : 0;
        $list['lnvoice'] = 0;
        $list['auto_sent'] = 0;

        if ($this->shop_open) {
            $list = $this->shop->default_value($list);
            $list_s['paraku'] = $this->specification_admin->dogetspeclist();
            $list_s['speclist'] = jsonencode($list_s['paraku']);
            $list = array_merge($list, $list_s);
        }
        $column_list = $this->_columnjson();
        $access_option = $this->access_option($list['access']);

        $redata['list'] = $list;
        $redata['access_option'] = $access_option;
        $redata = array_merge($redata, $column_list);

        if (is_mobile()) {
            $this->success($redata);
        } else {
            if ($_M['form']['app_type'] == 'shop') {
                require $this->shop->get_tmpname('product_shop');
            } else {
                return $redata;
            }
        }
    }

    function doaddsave()
    {
        global $_M;
        $redata = array();
        $_M['form']['addtime'] = $_M['form']['addtype'] == 2 ? $_M['form']['addtime'] : date("Y-m-d H:i:s");
        $pid = $this->insert_list($_M['form']);
        if ($pid) {
            //商城产品属性
            if ($this->shop_open) {
                $this->shop->save_product($pid, $_M['form']);
            }

            //plugin
            $plugin_para = array(
                'lang' => $_M['lang'],
                'module' => $this->module,
                'aid' => $pid,
            );
            load::plugin('doaddsave', 0, $plugin_para);

            $url = "{$_M['url']['own_form']}a=doindex{$_M['form']['turnurl']}";
            $html_res = $this->html_op->htmlGenerate($_M['form']['class1'], $pid, $url);

            //写日志
            logs::addAdminLog('administration', 'addinfo', 'jsok', 'doaddsave');
            if ($_M['form']['app_type']) {
                okinfo($_M['form']['turnurl'], $_M['word']['jsok']);
            } else {
                $redata['status'] = 1;
                $redata['msg'] = $_M['word']['jsok'];
                $redata['html_res'] = $html_res;
                $redata['back_url'] = $url;
                $this->ajaxReturn($redata);
            }
        } else {
            //写日志
            logs::addAdminLog('administration', 'addinfo', 'dataerror', 'doaddsave');
            if ($_M['form']['app_type']) {
                okinfo('-1', $_M['word']['dataerror']);
            } else {
                $redata['status'] = 0;
                $redata['msg'] = $_M['word']['dataerror'];
                $redata['error'] = $this->error;
                $this->ajaxReturn($redata);
            }
        }
    }

    /**
     * @param array $list
     * @return bool|mixed|number
     */
    public function insert_list($list = array())
    {
        global $_M;
        $list['classother'] = $list['classother'] ? $list['classother'] : '';//mod2
        return parent::insert_list($list);
    }

    /**
     * 产品编辑
     */
    public function doeditor()
    {
        global $_M;
        $id = $_M['form']['id'] ? intval($_M['form']['id']) : null;
        $hid = $_M['form']['hid'] ? intval($_M['form']['hid']) : null;

        $data = $this->database->get_list_one_by_id($id);
        if (!$data) return is_mobile() ? $this->error() : array();

        if ($hid) {
            $data_his = load::mod_class('history/history_op', 'new')->getHistoryByid($hid);
            if (!$data_his) return is_mobile() ? $this->error() : array();
            unset($data_his['aid']);
            $data_his['id'] = $data['id'];
            $data = $data_his;
        }

        $list = $this->listAnalysis($data);

        $list['imgurl_all'] = $list['imgurl'];
        $displayimg = explode("|", $list['displayimg']);
        foreach ($displayimg as $val) {
            if ($val) {
                $img = explode("*", $val);
                $list['imgurl_all'] .= '|' . $img[1];
            }
        }
        $list['imgurl_all'] = trim($list['imgurl_all'], '|');
        if ($list['classother']) {
            $list['classother_str'] = str_replace("-|-", '|', $list['classother']);
            $list['classother_str'] = str_replace('|-', '|', $list['classother_str']);
            $list['classother_str'] = str_replace('-|', '', $list['classother_str']);
        }

        //商城商品数据
        if ($this->shop_open) {
            $list_s = $this->shop->default_value($list);
            $list_s['paraku'] = $this->specification_admin->dogetspeclist();
            $list_s['speclist'] = jsonencode($list_s['paraku']);
            $list = array_merge($list, $list_s);
        }
        $column_list = $this->_columnjson();

        $column_own = load::sys_class('label', 'new')->get('column')->get_column_id($list['class_now']);
        $access_option = $this->access_option($column_own['access']);

        $redata = array();
        $redata['list'] = $list;
        $redata['access_option'] = $access_option;
        $redata = array_merge($redata, $column_list);

        if (is_mobile()) {
            $this->success($redata);
        } else {
            if ($_M['form']['app_type'] == 'shop') {
                $column_seting = self::_GetColumnSeting($list['class1'], $list['class2'], $list['class3']);
                $tab_name = explode("|", $column_seting['tab_name']);
                $_M['config']['met_productTabname'] = $tab_name[0];
                $_M['config']['met_productTabname_1'] = $tab_name[1];
                $_M['config']['met_productTabname_2'] = $tab_name[2];
                $_M['config']['met_productTabname_3'] = $tab_name[3];
                $_M['config']['met_productTabname_4'] = $tab_name[4];
                $_M['config']['met_productTabok'] = $column_seting['tab_num'];
                require $this->shop->get_tmpname('product_shop');
            } else {
                return $redata;
            }
        }
    }

    /**
     * 保存编辑
     */
    public function doeditorsave()
    {
        global $_M;
        $list = $_M['form'];
        $id = $list['id'];
        $redata = array();

        if (!is_numeric($id)) {
            //写日志
            logs::addAdminLog('administration', 'physicalupdate', 'dataerror', 'doeditorsave');
            $this->error($_M['word']['dataerror'], "No id");
        }

        if ($this->update_list($list, $id)) {
            if ($this->shop_open && $_M['form']['app_type'] == 'shop') {
                $this->shop->save_product($id, $list);
            }

            //plugin
            $plugin_para = array(
                'lang' => $_M['lang'],
                'module' => $this->module,
                'aid' => $id,
            );
            load::plugin('doeditorsave', 0, $plugin_para);

            //if($_M['config']['met_webhtm'] == 2 && $_M['config']['met_htmlurl'] == 0){
            $url = "{$_M['url']['own_form']}a=doindex&class1={$_M['form']['class1']}&class2={$_M['form']['class2']}&class3={$_M['form']['class3']}";
            $html_res = $this->html_op->htmlGenerate($_M['form']['class1'], $_M['form']['id'], $url);
            //写日志
            logs::addAdminLog('administration', 'editor', 'jsok', 'doaddsave');
            if ($_M['form']['app_type']) {
                okinfo($_M['form']['turnurl'], $_M['word']['jsok']);
            } else {
                $redata['status'] = 1;
                $redata['msg'] = $_M['word']['jsok'];
                $redata['html_res'] = $html_res;
                $redata['back_url'] = $url;
                $this->ajaxReturn($redata);
            }
        } else {
            //写日志
            logs::addAdminLog('administration', 'editor', 'dataerror', 'doeditorsave');

            if ($_M['form']['app_type']) {
                okinfo('-1', $_M['word']['dataerror']);
            } else {
                $this->error($_M['word']['dataerror']);
            }
        }
    }

    /**
     * 更新产品
     * @param array $list
     * @param string $id
     * @return bool
     */
    public function update_list($list = array(), $id = '')
    {
        $list['displayimg'] = $this->displayimg_check($list['displayimg']);
        return parent::update_list($list, $id);
    }

    /**
     * 内容列表
     */
    public function dojson_list()
    {
        global $_M;
        if ($this->shop_open && $_M['form']['app_type'] == 'shop') {
            $this->shop->plgin_json_list();
            return;
        }

        $class1 = is_numeric($_M['form']['class1']) ? $_M['form']['class1'] : '';
        $class2 = is_numeric($_M['form']['class2']) ? $_M['form']['class2'] : '';
        $class3 = is_numeric($_M['form']['class3']) ? $_M['form']['class3'] : '';
        $keyword = $_M['form']['keyword'];
        $search_type = $_M['form']['search_type'];
        foreach ($_M['form']['order'] as $key => $value) {
            $order[$value['name']] = $value['value'];
        }

        $list = self::getJsonList($class1, $class2, $class3, $keyword, $search_type, $order['hits'], $order['updatetime']);

        return $this->json_return($list);
    }

    public function json_return($data)
    {
        return $this->tabledata->rdata($data);
    }

    /**
     * @param string $class1
     * @param string $class2
     * @param string $class3
     * @param string $keyword
     * @param string $search_type
     * @param string $orderby_hits
     * @param string $orderby_updatetime
     * @return array
     */
    public function getJsonList($class1 = '', $class2 = '', $class3 = '', $keyword = '', $search_type = '', $orderby_hits = '', $orderby_updatetime = '')
    {
        global $_M;
        //栏目访问权限
        if (($class1 && !in_array($class1, $this->allow_class['class1'])) || ($class2 && !in_array($class2, $this->allow_class['class2'])) || ($class3 && !in_array($class3, $this->allow_class['class3']))) {
            return false;
        }
        $allow_class1 = implode(',', $this->allow_class['class1']);
        $allow_class2 = implode(',', $this->allow_class['class2']);
        $allow_class3 = implode(',', $this->allow_class['class3']);

        $classnow = $class3 ? $class3 : ($class2 ? $class2 : $class1);
        $_where = '';
        $ps = '';

        $_class = '(';
        $_class .= $class1 ? " class1 = '{$class1}'" : " class1 IN ({$allow_class1}) ";
        $_class .= $class2 ? " AND class2 = '{$class2}'" : " AND  class2 IN ({$allow_class2}) ";
        $_class .= $class3 ? " AND class3 = '{$class3}'" : " AND  class3 IN ({$allow_class3}) ";
        $_class .= ")";

        if ($class3) {
            $_classother = "|-{$class1}-{$class2}-{$class3}-|";
        } elseif ($class2) {
            #$_classother = "|-{$class1}-{$class2}-0-|";
            $_classother = "|-{$class1}-{$class2}-";
        } elseif ($class1) {
            #$$_classother = "|-{$class1}-0-0-|";
            $_classother = "|-{$class1}-";
        }

        //栏目筛选
        if ($_classother) {
            $_where .= " AND ($_class OR (classother like '%{$_classother}%') ) ";
        } else {
            $_where .= " AND $_class ";
        }

        //筛选
        switch ($search_type) {
            case 0:
                break;
            case 1:
                $_where .= " AND {$ps}displaytype = '0'";
                break;
            case 2:
                $_where .= " AND {$ps}com_ok = '1'";
                break;
            case 3:
                $_where .= " AND {$ps}top_ok = '1'";
                break;
            case 4:
                $_where .= " AND {$ps}displaytype = '-1'";
                break;
        }

        //搜索
        $_where .= $keyword ? " AND title like '%{$keyword}%'" : '';

        //排序规则
        $met_class = $this->column(2, $this->module);
        $order = $this->list_order($met_class[$classnow]['list_order']);
        if ($orderby_hits) $order = "{$ps}hits {$orderby_hits}";
        if ($orderby_updatetime) $order = "{$ps}updatetime {$orderby_updatetime}";
        $data = $this->pagelist($_where, $order);

        foreach ($data as $key => $val) {
            $row = array();
            $row['id'] = $val['id'];
            $row['no_order'] = $val['no_order'];
            $row['title'] = $val['title'];
            $row['url'] = $this->url($val, $this->module);
            $row['imgurl'] = $val['imgurl'];
            $row['com_ok'] = $val['com_ok'];
            $row['top_ok'] = $val['top_ok'];
            $row['displaytype'] = $val['displaytype'];
            $row['addtype'] = strtotime($val['addtime']) > time() ? 1 : 0;
            $row['price_html'] = $val['price_html'];
            $row['hits'] = $val['hits'];
            $row['updatetime'] = $val['updatetime'];
            #$row['state'] 	    = $state;
            $row['editor_url'] = "{$_M['url']['own_form']}a=doeditor&id={$val['id']}&class1={$class1}&class2={$class2}&class3={$class3}";
            $row['del_url'] = "{$_M['url']['own_form']}a=dolistsave&submit_type=del&allid={$val['id']}&class1={$class1}&class2={$class2}&class3={$class3}";
            $rarray[] = $row;
        }
        return $rarray;
    }

    /**
     * @param array $where
     * @param array $order
     * @return mixed
     */
    public function pageList($where = '', $order = '')
    {
        global $_M;
        $this->tabledata = load::sys_class('tabledata', 'new');

        $p = $_M['table']['product'];
        $s = $_M['table']['shopv2_product'];

        if ($this->shop_open) {//开启在线订购时
            $table = $p . ' LEFT JOIN ' . $s . " ON ({$p}.id = {$s}.pid)";
            $where = "{$p}.lang='{$_M['lang']}' AND ({$p}.recycle = '0' OR {$p}.recycle = '-1') {$where}";
        } else {
            $table = $p;
            $where = "lang='{$_M['lang']}' AND (recycle = '0' OR recycle = '-1') {$where}";
        }

        if ($this->admin_member['admin_issueok']) {
            $where = "({$where})  AND (issue = '{$this->admin_member['admin_id']}')";
        }

        $data = $this->tabledata->getdata($table, '*', $where, $order);
        return $data;
    }

    /**
     * 保存列表
     */
    public function dolistsave()
    {
        global $_M;
        $redata = array();
        $list = explode(",", $_M['form']['allid']);

        foreach ($list as $id) {
            if ($id) {
                switch ($_M['form']['submit_type']) {
                    case 'save':
                        $log_name = 'submit';
                        $list['no_order'] = $_M['form']['no_order-' . $id];
                        $res = $this->list_no_order($id, $list['no_order']);
                        break;
                    case 'del':
                        $log_name = 'jslang1';
                        $res = $this->del_list($id, $_M['form']['recycle']);
                        if ($_M['form']['recycle'] == 0) {
                            if ($this->shop_open) {
                                $this->shop->del_product($id);
                            }
                            $log_name = 'jslang0';
                        }
                        $this->html_op->htmlDel($id, $this->module);
                        break;
                    case 'recycle':
                        $log_name = 'jslang0';
                        $res = $this->del_list($id, 1);
                        $this->html_op->htmlDel($id, $this->module);
                        break;
                    case 'comok':
                        $log_name = 'recom';
                        $res = $this->list_com($id, 1);
                        break;
                    case 'comno':
                        $log_name = 'unrecom';
                        $res = $this->list_com($id, 0);
                        break;
                    case 'topok':
                        $log_name = 'top';
                        $res = $this->list_top($id, 1);
                        break;
                    case 'topno':
                        $log_name = 'untop';
                        $res = $this->list_top($id, 0);
                        break;
                    case 'displayok':
                        $log_name = 'frontshow';
                        $res = $this->list_display($id, 1);
                        break;
                    case 'displayno':
                        $log_name = 'fronthidden';
                        $res = $this->list_display($id, 0);
                        $this->html_op->htmlDel($id, $this->module);
                        break;
                    case 'move':
                        if (!isset($_M['form']['columnid'])) {
                            break;
                        }
                        $log_name = 'columnmove1';
                        $class = explode("-", $_M['form']['columnid']);
                        $class1 = $class[0];
                        $class2 = $class[1];
                        $class3 = $class[2];
                        $res = $this->list_move($id, $class1, $class2, $class3);
                        break;
                    case 'copy':
                        if (!isset($_M['form']['columnid'])) {
                            break;
                        }
                        $log_name = 'copyotherlang2';
                        $class = explode("-", $_M['form']['columnid']);
                        $class1 = $class[0];
                        $class2 = $class[1];
                        $class3 = $class[2];
                        $newid = $this->list_copy($id, $class1, $class2, $class3);
                        break;
                    case 'copy_tolang':
                        if (!isset($_M['form']['columnid'])) {
                            break;
                        }
                        $log_name = 'copy_tolang';
                        $new_class = explode("-", $_M['form']['columnid']);
                        $tolang = $_M['form']['tolang'];
                        $module = $_M['form']['module'];
                        $res = $this->copy_tolang($id, $module, $tolang, $new_class);
                        break;
                }
            }
        }

        if (!$this->error) {
            $url = "{$_M['url']['own_form']}a=doindex&class1={$_M['form']['class1']}&class2={$_M['form']['class2']}&class3={$_M['form']['class3']}";
            $html_res = $this->html_op->htmlGenerate($_M['form']['class1'], $_M['form']['allid'], $url);
            $redata['status'] = 1;
            $redata['msg'] = $_M['word']['jsok'];
            $redata['html_res'] = $html_res;
            $redata['back_url'] = $url;
            //写日志
            logs::addAdminLog('administration', $log_name, 'jsok', 'dolistsave');
        } else {
            $redata['status'] = 0;
            $redata['msg'] = $this->error[0];
            $redata['error'] = $this->error;
            //写日志
            logs::addAdminLog('administration', $log_name, $this->error[0], 'dolistsave');

        }

        if ($_M['form']['app_type']) {
            okinfo('-1', $redata['msg']);
        } else {
            $this->ajaxReturn($redata);
        }
    }

    /**
     * 多语言内容复制
     */
    public function list_copy($id = '', $class1 = '', $class2 = '', $class3 = '')
    {
        global $_M;
        $copyid = parent::list_copy($id, $class1, $class2, $class3);
        if ($copyid) {
            //开启在线订购时
            if ($this->shop_open) {
                $this->shop->copy_product($id, $copyid);
            }
            return $copyid;
        }
        $this->error[] = 'error no id';
        return false;
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
