<?php

namespace Event;

use Core\Listener;
use Model\SubtaskHistory;

/**
 * Subtask history listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class SubtaskHistoryListener implements Listener
{
    /**
     * Comment History model
     *
     * @accesss private
     * @var \Model\SubtaskHistory
     */
    private $model;

    /**
     * Constructor
     *
     * @access public
     * @param  \Model\SubtaskHistory   $model   Subtask History model instance
     */
    public function __construct(SubtaskHistory $model)
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

        if ($creator_id && isset($data['task_id']) && isset($data['id'])) {

            $task = $this->model->task->getById($data['task_id']);

            $this->model->create(
                $task['project_id'],
                $data['task_id'],
                $data['id'],
                $creator_id,
                $this->model->event->getLastTriggeredEvent(),
                ''
            );
        }

        return false;
    }
}
