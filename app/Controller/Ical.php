<?php

namespace Kanboard\Controller;

use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Formatter\TaskICalFormatter;
use Kanboard\Model\Task as TaskModel;
use Eluceo\iCal\Component\Calendar as iCalendar;

/**
 * iCalendar controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Ical extends Base
{
    /**
     * Get user iCalendar
     *
     * @access public
     */
    public function user()
    {
        $token = $this->request->getStringParam('token');
        $user = $this->user->getByToken($token);

        // Token verification
        if (empty($user)) {
            $this->forbidden(true);
        }

        // Common filter
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->withQuery($this->taskFinder->getICalQuery())
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskAssigneeFilter($user['id']));

        // Calendar properties
        $calendar = new iCalendar('Kanboard');
        $calendar->setName($user['name'] ?: $user['username']);
        $calendar->setDescription($user['name'] ?: $user['username']);
        $calendar->setPublishedTTL('PT1H');

        $this->renderCalendar($queryBuilder, $calendar);
    }

    /**
     * Get project iCalendar
     *
     * @access public
     */
    public function project()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->project->getByToken($token);

        // Token verification
        if (empty($project)) {
            $this->forbidden(true);
        }

        // Common filter
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->withQuery($this->taskFinder->getICalQuery())
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskProjectFilter($project['id']));

        // Calendar properties
        $calendar = new iCalendar('Kanboard');
        $calendar->setName($project['name']);
        $calendar->setDescription($project['name']);
        $calendar->setPublishedTTL('PT1H');

        $this->renderCalendar($queryBuilder, $calendar);
    }

    /**
     * Common method to render iCal events
     *
     * @access private
     */
    private function renderCalendar(QueryBuilder $queryBuilder, iCalendar $calendar)
    {
        $start = $this->request->getStringParam('start', strtotime('-2 month'));
        $end = $this->request->getStringParam('end', strtotime('+6 months'));

        $this->helper->ical->addTaskDateDueEvents($queryBuilder, $calendar, $start, $end);

        $formatter = new TaskICalFormatter($this->container);
        $this->response->ical($formatter->setCalendar($calendar)->format());
    }
}
