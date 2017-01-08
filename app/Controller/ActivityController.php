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
     * Activity page for a project
     *
     * @access public
     */
    public function project()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->app('activity/project', array(
            'events' => $this->helper->projectActivity->getProjectEvents($project['id']),
            'project' => $project,
            'title' => t('%s\'s activity', $project['name'])
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
            'title' => $task['title'],
            'task' => $task,
            'project' => $this->projectModel->getById($task['project_id']),
            'events' => $this->helper->projectActivity->getTaskEvents($task['id']),
            'tags' => $this->taskTagModel->getList($task['id']),
        )));
    }
}
