<?php

namespace Kanboard\Console;

use Kanboard\Model\ProjectModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectArchiveCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('projects:archive')
            ->setDescription('Disable projects not touched during one year');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->db->table(ProjectModel::TABLE)
            ->eq('is_active', 1)
            ->lt('last_modified', strtotime('-1 year'))
            ->findAll();

        foreach ($projects as $project) {
            $output->writeln('Deactivating project: #'.$project['id'].' - '.$project['name']);
            $this->projectModel->disable($project['id']);
        }
    }
}
