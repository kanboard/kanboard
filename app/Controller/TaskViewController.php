<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Model\UserMetadataModel;

/**
 * Task Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskViewController extends BaseController
{
    /**
     * Public access (display a task)
     *
     * @access public
     */
    public function readonly()
    {
        $project = $this->projectModel->getByToken($this->request->getStringParam('token'));

        if (empty($project)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $task = $this->taskFinderModel->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            throw PageNotFoundException::getInstance()->withoutLayout();
        }

        if ($task['project_id'] != $project['id']) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $this->response->html($this->helper->layout->app('task/public', array(
            'project' => $project,
            'comments' => $this->commentModel->getAll($task['id']),
            'subtasks' => $this->subtaskModel->getAll($task['id']),
            'links' => $this->taskLinkModel->getAllGroupedByLabel($task['id']),
            'task' => $task,
            'columns_list' => $this->columnModel->getList($task['project_id']),
            'colors_list' => $this->colorModel->getList(),
            'tags' => $this->taskTagModel->getList($task['id']),
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
        $subtasks = $this->subtaskModel->getAll($task['id']);
        $commentSortingDirection = $this->userMetadataCacheDecorator->get(UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION, 'ASC');

        $this->response->html($this->helper->layout->task('task/show', array(
            'task' => $task,
            'project' => $this->projectModel->getById($task['project_id']),
            'files' => $this->taskFileModel->getAllDocuments($task['id']),
            'images' => $this->taskFileModel->getAllImages($task['id']),
            'comments' => $this->commentModel->getAll($task['id'], $commentSortingDirection),
            'subtasks' => $subtasks,
            'internal_links' => $this->taskLinkModel->getAllGroupedByLabel($task['id']),
            'external_links' => $this->taskExternalLinkModel->getAll($task['id']),
            'link_label_list' => $this->linkModel->getList(0, false),
            'tags' => $this->taskTagModel->getList($task['id']),
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
            'project' => $this->projectModel->getById($task['project_id']),
            'lead_time' => $this->taskAnalyticModel->getLeadTime($task),
            'cycle_time' => $this->taskAnalyticModel->getCycleTime($task),
            'time_spent_columns' => $this->taskAnalyticModel->getTimeSpentByColumn($task),
            'tags' => $this->taskTagModel->getList($task['id']),
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
            ->setUrl('TaskViewController', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'pagination' => 'subtasks'))
            ->setMax(15)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTrackingModel->getTaskQuery($task['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->helper->layout->task('task/time_tracking_details', array(
            'task' => $task,
            'project' => $this->projectModel->getById($task['project_id']),
            'subtask_paginator' => $subtask_paginator,
            'tags' => $this->taskTagModel->getList($task['id']),
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
            'project' => $this->projectModel->getById($task['project_id']),
            'transitions' => $this->transitionModel->getAllByTask($task['id']),
            'tags' => $this->taskTagModel->getList($task['id']),
        )));
    }
}
