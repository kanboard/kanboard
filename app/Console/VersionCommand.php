<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class VersionCommand
 *
 * @package Kanboard\Console
 * @author  Frederic Guillot
 */
class VersionCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('version')
            ->setDescription('Display Kanboard version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(APP_VERSION);
        return 0;
    }
}
