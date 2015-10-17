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
     * @param  array   $subtask
     * @param  string  $redirect
     * @return string
     */
    public function toggleStatus(array $subtask, $redirect)
    {
        if ($subtask['status'] == 0 && isset($this->session['has_subtask_inprogress']) && $this->session['has_subtask_inprogress'] === true) {
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
