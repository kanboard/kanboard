<?php

namespace Kanboard\Controller;

use Kanboard\Model\ProjectModel;
use Kanboard\Model\SubtaskModel;

/**
 * Dashboard Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class DashboardController extends BaseController
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
            ->setUrl('DashboardController', $action, array('pagination' => 'projects', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder(ProjectModel::TABLE.'.name')
            ->setQuery($this->projectModel->getQueryColumnStats($this->projectPermissionModel->getActiveProjectIds($user_id)))
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
            ->setUrl('DashboardController', $action, array('pagination' => 'tasks', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder('tasks.id')
            ->setQuery($this->taskFinderModel->getUserQuery($user_id))
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
            ->setUrl('DashboardController', $action, array('pagination' => 'subtasks', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder('tasks.id')
            ->setQuery($this->subtaskModel->getUserQuery($user_id, array(SubTaskModel::STATUS_TODO, SubtaskModel::STATUS_INPROGRESS)))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');
    }

    /**
     * Dashboard overview
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('dashboard/show', array(
            'title' => t('Dashboard'),
            'project_paginator' => $this->getProjectPaginator($user['id'], 'show', 10),
            'task_paginator' => $this->getTaskPaginator($user['id'], 'show', 10),
            'subtask_paginator' => $this->getSubtaskPaginator($user['id'], 'show', 10),
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

        $this->response->html($this->helper->layout->dashboard('dashboard/tasks', array(
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

        $this->response->html($this->helper->layout->dashboard('dashboard/subtasks', array(
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

        $this->response->html($this->helper->layout->dashboard('dashboard/projects', array(
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

        $this->response->html($this->helper->layout->dashboard('dashboard/activity', array(
            'title' => t('My activity stream'),
            'events' => $this->helper->projectActivity->getProjectsEvents($this->projectPermissionModel->getActiveProjectIds($user['id']), 100),
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
        $this->response->html($this->helper->layout->dashboard('dashboard/calendar', array(
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

        $this->response->html($this->helper->layout->dashboard('dashboard/notifications', array(
            'title' => t('My notifications'),
            'notifications' => $this->userUnreadNotificationModel->getAll($user['id']),
            'user' => $user,
        )));
    }
}
