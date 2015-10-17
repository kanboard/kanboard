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

        foreach ($this->projectNotificationType->getSelectedTypes($project_id) as $type) {
            $this->projectNotificationType->getType($type)->notifyProject($project, $event_name, $event_data);
        }
    }
}
