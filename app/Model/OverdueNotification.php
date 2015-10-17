<?php

namespace Kanboard\Model;

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
}
