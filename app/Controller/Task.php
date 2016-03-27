<?php

namespace Kanboard\Controller;

use Kanboard\Core\DateParser;

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
            return $this->forbidden(true);
        }

        $task = $this->taskFinder->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            return $this->notfound(true);
        }

        if ($task['project_id'] != $project['id']) {
            return $this->forbidden(true);
        }

        $this->response->html($this->helper->layout->app('task/public', array(
            'project' => $project,
            'comments' => $this->comment->getAll($task['id']),
            'subtasks' => $this->subtask->getAll($task['id']),
            'links' => $this->taskLink->getAllGroupedByLabel($task['id']),
            'task' => $task,
            'columns_list' => $this->column->getList($task['project_id']),
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

        $values = $this->dateParser->format($values, array('date_started'), $this->config->get('application_datetime_format', DateParser::DATE_TIME_FORMAT));

        $this->response->html($this->helper->layout->task('task/show', array(
            'task' => $task,
            'project' => $this->project->getById($task['project_id']),
            'values' => $values,
            'files' => $this->taskFile->getAllDocuments($task['id']),
            'images' => $this->taskFile->getAllImages($task['id']),
            'comments' => $this->comment->getAll($task['id'], $this->userSession->getCommentSorting()),
            'subtasks' => $subtasks,
            'internal_links' => $this->taskLink->getAllGroupedByLabel($task['id']),
            'external_links' => $this->taskExternalLink->getAll($task['id']),
            'link_label_list' => $this->link->getList(0, false),
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

        $this->response->html($this->helper->layout->task('task/analytics', array(
            'task' => $task,
            'project' => $this->project->getById($task['project_id']),
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
            ->setUrl('task', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'pagination' => 'subtasks'))
            ->setMax(15)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTracking->getTaskQuery($task['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->helper->layout->task('task/time_tracking_details', array(
            'task' => $task,
            'project' => $this->project->getById($task['project_id']),
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

        $this->response->html($this->helper->layout->task('task/transitions', array(
            'task' => $task,
            'project' => $this->project->getById($task['project_id']),
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
                $this->flash->success(t('Task removed successfully.'));
            } else {
                $this->flash->failure(t('Unable to remove this task.'));
            }

            $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
        }

        $this->response->html($this->template->render('task/remove', array(
            'task' => $task,
        )));
    }
}
