<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskDueDateRangeFilter;
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

        $this->taskICalFormatter
            ->setColumns('date_due')
            ->setCalendar($calendar)
            ->withQuery($queryBuilder->getQuery())
            ->addFullDayEvents();
    }
}
