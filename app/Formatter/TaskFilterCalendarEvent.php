<?php

namespace Kanboard\Formatter;

use Kanboard\Model\TaskFilter;

/**
 * Common class to handle calendar events
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
abstract class TaskFilterCalendarEvent extends TaskFilter
{
    /**
     * Column used for event start date
     *
     * @access protected
     * @var string
     */
    protected $startColumn = 'date_started';

    /**
     * Column used for event end date
     *
     * @access protected
     * @var string
     */
    protected $endColumn = 'date_completed';

    /**
     * Full day event flag
     *
     * @access private
     * @var boolean
     */
    private $fullDay = false;

    /**
     * Transform results to calendar events
     *
     * @access public
     * @param  string  $start_column    Column name for the start date
     * @param  string  $end_column      Column name for the end date
     * @return TaskFilterCalendarEvent
     */
    public function setColumns($start_column, $end_column = '')
    {
        $this->startColumn = $start_column;
        $this->endColumn = $end_column ?: $start_column;
        return $this;
    }

    /**
     * When called calendar events will be full day
     *
     * @access public
     * @return TaskFilterCalendarEvent
     */
    public function setFullDay()
    {
        $this->fullDay = true;
        return $this;
    }

    /**
     * Return true if the events are full day
     *
     * @access public
     * @return boolean
     */
    public function isFullDay()
    {
        return $this->fullDay;
    }
}
