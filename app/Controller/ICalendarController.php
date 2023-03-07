<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskDueDateRangeFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Model\TaskModel;
use Eluceo\iCal\Component\Calendar as iCalendar;

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

        $startColumn = $this->configModel->get('calendar_user_tasks', 'date_started');

        $calendar = new iCalendar('Kanboard');
        $calendar->setName($user['name'] ?: $user['username']);
        $calendar->setDescription($user['name'] ?: $user['username']);
        $calendar->setPublishedTTL('PT1H');

        $queryDueDateOnly = QueryBuilder::create()
            ->withQuery($this->taskFinderModel->getICalQuery())
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskDueDateRangeFilter(array($startRange, $endRange)))
            ->withFilter(new TaskAssigneeFilter($user['id']))
            ->getQuery();

        $queryStartAndDueDate = QueryBuilder::create()
            ->withQuery($this->taskFinderModel->getICalQuery())
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskAssigneeFilter($user['id']))
            ->getQuery()
            ->addCondition($this->getConditionForTasksWithStartAndDueDate($startRange, $endRange, $startColumn, 'date_due'));

        $this->response->ical($this->taskICalFormatter
            ->setCalendar($calendar)
            ->addTasksWithDueDateOnly($queryDueDateOnly)
            ->addTasksWithStartAndDueDate($queryStartAndDueDate, $startColumn, 'date_due')
            ->format());
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

        $startColumn = $this->configModel->get('calendar_project_tasks', 'date_started');

        $calendar = new iCalendar('Kanboard');
        $calendar->setName($project['name']);
        $calendar->setDescription($project['name']);
        $calendar->setPublishedTTL('PT1H');

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

        $this->response->ical($this->taskICalFormatter
            ->setCalendar($calendar)
            ->addTasksWithDueDateOnly($queryDueDateOnly)
            ->addTasksWithStartAndDueDate($queryStartAndDueDate, $startColumn, 'date_due')
            ->format());
    }

    protected function getConditionForTasksWithStartAndDueDate($start_time, $end_time, $start_column, $end_column)
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
