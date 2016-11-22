<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskDueDateRangeFilter;
use Kanboard\Formatter\SubtaskTimeTrackingCalendarFormatter;
use Kanboard\Formatter\TaskCalendarFormatter;

/**
 * Calendar Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class CalendarHelper extends Base
{
    /**
     * Render calendar component
     *
     * @param  string $checkUrl
     * @param  string $saveUrl
     * @return string
     */
    public function render($checkUrl, $saveUrl)
    {
        $params = array(
            'checkUrl' => $checkUrl,
            'saveUrl' => $saveUrl,
        );

        return '<div class="js-calendar" data-params=\''.json_encode($params, JSON_HEX_APOS).'\'></div>';
    }

    /**
     * Get formatted calendar task due events
     *
     * @access public
     * @param  QueryBuilder       $queryBuilder
     * @param  string             $start
     * @param  string             $end
     * @return array
     */
    public function getTaskDateDueEvents(QueryBuilder $queryBuilder, $start, $end)
    {
        $formatter = new TaskCalendarFormatter($this->container);
        $formatter->setFullDay();
        $formatter->setColumns('date_due');

        return $queryBuilder
            ->withFilter(new TaskDueDateRangeFilter(array($start, $end)))
            ->format($formatter);
    }

    /**
     * Get formatted calendar task events
     *
     * @access public
     * @param  QueryBuilder       $queryBuilder
     * @param  string             $start
     * @param  string             $end
     * @return array
     */
    public function getTaskEvents(QueryBuilder $queryBuilder, $start, $end)
    {
        $startColumn = $this->configModel->get('calendar_project_tasks', 'date_started');

        $queryBuilder->getQuery()->addCondition($this->getCalendarCondition(
            $this->dateParser->getTimestampFromIsoFormat($start),
            $this->dateParser->getTimestampFromIsoFormat($end),
            $startColumn,
            'date_due'
        ));

        $formatter = new TaskCalendarFormatter($this->container);
        $formatter->setColumns($startColumn, 'date_due');

        return $queryBuilder->format($formatter);
    }

    /**
     * Get formatted calendar subtask time tracking events
     *
     * @access public
     * @param  integer $user_id
     * @param  string  $start
     * @param  string  $end
     * @return array
     */
    public function getSubtaskTimeTrackingEvents($user_id, $start, $end)
    {
        $formatter = new SubtaskTimeTrackingCalendarFormatter($this->container);
        return $formatter
            ->withQuery($this->subtaskTimeTrackingModel->getUserQuery($user_id)
                ->addCondition($this->getCalendarCondition(
                    $this->dateParser->getTimestampFromIsoFormat($start),
                    $this->dateParser->getTimestampFromIsoFormat($end),
                    'start',
                    'end'
                ))
            )
            ->format();
    }

    /**
     * Build SQL condition for a given time range
     *
     * @access public
     * @param  string   $start_time     Start timestamp
     * @param  string   $end_time       End timestamp
     * @param  string   $start_column   Start column name
     * @param  string   $end_column     End column name
     * @return string
     */
    public function getCalendarCondition($start_time, $end_time, $start_column, $end_column)
    {
        $start_column = $this->db->escapeIdentifier($start_column);
        $end_column = $this->db->escapeIdentifier($end_column);

        $conditions = array(
            "($start_column >= '$start_time' AND $start_column <= '$end_time')",
            "($start_column <= '$start_time' AND $end_column >= '$start_time')",
            "($start_column <= '$start_time' AND ($end_column = '0' OR $end_column IS NULL))",
        );

        return $start_column.' IS NOT NULL AND '.$start_column.' > 0 AND ('.implode(' OR ', $conditions).')';
    }
}
