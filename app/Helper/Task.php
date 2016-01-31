<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Task helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Task extends Base
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
        $html .= '<a href="#" class="assign-me" data-target-id="form-owner_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';

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
            $this->columns[$task['project_id']] = $this->board->getColumnsList($task['project_id']);
        }

        return $this->task->getProgress($task, $this->columns[$task['project_id']]);
    }
}
