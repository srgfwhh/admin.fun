<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/17 0017
 * Time: 16:42
 */
namespace app\classes\helper;
use think\Log;

class CurlHttpSimulator
{


    public function login_post_switch($url,$post,$cookie_txt,$headers = [])
    {
        $data = is_array($post) ? http_build_query($post) : $post;

        $ch = curl_init(); //初始化curl模块

        if($headers){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_URL, $url); //登录提交的地址
        curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否自动显示返回的信息
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_txt);
        curl_setopt($ch, CURLOPT_POST, 1); //以POST方式提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//要执行的信息
        $response = curl_exec($ch); //执行CURL
        curl_close($ch);
        return $response; //返回字符串
    }



    public function login_post($url,$post,$cookie_txt = '',$headers = [])
    {
        $data = is_array($post) ? http_build_query($post) : $post;
        $ch = curl_init(); //初始化curl模块

        if($headers){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $url); //登录提交的地址
        curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否自动显示返回的信息
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        if ($cookie_txt){
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_txt);
        }
        curl_setopt($ch, CURLOPT_POST, 1); //以POST方式提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//要执行的信息
        $response = curl_exec($ch);//执行CURL
        curl_close($ch);
        return $response; //返回字符串
    }


    public function get_content($url, $cookie_txt = '', $data = [], $method = 'get',$headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否自动显示返回的信息
        if ($cookie_txt){
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_txt);
        }

        if($headers){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if($method == 'post'){

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            $data = is_array($data) ? http_build_query($data) : $data;

            curl_setopt($ch, CURLOPT_POST, 1); //以POST方式提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//要执行的信息
        }

        $response = curl_exec($ch); //执行CURL
        curl_close($ch);
        return $response; //返回字符串
    }



}