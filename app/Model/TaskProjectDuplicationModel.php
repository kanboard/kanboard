<?php

namespace Kanboard\Model;

/**
 * Task Project Duplication
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskProjectDuplicationModel extends TaskDuplicationModel
{
    /**
     * Duplicate a task to another project
     *
     * @access public
     * @param  integer    $task_id
     * @param  integer    $project_id
     * @param  integer    $swimlane_id
     * @param  integer    $column_id
     * @param  integer    $category_id
     * @param  integer    $owner_id
     * @return boolean|integer
     */
    public function duplicateToProject($task_id, $project_id, $swimlane_id = null, $column_id = null, $category_id = null, $owner_id = null)
    {
        $values = $this->prepare($task_id, $project_id, $swimlane_id, $column_id, $category_id, $owner_id);
        $this->checkDestinationProjectValues($values);
        $new_task_id = $this->save($task_id, $values);

        if ($new_task_id !== false) {
            $this->tagDuplicationModel->duplicateTaskTagsToAnotherProject($task_id, $new_task_id, $project_id);
            $this->taskLinkModel->create($new_task_id, $task_id, 4);
        }

        return $new_task_id;
    }

    /**
     * Prepare values before duplication
     *
     * @access protected
     * @param  integer $task_id
     * @param  integer $project_id
     * @param  integer $swimlane_id
     * @param  integer $column_id
     * @param  integer $category_id
     * @param  integer $owner_id
     * @return array
     */
    protected function prepare($task_id, $project_id, $swimlane_id, $column_id, $category_id, $owner_id)
    {
        $values = $this->copyFields($task_id);
        $values['project_id'] = $project_id;
        $values['column_id'] = $column_id !== null ? $column_id : $values['column_id'];
        $values['swimlane_id'] = $swimlane_id !== null ? $swimlane_id : $values['swimlane_id'];
        $values['category_id'] = $category_id !== null ? $category_id : $values['category_id'];
        $values['owner_id'] = $owner_id !== null ? $owner_id : $values['owner_id'];
        return $values;
    }
}
