<?php

namespace Model;

use Core\Translator;

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
     * User filters
     *
     * @var integer
     */
    const FILTER_NONE      = 1;
    const FILTER_ASSIGNEE  = 2;
    const FILTER_CREATOR   = 3;
    const FILTER_BOTH      = 4;

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
            if ($this->notification->shouldReceiveNotification($user, array('task' => $task))) {
                $user_tasks[] = $task;
            }
        }

        if (! empty($user_tasks)) {
            $this->sendEmailNotification(
                $user,
                Task::EVENT_OVERDUE,
                array('tasks' => $user_tasks, 'project_name' => $tasks[0]['project_name'])
            );
        }
    }

    /**
     * Send notifications to people
     *
     * @access public
     * @param  string $event_name
     * @param  array  $event_data
     */
    public function sendNotifications($event_name, array $event_data)
    {
        $logged_user_id = $this->userSession->isLogged() ? $this->userSession->getId() : 0;
        $users = $this->notification->getUsersWithNotificationEnabled($event_data['task']['project_id'], $logged_user_id);

        foreach ($users as $user) {
            if ($this->shouldReceiveNotification($user, $event_data)) {
                $this->sendEmailNotification($user, $event_name, $event_data);
            }
        }

        // Restore locales
        $this->config->setupTranslations();
    }

    /**
     * Send email notification to someone
     *
     * @access public
     * @param  array     $user        User
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function sendEmailNotification(array $user, $event_name, array $event_data)
    {
        // Use the user language otherwise use the application language (do not use the session language)
        if (! empty($user['language'])) {
            Translator::load($user['language']);
        }
        else {
            Translator::load($this->config->get('application_language', 'en_US'));
        }

        $this->emailClient->send(
            $user['email'],
            $user['name'] ?: $user['username'],
            $this->getMailSubject($event_name, $event_data),
            $this->getMailContent($event_name, $event_data)
        );
    }

    /**
     * Return true if the user should receive notification
     *
     * @access public
     * @param  array  $user
     * @param  array  $event_data
     * @return boolean
     */
    public function shouldReceiveNotification(array $user, array $event_data)
    {
        $filters = array(
            'filterNone',
            'filterAssignee',
            'filterCreator',
            'filterBoth',
        );

        foreach ($filters as $filter) {
            if ($this->$filter($user, $event_data)) {
                return $this->filterProject($user, $event_data);
            }
        }

        return false;
    }

    /**
     * Return true if the user will receive all notifications
     *
     * @access public
     * @param  array  $user
     * @return boolean
     */
    public function filterNone(array $user)
    {
        return $user['notifications_filter'] == self::FILTER_NONE;
    }

    /**
     * Return true if the user is the assignee and selected the filter "assignee"
     *
     * @access public
     * @param  array  $user
     * @param  array  $event_data
     * @return boolean
     */
    public function filterAssignee(array $user, array $event_data)
    {
        return $user['notifications_filter'] == self::FILTER_ASSIGNEE && $event_data['task']['owner_id'] == $user['id'];
    }

    /**
     * Return true if the user is the creator and enabled the filter "creator"
     *
     * @access public
     * @param  array  $user
     * @param  array  $event_data
     * @return boolean
     */
    public function filterCreator(array $user, array $event_data)
    {
        return $user['notifications_filter'] == self::FILTER_CREATOR && $event_data['task']['creator_id'] == $user['id'];
    }

    /**
     * Return true if the user is the assignee or the creator and selected the filter "both"
     *
     * @access public
     * @param  array  $user
     * @param  array  $event_data
     * @return boolean
     */
    public function filterBoth(array $user, array $event_data)
    {
        return $user['notifications_filter'] == self::FILTER_BOTH &&
               ($event_data['task']['creator_id'] == $user['id'] || $event_data['task']['owner_id'] == $user['id']);
    }

    /**
     * Return true if the user want to receive notification for the selected project
     *
     * @access public
     * @param  array  $user
     * @param  array  $event_data
     * @return boolean
     */
    public function filterProject(array $user, array $event_data)
    {
        $projects = $this->db->table(self::TABLE)->eq('user_id', $user['id'])->findAllByColumn('project_id');

        if (! empty($projects)) {
            return in_array($event_data['task']['project_id'], $projects);
        }

        return true;
    }

    /**
     * Get a list of people with notifications enabled
     *
     * @access public
     * @param  integer   $project_id        Project id
     * @param  integer   $exclude_user_id   User id to exclude
     * @return array
     */
    public function getUsersWithNotificationEnabled($project_id, $exclude_user_id = 0)
    {
        if ($this->projectPermission->isEverybodyAllowed($project_id)) {

            return $this->db
                        ->table(User::TABLE)
                        ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name', User::TABLE.'.email', User::TABLE.'.language', User::TABLE.'.notifications_filter')
                        ->eq('notifications_enabled', '1')
                        ->neq('email', '')
                        ->neq(User::TABLE.'.id', $exclude_user_id)
                        ->findAll();
        }

        return $this->db
            ->table(ProjectPermission::TABLE)
            ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name', User::TABLE.'.email', User::TABLE.'.language', User::TABLE.'.notifications_filter')
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->eq('notifications_enabled', '1')
            ->neq('email', '')
            ->neq(User::TABLE.'.id', $exclude_user_id)
            ->findAll();
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
                'notifications_enabled' => '1',
                'notifications_filter'  => empty($values['notifications_filter']) ? self::FILTER_BOTH : $values['notifications_filter'],
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
        $values = $this->db->table(User::TABLE)->eq('id', $user_id)->columns('notifications_enabled', 'notifications_filter')->findOne();
        $projects = $this->db->table(self::TABLE)->eq('user_id', $user_id)->findAllByColumn('project_id');

        foreach ($projects as $project_id) {
            $values['project_'.$project_id] = true;
        }

        return $values;
    }
}
