<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');
load::sys_class('admin');

class index extends admin {

    public function __construct()
    {
        global $_M;
        parent::__construct();
    }

    //日志列表
    public function dolist(){
        global $_M;
        $table = load::sys_class('tabledata', 'new');
        $where = 'id>0';
        $list = $table->getdata($_M['table']['admin_logs'], '*',$where, "time DESC");
        foreach ($list as $key => $value){
            $list[$key]['name'] = $_M['word'][$value['name']] ?: $value['name'];
            $list[$key]['module'] = $_M['word'][$value['module']]?: $value['module'];
            $list[$key]['result'] = $_M['word'][$value['result']]?: $value['result'];
            $list[$key]['time'] = date("Y-m-d H:i:s",$value['time']);
        }

        $table->rdata($list);
    }

    public function dodel(){
        global $_M;
        $id = isset($_M['form']['id']) ? $_M['form']['id'] : '';
        if (!$id){
            $this->error($_M['word']['js10']);
        }
        $str = '';
        foreach ($id as $val) {
            if (is_numeric($val)) {
                $str .= "$val,";
            };
        }
        $str = trim($str, ',');
        // $before = time()-180*86400;
        $before = time();
        $del_resutl = DB::query("DELETE FROM {$_M['table']['admin_logs']} WHERE id IN ({$str}) AND time < {$before}");
        if (!$del_resutl){
            $this->error($_M['word']['opfailed']);
        }

        $this->success('',$_M['word']['jsok']);
    }

    public function doLogClean()
    {
        global $_M;
        $before = time();
        $sql = "DELETE FROM {$_M['table']['admin_logs']} WHERE time < {$before}";
        DB::query($sql);
        $this->success('',$_M['word']['jsok']);
    }

}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.