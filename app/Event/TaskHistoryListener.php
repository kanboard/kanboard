<?php

namespace Event;

use Core\Listener;
use Model\TaskHistory;

/**
 * Task history listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class TaskHistoryListener implements Listener
{
    /**
     * Task History model
     *
     * @accesss private
     * @var \Model\TaskHistory
     */
    private $model;

    /**
     * Constructor
     *
     * @access public
     * @param  \Model\TaskHistory   $model   Task History model instance
     */
    public function __construct(TaskHistory $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        $creator_id = $this->model->acl->getUserId();

        if ($creator_id && isset($data['task_id']) && isset($data['project_id'])) {
            $this->model->create($data['project_id'], $data['task_id'], $creator_id, $this->model->event->getLastTriggeredEvent());
        }

        return false;
    }
}
