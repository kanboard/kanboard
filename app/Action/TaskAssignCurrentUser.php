<?php

namespace Action;

use Model\Task;
use Model\Acl;

/**
 * Assign a task to the logged user
 *
 * @package action
 * @author  Frederic Guillot
 */
class TaskAssignCurrentUser extends Base
{
    /**
     * Task model
     *
     * @accesss private
     * @var \Model\Task
     */
    private $task;

    /**
     * Acl model
     *
     * @accesss private
     * @var \Model\Acl
     */
    private $acl;

    /**
     * Constructor
     *
     * @access public
     * @param  integer  $project_id  Project id
     * @param  \Model\Task     $task        Task model instance
     * @param  \Model\Acl      $acl         Acl model instance
     */
    public function __construct($project_id, Task $task, Acl $acl)
    {
        parent::__construct($project_id);
        $this->task = $task;
        $this->acl = $acl;
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
            'column_id' => t('Column'),
        );
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'column_id',
        );
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
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
