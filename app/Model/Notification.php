<?php

namespace Model;

use Core\Session;
use Core\Translator;
use Core\Template;
use Event\NotificationListener;
use Swift_Message;
use Swift_Mailer;
use Swift_TransportException;

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
     * Get a list of people with notifications enabled
     *
     * @access public
     * @param  integer   $project_id     Project id
     * @param  array     $exlude_users   List of user_id to exclude
     * @return array
     */
    public function getUsersWithNotification($project_id, array $exclude_users = array())
    {
        if ($this->projectPermission->isEverybodyAllowed($project_id)) {

            return $this->db
                        ->table(User::TABLE)
                        ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name', User::TABLE.'.email')
                        ->eq('notifications_enabled', '1')
                        ->neq('email', '')
                        ->notin(User::TABLE.'.id', $exclude_users)
                        ->findAll();
        }

        return $this->db
            ->table(ProjectPermission::TABLE)
            ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name', User::TABLE.'.email')
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->eq('notifications_enabled', '1')
            ->neq('email', '')
            ->notin(User::TABLE.'.id', $exclude_users)
            ->findAll();
    }

    /**
     * Get the list of users to send the notification for a given project
     *
     * @access public
     * @param  integer   $project_id     Project id
     * @param  array     $exlude_users   List of user_id to exclude
     * @return array
     */
    public function getUsersList($project_id, array $exclude_users = array())
    {
        // Exclude the connected user
        if (Session::isOpen()) {
            $exclude_users[] = $this->acl->getUserId();
        }

        $users = $this->getUsersWithNotification($project_id, $exclude_users);

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
        $events = array(
            Task::EVENT_CREATE => 'task_creation',
            Task::EVENT_UPDATE => 'task_update',
            Task::EVENT_CLOSE => 'task_close',
            Task::EVENT_OPEN => 'task_open',
            Task::EVENT_MOVE_COLUMN => 'task_move_column',
            Task::EVENT_MOVE_POSITION => 'task_move_position',
            Task::EVENT_ASSIGNEE_CHANGE => 'task_assignee_change',
            SubTask::EVENT_CREATE => 'subtask_creation',
            SubTask::EVENT_UPDATE => 'subtask_update',
            Comment::EVENT_CREATE => 'comment_creation',
            Comment::EVENT_UPDATE => 'comment_update',
            File::EVENT_CREATE => 'file_creation',
        );

        foreach ($events as $event_name => $template_name) {

            $listener = new NotificationListener($this->container);
            $listener->setTemplate($template_name);

            $this->event->attach($event_name, $listener);
        }
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
        try {
            $mailer = Swift_Mailer::newInstance($this->container['mailer']);

            $message = Swift_Message::newInstance()
                            ->setSubject($this->getMailSubject($template, $data))
                            ->setFrom(array(MAIL_FROM => 'Kanboard'))
                            ->setBody($this->getMailContent($template, $data), 'text/html');

            foreach ($users as $user) {
                $message->setTo(array($user['email'] => $user['name'] ?: $user['username']));
                $mailer->send($message);
            }
        }
        catch (Swift_TransportException $e) {
            $this->container['logger']->addError($e->getMessage());
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
            case 'file_creation':
                $subject = e('[%s][New attachment] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'comment_creation':
                $subject = e('[%s][New comment] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'comment_update':
                $subject = e('[%s][Comment updated] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'subtask_creation':
                $subject = e('[%s][New subtask] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'subtask_update':
                $subject = e('[%s][Subtask updated] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_creation':
                $subject = e('[%s][New task] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_update':
                $subject = e('[%s][Task updated] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_close':
                $subject = e('[%s][Task closed] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_open':
                $subject = e('[%s][Task opened] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_move_column':
                $subject = e('[%s][Column Change] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_move_position':
                $subject = e('[%s][Position Change] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_assignee_change':
                $subject = e('[%s][Assignee Change] %s (#%d)', $data['task']['project_name'], $data['task']['title'], $data['task']['id']);
                break;
            case 'task_due':
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
        return $tpl->load('notification/'.$template, $data + array('application_url' => $this->config->get('application_url')));
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
