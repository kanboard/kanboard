<?php

namespace Kanboard\Console;

use Kanboard\Model\Task;
use Symfony\Component\Console\Helper\Table;
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
        $tasks = $this->sendOverdueTaskNotifications();

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

    /**
     * Send overdue tasks
     *
     * @access public
     */
    public function sendOverdueTaskNotifications()
    {
        $tasks = $this->taskFinder->getOverdueTasks();

        foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
            $users = $this->userNotification->getUsersWithNotificationEnabled($project_id);

            foreach ($users as $user) {
                $this->sendUserOverdueTaskNotifications($user, $project_tasks);
            }
        }

        return $tasks;
    }

    /**
     * Send overdue tasks for a given user
     *
     * @access public
     * @param  array   $user
     * @param  array   $tasks
     */
    public function sendUserOverdueTaskNotifications(array $user, array $tasks)
    {
        $user_tasks = array();

        foreach ($tasks as $task) {
            if ($this->userNotificationFilter->shouldReceiveNotification($user, array('task' => $task))) {
                $user_tasks[] = $task;
            }
        }

        if (! empty($user_tasks)) {
            $this->userNotification->sendUserNotification(
                $user,
                Task::EVENT_OVERDUE,
                array('tasks' => $user_tasks, 'project_name' => $tasks[0]['project_name'])
            );
        }
    }

    /**
     * Group a collection of records by a column
     *
     * @access public
     * @param  array   $collection
     * @param  string  $column
     * @return array
     */
    public function groupByColumn(array $collection, $column)
    {
        $result = array();

        foreach ($collection as $item) {
            $result[$item[$column]][] = $item;
        }

        return $result;
    }
}
