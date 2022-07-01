<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu;


use Exception;
use Firebase\JWT\ExpiredException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tufu\Core\ConfigManager;
use Tufu\Core\RouteManager;
use Tufu\Core\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Capsule\Manager as Capsule;

class Tufu
{
    private $routeManager;
    private $database;

    /**
     * Tufu constructor.
     */
    public function __construct()
    {
        $this->routeManager = RouteManager::getInstance();
        $this->database = new Capsule();
    }

    public function run(Request $request = null)
    {
        try {
            $this->bootDatabase();
            if ($request === null) {
                $request = Request::createFromGlobals();
            }

            if ($this->routeManager->routeMatch($request)) {
                $this->invokeRequestListener($request);

                $response = $this->routeManager->routeToRoute();

                if (!$response instanceof Response) {
                    $response = new Response($response);
                }

                $this->invokeResponseListener($response);
                $response->send();
            } else {
                throw new RouteNotFoundException();
            }

        } catch (Exception $e) {
            $exceptionCode = $e->getCode();

            if ($e instanceof ExpiredException) {
                $exceptionCode = 401;
            }

            if ($exceptionCode === 0) {
                $exceptionCode = 500;
            }

            $response = new Response($e->getMessage(), $exceptionCode);
            $response->send();
        }
    }

    public function bootDatabase()
    {
        $this->database->addConnection(ConfigManager::get('database'));
        $this->database->bootEloquent();
    }

    public function db()
    {
        return $this->database;
    }

    private function invokeResponseListener(Response &$response)
    {
        $route = $this->routeManager->getResolvedRoute();
        $listener = $route->getMetaListener();

        if (array_key_exists('response', $listener)) {
            foreach ($listener['response'] as $responseListener) {
                call_user_func_array([new $responseListener, 'beforeResponse'], array(&$response));
            }
        }

        return $response;
    }

    private function invokeRequestListener(Request &$request)
    {
        $route = $this->routeManager->getResolvedRoute();
        $listener = $route->getMetaListener();

        if (array_key_exists('request', $listener)) {
            foreach ($listener['request'] as $requestListener) {
                call_user_func_array([new $requestListener, 'beforeRequest'], array(&$request));
            }
        }

        return $request;
    }
}
