<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Task helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class TaskHelper extends Base
{
    /**
     * Local cache for project columns
     *
     * @access private
     * @var array
     */
    private $columns = array();

    public function getColors()
    {
        return $this->color->getList();
    }

    public function recurrenceTriggers()
    {
        return $this->task->getRecurrenceTriggerList();
    }

    public function recurrenceTimeframes()
    {
        return $this->task->getRecurrenceTimeframeList();
    }

    public function recurrenceBasedates()
    {
        return $this->task->getRecurrenceBasedateList();
    }

    public function canRemove(array $task)
    {
        return $this->taskPermission->canRemoveTask($task);
    }

    public function selectAssignee(array $users, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="3"'), $attributes);

        $html = $this->helper->form->label(t('Assignee'), 'owner_id');
        $html .= $this->helper->form->select('owner_id', $users, $values, $errors, $attributes);
        $html .= '&nbsp;<a href="#" class="assign-me" data-target-id="form-owner_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';

        return $html;
    }

    public function selectCategory(array $categories, array $values, array $errors = array(), array $attributes = array(), $allow_one_item = false)
    {
        $attributes = array_merge(array('tabindex="4"'), $attributes);
        $html = '';

        if (! (! $allow_one_item && count($categories) === 1 && key($categories) == 0)) {
            $html .= $this->helper->form->label(t('Category'), 'category_id');
            $html .= $this->helper->form->select('category_id', $categories, $values, $errors, $attributes);
        }

        return $html;
    }

    public function selectSwimlane(array $swimlanes, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="5"'), $attributes);
        $html = '';

        if (! (count($swimlanes) === 1 && key($swimlanes) == 0)) {
            $html .= $this->helper->form->label(t('Swimlane'), 'swimlane_id');
            $html .= $this->helper->form->select('swimlane_id', $swimlanes, $values, $errors, $attributes);
        }

        return $html;
    }

    public function selectColumn(array $columns, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="6"'), $attributes);

        $html = $this->helper->form->label(t('Column'), 'column_id');
        $html .= $this->helper->form->select('column_id', $columns, $values, $errors, $attributes);

        return $html;
    }

    public function selectPriority(array $project, array $values)
    {
        $html = '';

        if ($project['priority_end'] > $project['priority_start']) {
            $range = range($project['priority_start'], $project['priority_end']);
            $options = array_combine($range, $range);
            $values += array('priority' => $project['priority_default']);

            $html .= $this->helper->form->label(t('Priority'), 'priority');
            $html .= $this->helper->form->select('priority', $options, $values, array(), array('tabindex="7"'));
        }

        return $html;
    }

    public function selectScore(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="8"'), $attributes);

        $html = $this->helper->form->label(t('Complexity'), 'score');
        $html .= $this->helper->form->number('score', $values, $errors, $attributes);

        return $html;
    }

    public function selectTimeEstimated(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="9"'), $attributes);

        $html = $this->helper->form->label(t('Original estimate'), 'time_estimated');
        $html .= $this->helper->form->numeric('time_estimated', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function selectTimeSpent(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="10"'), $attributes);

        $html = $this->helper->form->label(t('Time spent'), 'time_spent');
        $html .= $this->helper->form->numeric('time_spent', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function selectStartDate(array $values, array $errors = array(), array $attributes = array())
    {
        $placeholder = date($this->config->get('application_date_format', 'm/d/Y H:i'));
        $attributes = array_merge(array('tabindex="11"', 'placeholder="'.$placeholder.'"'), $attributes);

        $html = $this->helper->form->label(t('Start Date'), 'date_started');
        $html .= $this->helper->form->text('date_started', $values, $errors, $attributes, 'form-datetime');

        return $html;
    }

    public function selectDueDate(array $values, array $errors = array(), array $attributes = array())
    {
        $placeholder = date($this->config->get('application_date_format', 'm/d/Y'));
        $attributes = array_merge(array('tabindex="12"', 'placeholder="'.$placeholder.'"'), $attributes);

        $html = $this->helper->form->label(t('Due Date'), 'date_due');
        $html .= $this->helper->form->text('date_due', $values, $errors, $attributes, 'form-date');

        return $html;
    }

    public function formatPriority(array $project, array $task)
    {
        $html = '';

        if ($project['priority_end'] > $project['priority_start']) {
            $html .= '<span class="task-board-priority" title="'.t('Task priority').'">';
            $html .= $task['priority'] >= 0 ? 'P'.$task['priority'] : '-P'.abs($task['priority']);
            $html .= '</span>';
        }

        return $html;
    }

    public function getProgress($task)
    {
        if (! isset($this->columns[$task['project_id']])) {
            $this->columns[$task['project_id']] = $this->column->getList($task['project_id']);
        }

        return $this->task->getProgress($task, $this->columns[$task['project_id']]);
    }
}
