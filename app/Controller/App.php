<?php

namespace Kanboard\Controller;

use Kanboard\Model\Subtask as SubtaskModel;

/**
 * Application controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class App extends Base
{
    /**
     * Common layout for dashboard views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());
        $params['content_for_sublayout'] = $this->template->render($template, $params);

        return $this->template->layout('app/layout', $params);
    }

    /**
     * Get project pagination
     *
     * @access private
     * @param  integer  $user_id
     * @param  string   $action
     * @param  integer  $max
     */
    private function getProjectPaginator($user_id, $action, $max)
    {
        return $this->paginator
            ->setUrl('app', $action, array('pagination' => 'projects', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder('name')
            ->setQuery($this->project->getQueryColumnStats($this->projectPermission->getActiveMemberProjectIds($user_id)))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'projects');
    }

    /**
     * Get task pagination
     *
     * @access private
     * @param  integer  $user_id
     * @param  string   $action
     * @param  integer  $max
     */
    private function getTaskPaginator($user_id, $action, $max)
    {
        return $this->paginator
            ->setUrl('app', $action, array('pagination' => 'tasks', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder('tasks.id')
            ->setQuery($this->taskFinder->getUserQuery($user_id))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks');
    }

    /**
     * Get subtask pagination
     *
     * @access private
     * @param  integer  $user_id
     * @param  string   $action
     * @param  integer  $max
     */
    private function getSubtaskPaginator($user_id, $action, $max)
    {
        return $this->paginator
            ->setUrl('app', $action, array('pagination' => 'subtasks', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder('tasks.id')
            ->setQuery($this->subtask->getUserQuery($user_id, array(SubTaskModel::STATUS_TODO, SubtaskModel::STATUS_INPROGRESS)))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');
    }

    /**
     * Check if the user is connected
     *
     * @access public
     */
    public function status()
    {
        $this->response->text('OK');
    }

    /**
     * Dashboard overview
     *
     * @access public
     */
    public function index()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('app/overview', array(
            'title' => t('Dashboard'),
            'project_paginator' => $this->getProjectPaginator($user['id'], 'index', 10),
            'task_paginator' => $this->getTaskPaginator($user['id'], 'index', 10),
            'subtask_paginator' => $this->getSubtaskPaginator($user['id'], 'index', 10),
            'user' => $user,
        )));
    }

    /**
     * My tasks
     *
     * @access public
     */
    public function tasks()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('app/tasks', array(
            'title' => t('My tasks'),
            'paginator' => $this->getTaskPaginator($user['id'], 'tasks', 50),
            'user' => $user,
        )));
    }

    /**
     * My subtasks
     *
     * @access public
     */
    public function subtasks()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('app/subtasks', array(
            'title' => t('My subtasks'),
            'paginator' => $this->getSubtaskPaginator($user['id'], 'subtasks', 50),
            'user' => $user,
        )));
    }

    /**
     * My projects
     *
     * @access public
     */
    public function projects()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('app/projects', array(
            'title' => t('My projects'),
            'paginator' => $this->getProjectPaginator($user['id'], 'projects', 25),
            'user' => $user,
        )));
    }

    /**
     * My activity stream
     *
     * @access public
     */
    public function activity()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('app/activity', array(
            'title' => t('My activity stream'),
            'events' => $this->projectActivity->getProjects($this->projectPermission->getActiveMemberProjectIds($user['id']), 100),
            'user' => $user,
        )));
    }

    /**
     * My calendar
     *
     * @access public
     */
    public function calendar()
    {
        $this->response->html($this->layout('app/calendar', array(
            'title' => t('My calendar'),
            'user' => $this->getUser(),
        )));
    }

    /**
     * My notifications
     *
     * @access public
     */
    public function notifications()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('app/notifications', array(
            'title' => t('My notifications'),
            'notifications' => $this->userUnreadNotification->getAll($user['id']),
            'user' => $user,
        )));
    }

    /**
     * Render Markdown text and reply with the HTML Code
     *
     * @access public
     */
    public function preview()
    {
        $payload = $this->request->getJson();

        if (empty($payload['text'])) {
            $this->response->html('<p>'.t('Nothing to preview...').'</p>');
        }

        $this->response->html($this->helper->text->markdown($payload['text']));
    }

    /**
     * Task autocompletion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');

        $filter = $this->taskFilterAutoCompleteFormatter
            ->create()
            ->filterByProjects($this->projectPermission->getActiveMemberProjectIds($this->userSession->getId()))
            ->excludeTasks(array($this->request->getIntegerParam('exclude_task_id')));

        // Search by task id or by title
        if (ctype_digit($search)) {
            $filter->filterById($search);
        } else {
            $filter->filterByTitle($search);
        }

        $this->response->json($filter->format());
    }
}
