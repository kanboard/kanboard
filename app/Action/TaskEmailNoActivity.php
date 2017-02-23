<?php

namespace Kanboard\Action;

use Kanboard\Model\TaskModel;

/**
 * Email a task with no activity
 *
 * @package Kanboard\Action
 * @author  Frederic Guillot
 */
class TaskEmailNoActivity extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Send email when there is no activity on a task');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            TaskModel::EVENT_DAILY_CRONJOB,
        );
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
            'user_id' => t('User that will receive the email'),
            'subject' => t('Email subject'),
            'duration' => t('Duration in days'),
        );
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array('tasks');
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        return count($data['tasks']) > 0;
    }

    /**
     * Execute the action (move the task to another column)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $results = array();
        $max = $this->getParam('duration') * 86400;
        $user = $this->userModel->getById($this->getParam('user_id'));

        if (! empty($user['email'])) {
            foreach ($data['tasks'] as $task) {
                $duration = time() - $task['date_modification'];

                if ($duration > $max) {
                    $results[] = $this->sendEmail($task['id'], $user);
                }
            }
        }

        return in_array(true, $results, true);
    }

    /**
     * Send email
     *
     * @access private
     * @param  integer $task_id
     * @param  array   $user
     * @return boolean
     */
    private function sendEmail($task_id, array $user)
    {
        $task = $this->taskFinderModel->getDetails($task_id);

        $this->emailClient->send(
            $user['email'],
            $user['name'] ?: $user['username'],
            $this->getParam('subject'),
            $this->template->render('notification/task_create', array('task' => $task))
        );

        return true;
    }
}
