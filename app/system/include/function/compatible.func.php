<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

/**
 * @param string $path 要包含的模板文件地址，已“模板文件类型/模板文件名称”方式输入
 * @模板文件类型：own:应用自己的模板文件，ui:系统UI模板文件，tem:模板文件
 * @除前台模板文件外，其他包含的文件一定是php格式
 * @param $path
 * @return string
 */
function template($path)
{
    global $_M;
    //替换系统Url
    $web_site = $_M['url']['web_site'];
    $url = array();
    foreach ($_M['url'] as $key => $val) {
        if (in_array($key, array('web_site', 'admin_site'))) {
            $url[$key] = $val;
        } else {
            $val = str_replace($web_site, '../', $val);
            $url[$key] = $val;
        }
    }
    $_M['url'] = $url;

    // 前缀、路径转换优化（新模板框架v2）
    $dir = explode('/', $path);
    $postion = $dir[0];
    $file = substr(strstr($path, '/'), 1);

    if ($postion == 'own') {
        return PATH_OWN_FILE . "templates/{$file}.php";
    }
    if ($postion == 'ui') {
        if (M_MODULE == 'admin') {
            $ui = 'admin_old';
        } else {
            $ui = 'web';
        }

        return PATH_SYS_TEM . "{$ui}/{$file}.php";
    }
    if ($postion == 'tem') {
        if (M_MODULE == 'admin') {
            if (file_exists(PATH_SYS . '/' . M_NAME . "/admin/templates/{$file}.php")) {
                return PATH_SYS . '/' . M_NAME . "/admin/templates/{$file}.php";
            } else {
                return PATH_SYS . "index/admin/templates/{$file}.php";
            }
        } else {
            $tem_w = 'php';
            if ($_M['form']['ajax'] == 1) {
                $file_ajax = 'ajax/' . $file;
                if (file_exists(PATH_TEM . "{$file_ajax}.php")) {
                    return PATH_TEM . "{$file_ajax}.php";
                }
                if (file_exists(PATH_TEM . "{$file_ajax}.html")) {
                    return PATH_TEM . "{$file_ajax}.html";
                }
                if (file_exists(PATH_TEM . "{$file_ajax}.htm")) {
                    return PATH_TEM . "{$file_ajax}.htm";
                }
                if (file_exists(PATH_PUBLIC_WEB . "templates/{$file_ajax}.{$tem_w}")) {
                    return PATH_PUBLIC_WEB . "templates/{$file_ajax}.{$tem_w}";
                }
            }
            if (file_exists(PATH_TEM . "{$file}.php")) {
                return PATH_TEM . "{$file}.php";
            }
            if (file_exists(PATH_TEM . "{$file}.html")) {
                return PATH_TEM . "{$file}.html";
            }
            if (file_exists(PATH_TEM . "{$file}.htm")) {
                return PATH_TEM . "{$file}.htm";
            }

            return PATH_PUBLIC_WEB . "templates/{$file}.{$tem_w}";
        }
    }
}

