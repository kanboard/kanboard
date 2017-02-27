<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Class SubtaskListFormatter
 *
 * @package Kanboard\Formatter
 * @author  Frederic Guillot
 */
class SubtaskListFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $status = $this->subtaskModel->getStatusList();
        $subtasks = $this->query->findAll();

        foreach ($subtasks as &$subtask) {
            $subtask['status_name'] = $status[$subtask['status']];
            $subtask['timer_start_date'] = isset($subtask['timer_start_date']) ? $subtask['timer_start_date'] : 0;
            $subtask['is_timer_started'] = ! empty($subtask['timer_start_date']);
        }

        return $subtasks;
    }
}
