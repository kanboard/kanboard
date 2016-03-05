<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Subtask helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class SubtaskHelper extends Base
{
    public function getTitle(array $subtask)
    {
        if ($subtask['status'] == 0) {
            $html = '<i class="fa fa-square-o fa-fw"></i>';
        } elseif ($subtask['status'] == 1) {
            $html = '<i class="fa fa-gears fa-fw"></i>';
        } else {
            $html = '<i class="fa fa-check-square-o fa-fw"></i>';
        }

        return $html.$this->helper->text->e($subtask['title']);
    }

    /**
     * Get the link to toggle subtask status
     *
     * @access public
     * @param  array    $subtask
     * @param  integer  $project_id
     * @param  boolean  $refresh_table
     * @return string
     */
    public function toggleStatus(array $subtask, $project_id, $refresh_table = false)
    {
        if (! $this->helper->user->hasProjectAccess('subtask', 'edit', $project_id)) {
            return $this->getTitle($subtask);
        }

        $params = array('task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'refresh-table' => (int) $refresh_table);

        if ($subtask['status'] == 0 && isset($this->sessionStorage->hasSubtaskInProgress) && $this->sessionStorage->hasSubtaskInProgress) {
            return $this->helper->url->link($this->getTitle($subtask), 'SubtaskRestriction', 'popover', $params, false, 'popover');
        }

        $class = 'subtask-toggle-status '.($refresh_table ? 'subtask-refresh-table' : '');
        return $this->helper->url->link($this->getTitle($subtask), 'SubtaskStatus', 'change', $params, false, $class);
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