// 判断后台各模块入口权限(功能大全列表)兼容手机端后台
function get_auth($member)
{
    global $_M;
    $admin_type = array();
    $permissions_s = $member['permissions']['s'];
    foreach ($permissions_s as $permissions) {
        $admin_type[] = "s{$permissions['aid']}";
    }
    $permissions_a = $member['permissions']['a'];
    foreach ($permissions_a as $permissions) {
        $admin_type[] = "a{$permissions['aid']}";
    }
    $power['admin_type'] = implode('-', $admin_type);

//        $power = admin_information();
    $data = array();
    //判断是否有环境检测的权限
    if (strstr($power['admin_type'], 's1903') || strstr($power['admin_type'], 'metinfo')) {
        $data['environmental_test'] = 1;
    }
    //判断是否有功能大全的权限
    if (strstr($power['admin_type'], 's1902') || strstr($power['admin_type'], 'metinfo')) {
        $data['function_complete'] = 1;
    }
    //判断是否有清空缓存的权限
    if (strstr($power['admin_type'], 's1901') || strstr($power['admin_type'], 'metinfo')) {
        $data['clear_cache'] = 1;
    }
    //判断是否有检测更新的权限
    if (strstr($power['admin_type'], 's1104') || strstr($power['admin_type'], 'metinfo')) {
        $data['checkupdate'] = 1;
        if (!$_M['config']['met_agents_update']) {
            $data['checkupdate'] = 0;
        }
    }

    //判断是否有基本信息设置权限
    if (strstr($power['admin_type'], 's1007') || strstr($power['admin_type'], 'metinfo')) {
        $data['basic_info'] = 1;
    }

    //判断是否有栏目设置权限
    if (strstr($power['admin_type'], 's1201') || strstr($power['admin_type'], 'metinfo')) {
        $data['column'] = 1;
    }

    //判断是否有内容设置权限
    if (strstr($power['admin_type'], 's1301') || strstr($power['admin_type'], 'metinfo')) {
        $data['content'] = 1;
    }

    //判断是否有网站模板权限
    if (strstr($power['admin_type'], 's1405') || strstr($power['admin_type'], 'metinfo')) {
        $data['site_template'] = 1;
        $query = "SELECT * FROM {$_M['table']['admin_column']} WHERE name = 'lang_appearance'";
        $info = DB::get_one($query);
        if (!$info['type'] && !$_M['config']['lang_appearance']) {
            $data['site_template'] = 0;
        }
    }
    //判断是否有水印缩略图权限
    if (strstr($power['admin_type'], 's1003') || strstr($power['admin_type'], 'metinfo')) {
        $data['watermark_thumbnail'] = 1;
    }
    //判断是否有banner管理权限
    if (strstr($power['admin_type'], 's1604') || strstr($power['admin_type'], 'metinfo')) {
        $data['banner'] = 1;
    }
    //判断是否有手机菜单权限
    if (strstr($power['admin_type'], 's1605') || strstr($power['admin_type'], 'metinfo')) {
        $data['mobile_menu'] = 1;
    }
    //判断是否有seo权限
    if (strstr($power['admin_type'], 's1404') || strstr($power['admin_type'], 'metinfo')) {
        $data['seo'] = 1;
    }
    //判断是否有友情链接权限
    if (strstr($power['admin_type'], 's1406') || strstr($power['admin_type'], 'metinfo')) {
        $data['link'] = 1;
    }
    //判断是否有语言权限
    if (strstr($power['admin_type'], 's1002') || strstr($power['admin_type'], 'metinfo')) {
        $data['language'] = 1;
    }
    //判断是否有应用插件权限
    $data['myapp'] = 0;
    if (strstr($power['admin_type'], 's1505') || strstr($power['admin_type'], 'metinfo')) {
        $data['myapp'] = 1;
//            $query = "SELECT * FROM {$_M['table']['admin_column']} WHERE name = 'lang_myapp'";
//            $info = DB::get_one($query);
//            if (!$_M['config']['lang_myapp'] && !$info['type']) {
//                $data['myapp'] = 0;
//            }
    }
    if (strstr($power['admin_type'], 'a10070') || strstr($power['admin_type'], 'metinfo')) {
        $data['met_agents_sms'] = 1;
        if (!$_M['config']['met_agents_sms']) {
            $data['met_agents_sms'] = 0;
        }
    }
    //判断是否有备份恢复权限
    if (strstr($power['admin_type'], 's1005') || strstr($power['admin_type'], 'metinfo')) {
        $data['databack'] = 1;
    }
    //判断是否有客服管理权限
    if (strstr($power['admin_type'], 's1106') || strstr($power['admin_type'], 'metinfo')) {
        $data['online'] = 1;
    }
    //判断是否有用户管理权限
    if (strstr($power['admin_type'], 's1506') || strstr($power['admin_type'], 'metinfo')) {
        $data['user'] = 1;
    }
    //判断是否有会员管理权限
    if (strstr($power['admin_type'], 's1601') || strstr($power['admin_type'], 'metinfo')) {
        $data['web_user'] = 1;
    }
    //判断是否有安全与效率权限
    if (strstr($power['admin_type'], 's1004') || strstr($power['admin_type'], 'metinfo')) {
        $data['safe'] = 1;
    }
    //判断是否有管理员设置权限
    if (strstr($power['admin_type'], 's1603') || strstr($power['admin_type'], 'metinfo')) {
        $data['admin_user'] = 1;
    }
    //判断是否有企业超市权限
    if (strstr($power['admin_type'], 's1508') || strstr($power['admin_type'], 'metinfo')) {
        $data['partner'] = 1;
        $query = "SELECT * FROM {$_M['table']['admin_column']} WHERE name = 'cooperation_platform'";
        $info = DB::get_one($query);
        if (!$_M['config']['met_agents_metmsg'] && !$info['type']) {
            $data['partner'] = 0;
        }
    }
    //判断是否有风格设置权限
    if (strstr($power['admin_type'], 's1905') || strstr($power['admin_type'], 'metinfo')) {
        $data['style_settings'] = 1;
    }
    //判断是否有导航菜单设置权限
    if (strstr($power['admin_type'], 's1904') || strstr($power['admin_type'], 'metinfo')) {
        $data['nav_setting'] = 1;
    }
    // 可视化权限
    if (strstr($power['admin_type'], 's1802') || strstr($power['admin_type'], 'metinfo')) {
        $data['ui_set'] = 1;
    }

    return $data;
}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>