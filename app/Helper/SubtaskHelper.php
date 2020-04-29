<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\SubtaskModel;

/**
 * Subtask helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class SubtaskHelper extends Base
{
    /**
     * Return if the current user has a subtask in progress
     *
     * @return bool
     */
    public function hasSubtaskInProgress()
    {
        return session_is_true('hasSubtaskInProgress');
    }

    /**
     * Render subtask title
     *
     * @param  array $subtask
     * @return string
     */
    public function renderTitle(array $subtask)
    {
        if ($subtask['status'] == 0) {
            $html = '<i class="fa fa-square-o fa-fw ' . ($this->hasSubtaskInProgress() ? 'js-modal-confirm' : '') . '"></i>';
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
     * @param  array  $task
     * @param  array  $subtask
     * @param  string $fragment
     * @param  int    $userId
     * @return string
     */
    public function renderToggleStatus(array $task, array $subtask, $fragment = '', $userId = 0)
    {
        if (! $this->helper->user->hasProjectAccess('SubtaskController', 'edit', $task['project_id'])) {
            $html = $this->renderTitle($subtask);
        } else {
            $title = $this->renderTitle($subtask);
            $params = array(
                'project_id' => $task['project_id'],
                'task_id'    => $subtask['task_id'],
                'subtask_id' => $subtask['id'],
                'user_id'    => $userId,
                'fragment'   => $fragment,
            );

            if ($subtask['status'] == 0 && $this->hasSubtaskInProgress()) {
                $html = $this->helper->url->link($title, 'SubtaskRestrictionController', 'show', $params, false, 'js-modal-confirm', $this->getSubtaskTooltip($subtask));
            } else {
                $html = $this->helper->url->link($title, 'SubtaskStatusController', 'change', $params, false, 'js-subtask-toggle-status', $this->getSubtaskTooltip($subtask));
            }
        }

        return '<span class="subtask-title">'.$html.'</span>';
    }

    public function renderTimer(array $task, array $subtask)
    {
        $html = '<span class="subtask-timer-toggle">';

        if ($subtask['is_timer_started']) {
            $html .= $this->helper->url->icon('pause', t('Stop timer'), 'SubtaskStatusController', 'timer', array('timer' => 'stop', 'project_id' => $task['project_id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id']), false, 'js-subtask-toggle-timer');
            $html .= ' (' . $this->helper->dt->age($subtask['timer_start_date']) .')';
        } else {
            $html .= $this->helper->url->icon('play-circle-o', t('Start timer'), 'SubtaskStatusController', 'timer', array('timer' => 'start', 'project_id' => $task['project_id'], 'task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id']), false, 'js-subtask-toggle-timer');
        }

        $html .= '</span>';

        return $html;
    }

    public function renderBulkTitleField(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="1"', 'required'), $attributes);

        $html = $this->helper->form->label(t('Title'), 'title');
        $html .= $this->helper->form->textarea('title', $values, $errors, $attributes);
        $html .= '<p class="form-help">'.t('Enter one subtask by line.').'</p>';

        return $html;
    }

    public function renderTitleField(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="1"', 'required'), $attributes);

        $html = $this->helper->form->label(t('Title'), 'title');
        $html .= $this->helper->form->text('title', $values, $errors, $attributes, 'form-max-width');

        return $html;
    }

    public function renderAssigneeField(array $users, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="2"'), $attributes);

        $html = $this->helper->form->label(t('Assignee'), 'user_id');
        $html .= $this->helper->form->select('user_id', $users, $values, $errors, $attributes);
        $html .= '&nbsp;';
        $html .= '<small>';
        $html .= '<a href="#" class="assign-me" data-target-id="form-user_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';
        $html .= '</small>';

        return $html;
    }

    public function renderTimeEstimatedField(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="3"'), $attributes);

        $html = $this->helper->form->label(t('Original estimate'), 'time_estimated');
        $html .= $this->helper->form->numeric('time_estimated', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function renderTimeSpentField(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="4"'), $attributes);

        $html = $this->helper->form->label(t('Time spent'), 'time_spent');
        $html .= $this->helper->form->numeric('time_spent', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function getSubtaskTooltip(array $subtask)
    {
        switch ($subtask['status']) {
            case SubtaskModel::STATUS_TODO:
                return t('Subtask not started');
            case SubtaskModel::STATUS_INPROGRESS:
                return t('Subtask currently in progress');
            case SubtaskModel::STATUS_DONE:
                return t('Subtask completed');
        }

        return '';
    }
}
