<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WorkerCommand
 *
 * @package Kanboard\Console
 * @author  Frederic Guillot
 */
class WorkerCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('worker')
            ->setDescription('Execute queue worker')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queueManager->listen();
    }
}
