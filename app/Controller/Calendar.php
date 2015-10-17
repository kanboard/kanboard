<?php

namespace Kanboard\Controller;

use Kanboard\Model\Task as TaskModel;

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
        $filter = $this->taskFilterCalendarFormatter
            ->search($this->userSession->getFilters($project_id))
            ->filterByProject($project_id);

        // Tasks
        if ($this->config->get('calendar_project_tasks', 'date_started') === 'date_creation') {
            $events = $filter->copy()->filterByCreationDateRange($start, $end)->setColumns('date_creation', 'date_completed')->format();
        } else {
            $events = $filter->copy()->filterByStartDateRange($start, $end)->setColumns('date_started', 'date_completed')->format();
        }

        // Tasks with due date
        $events = array_merge($events, $filter->copy()->filterByDueDateRange($start, $end)->setColumns('date_due')->setFullDay()->format());

        $events = $this->hook->merge('controller:calendar:project:events', $events, array(
            'project_id' => $project_id,
            'start' => $start,
            'end' => $end,
        ));

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
        $filter = $this->taskFilterCalendarFormatter->create()->filterByOwner($user_id)->filterByStatus(TaskModel::STATUS_OPEN);

        // Task with due date
        $events = $filter->copy()->filterByDueDateRange($start, $end)->setColumns('date_due')->setFullDay()->format();

        // Tasks
        if ($this->config->get('calendar_user_tasks', 'date_started') === 'date_creation') {
            $events = array_merge($events, $filter->copy()->filterByCreationDateRange($start, $end)->setColumns('date_creation', 'date_completed')->format());
        } else {
            $events = array_merge($events, $filter->copy()->filterByStartDateRange($start, $end)->setColumns('date_started', 'date_completed')->format());
        }

        // Subtasks time tracking
        if ($this->config->get('calendar_user_subtasks_time_tracking') == 1) {
            $events = array_merge($events, $this->subtaskTimeTracking->getUserCalendarEvents($user_id, $start, $end));
        }

        $events = $this->hook->merge('controller:calendar:user:events', $events, array(
            'user_id' => $user_id,
            'start' => $start,
            'end' => $end,
        ));

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
