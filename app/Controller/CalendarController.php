<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Model\TaskModel;

/**
 * Calendar Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 * @author   Timo Litzbarski
 */
class CalendarController extends BaseController
{
    /**
     * Show calendar view for projects
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->app('calendar/show', array(
            'project' => $project,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
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
        $search = $this->userSession->getFilters($project_id);
        $queryBuilder = $this->taskLexer->build($search)->withFilter(new TaskProjectFilter($project_id));

        $events = $this->helper->calendar->getTaskDateDueEvents(clone($queryBuilder), $start, $end);
        $events = array_merge($events, $this->helper->calendar->getTaskEvents(clone($queryBuilder), $start, $end));

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
        $queryBuilder = $this->taskQuery
            ->withFilter(new TaskAssigneeFilter($user_id))
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN));

        $events = $this->helper->calendar->getTaskDateDueEvents(clone($queryBuilder), $start, $end);
        $events = array_merge($events, $this->helper->calendar->getTaskEvents(clone($queryBuilder), $start, $end));

        if ($this->configModel->get('calendar_user_subtasks_time_tracking') == 1) {
            $events = array_merge($events, $this->helper->calendar->getSubtaskTimeTrackingEvents($user_id, $start, $end));
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

            $this->taskModificationModel->update(array(
                'id' => $values['task_id'],
                'date_due' => substr($values['date_due'], 0, 10),
            ));
        }
    }
}
