<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core;


class ConfigManager
{
    /**
     * @var array
     */
    static protected $config = [];

    static private $instance = null;

    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){}

    private function __clone() {}

    public static function setConfig(array $config = array())
    {
        static::$config = $config;
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        if (array_key_exists($name, static::$config)) {
            return static::$config[$name];
        }
        return null;
    }
}