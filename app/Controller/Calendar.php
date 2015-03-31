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
        $project = $this->getProject();

        $this->response->html($this->template->layout('calendar/show', array(
            'check_interval' => $this->config->get('board_private_refresh_interval'),
            'users_list' => $this->projectPermission->getMemberList($project['id'], true, true),
            'categories_list' => $this->category->getList($project['id'], true, true),
            'columns_list' => $this->board->getColumnsList($project['id'], true),
            'swimlanes_list' => $this->swimlane->getList($project['id'], true),
            'colors_list' => $this->color->getList(true),
            'status_list' => $this->taskStatus->getList(true),
            'project' => $project,
            'title' => t('Calendar for "%s"', $project['name']),
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
        )));
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

        $due_tasks = $this->taskFilter
            ->create()
            ->filterByProject($project_id)
            ->filterByCategory($this->request->getIntegerParam('category_id', -1))
            ->filterByOwner($this->request->getIntegerParam('owner_id', -1))
            ->filterByColumn($this->request->getIntegerParam('column_id', -1))
            ->filterBySwimlane($this->request->getIntegerParam('swimlane_id', -1))
            ->filterByColor($this->request->getStringParam('color_id'))
            ->filterByStatus($this->request->getIntegerParam('is_active', -1))
            ->filterByDueDateRange($start, $end)
            ->toCalendarEvents();

        $this->response->json($due_tasks);
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

        $due_tasks = $this->taskFilter
                          ->create()
                          ->filterByOwner($user_id)
                          ->filterByStatus(TaskModel::STATUS_OPEN)
                          ->filterByDueDateRange($start, $end)
                          ->toCalendarEvents();

        $subtask_timeslots = $this->subtaskTimeTracking->getUserCalendarEvents($user_id, $start, $end);

        $subtask_forcast = $this->config->get('subtask_forecast') == 1 ? $this->subtaskForecast->getCalendarEvents($user_id, $end) : array();

        $this->response->json(array_merge($due_tasks, $subtask_timeslots, $subtask_forcast));
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
