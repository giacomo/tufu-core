<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core;


use Exception;

class RouteNotFoundException extends Exception
{
    protected $code = 404;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = 'Route not found!';
    }

    public function __toString()
    {
        echo '<h1>Fatal error: ' . $this->getMessage() . '</h1>';
        echo '<pre>';
        return parent::__toString();
    }
}
