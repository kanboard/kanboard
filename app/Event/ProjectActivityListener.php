<?php

namespace Event;

/**
 * Project activity listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class ProjectActivityListener extends Base
{
    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        if (isset($data['task_id'])) {

            $values = $this->getValues($data);

            return $this->projectActivity->createEvent(
                $values['task']['project_id'],
                $values['task']['id'],
                $this->acl->getUserId(),
                $this->container['event']->getLastTriggeredEvent(),
                $values
            );
        }

        return false;
    }

    /**
     * Get event activity data
     *
     * @access private
     * @param  array   $data   Event data dictionary
     * @return array
     */
    private function getValues(array $data)
    {
        $values = array();
        $values['task'] = $this->taskFinder->getDetails($data['task_id']);

        switch ($this->getEventNamespace()) {
            case 'subtask':
                $values['subtask'] = $this->subTask->getById($data['id'], true);
                break;
            case 'comment':
                $values['comment'] = $this->comment->getById($data['id']);
                break;
        }

        return $values;
    }
}
