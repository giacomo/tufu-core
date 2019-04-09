<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2016
 */

namespace Tufu\Core\View\Extensions;

use Exception;
use Tufu\Core\ConfigManager;
use Tufu\Core\Route;
use Tufu\Core\RouteManager;
use Twig_Environment;

class AppExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('route', array($this, 'getRoute')),
            new \Twig_SimpleFunction('asset', array($this, 'getAsset')),
            new \Twig_SimpleFunction('dump', array($this, 'getDump'), array('needs_environment' => true)),
            new \Twig_SimpleFunction('has_error', array($this, 'hasError')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * @param $routeName
     * @param bool $absolute
     * @return string
     */
    public function getRoute($routeName, $absolute = false)
    {
        $manager = RouteManager::getInstance();
        $route = $manager->getRoute($routeName);

        if ($route instanceof Route) {
            return $route->getRule();
        }

        return '';
    }

    public function getAsset($fileName, $bustQuery = true)
    {
        $realPath = realpath(ConfigManager::get('basepath') . '/public/' . $fileName);

        if (!file_exists($realPath)) {
            throw new Exception("File not found at [{$realPath}]");
        }

        // Get the last updated timestamp of the file.
        $timestamp = filemtime($realPath);

        if (!$bustQuery) {
            // Get the extension of the file.
            $extension = pathinfo($realPath, PATHINFO_EXTENSION);

            // Strip the extension off of the path.
            $stripped = substr($fileName, 0, -(strlen($extension) + 1));

            // Put the timestamp between the filename and the extension.
            $fileName = implode('.', array($stripped, $timestamp, $extension));
        } else {
            // Append the timestamp to the path as a query string.
            $fileName  .= '?v=' . $timestamp;
        }

        return $fileName;
    }

    public function getDump(Twig_Environment $env, $var)
    {
        if (!$env->isDebug()) {
            return '';
        }

        return dump($var);
    }

    public function hasError($errors, $name)
    {
        $exists = false;

        foreach ($errors as $error) {
            if (array_key_exists($name, $error)) {
                $exists = true;
            }
        }

        return $exists;
    }
}