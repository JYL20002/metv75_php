<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.

defined('IN_MET') or exit('No permission');

load::sys_class('web');

class html extends web
{
    public function __construct()
    {
        global $_M;
        parent::__construct();
        $this->html_handle = load::mod_class('html/html_handle', 'new');
    }

    /**
     * 启动定时任务(定时跟新静态页)
     */
    public function doSetval()
    {
        global $_M;
        $sleep = 3600;
        $url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doSetval&lang={$_M['lang']}";
        //开启静态且自动更新
        if ($_M['config']['met_html_auto'] && $_M['config']['met_webhtm']) {
            self::updatePage();
            sleep($sleep);
            $stream_opts = array(
                "ssl" => array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false
                )
            );
            file_get_contents($url.'&time='.time(),false, stream_context_create($stream_opts));
        }
        return;
    }

    /**
     * 定时更新
     */
    public function updatePage()
    {
        global $_M;
        $index = $this->html_handle->homePage();    //首页静态文件
        $static_file = PATH_WEB . $index['filename'];
        if (is_file($static_file)) {
            $filemtime = filemtime($static_file);
        }else{
            $filemtime = strtotime("-1 hour");
        }

        switch ($_M['config']['met_html_auto']) {
            case 1://daily
                $offset = 3600 * 24;
                break;
            case 2://weekly
                $offset = 3600 * 24 * 7;
                break;
            case 3://monthly
                $offset = 3600 * 24 * 7 * 30;
                break;
            default:
                return;
                break;
        }
        $expires_in = $filemtime + $offset;

        if ($expires_in > time()){//有效期内
            return;
        };

        $hour = intval(date('H'));
        if (!($hour <= 4)) {//每天4点前更新静态文件
            return;
        }

        $pageinfo = array();
        $pageinfo[] = $this->html_handle->homePage();
        $pageinfo = $this->html_handle->getPageInfo($pageinfo, '', '', '', '', 1, '');
        $pages = $this->html_handle->getQueryList($pageinfo);
        foreach ($pages as $key => $rwo) {
            if (!strstr($rwo['url'], '../')) {
                continue;
            }
            $page['url'] = $_M['url']['web_site'] . str_replace(array('../',"..%2F"),'',$rwo['url']);
            $page['filename'] = str_replace('../','',$rwo['filename']);
            $pages[$key] = $page;
        }

        //静态页列表写入缓存
        $this->html_handle->htmlCache('put',"static_list_" . $_M['lang'], $pages);
        $this->html_handle->htmlCache('del',"static_list_err_" . $_M['lang']);
        $this->html_handle->htmlCache('del',"static_list_suc_" . $_M['lang']);

        $loop_url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doLoop&lang={$_M['lang']}";

        self::request($loop_url);
        return;
    }

    /**
     * @param $url
     * @param array $param
     */
    protected function request($url ,$param = array())
    {
        global $_M;
        $urlinfo = parse_url($url);
        $scheme = $urlinfo['scheme'];
        $host = $urlinfo['host'];
        $path = $urlinfo['path'];
        $query = $urlinfo['query'];
        //$query = isset($param)? http_build_query($param) : '';

        $port = $scheme == 'https' ? 443 : 80;
        $errno = 0;
        $errstr = '';
        $timeout = 10;

        if (!function_exists('fsockopen')) {
            return;
        }
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        $out = "POST ".$path." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n";
        $out .= "content-length:".strlen($query)."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;

        fputs($fp, $out);
        fclose($fp);
        return;
    }

    /**
     * 循环生成静态页
     */
    public function doLoop()
    {
        global $_M;
        $met_member_force = isset($_M['form']['metinfonow']) ? $_M['form']['metinfonow'] : null;
        if (!$met_member_force || $met_member_force!==$_M['config']['met_member_force']) {
            jsoncallback("404");
        }

        $sleep = 1;
        $loop_url = $_M['url']['web_site'] . "app/system/entrance.php?n=html&c=html&a=doLoop&metinfonow={$_M['config']['met_member_force']}&lang={$_M['lang']}";

        //开启静态且自动更新
        if ($_M['config']['met_webhtm']) {
            if (self::createPage() === true) {
                sleep($sleep);
                //loop
                $stream_opts = array(
                    "ssl" => array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false
                    )
                );
                file_get_contents($loop_url.'&time='.time(),false, stream_context_create($stream_opts));
            }
            //finished
            jsoncallback("Finished");
            return;
        }
        return;
    }

    /**
     * @return bool
     */
    public function createPage()
    {
        global $_M;
        $page_list = $this->html_handle->htmlCache('get',"static_list_" . $_M['lang']);
        
        $suc = $this->html_handle->htmlCache('get',"static_list_suc_" . $_M['lang'])?: array();
        $err = $this->html_handle->htmlCache('get',"static_list_err_" . $_M['lang'])?: array();
        if (!$page_list) return false;

        $request_list = $urls = array();
        $limit = 5;

        for ($i = 1; $i <= $limit; $i++) {
            if (!$page_list) break;
            $res = array_shift($page_list);
            $request_list[] = $res;
            $urls[] = $res['url'];
        }

        //页面生成请求
        if (function_exists('curl_multi_init') && 0) {
            $res_list = self::curlRequestMulti($urls); //方法停用，不兼容第三方服务器环境
        }else {
            $res_list = self::curlRequest($urls);
            
        }

        foreach ($res_list as $key => $res) {
            $result = json_decode($res, true);
            if ($result && $result['suc'] == 1) {
                //success
                $suc[] = $request_list[$key];
            } else {
                //error && curlError
                $err_msg = $request_list[$key];
                $err_msg['curlError'] = $res;
                $err[] = $err_msg;
            }
        }

        $this->html_handle->htmlCache('put',"static_list_suc_" . $_M['lang'],$suc);
        $this->html_handle->htmlCache('put',"static_list_err_" . $_M['lang'],$err);
        $this->html_handle->htmlCache('put',"static_list_" . $_M['lang'],$page_list);
        return true;
    }

    /**
     * @param $urls
     * @return void
     */
    protected function curlRequest($urls = array())
    {
        $redata = array();
        foreach ($urls as $key => $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array());
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $result = curl_exec($ch);

            if (curl_error($ch)) {
                $error_msg = "curl_error:" . curl_error($ch) . " curl_errno:" . curl_errno($ch);
                $redata[$key] = $error_msg;
            }else{
                $redata[$key] = $result;
            }
            curl_close($ch);
        }
        return $redata;
    }

    /**
     * multi request
     * @param array $urls
     * @return array|bool
     */
    protected function curlRequestMulti($urls = array())
    {
        if (!$urls) {
            return false;
        }

        $mh = curl_multi_init();
        foreach ($urls as $i => $url) {
            $conn[$i] = curl_init($url);
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($conn[$i], CURLOPT_HEADER, 0);
            curl_multi_add_handle($mh, $conn[$i]);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc === CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        //获取当前传输的有关信息(该步骤不可省略，否则无法获取curl报错信息)
        while (curl_multi_info_read($mh)) {
        }

        $res = array();
        foreach ($conn as $i=>$ch) {
            if (curl_errno($ch)) {
                //获取curl报错信息
                $error_msg = "curl_error:" . curl_error($ch) . " curl_errno:" . curl_errno($ch);
                $res[$i] = $error_msg;
            }else{
                //获取某个curl句柄的返回值
                $res[$i] = curl_multi_getcontent($ch);
            }
            //移除批处理句柄中的某个句柄资源
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);
        return $res;
    }

    // 在file_get_contents请求失败时使用curl
    public function curlLoop($url = '')
	{
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, array());
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $result = curl_exec($ch);
			
			return $result;
	}
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>
