<?php

namespace Kanboard\Console;

use Kanboard\ServiceProvider\DatabaseProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseVersionCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('db:version')
            ->setDescription('Show database schema version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Current version: '.DatabaseProvider::getSchemaVersion($this->container['db']).'</info>');
        $output->writeln('<info>Last version: '.\Schema\VERSION.'</info>');
        return 0;
    }
}
