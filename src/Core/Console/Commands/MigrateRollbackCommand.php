<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2016
 */

namespace Tufu\Core\Console\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tufu\Core\ConfigManager;
use Tufu\Core\Migration;

class MigrateRollbackCommand extends Command
{
    protected $commandName = 'migrate:rollback';
    protected $commandDescription = "Rollback all migration files.";

    protected function configure()
    {
        $this->setName($this->commandName)
             ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->getFiles();
        foreach ($files as $file) {
            include $this->getMigrationPath() . '/' . $file;

            $className = str_replace('.php', '', $file);

            /** @var Migration $migration */
            $migration = new $className();
            $migration->down();

            $output->writeln($file . ' rollbacked.');
        }
    }

    public function getMigrationPath()
    {
        return realpath(ConfigManager::get('basepath') . '/config/database/migrations');
    }

    public function getFiles()
    {
        return array_diff(scandir($this->getMigrationPath()), array('.', '..'));
    }


}