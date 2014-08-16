<?php

namespace Event;

use Event\BaseNotificationListener;

/**
 * SubTask notification listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class SubTaskNotificationListener extends BaseNotificationListener
{
    /**
     * Fetch data for the mail template
     *
     * @access public
     * @param  array    $data    Event data
     * @return array
     */
    public function getTemplateData(array $data)
    {
        $values = array();
        $values['subtask'] = $this->notification->subtask->getById($data['id'], true);
        $values['task'] = $this->notification->task->getById($data['task_id'], true);

        return $values;
    }
}
