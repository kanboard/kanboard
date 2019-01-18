<?php

namespace Kanboard\Controller;

/**
 * Activity Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ActivityController extends BaseController
{
    /**
     * Activity page for a user
     *
     * @access public
     */
    public function user()
    {
        $user = $this->getUser();

        $this->response->html($this->template->render('activity/user', array(
            'title'  => t('Activity stream for %s', $this->helper->user->getFullname($user)),
            'events' => $this->helper->projectActivity->getProjectsEvents($this->projectPermissionModel->getActiveProjectIds($user['id']), 100),
            'user'   => $user,
        )));
    }

    /**
     * Activity page for a project
     *
     * @access public
     */
    public function project()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->app('activity/project', array(
            'title'   => t('%s\'s activity', $project['name']),
            'events'  => $this->helper->projectActivity->getProjectEvents($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Display task activities
     *
     * @access public
     */
    public function task()
    {
        $task = $this->getTask();

        $this->response->html($this->helper->layout->task('activity/task', array(
            'title'   => $task['title'],
            'task'    => $task,
            'project' => $this->projectModel->getById($task['project_id']),
            'events'  => $this->helper->projectActivity->getTaskEvents($task['id']),
            'tags'    => $this->taskTagModel->getTagsByTask($task['id']),
        )));
    }
}
