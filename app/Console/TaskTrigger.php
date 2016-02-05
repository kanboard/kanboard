<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kanboard\Model\Task;
use Kanboard\Event\TaskListEvent;

class TaskTrigger extends Base
{
    protected function configure()
    {
        $this
            ->setName('trigger:tasks')
            ->setDescription('Trigger scheduler event for all tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getProjectIds() as $project_id) {
            $tasks = $this->taskFinder->getAll($project_id);
            $nb_tasks = count($tasks);

            if ($nb_tasks > 0) {
                $output->writeln('Trigger task event: project_id='.$project_id.', nb_tasks='.$nb_tasks);
                $this->sendEvent($tasks, $project_id);
            }
        }
    }

    private function getProjectIds()
    {
        $listeners = $this->dispatcher->getListeners(Task::EVENT_DAILY_CRONJOB);
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

        $this->dispatcher->dispatch(Task::EVENT_DAILY_CRONJOB, $event);
    }
}
