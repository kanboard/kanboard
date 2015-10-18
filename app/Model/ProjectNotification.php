<?php

namespace Kanboard\Model;

/**
 * Project Notification
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectNotification extends Base
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
        $project = $this->project->getById($project_id);

        $types = array_merge(
            $this->projectNotificationType->getHiddenTypes(),
            $this->projectNotificationType->getSelectedTypes($project_id)
        );

        foreach ($types as $type) {
            $this->projectNotificationType->getType($type)->notifyProject($project, $event_name, $event_data);
        }
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
        $this->projectNotificationType->saveSelectedTypes($project_id, $types);

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
            'notification_types' => $this->projectNotificationType->getSelectedTypes($project_id),
        );
    }
}
