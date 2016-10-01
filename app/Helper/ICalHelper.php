<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskDueDateRangeFilter;
use Kanboard\Formatter\TaskICalFormatter;
use Eluceo\iCal\Component\Calendar as iCalendar;

/**
 * ICal Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class ICalHelper extends Base
{
    /**
     * Get formatted calendar task due events
     *
     * @access public
     * @param  QueryBuilder  $queryBuilder
     * @param  iCalendar     $calendar
     * @param  string        $start
     * @param  string        $end
     */
    public function addTaskDateDueEvents(QueryBuilder $queryBuilder, iCalendar $calendar, $start, $end)
    {
        $queryBuilder->withFilter(new TaskDueDateRangeFilter(array($start, $end)));

        $formatter = new TaskICalFormatter($this->container);
        $formatter->setColumns('date_due');
        $formatter->setCalendar($calendar);
        $formatter->withQuery($queryBuilder->getQuery());
        $formatter->addFullDayEvents();
    }
}
