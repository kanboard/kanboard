<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

/**
 * Project Notification
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectNotificationModel extends Base
{
    /**
     * Send notifications
     *
     * @access public
     * @param  integer  $project_id
     * @param  string   $event_name
     * @param  array    $event_data
     */
    public function sendNotifications($project_id, $event_name, array $event_data)
    {
        $project = $this->projectModel->getById($project_id);
        $types = $this->projectNotificationTypeModel->getHiddenTypes();

        if (! $this->hasRestrictedComment($event_data)) {
            $types = array_merge($types, $this->projectNotificationTypeModel->getSelectedTypes($project_id));
        }

        foreach ($types as $type) {
            $this->projectNotificationTypeModel->getType($type)->notifyProject($project, $event_name, $event_data);
        }
    }

    /**
     * Return true if the event contains a comment not visible to standard users
     *
     * Notification types selected for a project are configured by its managers,
     * so they receive only the comments that standard users can see.
     *
     * @access private
     * @param  array $event_data
     * @return bool
     */
    private function hasRestrictedComment(array $event_data)
    {
        return isset($event_data['comment']['visibility']) && $event_data['comment']['visibility'] !== Role::APP_USER;
    }

    /**
     * Save settings for the given project
     *
     * @access public
     * @param  integer   $project_id
     * @param  array     $values
     */
    public function saveSettings($project_id, array $values)
    {
        $this->db->startTransaction();

        $types = empty($values['notification_types']) ? array() : array_keys($values['notification_types']);
        $this->projectNotificationTypeModel->saveSelectedTypes($project_id, $types);

        $this->db->closeTransaction();
    }

    /**
     * Read user settings to display the form
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function readSettings($project_id)
    {
        return array(
            'notification_types' => $this->projectNotificationTypeModel->getSelectedTypes($project_id),
        );
    }
}
