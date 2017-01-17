<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskAssigneeFilter;
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
    /**
     * Get user iCalendar
     *
     * @access public
     */
    public function user()
    {
        $token = $this->request->getStringParam('token');
        $user = $this->userModel->getByToken($token);

        // Token verification
        if (empty($user)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        // Common filter
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->withQuery($this->taskFinderModel->getICalQuery())
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
        $project = $this->projectModel->getByToken($token);

        // Token verification
        if (empty($project)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        // Common filter
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->withQuery($this->taskFinderModel->getICalQuery())
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
     * @param QueryBuilder $queryBuilder
     * @param iCalendar    $calendar
     */
    private function renderCalendar(QueryBuilder $queryBuilder, iCalendar $calendar)
    {
        $start = $this->request->getStringParam('start', strtotime('-2 month'));
        $end = $this->request->getStringParam('end', strtotime('+6 months'));

        $this->helper->ical->addTaskDateDueEvents($queryBuilder, $calendar, $start, $end);
        $this->response->ical($this->taskICalFormatter->setCalendar($calendar)->format());
    }
}
