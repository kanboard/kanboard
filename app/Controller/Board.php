<?php

namespace Controller;

/**
 * Board controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Board extends Base
{
    /**
     * Display the public version of a board
     * Access checked by a simple token, no user login, read only, auto-refresh
     *
     * @access public
     */
    public function readonly()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->project->getByToken($token);

        // Token verification
        if (empty($project)) {
            $this->forbidden(true);
        }

        list($categories_listing, $categories_description) = $this->category->getBoardCategories($project['id']);

        // Display the board with a specific layout
        $this->response->html($this->template->layout('board/public', array(
            'project' => $project,
            'swimlanes' => $this->board->getBoard($project['id']),
            'categories_listing' => $categories_listing,
            'categories_description' => $categories_description,
            'title' => $project['name'],
            'description' => $project['description'],
            'no_layout' => true,
            'not_editable' => true,
            'board_public_refresh_interval' => $this->config->get('board_public_refresh_interval'),
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        )));
    }

    /**
     * Redirect the user to the default project
     *
     * @access public
     */
    public function index()
    {
        $last_seen_project_id = $this->userSession->getLastSeenProjectId();
        $favorite_project_id = $this->userSession->getFavoriteProjectId();
        $project_id = $last_seen_project_id ?: $favorite_project_id;

        if (! $project_id) {
            $projects = $this->projectPermission->getAllowedProjects($this->userSession->getId());

            if (empty($projects)) {

                if ($this->userSession->isAdmin()) {
                    $this->redirectNoProject();
                }

                $this->forbidden();
            }

            $project_id = key($projects);
        }

        $this->show($project_id);
    }

    /**
     * Show a board for a given project
     *
     * @access public
     * @param  integer   $project_id    Default project id
     */
    public function show($project_id = 0)
    {
        $project = $this->getProject($project_id);
        $projects = $this->projectPermission->getAllowedProjects($this->userSession->getId());

        $board_selector = $projects;
        unset($board_selector[$project['id']]);

        $this->userSession->storeLastSeenProjectId($project['id']);

        list($categories_listing, $categories_description) = $this->category->getBoardCategories($project['id']);

        $this->response->html($this->template->layout('board/index', array(
            'users' => $this->projectPermission->getMemberList($project['id'], true, true),
            'projects' => $projects,
            'project' => $project,
            'swimlanes' => $this->board->getBoard($project['id']),
            'categories_listing' => $categories_listing,
            'categories_description' => $categories_description,
            'title' => $project['name'],
            'description' => $project['description'],
            'board_selector' => $board_selector,
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        )));
    }

    /**
     * Save the board (Ajax request made by the drag and drop)
     *
     * @access public
     */
    public function save()
    {
        $project_id = $this->request->getIntegerParam('project_id');

        if (! $project_id || ! $this->request->isAjax()) {
            return $this->response->status(403);
        }

        if (! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
            $this->response->text('Forbidden', 403);
        }

        $values = $this->request->getJson();

        $result =$this->taskPosition->movePosition(
            $project_id,
            $values['task_id'],
            $values['column_id'],
            $values['position'],
            $values['swimlane_id']
        );

        if (! $result) {
            return $this->response->status(400);
        }

        list($categories_listing, $categories_description) = $this->category->getBoardCategories($project_id);

        $this->response->html(
            $this->template->render('board/show', array(
                'project' => $this->project->getById($project_id),
                'swimlanes' => $this->board->getBoard($project_id),
                'categories_listing' => $categories_listing,
                'categories_description' => $categories_description,
                'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
                'board_highlight_period' => $this->config->get('board_highlight_period'),
            )),
            201
        );
    }

    /**
     * Check if the board have been changed
     *
     * @access public
     */
    public function check()
    {
        if (! $this->request->isAjax()) {
            return $this->response->status(403);
        }

        $project_id = $this->request->getIntegerParam('project_id');
        $timestamp = $this->request->getIntegerParam('timestamp');

        if (! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
            $this->response->text('Forbidden', 403);
        }

        if (! $this->project->isModifiedSince($project_id, $timestamp)) {
            return $this->response->status(304);
        }

        list($categories_listing, $categories_description) = $this->category->getBoardCategories($project_id);

        $this->response->html(
            $this->template->render('board/show', array(
                'project' => $this->project->getById($project_id),
                'swimlanes' => $this->board->getBoard($project_id),
                'categories_listing' => $categories_listing,
                'categories_description' => $categories_description,
                'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
                'board_highlight_period' => $this->config->get('board_highlight_period'),
            ))
        );
    }

    /**
     * Get links on mouseover
     *
     * @access public
     */
    public function tasklinks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/tasklinks', array(
            'links' => $this->taskLink->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Get subtasks on mouseover
     *
     * @access public
     */
    public function subtasks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/subtasks', array(
            'subtasks' => $this->subtask->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Display all attachments during the task mouseover
     *
     * @access public
     */
    public function attachments()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/files', array(
            'files' => $this->file->getAllDocuments($task['id']),
            'images' => $this->file->getAllImages($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Display comments during a task mouseover
     *
     * @access public
     */
    public function comments()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/comments', array(
            'comments' => $this->comment->getAll($task['id'])
        )));
    }

    /**
     * Display task description
     *
     * @access public
     */
    public function description()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/description', array(
            'task' => $task
        )));
    }

    /**
     * Change a task assignee directly from the board
     *
     * @access public
     */
    public function changeAssignee()
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);

        $this->response->html($this->template->render('board/assignee', array(
            'values' => $task,
            'users_list' => $this->projectPermission->getMemberList($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Validate an assignee modification
     *
     * @access public
     */
    public function updateAssignee()
    {
        $values = $this->request->getValues();

        list($valid,) = $this->taskValidator->validateAssigneeModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->session->flash(t('Task updated successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url('board', 'show', array('project_id' => $values['project_id'])));
    }

    /**
     * Change a task category directly from the board
     *
     * @access public
     */
    public function changeCategory()
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);

        $this->response->html($this->template->render('board/category', array(
            'values' => $task,
            'categories_list' => $this->category->getList($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Validate a category modification
     *
     * @access public
     */
    public function updateCategory()
    {
        $values = $this->request->getValues();

        list($valid,) = $this->taskValidator->validateCategoryModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->session->flash(t('Task updated successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url('board', 'show', array('project_id' => $values['project_id'])));
    }

    /**
     * Screenshot popover
     *
     * @access public
     */
    public function screenshot()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('file/screenshot', array(
            'task' => $task,
            'redirect' => 'board',
        )));
    }

    /**
     * Get recurrence information on mouseover
     *
     * @access public
     */
    public function recurrence()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('task/recurring_info', array(
            'task' => $task,
            'recurrence_trigger_list' => $this->task->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->task->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->task->getRecurrenceBasedateList(),
        )));
    }
}
