<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core\View;


abstract class View
{
    abstract public function render($name, array $context = array());
}