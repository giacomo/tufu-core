<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Tufu\Core\View\View;

class Controller {

    /** @var Request */
    protected $request;

    /** @var View */
    protected $view;

    /** @var FormValidator */
    protected $validator;
    protected $session;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->validator = new FormValidator();
        $this->session = new Session();
        $this->session->start();

        $viewAdapter = ConfigManager::get('view.adapter');
        $this->view = new $viewAdapter;
    }

    /**
     * Renders html by defined Template engine.
     *
     * @param $name
     * @param array $context
     */
    protected function render($name, array $context = array())
    {
        return $this->view->render($name, $this->getRenderContext($context));
    }

    protected function getRenderContext($context = array())
    {
        return array_merge($context, array(
            'errors' => $this->session->getFlashBag()->get('error')
        ));
    }

    protected function redirect($url, $statusCode = 302, $headers = array())
    {
        $redirectUrl = ConfigManager::get('baseurl') . $url;
        $redirect = new RedirectResponse($redirectUrl, $statusCode, $headers);
        return $redirect->send();
    }

    protected function back()
    {
        $this->saveFlashErrors();
        return new RedirectResponse($this->request->headers->get('referer'));
    }

    private function saveFlashErrors()
    {
        foreach ($this->validator->getErrors() as $name => $error) {
            $this->session->getFlashBag()->add('error', [$name => $error]);
        }
    }
}
