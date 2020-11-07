<?php
namespace app\classes\helper;
/**
 * 单例封装redis类库
 * Class RedisManager
 * @package app\classes\redis
 */
class RedisManager
{
    /**
     * @var \Redis $_instance
     */
    private static $_instance;

    /**
     * 私有化构造函数，防止类被实例化
     * RedisManager constructor.
     */
    private function __construct()
    {
    }

    /**
     * 单例方法，用于访问实例
     * @param array $config
     * @return \Redis
     */
    public static function getInstance(array $config)
    {
        if(!(self::$_instance instanceof \Redis)){
            self::$_instance = new \Redis();
            try {
                self::$_instance->connect($config['host'], $config['port'], 1);
                if (isset($config['password'])) {
                    self::$_instance->auth($config['password']);
                }
                if (!isset($config['database'])) {
                    $config['database'] = 0;
                }
                self::$_instance->select($config['database']);
            }catch (\Exception $e){
                throw new \Exception($e->getMessage());
            }
        }

        return static::$_instance;
    }

    /**
     * 私有化克隆函数，防止类外克隆本对象
     */
    private function __clone()
    {
    }
}