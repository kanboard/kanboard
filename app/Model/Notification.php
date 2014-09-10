<?php

namespace Model;

use Core\Translator;
use Core\Template;
use Event\TaskNotificationListener;
use Event\CommentNotificationListener;
use Event\FileNotificationListener;
use Event\SubTaskNotificationListener;
use Swift_Message;
use Swift_Mailer;

/**
 * Notification model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Notification extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'user_has_notifications';

    /**
     * Get the list of users to send the notification for a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getUsersList($project_id)
    {
        $users = $this->db->table(User::TABLE)
                          ->columns('id', 'username', 'name', 'email')
                          ->eq('notifications_enabled', '1')
                          ->neq('email', '')
                          ->findAll();

        foreach ($users as $index => $user) {

            $projects = $this->db->table(self::TABLE)
                                 ->eq('user_id', $user['id'])
                                 ->findAllByColumn('project_id');

            // The user have selected only some projects
            if (! empty($projects)) {

                // If the user didn't select this project we remove that guy from the list
                if (! in_array($project_id, $projects)) {
                    unset($users[$index]);
                }
            }
        }

        return $users;
    }

    /**
     * Attach events
     *
     * @access public
     */
    public function attachEvents()
    {
        $this->event->attach(File::EVENT_CREATE, new FileNotificationListener($this, 'notification_file_creation'));

        $this->event->attach(Comment::EVENT_CREATE, new CommentNotificationListener($this, 'notification_comment_creation'));
        $this->event->attach(Comment::EVENT_UPDATE, new CommentNotificationListener($this, 'notification_comment_update'));

        $this->event->attach(SubTask::EVENT_CREATE, new SubTaskNotificationListener($this, 'notification_subtask_creation'));
        $this->event->attach(SubTask::EVENT_UPDATE, new SubTaskNotificationListener($this, 'notification_subtask_update'));

        $this->event->attach(Task::EVENT_CREATE, new TaskNotificationListener($this, 'notification_task_creation'));
        $this->event->attach(Task::EVENT_UPDATE, new TaskNotificationListener($this, 'notification_task_update'));
        $this->event->attach(Task::EVENT_CLOSE, new TaskNotificationListener($this, 'notification_task_close'));
        $this->event->attach(Task::EVENT_OPEN, new TaskNotificationListener($this, 'notification_task_open'));
    }

    /**
     * Send the email notifications
     *
     * @access public
     * @param  string    $template    Template name
     * @param  array     $users       List of users
     * @param  array     $data        Template data
     */
    public function sendEmails($template, array $users, array $data)
    {
        $transport = $this->registry->shared('mailer');
        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance()
                        ->setSubject($this->getMailSubject($template, $data))
                        ->setFrom(array(MAIL_FROM => 'Kanboard'))
                        //->setTo(array($user['email'] => $user['name']))
                        ->setBody($this->getMailContent($template, $data), 'text/html');

        foreach ($users as $user) {
            $message->setTo(array($user['email'] => $user['name'] ?: $user['username']));
            $mailer->send($message);
        }
    }

    /**
     * Get the mail subject for a given template name
     *
     * @access public
     * @param  string    $template    Template name
     * @param  array     $data        Template data
     */
    public function getMailSubject($template, array $data)
    {
        switch ($template) {
            case 'notification_file_creation':
                $subject = e('[%s][New attachment] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_comment_creation':
                $subject = e('[%s][New comment] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_comment_update':
                $subject = e('[%s][Comment updated] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_subtask_creation':
                $subject = e('[%s][New subtask] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_subtask_update':
                $subject = e('[%s][Subtask updated] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_task_creation':
                $subject = e('[%s][New task] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_task_update':
                $subject = e('[%s][Task updated] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_task_close':
                $subject = e('[%s][Task closed] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_task_open':
                $subject = e('[%s][Task opened] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'notification_task_due':
                $subject = e('[%s][Due tasks]', $data['project']);
                break;
            default:
                $subject = e('[Kanboard] Notification');
        }

        return $subject;
    }

    /**
     * Get the mail content for a given template name
     *
     * @access public
     * @param  string    $template    Template name
     * @param  array     $data        Template data
     */
    public function getMailContent($template, array $data)
    {
        $tpl = new Template;
        return $tpl->load($template, $data);
    }

    /**
     * Save settings for the given user
     *
     * @access public
     * @param  integer   $user_id   User id
     * @param  array     $values    Form values
     */
    public function saveSettings($user_id, array $values)
    {
        // Delete all selected projects
        $this->db->table(self::TABLE)->eq('user_id', $user_id)->remove();

        if (isset($values['notifications_enabled']) && $values['notifications_enabled'] == 1) {

            // Activate notifications
            $this->db->table(User::TABLE)->eq('id', $user_id)->update(array(
                'notifications_enabled' => '1'
            ));

            // Save selected projects
            if (! empty($values['projects'])) {

                foreach ($values['projects'] as $project_id => $checkbox_value) {
                    $this->db->table(self::TABLE)->insert(array(
                        'user_id' => $user_id,
                        'project_id' => $project_id,
                    ));
                }
            }
        }
        else {

            // Disable notifications
            $this->db->table(User::TABLE)->eq('id', $user_id)->update(array(
                'notifications_enabled' => '0'
            ));
        }
    }

    /**
     * Read user settings to display the form
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return array
     */
    public function readSettings($user_id)
    {
        $values = array();
        $values['notifications_enabled'] = $this->db->table(User::TABLE)->eq('id', $user_id)->findOneColumn('notifications_enabled');

        $projects = $this->db->table(self::TABLE)->eq('user_id', $user_id)->findAllByColumn('project_id');

        foreach ($projects as $project_id) {
            $values['project_'.$project_id] = true;
        }

        return $values;
    }
}
