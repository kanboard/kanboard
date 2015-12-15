<?php

namespace Kanboard\Helper;

/**
 * Subtask helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Subtask extends \Kanboard\Core\Base
{
    /**
     * Get the link to toggle subtask status
     *
     * @access public
     * @param  array    $subtask
     * @param  string   $redirect
     * @param  integer  $project_id
     * @return string
     */
    public function toggleStatus(array $subtask, $redirect, $project_id = 0)
    {
        if ($project_id > 0 && ! $this->helper->user->hasProjectAccess('subtask', 'edit', $project_id)) {
            return trim($this->template->render('subtask/icons', array('subtask' => $subtask))) . $this->helper->e($subtask['title']);
        }

        if ($subtask['status'] == 0 && isset($this->sessionStorage->hasSubtaskInProgress) && $this->sessionStorage->hasSubtaskInProgress) {
            return $this->helper->url->link(
                trim($this->template->render('subtask/icons', array('subtask' => $subtask))) . $this->helper->e($subtask['title']),
                'subtask',
                'subtaskRestriction',
                array('task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'redirect' => $redirect),
                false,
                'popover task-board-popover'
            );
        }

        return $this->helper->url->link(
            trim($this->template->render('subtask/icons', array('subtask' => $subtask))) . $this->helper->e($subtask['title']),
            'subtask',
            'toggleStatus',
            array('task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'redirect' => $redirect)
        );
    }
}
