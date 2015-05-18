<?php

namespace Controller;

use Model\Task as TaskModel;

/**
 * iCalendar controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Ical extends Base
{
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

        $start = $this->request->getStringParam('start', strtotime('-1 month'));
        $end = $this->request->getStringParam('end', strtotime('+2 months'));

        // Common filter
        $filter = $this->taskFilter
            ->create()
            ->filterByProject($project['id']);

        // Tasks
        if ($this->config->get('calendar_project_tasks', 'date_started') === 'date_creation') {
            $calendar = $filter->copy()->filterByCreationDateRange($start, $end)->addDateTimeIcalEvents('date_creation', 'date_completed');
        }
        else {
            $calendar = $filter->copy()->filterByStartDateRange($start, $end)->addDateTimeIcalEvents('date_started', 'date_completed');
        }

        // Tasks with due date
        $calendar = $filter->copy()->filterByDueDateRange($start, $end)->addAllDayIcalEvents('date_due', $calendar);

        $this->response->contentType('text/calendar; charset=utf-8');
        echo $calendar->render();
    }
}
