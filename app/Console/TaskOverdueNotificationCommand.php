<?php

namespace Kanboard\Console;

use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Core\Security\Role;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskOverdueNotificationCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('notification:overdue-tasks')
            ->setDescription('Send notifications for overdue tasks')
            ->addOption('show', null, InputOption::VALUE_NONE, 'Show sent overdue tasks')
            ->addOption('group', null, InputOption::VALUE_NONE, 'Group all overdue tasks for one user (from all projects) in one email')
            ->addOption('manager', null, InputOption::VALUE_NONE, 'Send all overdue tasks to project manager(s) in one email')
            ->addOption('project', 'p', InputOption::VALUE_REQUIRED, 'Send notifications only the given project')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('project')) {
            $tasks = $this->taskFinderModel->getOverdueTasksQuery()
                ->beginOr()
                ->eq(TaskModel::TABLE.'.project_id', $input->getOption('project'))
                ->eq(ProjectModel::TABLE.'.identifier', $input->getOption('project'))
                ->closeOr()
                ->findAll();
        } else {
            $tasks = $this->taskFinderModel->getOverdueTasks();
        }

        if ($input->getOption('group')) {
            $tasks = $this->sendGroupOverdueTaskNotifications($tasks);
        } elseif ($input->getOption('manager')) {
            $tasks = $this->sendOverdueTaskNotificationsToManagers($tasks);
        } else {
            $tasks = $this->sendOverdueTaskNotifications($tasks);
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
                date('Y-m-d H:i', $task['date_due']),
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
     * Send all overdue tasks for one user in one email
     *
     * @access public
     * @param  array $tasks
     * @return array
     */
    public function sendGroupOverdueTaskNotifications(array $tasks)
    {
        foreach ($this->groupByColumn($tasks, 'owner_id') as $user_tasks) {
            $users = $this->userNotificationModel->getUsersWithNotificationEnabled($user_tasks[0]['project_id']);

            foreach ($users as $user) {
                $this->sendUserOverdueTaskNotifications($user, $user_tasks);
            }
        }

        return $tasks;
    }

    /**
     * Send all overdue tasks in one email to project manager(s)
     *
     * @access public
     * @param  array $tasks
     * @return array
     */
    public function sendOverdueTaskNotificationsToManagers(array $tasks)
    {
        foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
            $users = $this->userNotificationModel->getUsersWithNotificationEnabled($project_id);
            $managers = array();

            foreach ($users as $user) {
                $role = $this->projectUserRoleModel->getUserRole($project_id, $user['id']);
                if ($role == Role::PROJECT_MANAGER) {
                    $managers[] = $user;
                }
            }

            foreach ($managers as $manager) {
                $this->sendUserOverdueTaskNotificationsToManagers($manager, $project_tasks);
            }
        }

        return $tasks;
    }

    /**
     * Send overdue tasks
     *
     * @access public
     * @param  array $tasks
     * @return array
     */
    public function sendOverdueTaskNotifications(array $tasks)
    {
        foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
            $users = $this->userNotificationModel->getUsersWithNotificationEnabled($project_id);

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
        $project_names = array();

        foreach ($tasks as $task) {
            if ($this->userNotificationFilterModel->shouldReceiveNotification($user, array('task' => $task))) {
                $user_tasks[] = $task;
                $project_names[$task['project_id']] = $task['project_name'];
            }
        }

        if (! empty($user_tasks)) {
            $this->userNotificationModel->sendUserNotification(
                $user,
                TaskModel::EVENT_OVERDUE,
                array('tasks' => $user_tasks, 'project_name' => implode(', ', $project_names))
            );
        }
    }

    /**
     * Send overdue tasks for a project manager(s)
     *
     * @access public
     * @param  array   $manager
     * @param  array   $tasks
     */
    public function sendUserOverdueTaskNotificationsToManagers(array $manager, array $tasks)
    {
        $this->userNotificationModel->sendUserNotification(
            $manager,
            TaskModel::EVENT_OVERDUE,
            array('tasks' => $tasks, 'project_name' => $tasks[0]['project_name'])
        );
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
