<?php

namespace Kanboard\Controller;

/**
 * Task controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Task extends Base
{
    /**
     * Public access (display a task)
     *
     * @access public
     */
    public function readonly()
    {
        $project = $this->project->getByToken($this->request->getStringParam('token'));

        // Token verification
        if (empty($project)) {
            $this->forbidden(true);
        }

        $task = $this->taskFinder->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            $this->notfound(true);
        }

        $this->response->html($this->template->layout('task/public', array(
            'project' => $project,
            'comments' => $this->comment->getAll($task['id']),
            'subtasks' => $this->subtask->getAll($task['id']),
            'links' => $this->taskLink->getAllGroupedByLabel($task['id']),
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'title' => $task['title'],
            'no_layout' => true,
            'auto_refresh' => true,
            'not_editable' => true,
        )));
    }

    /**
     * Show a task
     *
     * @access public
     */
    public function show()
    {
        $task = $this->getTask();
        $subtasks = $this->subtask->getAll($task['id']);

        $values = array(
            'id' => $task['id'],
            'date_started' => $task['date_started'],
            'time_estimated' => $task['time_estimated'] ?: '',
            'time_spent' => $task['time_spent'] ?: '',
        );

        $this->dateParser->format($values, array('date_started'), 'Y-m-d H:i');

        $this->response->html($this->taskLayout('task/show', array(
            'project' => $this->project->getById($task['project_id']),
            'files' => $this->file->getAllDocuments($task['id']),
            'images' => $this->file->getAllImages($task['id']),
            'comments' => $this->comment->getAll($task['id'], $this->userSession->getCommentSorting()),
            'subtasks' => $subtasks,
            'links' => $this->taskLink->getAllGroupedByLabel($task['id']),
            'task' => $task,
            'values' => $values,
            'link_label_list' => $this->link->getList(0, false),
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'users_list' => $this->projectPermission->getMemberList($task['project_id'], true, false, false),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => $task['project_name'].' &gt; '.$task['title'],
            'recurrence_trigger_list' => $this->task->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->task->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->task->getRecurrenceBasedateList(),
        )));
    }

    /**
     * Display task analytics
     *
     * @access public
     */
    public function analytics()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('task/analytics', array(
            'title' => $task['title'],
            'task' => $task,
            'lead_time' => $this->taskAnalytic->getLeadTime($task),
            'cycle_time' => $this->taskAnalytic->getCycleTime($task),
            'time_spent_columns' => $this->taskAnalytic->getTimeSpentByColumn($task),
        )));
    }

    /**
     * Display the time tracking details
     *
     * @access public
     */
    public function timetracking()
    {
        $task = $this->getTask();

        $subtask_paginator = $this->paginator
            ->setUrl('task', 'timesheet', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'pagination' => 'subtasks'))
            ->setMax(15)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTracking->getTaskQuery($task['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->taskLayout('task/time_tracking_details', array(
            'task' => $task,
            'subtask_paginator' => $subtask_paginator,
        )));
    }

    /**
     * Display the task transitions
     *
     * @access public
     */
    public function transitions()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('task/transitions', array(
            'task' => $task,
            'transitions' => $this->transition->getAllByTask($task['id']),
        )));
    }

    /**
     * Remove a task
     *
     * @access public
     */
    public function remove()
    {
        $task = $this->getTask();

        if (! $this->taskPermission->canRemoveTask($task)) {
            $this->forbidden();
        }

        if ($this->request->getStringParam('confirmation') === 'yes') {
            $this->checkCSRFParam();

            if ($this->task->remove($task['id'])) {
                $this->session->flash(t('Task removed successfully.'));
            } else {
                $this->session->flashError(t('Unable to remove this task.'));
            }

            $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
        }

        $this->response->html($this->taskLayout('task/remove', array(
            'task' => $task,
        )));
    }
}
