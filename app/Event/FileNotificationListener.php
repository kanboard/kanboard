<?php

namespace Event;

use Event\BaseNotificationListener;

/**
 * File notification listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class FileNotificationListener extends BaseNotificationListener
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
        $values['file'] = $data;
        $values['task'] = $this->notification->task->getDetails($data['task_id']);

        return $values;
    }
}
