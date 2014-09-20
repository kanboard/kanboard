<?php

namespace Event;

use Event\BaseNotificationListener;

/**
 * Task notification listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class TaskNotificationListener extends BaseNotificationListener
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
        $values['task'] = $this->notification->task->getDetails($data['task_id']);

        return $values;
    }
}
