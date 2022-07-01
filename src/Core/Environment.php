<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2016
 */

namespace Tufu\Core;

use Dotenv\Dotenv;

class Environment
{
    protected $basePath;
    protected $dotEnv;

    /**
     * Environment constructor.
     * @param $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->loadEnvironment();
    }

    /**
     * Loads dotEnv.
     */
    private function loadEnvironment()
    {
        $this->dotEnv = Dotenv::createUnsafeImmutable($this->basePath);
        $this->dotEnv->load();
    }

    /**
     * @param $key
     * @param null $default
     * @return bool|null|string
     */
    public function getEnv($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }


        if (strlen($value) > 1
            && mb_strpos($value, '"') === 0
            && mb_strpos($value, '"', strlen($value) - 1) === strlen($value) - 1
        ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
