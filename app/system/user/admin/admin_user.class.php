<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('admin');

/**
 * 会员管理
 * Class admin_user
 */
class admin_user extends admin
{
    public $module;
    public $userclass;
    public $group;

    public function __construct()
    {
        parent::__construct();
        $this->module = 10;
        $this->user_database = load::mod_class('user/user_database', 'new');
        $this->user_handle = load::mod_class('user/user_handle', 'new');
        $this->user_group_database = load::mod_class('user/user_group_database', 'new');
    }

    /**
     * 获取会员信息
     */
    public function doGetUserInfo()
    {
        global $_M;
        $id = $_M['form']['id'];

        $user = $this->user_handle->getUserInfo($id);
        $this->success($user);
    }

    /**
     * 获取会员列表
     */
    public function doGetUserList()
    {
        global $_M;
        $groupid = $_M['form']['groupid'] ? : '';
        $keyword = $_M['form']['keyword'] ?  : '';
        $idvalid = $_M['form']['idvalid'] ? : '';


        $search = '';
        if (!empty($groupid)) {
            $search .= " AND groupid = '{$groupid}'";
        }
        if (!empty($idvalid)) {  //是否实名认证
            $search .= $idvalid == 1 ? " AND idvalid = '1'" : " AND idvalid != '1'";
        }
        if (!empty($keyword)) {
            $search .= " AND (username like '%{$keyword}%' || email LIKE '%{$keyword}%' || tel LIKE '%{$keyword}%')";
        }

        $table = load::sys_class('tabledata', 'new');
        $order = "login_time DESC,register_time DESC";
        $where = "lang='{$_M['lang']}' {$search}";
        $userlist = $table->getdata($_M['table']['user'], '*', $where, $order);


        //获取用户组列表
        $user_group = array();
        $group_list = $this->user_group_database->groupList();
        foreach ($group_list as $val) {
            $user_group[$val['id']] = $val['name'];
        }

        $redata = array();
        foreach ($userlist as $key => $user) {
            $user['source_name'] = self::sourceName($user);
            $user['valid_status'] = $user['valid'] ? $_M['word']['memberChecked'] : $_M['word']['memberUnChecked'];
            $user['register_time'] = timeFormat($user['register_time']);
            $user['login_time'] = timeFormat($user['login_time']);
            $user['group_name'] = $user_group[$user['groupid']];
            $user['login_time'] = $user['login_time'] ?: '--';
            $redata[$key] = $user;
        }
        $table->rdata($redata);
    }

    /**
     * @param $user
     * @return mixed|string
     */
    private function sourceName($user)
    {
        global $_M;
        switch ($user['source']) {
            case 'weixin':
                $source_name = $_M['word']['weixinlogin'];
                break;
            case 'weibo':
                $source_name = $_M['word']['sinalogin'];
                break;
            case 'qq':
                $source_name = $_M['word']['qqlogin'];
                break;
            case 'google':
                $source_name = "Google+";
                break;
            case 'facebook':
                $source_name = "Facebook";
                break;
            default:
                $source_name = $_M['word']['registration'];
                break;
        }
        return $source_name;
    }

    /**
     * 添加会员
     */
    public function doAddUser()
    {
        global $_M;
        $username = $_M['form']['username'] ?: '';
        $password = $_M['form']['password'] ?: '';
        $valid = $_M['form']['valid'] ?: '';
        $groupid = $_M['form']['groupid'] ?: '';

        if (empty($username) || empty($password)) {
            $this->error($_M['word']['dataerror']);
        }

        $res = $this->user_handle->createUser($username, $password, '', '', $valid, $groupid);
        if ($res) {
            logs::addAdminLog('memberist', 'memberAdd', 'jsok', 'doAddUser');
            $this->success('', $_M['word']['jsok']);
        }
        logs::addAdminLog('memberist', 'memberAdd', 'jsx10', 'doAddUser');
        $this->error($_M['word']['jsx10']);
    }

    /**
     * 保存会员信息
     */
    public function doSaveUser()
    {
        global $_M;
        $form = $_M['form'];
        $id = $form['id'];
        $user = $this->user_database->getOneById($id);
        if(!$user) $this->error($_M['dataerror']);

        //用户唯一性
        $para_list = array('email'=> $_M['word']['emailhave'], 'tel'=>$_M['word']['telhave']);
        foreach ($para_list as $name=>$msg) {
            $res = $this->user_database->hasOthers($user, $form[$name]);
            if ($res) $this->error($msg);
        }

        //setpassword
        if (!empty($form['password'])) {
            $res = $this->user_handle->setUserPassword($user, $form['password']);
            if (!$res) $this->error($_M['word']['user_tips4_v6']);
        }

        $data = array();
        $data['id'] = $form['id'];
        $data['email'] = $form['email'];
        $data['tel'] = $form['tel'];
        $data['groupid'] = $form['groupid'];
        $data['valid'] = $form['valid'];
        $res = $this->user_database->update_by_id($data);

        //user attr
        $paraclass = load::sys_class('para', 'new');
        $para_list = $paraclass->get_para_list(10);
        $info = array();
        foreach ($para_list as $val) {
            if (isset($_M['form']['info_' . $val['id']])) {
                $info[$val['id']] = $_M['form']['info_' . $val['id']];
            }
        }
        $paraclass->update_para($id, $info, 10);

        $this->success('', $_M['word']['jsok']);
    }

