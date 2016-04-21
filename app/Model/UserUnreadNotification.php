<?php

namespace Kanboard\Model;

/**
 * User Unread Notification
 *
 * @package  model
 * @author   Frederic Guillot
 */
class UserUnreadNotification extends Base
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
     * @param  integer   $user_id
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function create($user_id, $event_name, array $event_data)
    {
        $task_id = null;
        if(strpos(json_encode($event_data), 'comment')) {
            $task_id = $event_data['comment']['task_id'];
        } elseif(strpos(json_encode($event_data), 'task.overdue')) {
            if (count($event_data['tasks']) == 1) {
                $task_id = $event_data['tasks'][0]['id'];
            }
        } elseif(isset($event_data['task']['id'])) {
            $task_id = $event_data['task']['id'];
        }

        $this->db->table(self::TABLE)->insert(array(
            'user_id' => $user_id,
            'task_id' => $task_id,
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
     * @param  string $group_by
     * @return array
     */
    public function getAll($user_id, $group_by='date_creation')
    {
        $grouped_events = array();
        $events = $this->db->table(self::TABLE)->eq('user_id', $user_id)->asc('date_creation')->findAll();

        foreach ($events as &$event) {
            $event['event_data'] = json_decode($event['event_data'], true);
            $event['title'] = $this->notification->getTitleWithoutAuthor($event['event_name'], $event['event_data']);

            if($group_by == 'date_creation') {
                $grouped_events[strtotime(date("Y-m-d", $event['date_creation']))][] = $event;
            } else {
                $grouped_events[$event['task_id']][] = $event;
            }
        }

        ksort($grouped_events);

        return $grouped_events;
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
}
