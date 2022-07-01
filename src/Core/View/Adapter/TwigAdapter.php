<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2020
 */

namespace Tufu\Core\View\Adapter;

use DebugBar\Bridge\NamespacedTwigProfileCollector;
use DebugBar\StandardDebugBar;
use Tufu\Core\ConfigManager;
use Tufu\Core\View\Extensions\AppExtension;
use Tufu\Core\View\View;
use Twig\Extension\ProfilerExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Profiler\Profile;

class TwigAdapter extends View
{
    protected $twig;
    protected $debugbar;

    public function __construct()
    {
        $loader = new FilesystemLoader('../app/views');
        $this->twig = new Environment($loader, array(
            'debug' => ConfigManager::get('debug'),
//            'cache' => '../storage/template_cache',
        ));
        $this->twig->addExtension(new AppExtension());

        if (ConfigManager::get('debug')) {
            $debugbar = new StandardDebugBar();
            $profile = new Profile();
            $this->twig->addExtension(new ProfilerExtension($profile));
            $debugbar->addCollector(new NamespacedTwigProfileCollector($profile));
        }
    }

    public function render($name, array $context = array())
    {
        return $this->twig->render($name, $context);
    }
}
