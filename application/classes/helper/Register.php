<?php
/**
 * 注册器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/14
 * Time: 11:14
 */

namespace app\classes\helper;


class Register
{
    protected static $objects;

    static function set($alias, $object)
    {
        self::$objects[$alias] = $object;
    }

    static function get($key)
    {
        if (!isset(self::$objects[$key])) {
            return false;
        }
        return self::$objects[$key];
    }

    function _unset($alias)
    {
        unset(self::$objects[$alias]);
    }
}