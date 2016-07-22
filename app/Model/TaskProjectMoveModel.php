<?php

namespace Kanboard\Model;

/**
 * Task Project Move
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskProjectMoveModel extends TaskDuplicationModel
{
    /**
     * Move a task to another project
     *
     * @access public
     * @param  integer    $task_id
     * @param  integer    $project_id
     * @param  integer    $swimlane_id
     * @param  integer    $column_id
     * @param  integer    $category_id
     * @param  integer    $owner_id
     * @return boolean
     */
    public function moveToProject($task_id, $project_id, $swimlane_id = null, $column_id = null, $category_id = null, $owner_id = null)
    {
        $task = $this->taskFinderModel->getById($task_id);
        $values = $this->prepare($project_id, $swimlane_id, $column_id, $category_id, $owner_id, $task);

        $this->checkDestinationProjectValues($values);
        $this->tagDuplicationModel->syncTaskTagsToAnotherProject($task_id, $project_id);

        if ($this->db->table(TaskModel::TABLE)->eq('id', $task_id)->update($values)) {
            $this->queueManager->push($this->taskEventJob->withParams($task_id, array(TaskModel::EVENT_MOVE_PROJECT), $values));
        }

        return true;
    }

    /**
     * Prepare new task values
     *
     * @access protected
     * @param  integer $project_id
     * @param  integer $swimlane_id
     * @param  integer $column_id
     * @param  integer $category_id
     * @param  integer $owner_id
     * @param  array   $task
     * @return array
     */
    protected function prepare($project_id, $swimlane_id, $column_id, $category_id, $owner_id, array $task)
    {
        $values = array();
        $values['is_active'] = 1;
        $values['project_id'] = $project_id;
        $values['column_id'] = $column_id !== null ? $column_id : $task['column_id'];
        $values['position'] = $this->taskFinderModel->countByColumnId($project_id, $values['column_id']) + 1;
        $values['swimlane_id'] = $swimlane_id !== null ? $swimlane_id : $task['swimlane_id'];
        $values['category_id'] = $category_id !== null ? $category_id : $task['category_id'];
        $values['owner_id'] = $owner_id !== null ? $owner_id : $task['owner_id'];
        $values['priority'] = $task['priority'];
        return $values;
    }
}
