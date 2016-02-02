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

    public function selectTitle(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="1"', 'required', 'maxlength="255"'), $attributes);

        $html = $this->helper->form->label(t('Title'), 'title');
        $html .= $this->helper->form->text('title', $values, $errors, $attributes);

        return $html;
    }

    public function selectAssignee(array $users, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="2"'), $attributes);

        $html = $this->helper->form->label(t('Assignee'), 'user_id');
        $html .= $this->helper->form->select('user_id', $users, $values, $errors, $attributes);
        $html .= '&nbsp;<a href="#" class="assign-me" data-target-id="form-user_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';

        return $html;
    }

    public function selectTimeEstimated(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="3"'), $attributes);

        $html = $this->helper->form->label(t('Original estimate'), 'time_estimated');
        $html .= $this->helper->form->numeric('time_estimated', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function selectTimeSpent(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="4"'), $attributes);

        $html = $this->helper->form->label(t('Time spent'), 'time_spent');
        $html .= $this->helper->form->numeric('time_spent', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }
}
