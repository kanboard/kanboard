<?php

namespace Kanboard\Controller;

/**
 * Dashboard Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class DashboardController extends BaseController
{
    /**
     * Dashboard overview
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('dashboard/show', array(
            'title' => t('Dashboard for %s', $this->helper->user->getFullname($user)),
            'project_paginator' => $this->projectPagination->getDashboardPaginator($user['id'], 'show', 10),
            'task_paginator' => $this->taskPagination->getDashboardPaginator($user['id'], 'show', 10),
            'subtask_paginator' => $this->subtaskPagination->getDashboardPaginator($user['id'], 'show', 10),
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
            'title' => t('Tasks overview for %s', $this->helper->user->getFullname($user)),
            'paginator' => $this->taskPagination->getDashboardPaginator($user['id'], 'tasks', 50),
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
            'title' => t('Subtasks overview for %s', $this->helper->user->getFullname($user)),
            'paginator' => $this->subtaskPagination->getDashboardPaginator($user['id'], 'subtasks', 50),
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
            'title' => t('Projects overview for %s', $this->helper->user->getFullname($user)),
            'paginator' => $this->projectPagination->getDashboardPaginator($user['id'], 'projects', 25),
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
            'title' => t('Activity stream for %s', $this->helper->user->getFullname($user)),
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
        $user = $this->getUser();

        $this->response->html($this->helper->layout->dashboard('dashboard/calendar', array(
            'title' => t('Calendar for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
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
            'title' => t('Notifications for %s', $this->helper->user->getFullname($user)),
            'notifications' => $this->userUnreadNotificationModel->getAll($user['id']),
            'user' => $user,
        )));
    }
}
