<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kanboard\Model\TaskModel;
use Kanboard\Event\TaskListEvent;

class TaskTriggerCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('trigger:tasks')
            ->setDescription('Trigger scheduler event for all tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->getProjectIds() as $project_id) {
            $tasks = $this->taskFinderModel->getAll($project_id);
            $nb_tasks = count($tasks);

            if ($nb_tasks > 0) {
                $output->writeln('Trigger task event: project_id='.$project_id.', nb_tasks='.$nb_tasks);
                $this->sendEvent($tasks, $project_id);
            }
        }
        return 0;
    }

    private function getProjectIds()
    {
        $listeners = $this->dispatcher->getListeners(TaskModel::EVENT_DAILY_CRONJOB);
        $project_ids = array();

        foreach ($listeners as $listener) {
            $project_ids[] = $listener[0]->getProjectId();
        }

        return array_unique($project_ids);
    }

    private function sendEvent(array &$tasks, $project_id)
    {
        $event = new TaskListEvent(array('project_id' => $project_id));
        $event->setTasks($tasks);

        $this->dispatcher->dispatch($event, TaskModel::EVENT_DAILY_CRONJOB);
    }
}
