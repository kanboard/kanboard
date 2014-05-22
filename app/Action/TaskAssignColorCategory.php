<?php

namespace Action;

use Model\Task;

/**
 * Assign a color to a specific category
 *
 * @package action
 * @author  Frederic Guillot
 */
class TaskAssignColorCategory extends Base
{
    /**
     * Task model
     *
     * @accesss private
     * @var \Model\Task
     */
    private $task;

    /**
     * Constructor
     *
     * @access public
     * @param  integer  $project_id  Project id
     * @param  \Model\Task     $task        Task model instance
     */
    public function __construct($project_id, Task $task)
    {
        parent::__construct($project_id);
        $this->task = $task;
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
            'color_id' => t('Color'),
            'category_id' => t('Category'),
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
            'category_id',
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
        if ($data['category_id'] == $this->getParam('category_id')) {

            $this->task->update(array(
                'id' => $data['task_id'],
                'color_id' => $this->getParam('color_id'),
            ));

            return true;
        }

        return false;
    }
}
