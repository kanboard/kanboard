<?php

namespace Model;

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
    public function sendOverdueTaskNotifications()
    {
        $tasks = $this->taskFinder->getOverdueTasks();

        foreach ($this->groupByColumn($tasks, 'project_id') as $project_id => $project_tasks) {

            // Get the list of users that should receive notifications for each projects
            $users = $this->notification->getUsersWithNotificationEnabled($project_id);

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
            if ($this->notificationFilter->shouldReceiveNotification($user, array('task' => $task))) {
                $user_tasks[] = $task;
            }
        }

        if (! empty($user_tasks)) {
            $this->notification->sendUserNotification(
                $user,
                Task::EVENT_OVERDUE,
                array('tasks' => $user_tasks, 'project_name' => $tasks[0]['project_name'])
            );
        }
    }
}
