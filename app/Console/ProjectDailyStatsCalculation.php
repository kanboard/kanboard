<?php

namespace Kanboard\Console;

use Kanboard\Model\Project;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectDailyStatsCalculation extends Base
{
    protected function configure()
    {
        $this
            ->setName('projects:daily-stats')
            ->setDescription('Calculate daily statistics for all projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->project->getAllByStatus(Project::ACTIVE);

        foreach ($projects as $project) {
            $output->writeln('Run calculation for '.$project['name']);
            $this->projectDailyColumnStats->updateTotals($project['id'], date('Y-m-d'));
            $this->projectDailyStats->updateTotals($project['id'], date('Y-m-d'));
        }
    }
}
