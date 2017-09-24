<?php

namespace Kanboard\Controller;

use Kanboard\Model\SubtaskModel;

/**
 * Subtask Restriction
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class SubtaskRestrictionController extends BaseController
{
    /**
     * Show popup
     *
     * @access public
     */
    public function show()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);

        $this->response->html($this->template->render('subtask_restriction/show', array(
            'status_list' => array(
                SubtaskModel::STATUS_TODO => t('Todo'),
                SubtaskModel::STATUS_DONE => t('Done'),
            ),
            'subtask_inprogress' => $this->subtaskStatusModel->getSubtaskInProgress($this->userSession->getId()),
            'subtask' => $subtask,
            'task' => $task,
        )));
    }

    /**
     * Change status of the in progress subtask and the other subtask
     *
     * @access public
     */
    public function save()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);
        $values = $this->request->getValues();

        // Change status of the previous "in progress" subtask
        $this->subtaskModel->update(array(
            'id'      => $values['id'],
            'status'  => $values['status'],
        ));

        // Set the current subtask to "in progress"
        $this->subtaskModel->update(array(
            'id'      => $subtask['id'],
            'status'  => SubtaskModel::STATUS_INPROGRESS,
        ));

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
    }
}
