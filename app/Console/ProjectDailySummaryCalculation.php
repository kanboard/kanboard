<?php

namespace Console;

use Model\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectDailySummaryCalculation extends Base
{
    protected function configure()
    {
        $this
            ->setName('projects:daily-summary')
            ->setDescription('Calculate daily summary data for all projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->project->getAllByStatus(Project::ACTIVE);

        foreach ($projects as $project) {
            $output->writeln('Run calculation for '.$project['name']);
            $this->projectDailySummary->updateTotals($project['id'], date('Y-m-d'));
        }
    }
}
