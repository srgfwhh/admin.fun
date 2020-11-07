<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/3 0003
 * Time: 15:51
 */
namespace app\classes\helper;
use Swoole\Coroutine\Http\Client;
use think\Log;

class SwooleHttpClient
{
    /**
     * The URI to request
     *
     * @var string
     */
    protected $full_url;

    /**
     * The scheme to use for the request, i.e. http, https
     *
     * @var string
     */
    protected $scheme;

    /**
     * Buffer for the HTTP request data
     *
     * @var array
     */
    protected $post_arr;

    /**
     * http headers
     *
     * @var array
     */
    protected $headers = [];
    protected $uri_no_func;
    protected $connect_timeout_ms;
    protected $timeout_ms;
    protected $method;
    /**
     * Http 客户端
     *
     * @var Client
     */
    protected $curl_handle;
    protected $response;
    protected $cookies = [];
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    public function __construct($config = [])
    {
        $http_option = config('http_options');
        $this->connect_timeout_ms = isset($config['connect_timeout']) ? $config['connect_timeout'] : ($http_option['connect_timeout'] ?? 10);
        $this->timeout_ms = isset($config['timeout_ms']) ? $config['timeout_ms'] : ($http_option['timeout_ms'] ?? 100);
    }

    /**
     * Set read timeout
     *
     * @param float $timeout
     */
    public function setTimeoutSecs($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Close the transport.
     */
    public function close()
    {
        $this->request = '';
        $this->response = null;
    }

    public function get($url, $get_arr, $options = [])
    {
        $arr = explode(':', $url);
        $scheme = 'https' === $arr[0] ? 'https' : 'http';
        $this->scheme = $scheme;
        $this->full_url = $url . '?' . http_build_query($get_arr);
        if (!empty($options['time']['connect_timeout_ms'])) {
            $this->connect_timeout_ms = $options['time']['connect_timeout'];
        }
        if (!empty($options['time']['timeout_ms'])) {
            $this->timeout_ms = $options['time']['timeout_ms'];
        }
        if (!empty($options['headers'])) {
            $this->headers = $options['headers'];
        }
        if (!empty($options['cookies'])) {
            $this->cookies = $options['cookies'];
        }
        $this->method = self::METHOD_GET;
        $this->flush();
        return $this->response;
    }

    public function post($url, $post_arr, $options = [])
    {
        $arr = explode(':', $url);
        $scheme = 'https' === $arr[0] ? 'https' : 'http';
        $this->scheme = $scheme;
        $this->full_url = $url;
        $this->post_arr = $post_arr;
        if (!empty($options['time']['connect_timeout_ms'])) {
            $this->connect_timeout_ms = $options['time']['connect_timeout'];
        }
        if (!empty($options['time']['timeout_ms'])) {
            $this->timeout_ms = $options['time']['timeout_ms'];
        }
        if (!empty($options['headers'])) {
            $this->headers = $options['headers'];
        }
        if (!empty($options['cookies'])) {
            $this->cookies = $options['cookies'];
        }
        $this->method = self::METHOD_POST;
        $this->flush();
        return $this->response;
    }

    /**
     * @throws SwoftHttpException
     */
    public function flush()
    {
        register_shutdown_function([$this, 'closeCurlHandle']);

        $full_url = $this->full_url;
        $parts = parse_url($full_url);
        $scheme = 'http' === $parts['scheme'] ? false : true;
        $parts['port'] = $parts['port'] ?? '';

        if (true === $scheme && !$parts['port']) {
            $parts['port'] = 443;
        }

        $parts['port'] = $parts['port'] ? intval($parts['port']) : 80;

        $this->curl_handle = new Client($parts['host'], $parts['port'], $scheme);
        $full_url = $this->full_url;

        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = "$key: $value";
        }
        if (defined('VERSION_KEY') && defined('VERSION_VALUE_NO_PTEFIX')) {
            $options['headers'][VERSION_KEY] = VERSION_VALUE_NO_PTEFIX;
        }
        if (self::METHOD_POST === $this->method) {
            $options['body'] = $this->post_arr;
        }
        $options['timeout'] = $this->timeout_ms / 1000;
        $options['headers'] = $options['headers'] ?? [];

        $this->curl_handle->setHeaders($options['headers']);
        $this->curl_handle->set(['timeout' => $options['timeout']]);

        if (self::METHOD_POST === $this->method) {
            $this->curl_handle->post($parts['path'], $options['body']);
        } else {
            $this->curl_handle->get($parts['path']);
        }

        $this->response = $this->curl_handle->body;
        if (200 !== $this->curl_handle->errCode) {

            Log::record('ERROR_CODE:'.$this->curl_handle->errCode,'http');
        }

        // Connect failed
        if (!$this->response) {

            $this->curl_handle = null;
            $error = 'CurlHttpClient: Could not connect to ' . $full_url;
            Log::record('ERROR:'.$error,'http');

            throw new SwooleHttpException($error, SwooleHttpException::NOT_OPEN);
        }
    }

    public function closeCurlHandle()
    {
        if ($this->curl_handle) {
            $this->curl_handle = null;
        }
    }

    public function addHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }
}