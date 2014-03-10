<?php

namespace Action;

require_once __DIR__.'/base.php';

class TaskClose extends Base
{
    public function __construct($project_id, \Model\Task $task)
    {
        parent::__construct($project_id);
        $this->task = $task;
    }

    public function getActionRequiredParameters()
    {
        return array(
            'column_id' => t('Column'),
        );
    }

    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'column_id',
        );
    }

    public function doAction(array $data)
    {
        if ($data['column_id'] == $this->getParam('column_id')) {
            $this->task->close($data['task_id']);
            return true;
        }

        return false;
    }
}
