<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Core\ICalendar\Calendar;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskDueDateRangeFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Model\TaskModel;
use PicoDb\Table;

/**
 * iCalendar Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ICalendarController extends BaseController
{
    public function user()
    {
        $token = $this->request->getStringParam('token');
        $user = $this->userModel->getByToken($token);

        if (empty($user)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $startRange = strtotime('-2 months');
        $endRange = strtotime('+6 months');

        $startColumn = $this->getStartColumn('calendar_user_tasks');

        $name = $user['name'] ?: $user['username'];
        $calendar = new Calendar('Kanboard', $name, $name, 'PT1H');

        $queryDueDateOnly = QueryBuilder::create()
            ->withQuery($this->taskFinderModel->getUserICalQuery($user['id']))
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskDueDateRangeFilter(array($startRange, $endRange)))
            ->withFilter(new TaskAssigneeFilter($user['id']))
            ->getQuery();

        $queryStartAndDueDate = QueryBuilder::create()
            ->withQuery($this->taskFinderModel->getUserICalQuery($user['id']))
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskAssigneeFilter($user['id']))
            ->getQuery()
            ->addCondition($this->getConditionForTasksWithStartAndDueDate($startRange, $endRange, $startColumn, 'date_due'));

        $formatter = $this->taskICalFormatter
            ->setCalendar($calendar)
            ->addTasksWithDueDateOnly($queryDueDateOnly)
            ->addTasksWithStartAndDueDate($queryStartAndDueDate, $startColumn, 'date_due');

        if ($this->hasTodos()) {
            $queryWithoutDates = QueryBuilder::create()
                ->withQuery($this->taskFinderModel->getUserICalQuery($user['id']))
                ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
                ->withFilter(new TaskAssigneeFilter($user['id']))
                ->getQuery();

            $formatter->addTasksWithoutDates($this->filterTasksWithoutDates($queryWithoutDates, $startColumn));
        }

        $this->response->ical($formatter->format());
    }

    public function project()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->projectModel->getByToken($token);

        if (empty($project)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $startRange = strtotime('-2 months');
        $endRange = strtotime('+6 months');

        $startColumn = $this->getStartColumn('calendar_project_tasks');

        $calendar = new Calendar('Kanboard', $project['name'], $project['name'], 'PT1H');

        $queryDueDateOnly = QueryBuilder::create()
            ->withQuery($this->taskFinderModel->getICalQuery())
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskProjectFilter($project['id']))
            ->withFilter(new TaskDueDateRangeFilter(array($startRange, $endRange)))
            ->getQuery();

        $queryStartAndDueDate = QueryBuilder::create()
            ->withQuery($this->taskFinderModel->getICalQuery())
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskProjectFilter($project['id']))
            ->getQuery()
            ->addCondition($this->getConditionForTasksWithStartAndDueDate($startRange, $endRange, $startColumn, 'date_due'));

        $formatter = $this->taskICalFormatter
            ->setCalendar($calendar)
            ->addTasksWithDueDateOnly($queryDueDateOnly)
            ->addTasksWithStartAndDueDate($queryStartAndDueDate, $startColumn, 'date_due');

        if ($this->hasTodos()) {
            $queryWithoutDates = QueryBuilder::create()
                ->withQuery($this->taskFinderModel->getICalQuery())
                ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
                ->withFilter(new TaskProjectFilter($project['id']))
                ->getQuery();

            $formatter->addTasksWithoutDates($this->filterTasksWithoutDates($queryWithoutDates, $startColumn));
        }

        $this->response->ical($formatter->format());
    }

    /**
     * Tasks without dates are only added as to-dos on request,
     * most calendar clients ignore them in a subscribed feed
     */
    protected function hasTodos()
    {
        return $this->request->getIntegerParam('todo') === 1;
    }

    /**
     * Keep the tasks that cannot be an event: no due date and no start date
     */
    protected function filterTasksWithoutDates(Table $query, $startColumn)
    {
        foreach (array('date_due', $startColumn) as $column) {
            $query
                ->beginOr()
                ->isNull(TaskModel::TABLE.'.'.$column)
                ->eq(TaskModel::TABLE.'.'.$column, 0)
                ->closeOr();
        }

        return $query;
    }

    protected function getStartColumn($option)
    {
        $column = $this->configModel->get($option, 'date_started');
        return in_array($column, array('date_creation', 'date_started'), true) ? $column : 'date_started';
    }

    protected function getConditionForTasksWithStartAndDueDate($start_time, $end_time, $start_column, $end_column)
    {
        $start_time = (int) $start_time;
        $end_time = (int) $end_time;

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
