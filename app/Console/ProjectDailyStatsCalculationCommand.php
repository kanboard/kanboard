<?php

namespace Kanboard\Console;

use Kanboard\Model\ProjectModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectDailyStatsCalculationCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('projects:daily-stats')
            ->setDescription('Calculate daily statistics for all projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->projectModel->getAllByStatus(ProjectModel::ACTIVE);

        foreach ($projects as $project) {
            $output->writeln('Run calculation for '.$project['name']);
            $this->projectDailyColumnStatsModel->updateTotals($project['id'], date('Y-m-d'));
            $this->projectDailyStatsModel->updateTotals($project['id'], date('Y-m-d'));
        }
    }
}
