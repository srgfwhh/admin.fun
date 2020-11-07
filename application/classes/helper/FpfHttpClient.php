<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/3 0003
 * Time: 15:49
 */
namespace app\classes\helper;
class FpfHttpClient
{
    /**
     * @var  $http_client CurlHttpClient|SwoftHttpClient
     */
    private $http_client;

    public function __construct($config = [])
    {
        if ( isset($config['swoole_service']) && $config['swoole_service']  == 1 ) {
            $this->http_client = new SwooleHttpClient($config);
        } else {
            $this->http_client = new CurlHttpClient($config);
        }
    }

    public function get($url, $get_arr, $options = [])
    {
        return $this->http_client->get($url, $get_arr, $options);
    }

    public function post($url, $post_arr, $options = [])
    {
        return $this->http_client->post($url, $post_arr, $options);
    }
}