<?php

namespace Kanboard\Controller;

use Kanboard\Model\Project as ProjectModel;
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
     * Get project pagination
     *
     * @access private
     * @param  integer  $user_id
     * @param  string   $action
     * @param  integer  $max
     * @return \Kanboard\Core\Paginator
     */
    private function getProjectPaginator($user_id, $action, $max)
    {
        return $this->paginator
            ->setUrl('app', $action, array('pagination' => 'projects', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder(ProjectModel::TABLE.'.name')
            ->setQuery($this->project->getQueryColumnStats($this->projectPermission->getActiveProjectIds($user_id)))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'projects');
    }

    /**
     * Get task pagination
     *
     * @access private
     * @param  integer  $user_id
     * @param  string   $action
     * @param  integer  $max
     * @return \Kanboard\Core\Paginator
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
     * @return \Kanboard\Core\Paginator
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

        $this->response->html($this->helper->layout->dashboard('app/overview', array(
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

        $this->response->html($this->helper->layout->dashboard('app/tasks', array(
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

        $this->response->html($this->helper->layout->dashboard('app/subtasks', array(
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

        $this->response->html($this->helper->layout->dashboard('app/projects', array(
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

        $this->response->html($this->helper->layout->dashboard('app/activity', array(
            'title' => t('My activity stream'),
            'events' => $this->projectActivity->getProjects($this->projectPermission->getActiveProjectIds($user['id']), 100),
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
        $this->response->html($this->helper->layout->dashboard('app/calendar', array(
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

        $this->response->html($this->helper->layout->dashboard('app/notifications', array(
            'title' => t('My notifications'),
            'notifications' => $this->userUnreadNotification->getAll($user['id']),
            'user' => $user,
        )));
    }
}
