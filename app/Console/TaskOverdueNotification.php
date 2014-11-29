<?php

namespace Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskOverdueNotification extends Base
{
    protected function configure()
    {
        $this
            ->setName('notification:overdue-tasks')
            ->setDescription('Send notifications for overdue tasks')
            ->addOption('show', null, InputOption::VALUE_NONE, 'Show sent overdue tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = array();
        $tasks = $this->taskFinder->getOverdueTasks();

        // Group tasks by project
        foreach ($tasks as $task) {
            $projects[$task['project_id']][] = $task;
        }

        // Send notifications for each project
        foreach ($projects as $project_id => $project_tasks) {

            $users = $this->notification->getUsersList($project_id);

            $this->notification->sendEmails(
                'task_due',
                $users,
                array('tasks' => $project_tasks, 'project' => $project_tasks[0]['project_name'])
            );
        }

        if ($input->getOption('show')) {
            $this->showTable($output, $tasks);
        }
    }

    public function showTable(OutputInterface $output, array $tasks)
    {
        $rows = array();

        foreach ($tasks as $task) {
            $rows[] = array(
                $task['id'],
                $task['title'],
                date('Y-m-d', $task['date_due']),
                $task['project_id'],
                $task['project_name'],
                $task['assignee_name'] ?: $task['assignee_username'],
            );
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('Id', 'Title', 'Due date', 'Project Id', 'Project name', 'Assignee'))
            ->setRows($rows)
            ->render();
    }
}
