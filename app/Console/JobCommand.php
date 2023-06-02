<?php

namespace Kanboard\Console;

use Kanboard\Core\Queue\JobHandler;
use SimpleQueue\Job;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JobCommand
 *
 * @package Kanboard\Console
 * @author  Frederic Guillot
 */
class JobCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('job')
            ->setDescription('Execute individual job (read payload from stdin)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $payload = fgets(STDIN);

        $job = new Job();
        $job->unserialize($payload);

        JobHandler::getInstance($this->container)->executeJob($job);
        return 0;
    }
}
