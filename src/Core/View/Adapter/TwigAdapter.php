<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2020
 */

namespace Tufu\Core\View\Adapter;

use Tufu\Core\ConfigManager;
use Tufu\Core\View\Extensions\AppExtension;
use Tufu\Core\View\View;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class TwigAdapter extends View
{
    protected $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../app/views');
        $this->twig = new Environment($loader, array(
            'debug' => ConfigManager::get('debug'),
//            'cache' => '../storage/template_cache',
        ));
        $this->twig->addExtension(new AppExtension());
    }

    public function render($name, array $context = array())
    {
        return $this->twig->render($name, $context);
    }
}
