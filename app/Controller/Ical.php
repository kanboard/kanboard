<?php

namespace Controller;

use Model\TaskFilter;
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
        $filter = $this->taskFilter
            ->create()
            ->filterByOwner($user['id']);

        // Calendar properties
        $calendar = new iCalendar('Kanboard');
        $calendar->setName($user['name'] ?: $user['username']);
        $calendar->setDescription($user['name'] ?: $user['username']);
        $calendar->setPublishedTTL('PT1H');

        $this->renderCalendar($filter, $calendar);
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
        $filter = $this->taskFilter
            ->create()
            ->filterByProject($project['id']);

        // Calendar properties
        $calendar = new iCalendar('Kanboard');
        $calendar->setName($project['name']);
        $calendar->setDescription($project['name']);
        $calendar->setPublishedTTL('PT1H');

        $this->renderCalendar($filter, $calendar);
    }

    /**
     * Common method to render iCal events
     *
     * @access private
     */
    private function renderCalendar(TaskFilter $filter, iCalendar $calendar)
    {
        $start = $this->request->getStringParam('start', strtotime('-2 month'));
        $end = $this->request->getStringParam('end', strtotime('+6 months'));

        // Tasks
        if ($this->config->get('calendar_project_tasks', 'date_started') === 'date_creation') {
            $filter->copy()->filterByCreationDateRange($start, $end)->addDateTimeIcalEvents('date_creation', 'date_completed', $calendar);
        }
        else {
            $filter->copy()->filterByStartDateRange($start, $end)->addDateTimeIcalEvents('date_started', 'date_completed', $calendar);
        }

        // Tasks with due date
        $filter->copy()->filterByDueDateRange($start, $end)->addAllDayIcalEvents('date_due', $calendar);

        $this->response->contentType('text/calendar; charset=utf-8');
        echo $calendar->render();
    }
}
