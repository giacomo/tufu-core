<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2019
 */

namespace Tufu\Core;


abstract class AbstractRequestInterceptor
{
    abstract function beforeRequest(&$request);
}
