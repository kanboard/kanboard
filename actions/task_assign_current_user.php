<?php

namespace Action;

require_once __DIR__.'/base.php';

class TaskAssignCurrentUser extends Base
{
    public function __construct($project_id, \Model\Task $task, \Model\Acl $acl)
    {
        parent::__construct($project_id);
        $this->task = $task;
        $this->acl = $acl;
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

            $this->task->update(array(
                'id' => $data['task_id'],
                'owner_id' => $this->acl->getUserId(),
            ));

            return true;
        }

        return false;
    }
}
