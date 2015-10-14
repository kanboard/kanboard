<?php

namespace Kanboard\Formatter;

/**
 * Calendar event formatter for task filter
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class TaskFilterCalendarFormatter extends TaskFilterCalendarEvent implements FormatterInterface
{
    /**
     * Transform tasks to calendar events
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $events = array();

        foreach ($this->query->findAll() as $task) {
            $events[] = array(
                'timezoneParam' => $this->config->getCurrentTimezone(),
                'id' => $task['id'],
                'title' => t('#%d', $task['id']).' '.$task['title'],
                'backgroundColor' => $this->color->getBackgroundColor($task['color_id']),
                'borderColor' => $this->color->getBorderColor($task['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
                'start' => date($this->getDateTimeFormat(), $task[$this->startColumn]),
                'end' => date($this->getDateTimeFormat(), $task[$this->endColumn] ?: time()),
                'editable' => $this->isFullDay(),
                'allday' => $this->isFullDay(),
            );
        }

        return $events;
    }

    /**
     * Get DateTime format for event
     *
     * @access private
     * @return string
     */
    private function getDateTimeFormat()
    {
        return $this->isFullDay() ? 'Y-m-d' : 'Y-m-d\TH:i:s';
    }
}
