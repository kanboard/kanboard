<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

class SubtaskTimeTrackingCalendarFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Format calendar events
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $events = array();

        foreach ($this->query->findAll() as $row) {
            $user = isset($row['username']) ? ' ('.($row['user_fullname'] ?: $row['username']).')' : '';

            $events[] = array(
                'id' => $row['id'],
                'subtask_id' => $row['subtask_id'],
                'title' => t('#%d', $row['task_id']).' '.$row['subtask_title'].$user,
                'start' => date('Y-m-d\TH:i:s', $row['start']),
                'end' => date('Y-m-d\TH:i:s', $row['end'] ?: time()),
                'backgroundColor' => $this->colorModel->getBackgroundColor($row['color_id']),
                'borderColor' => $this->colorModel->getBorderColor($row['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url->to('TaskViewController', 'show', array('task_id' => $row['task_id'])),
                'editable' => false,
            );
        }

        return $events;
    }
}
