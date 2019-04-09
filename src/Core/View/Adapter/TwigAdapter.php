<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2015
 */

namespace Tufu\Core\View\Adapter;

use Tufu\Core\ConfigManager;
use Tufu\Core\View\Extensions\AppExtension;
use Tufu\Core\View\View;
use Twig_Extensions_Extension_Text;

class TwigAdapter extends View
{
    protected $twig;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem('../app/views');
        $this->twig = new \Twig_Environment($loader, array(
            'debug' => ConfigManager::get('debug'),
//            'cache' => '../storage/template_cache',
        ));
        $this->twig->addExtension(new Twig_Extensions_Extension_Text());
        $this->twig->addExtension(new AppExtension());
    }

    public function render($name, array $context = array())
    {
        return $this->twig->render($name, $context);
    }
}