<?php

namespace Action;

require_once __DIR__.'/base.php';

class TaskAssignColorUser extends Base
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
            'color_id' => t('Color'),
            'user_id' => t('Assignee'),
        );
    }

    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'owner_id',
            'column_id',
        );
    }

    public function doAction(array $data)
    {
        if ($data['column_id'] == $this->getParam('column_id') && $data['owner_id'] == $this->getParam('user_id')) {

            $this->task->update(array(
                'id' => $data['task_id'],
                'color_id' => $this->getParam('color_id'),
            ));

            return true;
        }

        return false;
    }
}
