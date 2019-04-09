<?php

namespace Tufu\Core\Console\Commands;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tufu\Core\ConfigManager;

class ResourceCreateCommand extends Command
{
    protected $commandName = 'create:resource';
    protected $commandDescription = "Create a ResourceController and adds to the route list.";

    protected $commandArgumentSingName = "singularName";
    protected $commandArgumentSingDescription = "Singular name of the resource.";

    protected $commandArgumentPluName = "pluralName";
    protected $commandArgumentPluDescription = "Plural name of the resource. (optional, default is singular)";



    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentSingName,
                InputArgument::REQUIRED,
                $this->commandArgumentSingDescription
            )->addArgument(
                $this->commandArgumentPluName,
                InputArgument::OPTIONAL,
                $this->commandArgumentPluDescription
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument($this->commandArgumentSingName);
        $name = ucfirst(strtolower($name));

        $plural = $input->getArgument($this->commandArgumentPluName);
        $plural = ucfirst(strtolower($plural));

        if ($plural === '') {
            $plural = $name;
        }

        $adapter = new Local($this->getRealPath());
        $filesystem = new Filesystem($adapter);

        if ($filesystem->write('/app/controllers/'.$name . '.php', $this->getTemplate($name))) {
            $output->writeln('Controller: ' . $name . ' successful created.');
        }

        if ($filesystem->update(
            '/config/routes.php',
            $filesystem->read('/config/routes.php') . PHP_EOL . $this->getRouteConfig($name, $plural))
        ) {
            $output->writeln('Routing: ' . $name . '-Resource added to routes.php');
        }

    }

    public function getRealPath()
    {
        return realpath(ConfigManager::get('basepath'));
    }


    public function getTemplate($controllerName)
    {
        return <<<EOT
<?php

namespace App\Controller;
        
use Symfony\Component\HttpFoundation\Response;
use Tufu\Core\Controller;

class $controllerName extends Controller
{
    public function indexAction()
    {
        return new Response('index works');
    }
    
    public function getAction(\$id)
    {
        return new Response('get works');
    }
    
    public function postAction()
    {
        return new Response('post works');
    }
    
    public function putAction(\$id)
    {
        return new Response('put works');
    }
    
    public function patchAction(\$id)
    {
        return new Response('patch works');
    }
    
    public function deleteAction(\$id)
    {
        return new Response('delete works');
    }
}
EOT;
    }

    private function getRouteConfig($name, $plural)
    {
        return '$routesManager->addResource(\'' . strtolower($name) . '\', \'' . strtolower($plural) . '\');';
    }
}
