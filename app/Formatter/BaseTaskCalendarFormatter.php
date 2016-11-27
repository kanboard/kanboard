<?php

namespace Kanboard\Formatter;

/**
 * Common class to handle calendar events
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
abstract class BaseTaskCalendarFormatter extends BaseFormatter
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
     * Transform results to calendar events
     *
     * @access public
     * @param  string  $start_column    Column name for the start date
     * @param  string  $end_column      Column name for the end date
     * @return $this
     */
    public function setColumns($start_column, $end_column = '')
    {
        $this->startColumn = $start_column;
        $this->endColumn = $end_column ?: $start_column;
        return $this;
    }
}