    /**
     * 删除会员
     */
    public function doDelUser()
    {
        global $_M;
        $id_list = $_M['form']['id'];
        foreach ($id_list as $id) {
            if(!is_number($id)) continue;
            $this->user_database->delUserByid($id);
        }
        //写日志
        logs::addAdminLog('memberist', 'delete', 'jsx10', 'doDelUser');
        $this->success('', $_M['word']['jsok']);
    }

    /**
     * Ajax检查用户名是否可用
     */
    public function doCheckUsername()
    {
        global $_M;
        $username = $_M['form']['username'] ?: '';
        if (empty($username)) {
            $res['message'] = $_M['word']['loginid'];
            $res['valid'] = false;
            $this->ajaxReturn($res);
        }

        if (!$this->user_handle->checkUsernameStr($username)) {
            $res['message'] = $_M['word']['user_tips2_v6'];
            $res['valid'] = false;
            $this->ajaxReturn($res);
        }

        if (
            $this->user_database->getOneByUsername($username) ||
            $this->user_database->hasAdminMember($username)
        ) {
            $res['message'] = $_M['word']['user_tips3_v6'];
            $res['valid'] = false;
            $this->ajaxReturn($res);
        }

        $res['message'] = $_M['word']['user_tips1_v6'];
        $res['valid'] = true;
        $this->ajaxReturn($res);
    }

    //导出会员列表
    public function doExportUser()
    {
        global $_M;
        $groupid = isset($_M['form']['groupid']) ? : '';
        $keyword = isset($_M['form']['keyword']) ?  : '';
        $idvalid = isset($_M['form']['idvalid']) ? : '';

        $search = '';
        if (!empty($groupid)) {
            $search .= " AND groupid = '{$groupid}'";
        }
        if (!empty($idvalid)) {  //是否实名认证
            $search .= $idvalid == 1 ? " AND idvalid = '1'" : " AND idvalid != '1'";
        }
        if (!empty($keyword)) {
            $search .= " AND (username like '%{$keyword}%' || email LIKE '%{$keyword}%' || tel LIKE '%{$keyword}%')";
        }
        /*查询表用户*/
        $query = "SELECT * FROM {$_M['table']['user']} WHERE lang='{$_M['lang']}' {$search} ORDER BY login_time DESC,register_time DESC";
        $user_data = DB::get_all($query);


        //表头
        $head = array(
            $_M['word']['loginusename'],
            $_M['word']['membergroup'],
            $_M['word']['membertips1'],
            $_M['word']['lastactive'],
            $_M['word']['adminLoginNum'],
            $_M['word']['memberCheck'],
            $_M['word']['source'],
            $_M['word']['bindingmail'],
            $_M['word']['bindingmobile']
        );

        $parameter_database = load::mod_class('parameter/parameter_database', 'new');
        $parameter_list = load::mod_class('parameter/parameter_op', 'new')->get_para_list($this->module);
        $para_list = array();
        foreach ($parameter_list as $key => $para) {
            $head[] = $para['name'];   //表头
            if (in_array($para['type'], array(2, 4, 6))) {
                $para['options'] = $parameter_database->get_para_values($para['module'], $para['id']);
                $para_list[$para['id']] = $para;
            }
            $para_list[$para['id']] = $para;
        }

        //获取用户组列表
        $user_group = array();
        $group_list = $this->user_group_database->groupList();
        foreach ($group_list as $val) {
            $user_group[$val['id']] = $val['name'];
        }

        $rarray = array();
        foreach ($user_data as $key => $user) {
            $user['source_name'] = self::sourceName($user);
            $user['register_date'] = date('Y-m-d H:i:s', $user['register_time']);
            $user['login_date'] = $user['login_time']?date('Y-m-d H:i:s', $user['login_time']):'--';
            $group_name = $user_group[$user['geoup_id']];
            
            $list = array();
            $list[] = $user['username'];
            $list[] = $group_name;
            $list[] = $user['register_date'];
            $list[] = $user['login_date'];
            $list[] = $user['login_count'];
            $list[] = $user['valid'] ? $_M['word']['memberChecked'] : $_M['word']['memberUnChecked'];
            $list[] = $user['source_name'];
            $list[] = $user['email'];
            $list[] = $user['tel'];

            $query = "SELECT * FROM {$_M['table']['user_list']} WHERE listid='{$user['id']}'";
            $user_para = DB::get_all($query);

            $para_info = array();
            foreach ($user_para as $para_row) {
                $parameter = $para_list[$para_row['paraid']];
                if (in_array($parameter['type'], array(2, 4, 6))) {
                    $select = explode('#@met@#', $para_row['info']);
                    $option_str = '';
                    foreach ($parameter['options'] as $option) {
                        if (in_array($option['id'], $select)) {
                            $option_str .= $option['value'] . ',';
                        }
                    }
                    $para_info[$para_row['paraid']] = trim($option_str, ',');
                }else{
                    $para_info[$para_row['paraid']] = $para_row['info'];
                }
            }

            foreach ($para_list as $para) {
                $list[] = $para_info[$para['id']];
            }

            //导出表单数据
            $rarray[] = $list;
        }

        $filename = "USER_" . date('Y-m-d', time()) . "_ACCLOG";
        $csv = load::sys_class('csv', 'new');
        $csv->get_csv($filename, $rarray, $head);
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>