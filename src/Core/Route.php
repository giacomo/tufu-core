<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core;


use Exception;
use ReflectionClass;

class Route {

    protected $method;
    protected $rule;
    protected $options = array();
    protected $callback = null;

    protected $params = array();

    protected $identifier;

    /**
     * @param string $method
     * @param string|array $rule
     * @param array $options
     * @param null $callback
     */
    public function __construct($method, $rule, array $options = array(), $callback = null)
    {
        $this->setMethod($method);
        $this->setRule($rule);
        $this->setOptions($options);
        $this->setCallback($callback);
    }

    /**
     * Set method to POST, GET or ...
     *
     * @param $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param $rule
     */
    public function setRule($rule)
    {
        if (is_array($rule) && !array_key_exists('options', $rule)) {
            $rule = $rule['query'];
        }

        if (!is_array($rule)) {
            $this->rule = $rule;
        } else {
            $this->setParams(array_keys($rule['options']));
            $this->rule = str_replace(array_keys($rule['options']), array_values($rule['options']), $rule['query']);
        }
    }

    public function getRule()
    {
        return ConfigManager::get('baseurl') . $this->rule;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return \Closure|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return bool
     */
    public function hasCallback()
    {
        return $this->callback !== null;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function match($identifier)
    {
        $regExp = $pattern = '/' . str_replace('/', '\/', $this->getIdentifier()) . '/';

        if (preg_match($regExp, $identifier, $matches)) {
            if(count($this->getParams()) > 0 && count($matches) - 1 === count($this->getParams())) {
                array_shift($matches);
                $this->setParams($matches);
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->method . $this->rule;
    }

    public function getMetaListener()
    {
        $options = $this->getOptions();
        $listener = [];
        try {
            if (array_key_exists('uses', $options)) {
                $reflector = new ReflectionClass($options['uses']);
                $methodDoc = $reflector->getMethod($options['action'])->getDocComment();
                preg_match_all("/@ResponseListener\(\'(.*)\'\)/m", $methodDoc, $matchesResponse);
                preg_match_all("/@RequestListener\(\'(.*)\'\)/m", $methodDoc, $matchesRequest);

                if (count($matchesResponse) === 2) {
                    $listener = array_merge($listener, array('response' => array_unique($matchesResponse[1])));
                }

                if (count($matchesRequest) === 2) {
                    $listener = array_merge($listener, array('request' => array_unique($matchesRequest[1])));
                }
            }
        } catch (Exception $e) {

        }

        return $listener;
    }
}
