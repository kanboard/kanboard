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
}
