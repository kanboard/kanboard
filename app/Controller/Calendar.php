<?php

namespace Controller;

use Model\Task as TaskModel;

/**
 * Project Calendar controller
 *
 * @package  controller
 * @author   Frederic Guillot
 * @author   Timo Litzbarski
 */
class Calendar extends Base
{
    /**
     * Show calendar view for projects
     *
     * @access public
     */
    public function show()
    {
        $this->response->html($this->template->layout('calendar/show', array(
            'check_interval' => $this->config->get('board_private_refresh_interval'),
        ) + $this->getProjectFilters('calendar', 'show')));
    }

    /**
     * Get tasks to display on the calendar (project view)
     *
     * @access public
     */
    public function project()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $start = $this->request->getStringParam('start');
        $end = $this->request->getStringParam('end');

        // Common filter
        $filter = $this->taskFilter
            ->search($this->userSession->getFilters($project_id))
            ->filterByProject($project_id);

        // Tasks
        if ($this->config->get('calendar_project_tasks', 'date_started') === 'date_creation') {
            $events = $filter->copy()->filterByCreationDateRange($start, $end)->toDateTimeCalendarEvents('date_creation', 'date_completed');
        }
        else {
            $events = $filter->copy()->filterByStartDateRange($start, $end)->toDateTimeCalendarEvents('date_started', 'date_completed');
        }

        // Tasks with due date
        $events = array_merge($events, $filter->copy()->filterByDueDateRange($start, $end)->toAllDayCalendarEvents());

        $this->response->json($events);
    }

    /**
     * Get tasks to display on the calendar (user view)
     *
     * @access public
     */
    public function user()
    {
        $user_id = $this->request->getIntegerParam('user_id');
        $start = $this->request->getStringParam('start');
        $end = $this->request->getStringParam('end');
        $filter = $this->taskFilter->create()->filterByOwner($user_id)->filterByStatus(TaskModel::STATUS_OPEN);

        // Task with due date
        $events = $filter->copy()->filterByDueDateRange($start, $end)->toAllDayCalendarEvents();

        // Tasks
        if ($this->config->get('calendar_user_tasks', 'date_started') === 'date_creation') {
            $events = array_merge($events, $filter->copy()->filterByCreationDateRange($start, $end)->toDateTimeCalendarEvents('date_creation', 'date_completed'));
        }
        else {
            $events = array_merge($events, $filter->copy()->filterByStartDateRange($start, $end)->toDateTimeCalendarEvents('date_started', 'date_completed'));
        }

        // Subtasks time tracking
        if ($this->config->get('calendar_user_subtasks_time_tracking') == 1) {
            $events = array_merge($events, $this->subtaskTimeTracking->getUserCalendarEvents($user_id, $start, $end));
        }

        // Subtask estimates
        if ($this->config->get('calendar_user_subtasks_forecast') == 1) {
            $events = array_merge($events, $this->subtaskForecast->getCalendarEvents($user_id, $end));
        }

        $this->response->json($events);
    }

    /**
     * Update task due date
     *
     * @access public
     */
    public function save()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {

            $values = $this->request->getJson();

            $this->taskModification->update(array(
                'id' => $values['task_id'],
                'date_due' => substr($values['date_due'], 0, 10),
            ));
        }
    }
}
