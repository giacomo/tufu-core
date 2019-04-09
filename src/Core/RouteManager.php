<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core;


use function GuzzleHttp\Psr7\str;
use Symfony\Component\HttpFoundation\Request;

class RouteManager
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var Route|null 
     */
    protected $route = null;

    /**
     * @var RouteManager|null
     */
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

    /**
     * Adds a route to our collection.
     *
     * @param Route $route
     * @return $this
     */
    public function addRoute(Route $route)
    {
        $this->routes[$route->getIdentifier()] = $route;
        return $this;
    }

    public function addGetRoute($location, $options = null, $callback = null)
    {
        $this->addSimpleRoute('GET', $location, $options, $callback);
    }

    public function addPostRoute($location, $options = null, $callback = null)
    {
        $this->addSimpleRoute('POST', $location, $options, $callback);
    }

    public function addPutRoute($location, $options = null, $callback = null)
    {
        $this->addSimpleRoute('PUT', $location, $options, $callback);
    }

    public function addPatchRoute($location, $options = null, $callback = null)
    {
        $this->addSimpleRoute('PATCH', $location, $options, $callback);
    }

    public function addDeleteRoute($location, $options = null, $callback = null)
    {
        $this->addSimpleRoute('DELETE', $location, $options, $callback);
    }

    public function addOptionsRoute($location, $options = null, $callback = null)
    {
        $this->addSimpleRoute('OPTIONS', $location, $options, $callback);
    }

    public function addResource($singular, $plural, $options = [])
    {
        $globalOptions = [
            'methods' => ['index', 'get', 'post', 'put', 'patch', 'delete'],
            'controller' => 'App\Controller\\' . ucfirst($singular),
            'prefix' => '',
        ];

        $options = array_merge($globalOptions, $options);

        foreach ($options['methods'] as $method) {
            $this->addSimpleRoute(
                strtolower($method) === 'index' ? 'GET' : strtoupper($method),
                $options['prefix'] . '/' . $this->getResourceName($method, $singular, $plural),
                $options['controller'] . '@' . strtolower($method) . 'Action',
                null
            );
        }
    }

    public function getResourceName($method, $singular, $plural)
    {
        $method = strtolower($method);
        $singular = strtolower($singular);
        $plural = strtolower($plural);

        switch ($method) {
            case 'index':
                $resourceName = $plural;
                break;
            case 'put':
            case 'patch':
            case 'get':
            case 'delete':
                $resourceName = $singular . '/{id}';
                break;
            default:
                $resourceName = $singular;
                break;
        }

        return $resourceName;
    }

    /**
     * @param $method
     * @param $location
     * @param $options
     * @param $callback
     * @throws \Exception
     */
    public function addSimpleRoute($method, $location, $options, $callback)
    {
        $this->addRoute(
            new Route(
                $method,
                $this->buildRule($location, $options),
                $this->buildOptions($options),
                $callback
            )
        );
    }

    /**
     * @param $routeName
     * @return bool|Route
     */
    public function getRoute($routeName)
    {
        if (array_key_exists($routeName, $this->routes)) {
            return $this->routes[$routeName];
        }

        return false;
    }

    /**
     * @return Route|null
     */
    public function getResolvedRoute()
    {
        return $this->route;
    }

    /**
     * Check if route match.
     *
     * @param Request $request
     * @return bool
     */
    public function routeMatch(Request $request)
    {
        $identifier = $request->getMethod() . $request->getPathInfo();

        // search for edges
        if ($request->getMethod() === 'OPTIONS' && array_key_exists('OPTIONS/(.+)', $this->routes)) {
            $this->route = $this->routes['OPTIONS/(.+)'];
            return true;
        }

        // search for named routes
        if (array_key_exists($identifier, $this->routes)) {
            $this->route = $this->routes[$identifier];
            return true;
        }

        // search for dynamic routes
        foreach ($this->routes as $route) {
            if($route->match($identifier)){
                $this->route = $route;
                return true;
            }
        }

        return false;
    }

    /**
     * Return all routes.
     *
     * @return array
     */
    public function getRoutes(){
        return $this->routes;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function routeToRoute()
    {
        if ($this->route->hasCallback()) {
            $callback = $this->route->getCallback();
            return $callback->__invoke();
        }
        
        $options = $this->route->getOptions();

        if (!class_exists($options['uses'])) {
            throw new \Exception('Class not found check route');
        }

        return call_user_func_array([new $options['uses'], $options['action']], $this->route->getParams());
    }

    protected function buildRule($location, $options)
    {
        $rule = [];
        $globalOptions = [
            'query' => $location
        ];

        if (!is_array($options) || (is_array($options) && !array_key_exists('options', $options))) {
            preg_match_all('/{[\w\-\_]*}/', $location, $matches);

            foreach ($matches[0] as $match) {
                $rule['options'][$match] = '(.+)';
            }
        }

        if (is_array($options) && array_key_exists('options', $options)) {
            $rule = $options['options'];
        }

        return array_merge($globalOptions, $rule);
    }

    /**
     * @param $options
     * @return array
     * @throws \Exception
     */
    private function buildOptions($options)
    {
        if ($options === null) {
            return [];
        }

        if (is_array($options)) {
            return $options;
        }

        $options = explode('@', $options);

        if ($options !== false && count($options) != 2) {
            throw new \Exception('An error occurs building route options.');
        }

        return [
            'uses' => $options[0],
            'action' => $options[1]
        ];
    }
}
