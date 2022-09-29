<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class SubtaskTaskConversionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class SubtaskTaskConversionModel extends Base
{
    /**
     * Convert a subtask to a task
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $subtask_id
     * @return integer
     */
    public function convertToTask($project_id, $subtask_id)
    {
        $subtask = $this->subtaskModel->getById($subtask_id);
        $parent_task = $this->taskFinderModel->getById($subtask['task_id']);

        $task_id = $this->taskCreationModel->create(array(
            'project_id' => $project_id,
            'title' => $subtask['title'],
            'time_estimated' => $subtask['time_estimated'],
            'time_spent' => $subtask['time_spent'],
            'owner_id' => $subtask['user_id'],
            'swimlane_id' => $parent_task['swimlane_id'],
            'priority' => $parent_task['priority'],
            'column_id' => $parent_task['column_id'],
            'category_id' => $parent_task['category_id'],
            'color_id' => $parent_task['color_id']
        ));

        if ($task_id !== false) {
            $link = $this->linkModel->getByLabel('is a child of');
            if ($link) {
                $this->taskLinkModel->create($task_id, $subtask['task_id'], $link['id']);
            }

            $this->tagDuplicationModel->duplicateTaskTags($parent_task['id'], $task_id);
            $this->subtaskModel->remove($subtask_id);
        }

        return $task_id;
    }
}
