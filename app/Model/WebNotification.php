<?php

namespace Model;

use Core\NotificationInterface;

/**
 * Web Notification model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class WebNotification extends Base implements NotificationInterface
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'user_has_unread_notifications';

    /**
     * Add unread notification to someone
     *
     * @access public
     * @param  array     $user
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function send(array $user, $event_name, array $event_data)
    {
        $this->db->table(self::TABLE)->insert(array(
            'user_id' => $user['id'],
            'date_creation' => time(),
            'event_name' => $event_name,
            'event_data' => json_encode($event_data),
        ));
    }

    /**
     * Get all notifications for a user
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getAll($user_id)
    {
        $events = $this->db->table(self::TABLE)->eq('user_id', $user_id)->asc('date_creation')->findAll();

        foreach ($events as &$event) {
            $event['event_data'] = json_decode($event['event_data'], true);
            $event['title'] = $this->getTitleFromEvent($event['event_name'], $event['event_data']);
        }

        return $events;
    }

    /**
     * Mark a notification as read
     *
     * @access public
     * @param  integer $user_id
     * @param  integer $notification_id
     * @return boolean
     */
    public function markAsRead($user_id, $notification_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $notification_id)->eq('user_id', $user_id)->remove();
    }

    /**
     * Mark all notifications as read for a user
     *
     * @access public
     * @param  integer $user_id
     * @return boolean
     */
    public function markAllAsRead($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->remove();
    }

    /**
     * Return true if the user as unread notifications
     *
     * @access public
     * @param  integer $user_id
     * @return boolean
     */
    public function hasNotifications($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->exists();
    }

    /**
     * Get title from event
     *
     * @access public
     * @param  string  $event_name
     * @param  array  $event_data
     * @return string
     */
    public function getTitleFromEvent($event_name, array $event_data)
    {
        switch ($event_name) {
            case File::EVENT_CREATE:
                $title = t('New attachment on task #%d: %s', $event_data['file']['task_id'], $event_data['file']['name']);
                break;
            case Comment::EVENT_CREATE:
                $title = t('New comment on task #%d', $event_data['comment']['task_id']);
                break;
            case Comment::EVENT_UPDATE:
                $title = t('Comment updated on task #%d', $event_data['comment']['task_id']);
                break;
            case Subtask::EVENT_CREATE:
                $title = t('New subtask on task #%d', $event_data['subtask']['task_id']);
                break;
            case Subtask::EVENT_UPDATE:
                $title = t('Subtask updated on task #%d', $event_data['subtask']['task_id']);
                break;
            case Task::EVENT_CREATE:
                $title = t('New task #%d: %s', $event_data['task']['id'], $event_data['task']['title']);
                break;
            case Task::EVENT_UPDATE:
                $title = t('Task updated #%d', $event_data['task']['id']);
                break;
            case Task::EVENT_CLOSE:
                $title = t('Task #%d closed', $event_data['task']['id']);
                break;
            case Task::EVENT_OPEN:
                $title = t('Task #%d opened', $event_data['task']['id']);
                break;
            case Task::EVENT_MOVE_COLUMN:
                $title = t('Column changed for task #%d', $event_data['task']['id']);
                break;
            case Task::EVENT_MOVE_POSITION:
                $title = t('New position for task #%d', $event_data['task']['id']);
                break;
            case Task::EVENT_MOVE_SWIMLANE:
                $title = t('Swimlane changed for task #%d', $event_data['task']['id']);
                break;
            case Task::EVENT_ASSIGNEE_CHANGE:
                $title = t('Assignee changed on task #%d', $event_data['task']['id']);
                break;
            case Task::EVENT_OVERDUE:
                $nb = count($event_data['tasks']);
                $title = $nb > 1 ? t('%d overdue tasks', $nb) : t('Task #%d is overdue', $event_data['tasks'][0]['id']);
                break;
            default:
                $title = e('Notification');
        }

        return $title;
    }
}
