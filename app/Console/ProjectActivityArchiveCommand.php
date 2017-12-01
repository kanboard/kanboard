<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectActivityArchiveCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('projects:archive-activities')
            ->setDescription('Remove project activities after one year');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectActivityModel->cleanup(strtotime('-1 year'));
    }
}
