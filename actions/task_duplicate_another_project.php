<?php

namespace Action;

require_once __DIR__.'/base.php';

class TaskDuplicateAnotherProject extends Base
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
            'project_id' => t('Project'),
        );
    }

    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'column_id',
            'project_id',
        );
    }

    public function doAction(array $data)
    {
        if ($data['column_id'] == $this->getParam('column_id') && $data['project_id'] != $this->getParam('project_id')) {

            $this->task->duplicateToAnotherProject($data['task_id'], $this->getParam('project_id'));

            return true;
        }

        return false;
    }
}
