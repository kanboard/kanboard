<?php

namespace Event;

use Event\BaseNotificationListener;

/**
 * Comment notification listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class CommentNotificationListener extends BaseNotificationListener
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
        $values['comment'] = $this->notification->comment->getById($data['id']);
        $values['task'] = $this->notification->task->getById($data['task_id'], true);

        return $values;
    }
}
