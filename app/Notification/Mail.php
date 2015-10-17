<?php

namespace Kanboard\Notification;

use Kanboard\Core\Base;
use Kanboard\Model\Task;
use Kanboard\Model\File;
use Kanboard\Model\Comment;
use Kanboard\Model\Subtask;

/**
 * Email Notification
 *
 * @package  notification
 * @author   Frederic Guillot
 */
class Mail extends Base implements NotificationInterface
{
    /**
     * Send notification to a user
     *
     * @access public
     * @param  array     $user
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function notifyUser(array $user, $event_name, array $event_data)
    {
        if (! empty($user['email'])) {
            $this->emailClient->send(
                $user['email'],
                $user['name'] ?: $user['username'],
                $this->getMailSubject($event_name, $event_data),
                $this->getMailContent($event_name, $event_data)
            );
        }
    }

    /**
     * Send notification to a project
     *
     * @access public
     * @param  array     $project
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function notifyProject(array $project, $event_name, array $event_data)
    {
    }

    /**
     * Get the mail content for a given template name
     *
     * @access public
     * @param  string    $event_name  Event name
     * @param  array     $event_data  Event data
     * @return string
     */
    public function getMailContent($event_name, array $event_data)
    {
        return $this->template->render(
            'notification/'.str_replace('.', '_', $event_name),
            $event_data + array('application_url' => $this->config->get('application_url'))
        );
    }

    /**
     * Get the mail subject for a given template name
     *
     * @access public
     * @param  string    $event_name  Event name
     * @param  array     $event_data  Event data
     * @return string
     */
    public function getMailSubject($event_name, array $event_data)
    {
        switch ($event_name) {
            case File::EVENT_CREATE:
                $subject = $this->getStandardMailSubject(e('New attachment'), $event_data);
                break;
            case Comment::EVENT_CREATE:
                $subject = $this->getStandardMailSubject(e('New comment'), $event_data);
                break;
            case Comment::EVENT_UPDATE:
                $subject = $this->getStandardMailSubject(e('Comment updated'), $event_data);
                break;
            case Subtask::EVENT_CREATE:
                $subject = $this->getStandardMailSubject(e('New subtask'), $event_data);
                break;
            case Subtask::EVENT_UPDATE:
                $subject = $this->getStandardMailSubject(e('Subtask updated'), $event_data);
                break;
            case Task::EVENT_CREATE:
                $subject = $this->getStandardMailSubject(e('New task'), $event_data);
                break;
            case Task::EVENT_UPDATE:
                $subject = $this->getStandardMailSubject(e('Task updated'), $event_data);
                break;
            case Task::EVENT_CLOSE:
                $subject = $this->getStandardMailSubject(e('Task closed'), $event_data);
                break;
            case Task::EVENT_OPEN:
                $subject = $this->getStandardMailSubject(e('Task opened'), $event_data);
                break;
            case Task::EVENT_MOVE_COLUMN:
                $subject = $this->getStandardMailSubject(e('Column change'), $event_data);
                break;
            case Task::EVENT_MOVE_POSITION:
                $subject = $this->getStandardMailSubject(e('Position change'), $event_data);
                break;
            case Task::EVENT_MOVE_SWIMLANE:
                $subject = $this->getStandardMailSubject(e('Swimlane change'), $event_data);
                break;
            case Task::EVENT_ASSIGNEE_CHANGE:
                $subject = $this->getStandardMailSubject(e('Assignee change'), $event_data);
                break;
            case Task::EVENT_OVERDUE:
                $subject = e('[%s] Overdue tasks', $event_data['project_name']);
                break;
            default:
                $subject = e('Notification');
        }

        return $subject;
    }

    /**
     * Get the mail subject for a given label
     *
     * @access private
     * @param  string    $label       Label
     * @param  array     $data        Template data
     * @return string
     */
    private function getStandardMailSubject($label, array $data)
    {
        return sprintf('[%s][%s] %s (#%d)', $data['task']['project_name'], $label, $data['task']['title'], $data['task']['id']);
    }
}
