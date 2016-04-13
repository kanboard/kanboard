<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\NullOutput;

class CronjobCommand extends BaseCommand
{
    private $commands = array(
        'projects:daily-stats',
        'notification:overdue-tasks',
        'trigger:tasks',
    );

    protected function configure()
    {
        $this
            ->setName('cronjob')
            ->setDescription('Execute daily cronjob');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->commands as $command) {
            $job = $this->getApplication()->find($command);
            $job->run(new ArrayInput(array('command' => $command)), new NullOutput());
        }
    }
}
