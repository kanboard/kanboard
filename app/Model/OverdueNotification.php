<?php

namespace Kanboard\Model;

use Symfony\Component\Console\Input\InputInterface;
use Kanboard\Model\ProjectPermission;
/**
 * Task Overdue Notification model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class OverdueNotification extends Base
{
    /**
     * Send overdue tasks
     *
     * @access public
     */
    public function sendOverdueTaskNotifications(InputInterface $input)
    {
        $tasks = $this->taskFinder->getOverdueTasks();

        if ($input->getOption('group')) {
            $user_tasks = array();
            $user_by_task = array();
            $projects = array();

            foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
                $users = $this->userNotification->getUsersWithNotificationEnabled($project_id);

                foreach ($users as $user) {
                    foreach ($project_tasks as $task) {
                        if ($this->userNotificationFilter->shouldReceiveNotification($user, array('task' => $task))) {
                            $user_tasks[$user['id']][] = $task;
                            $user_by_task[$user['id']][] = $user;
                            $projects[$user['id']][$task['project_id']] = $task['project_name'];
                        }
                    }
                }
            }

            if (! empty($user_tasks)) {
                $this->sendGroupUserOverdueTaskNotifications($user_tasks, $user_by_task, $projects);
            }
        } else {
            foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
                $users = $this->userNotification->getUsersWithNotificationEnabled($project_id);

                foreach ($users as $user) {
                    $this->sendUserOverdueTaskNotifications($user, $project_tasks);
                }
            }
        }

        if ($input->getOption('admin')) {
            $overdue_tasks = array();
            $projects = array();
            $project_managers = array();

            foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {
                $users = $this->userNotification->getUsersWithNotificationEnabled($project_id);

                foreach ($project_tasks as $task) {
                    $managers = ProjectPermission::getManagers($task['project_id']);

                    $overdue_tasks[$project_id][] = $task;
                    $projects[$task['project_id']] = $task['project_name'];
                    $project_managers[$task['project_id']] = $managers;
                }
            }

            if (! empty($overdue_tasks)) {
                $this->sendOverdueTaskNotificationsToAdmin($overdue_tasks, $projects, $project_managers);
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
     * Send groups overdue tasks for a given user
     *
     * @access public
     * @param  array   $user_tasks
     * @param  array   $user_by_task
     * @param  array   $projects
     */
    public function sendGroupUserOverdueTaskNotifications(array $user_tasks, array $user_by_task, array $projects)
    {
        foreach ($user_tasks as $user_id => $tasks) {
            $this->userNotification->sendUserNotification(
                $user_by_task[$user_id][0],
                Task::EVENT_OVERDUE,
                array('tasks' => $tasks, 'project_name' => implode(", ", $projects[$user_id]))
            );
        }
    }

    /**
     * Send overdue tasks to project admin
     *
     * @access public
     * @param  array   $overdue_tasks
     * @param  array   $projects
     * @param  array   $project_managers
     */
    public function sendOverdueTaskNotificationsToAdmin(array $overdue_tasks, array $projects, array $project_managers)
    {
        foreach ($project_managers as $project_id => $managers) {
            foreach ($managers as $manager_id => $manager) {
                $user = User::getById($manager_id);

                $this->userNotification->sendUserNotification(
                    $user,
                    Task::EVENT_OVERDUE,
                    array('tasks' => $overdue_tasks[$project_id], 'project_name' => $projects[$project_id])
                );
            }
        }
    }
}
