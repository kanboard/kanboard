<?php

namespace Kanboard\Controller;

use Kanboard\Model\Subtask as SubtaskModel;

/**
 * Subtask Restriction
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class SubtaskRestriction extends Base
{
    /**
     * Show popup
     *
     * @access public
     */
    public function popover()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();

        $this->response->html($this->template->render('subtask_restriction/popover', array(
            'status_list' => array(
                SubtaskModel::STATUS_TODO => t('Todo'),
                SubtaskModel::STATUS_DONE => t('Done'),
            ),
            'subtask_inprogress' => $this->subtask->getSubtaskInProgress($this->userSession->getId()),
            'subtask' => $subtask,
            'task' => $task,
        )));
    }

    /**
     * Change status of the in progress subtask and the other subtask
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();
        $values = $this->request->getValues();

        // Change status of the previous "in progress" subtask
        $this->subtask->update(array(
            'id' => $values['id'],
            'status' => $values['status'],
        ));

        // Set the current subtask to "in progress"
        $this->subtask->update(array(
            'id' => $subtask['id'],
            'status' => SubtaskModel::STATUS_INPROGRESS,
        ));

        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
    }
}
