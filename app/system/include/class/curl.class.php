<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

/**
 * 远程连接类
 * @param string $host  主机名			   
 * @param string $file 	远程连接文件地址
 */
class curl
{
    public $host;
    public $file;
    public $curlerror;

    public function __construct()
    {
        global $_M;
        $this->set('host', $_M['config']['met_host']);
    }

    /**
     * 为字段赋值
     * @param  string $name 字段名称
     * @param  mixed $value 要赋给字段的值
     * @return boolean        属性名不正确或值没有返回false
     */
    public function set($name, $value)
    {
        if ($value === NULL) {
            return false;
        }
        switch ($name) {
            case 'host':
                $value = trim($value);
                if (substr($value, 0, 8) == 'https://') {
                    $this->set('ssl', 1);
                }
                $value = trim(str_replace('https://', '', $value), '/');
                $this->host = trim(str_replace('http://', '', $value), '/');
                break;
            case 'file':
                $this->file = trim($value, '/');
                break;
            case 'ignore':
                $this->ignore = $value;
                break;
            case 'ssl':
                $this->ssl = $value;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * 远程连接发送post
     * @param  array $host 发送的POST信息
     * @param  string $timeout 超时时间，默认30秒
     * @return string            返回请求信息
     */
    public function curl_post($post = array(), $timeout = 30)
    {
        global $_M;
        $result = '';
        if (
            get_extension_funcs('curl')
            && function_exists('curl_init')
            && function_exists('curl_setopt')
            && function_exists('curl_exec')
            && function_exists('curl_close')
        ) {
            $curlHandle = curl_init();
            if ($this->ssl == 1) {
                curl_setopt($curlHandle, CURLOPT_URL, 'https://' . $this->host . '/' . $this->file);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);
            } else {
                curl_setopt($curlHandle, CURLOPT_URL, 'http://' . $this->host . '/' . $this->file);
            }

            curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($curlHandle, CURLOPT_REFERER, $_M['url']['web_site']);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post);
            $result = curl_exec($curlHandle);
            curl_close($curlHandle);
        }

        $result = trim($result);
        if (substr($result, 0, 7) == 'metinfo') {
            return substr($result, 7);
        } elseif ($this->ignore) {
            return $result;
        } else {
            $this->curlerror = $result;
            return 'nohost';
        }
    }
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>