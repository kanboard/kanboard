<?php

namespace Kanboard\Console;

use Kanboard\ServiceProvider\DatabaseProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseMigrationCommand extends DatabaseVersionCommand
{
    protected function configure()
    {
        $this
            ->setName('db:migrate')
            ->setDescription('Execute SQL migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        DatabaseProvider::runMigrations($this->container['db']);
    }
}
